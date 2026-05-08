<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PayrollDeduction;

/**
 * PayrollDeductionController
 *
 * Manages the `payroll_deductions` table.
 *
 * ACTUAL table columns (from DB dump):
 * id, parent_id (nullable FK → self), name, type ('Fixed'|'Not Fixed'),
 * rate (display label), rate_value (decimal), rate_type ('percent'|'flat'),
 * limit_amount (nullable decimal), status (varchar), is_active (tinyint),
 * is_deducted (tinyint), entry_kind ('deduction'|'addition'),
 * sort_order (int), created_at, updated_at
 */
class PayrollDeductionController extends Controller
{
    // ─── INDEX ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $search    = $request->query('search', '');
        // Tab: 'all' | 'deduction' | 'addition'  (matches entry_kind column values)
        $activeTab = $request->query('tab', 'all');

        // Load root-level records (parent_id IS NULL) with their children eager-loaded
        $rootQuery = PayrollDeduction::whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->orderBy('sort_order')->orderBy('id');
            }]);

        if ($activeTab !== 'all') {
            $rootQuery->where('entry_kind', $activeTab);
        }

        if ($search) {
            $rootQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('children', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $groups = $rootQuery->orderBy('sort_order')->orderBy('id')->get();

        if ($request->expectsJson()) {
            return response()->json($groups->load('children'));
        }

        // Stats: count root groups per kind; count all active rows
        $stats = [
            'total'      => PayrollDeduction::whereNull('parent_id')->count(),
            'deductions' => PayrollDeduction::whereNull('parent_id')->where('entry_kind', 'deduction')->count(),
            'additions'  => PayrollDeduction::whereNull('parent_id')->where('entry_kind', 'addition')->count(),
            'active'     => PayrollDeduction::where('is_active', 1)->count(),
        ];

        return view('payroll.deductions', compact('groups', 'search', 'activeTab', 'stats'));
    }

    // ─── STORE ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'type'         => 'required|in:Fixed,Not Fixed',
            'rate_value'   => 'nullable|numeric|min:0',
            'rate_type'    => 'required|in:percent,flat',
            'limit_amount' => 'nullable|numeric|min:0',
            'sort_order'   => 'nullable|integer|min:0',
            'parent_id'    => 'nullable|integer|exists:payroll_deductions,id',
            'is_deducted'  => 'nullable|boolean',
            'entry_kind'   => 'required|in:deduction,addition',
        ]);

        $data['status']     = $data['name'];
        $data['rate']       = $this->buildRateLabel($data);
        $data['is_active']  = true;
        $data['rate_value'] = $data['rate_value'] ?? 0;
        $data['is_deducted'] = $data['is_deducted'] ?? ($data['entry_kind'] === 'deduction');

        if (empty($data['sort_order'])) {
            $data['sort_order'] = (PayrollDeduction::max('sort_order') ?? 0) + 1;
        }

        $deduction = PayrollDeduction::create($data);

        PayrollDeduction::flushCache();

        return response()->json(['success' => true, 'deduction' => $deduction]);
    }

    // ─── UPDATE ───────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $deduction = PayrollDeduction::findOrFail($id);

        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'type'         => 'required|in:Fixed,Not Fixed',
            'rate_value'   => 'nullable|numeric|min:0',
            'rate_type'    => 'required|in:percent,flat',
            'limit_amount' => 'nullable|numeric|min:0',
            'sort_order'   => 'nullable|integer|min:0',
            'parent_id'    => 'nullable|integer|exists:payroll_deductions,id',
            'is_deducted'  => 'nullable|boolean',
            'entry_kind'   => 'required|in:deduction,addition',
        ]);

        $data['status'] = $data['name'];
        $data['rate']   = $this->buildRateLabel($data);

        $deduction->update($data);

        return response()->json(['success' => true, 'deduction' => $deduction->fresh()]);
    }

    // ─── TOGGLE ACTIVE ────────────────────────────────────────────────────────
    public function toggle($id)
    {
        $deduction = PayrollDeduction::findOrFail($id);
        $deduction->update(['is_active' => !$deduction->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => (bool) $deduction->is_active,
        ]);
    }

    // ─── DESTROY ──────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $deduction = PayrollDeduction::findOrFail($id);
        
        // SOFT DELETE: We mark it as inactive instead of permanently deleting the row.
        // This preserves the name and history so old PDF payslips don't break.
        $deduction->update(['is_active' => 0]); 

        PayrollDeduction::flushCache();

        return response()->json(['success' => true, 'message' => 'Component safely archived.']);
    }

    // ─── REORDER ─────────────────────────────────────────────────────────────
    public function reorder(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        foreach ($request->ids as $order => $id) {
            PayrollDeduction::where('id', $id)
                ->update(['sort_order' => $order + 1]);
        }

        PayrollDeduction::flushCache();

        return response()->json(['success' => true]);
    }

    // ─── PRIVATE ─────────────────────────────────────────────────────────────

    /**
     * Build the human-readable rate label stored in the `rate` column.
     *
     * For Pag-IBIG (IDs 22 & 23) the label is auto-descriptive since the
     * rate is tiered. For all others, derive from rate_value + rate_type.
     *
     * Examples: "9%"  |  "₱200.00"  |  "Tiered (1–2%)"
     */
    private function buildRateLabel(array $data): ?string
    {
        // Pag-IBIG tiered label
        $parentId = $data['parent_id'] ?? null;
        // We can't know the ID before insert, so callers that need the exact
        // Pag-IBIG label should pass it explicitly; for generic saves the
        // standard path below is fine and will be overwritten on next edit.

        $val = $data['rate_value'] ?? null;

        if (!$val || $val == 0) {
            return null;
        }

        if ($data['rate_type'] === 'percent') {
            // rate_value is stored as a decimal fraction (0.09 = 9%)
            return round($val * 100, 4) . '%';
        }

        return '₱' . number_format($val, 2);
    }
}