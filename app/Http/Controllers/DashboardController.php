<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use App\Models\LeaveCreditBalance;
use App\Models\LeaveApplication;
use App\Models\HalfDay;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user     = Auth::user();
        $employee = $user->employee;
        $access   = optional(optional($employee)->access)->user_access ?? 'employee';
        $viewAs   = session('view_as', $access);

        // Latest finalized period, or auto-generate current month
        $period = PayrollPeriod::where('status', 'FINALIZED')
            ->orderByDesc('year')->orderByDesc('month')
            ->first();

        if (!$period) {
            $period = PayrollPeriod::current();
            $this->generatePayrollForPeriod($period);
            $period->refresh();
        }

        return $viewAs === 'admin'
            ? $this->adminDashboard($period)
            : $this->employeeDashboard($employee, $period);
    }

    // ─── ADMIN ────────────────────────────────────────────────────
    private function adminDashboard(PayrollPeriod $period)
    {
        // Aggregate totals for current period
        $totals = DB::table('payroll_record')
            ->where('period_id', $period->period_id)
            ->selectRaw('
                COUNT(*)                 AS total_employees,
                SUM(gross_salary)        AS total_gross,
                SUM(net_pay)             AS total_net,
                SUM(gsis_ee)             AS total_gsis,
                SUM(pagibig_ee)          AS total_pagibig,
                SUM(philhealth_ee)       AS total_philhealth,
                SUM(withholding_tax)     AS total_wtax,
                SUM(total_deductions)    AS total_deductions
            ')->first();

        // Per-employee data for chart + table + drawer
        $payrollData = DB::table('payroll_record as pr')
            ->join('employee as e', 'e.employee_id', '=', 'pr.employee_id')
            ->join('position as p', 'p.position_id', '=', 'e.position_id')
            ->where('pr.period_id', $period->period_id)
            ->select(
                'e.employee_id',
                DB::raw("CONCAT(e.last_name, ', ', e.first_name) AS name"),
                'p.position_name AS designation',
                'pr.gross_salary AS gross',
                'pr.gsis_ee      AS gsis',
                'pr.pagibig_ee   AS pagibig',
                'pr.philhealth_ee AS phic',
                'pr.withholding_tax AS wtax',
                'pr.loan_dbp    AS loan_dbp',
                'pr.loan_lbp    AS loan_lbp',
                'pr.loan_cngwmpc AS loan_cngwmpc',
                'pr.loan_paracle AS loan_paracle',
                'pr.allowance_pera AS allowance_pera',
                'pr.allowance_rata AS allowance_rata',
                'pr.allowance_other AS allowance_other',
                'pr.total_deductions',
                'pr.net_pay AS net'
            )
            ->orderBy('e.last_name')
            ->get()
            ->map(fn($r) => (array) $r)
            ->toArray();

        // Monthly trend (Jan–Dec current year)
        $monthlyTrend = $this->buildMonthlyTrend($period);

        // ── Current period counts ─────────────────────────────────
        $totalEmployees = Employee::where('is_active', 1)->count();

        // All approved leaves that are ongoing or upcoming (end_date >= today), exclude monetized leave
        $activeLeaves = LeaveApplication::with(['employee', 'leaveType'])
            ->where('status', 'APPROVED')
            ->whereDate('end_date', '>=', now()->toDateString())
            ->whereHas('leaveType', fn($q) => $q->where('type_code', '!=', 'ML'))
            ->orderBy('start_date')
            ->get();

        $onLeave        = $activeLeaves->count();
        $pendingLeave   = LeaveApplication::where('status', 'PENDING')->count();
        $pendingHalfDay = HalfDay::where('status', 'PENDING')->count();

        // ── Month-over-Month deltas ───────────────────────────────
        $prevMonthStart = now()->subMonth()->startOfMonth();
        $prevMonthEnd   = now()->subMonth()->endOfMonth();

        // Delta: Total Employees
        // Compare active headcount now vs headcount recorded in last month's payroll period
        $prevPeriod = PayrollPeriod::where('year', $prevMonthStart->year)
            ->where('month', $prevMonthStart->month)
            ->first();

        $prevEmployeeCount = $prevPeriod
            ? DB::table('payroll_record')->where('period_id', $prevPeriod->period_id)->count()
            : null;

        $deltaEmployees = $prevEmployeeCount !== null
            ? $totalEmployees - $prevEmployeeCount
            : null;

        // Delta: Employees on Leave
        // Count approved leaves that overlapped last calendar month
        $prevOnLeave = LeaveApplication::where('status', 'APPROVED')
            ->whereDate('start_date', '<=', $prevMonthEnd->toDateString())
            ->whereDate('end_date',   '>=', $prevMonthStart->toDateString())
            ->whereHas('leaveType', fn($q) => $q->where('type_code', '!=', 'ML'))
            ->count();
        $deltaOnLeave = $onLeave - $prevOnLeave;

        // Delta: Pending Leave
        // Current pending vs pending applications that existed before this month started
        $prevPendingLeave = LeaveApplication::where('status', 'PENDING')
            ->whereDate('created_at', '<', now()->startOfMonth()->toDateString())
            ->count();
        $deltaPendingLeave = $pendingLeave - $prevPendingLeave;

        // Delta: Pending Half-Day
        $prevPendingHalfDay = HalfDay::where('status', 'PENDING')
            ->whereDate('created_at', '<', now()->startOfMonth()->toDateString())
            ->count();
        $deltaPendingHalfDay = $pendingHalfDay - $prevPendingHalfDay;

        // ── MoM deltas for payroll chart cards ───────────────────
        $trendWithData = array_values(array_filter((array)$monthlyTrend, fn($r) => $r->has_data));
        $deltaGross = $deltaDed = $deltaNet = null;
        if (count($trendWithData) >= 2) {
            $last = end($trendWithData);
            $prev = $trendWithData[count($trendWithData) - 2];
            $deltaGross = $prev->total_gross      > 0 ? (($last->total_gross      - $prev->total_gross)      / $prev->total_gross      * 100) : null;
            $deltaDed   = $prev->total_deductions > 0 ? (($last->total_deductions - $prev->total_deductions) / $prev->total_deductions * 100) : null;
            $deltaNet   = $prev->total_net        > 0 ? (($last->total_net        - $prev->total_net)        / $prev->total_net        * 100) : null;
        }

        return view('dashboard', [
            'period'             => $period,
            'payrollData'        => $payrollData,
            'monthlyTrend'       => $monthlyTrend,
            'totalEmployees'     => $totalEmployees,
            'totalGross'         => (float)($totals->total_gross      ?? 0),
            'totalNet'           => (float)($totals->total_net        ?? 0),
            'totalGsis'          => (float)($totals->total_gsis       ?? 0),
            'totalPagibig'       => (float)($totals->total_pagibig    ?? 0),
            'totalPhilhealth'    => (float)($totals->total_philhealth ?? 0),
            'totalWtax'          => (float)($totals->total_wtax       ?? 0),
            'totalDeductions'    => (float)($totals->total_deductions ?? 0),
            'pendingLeave'       => $pendingLeave,
            'pendingHalfDay'     => $pendingHalfDay,
            // Absence panel
            'onLeave'            => $onLeave,
            'activeLeaves'       => $activeLeaves,
            // Payroll chart MoM deltas
            'deltaGross'         => $deltaGross,
            'deltaDed'           => $deltaDed,
            'deltaNet'           => $deltaNet,
            // Stat card MoM deltas
            'deltaEmployees'     => $deltaEmployees,
            'deltaOnLeave'       => $deltaOnLeave,
            'deltaPendingLeave'  => $deltaPendingLeave,
            'deltaPendingHalfDay'=> $deltaPendingHalfDay,
        ]);
    }

    // ─── EMPLOYEE ─────────────────────────────────────────────────
    private function employeeDashboard(?Employee $employee, PayrollPeriod $period)
    {
        $employeeId = $employee?->employee_id;
        $year       = now()->year;

        $row = PayrollRecord::where('period_id', $period->period_id)
            ->where('employee_id', $employeeId)
            ->first();

        if (!$row && $employee && $employee->salary > 0) {
            $computed = PayrollRecord::computeFromSalary((float) $employee->salary);
            $row = new PayrollRecord(array_merge(
                ['period_id' => $period->period_id, 'employee_id' => $employeeId],
                $computed
            ));
        }

        $vlBalance = LeaveCreditBalance::where('employee_id', $employeeId)
            ->whereHas('leaveType', fn($q) => $q->where('type_code', 'VL'))
            ->where('year', $year)->first();

        $slBalance = LeaveCreditBalance::where('employee_id', $employeeId)
            ->whereHas('leaveType', fn($q) => $q->where('type_code', 'SL'))
            ->where('year', $year)->first();

        $pendingLeave   = LeaveApplication::where('employee_id', $employeeId)->where('status', 'PENDING')->count();
        $pendingHalfDay = HalfDay::where('employee_id', $employeeId)->where('status', 'PENDING')->count();

        return view('dashboard_user', [
            'period'          => $period,
            'gross'           => (float)($row?->gross_salary    ?? $employee?->salary ?? 0),
            'net'             => (float)($row?->net_pay         ?? 0),
            'gsis'            => (float)($row?->gsis_ee         ?? 0),
            'pagibig'         => (float)($row?->pagibig_ee      ?? 0),
            'phic'            => (float)($row?->philhealth_ee   ?? 0),
            'wtax'            => (float)($row?->withholding_tax ?? 0),
            'totDed'          => (float)($row?->total_deductions?? 0),
            'vlBalance'       => $vlBalance,
            'slBalance'       => $slBalance,
            'pendingLeave'    => $pendingLeave,
            'pendingHalfDay'  => $pendingHalfDay,
        ]);
    }

    // ─── Monthly trend helper ──────────────────────────────────────
    private function buildMonthlyTrend(PayrollPeriod $period): array
    {
        $year = now()->year;

        $rows = DB::table('payroll_record as pr')
            ->join('payroll_period as pp', 'pp.period_id', '=', 'pr.period_id')
            ->where('pp.year', $year)
            ->selectRaw('
                pp.month,
                pp.period_label,
                1                        AS has_data,
                SUM(pr.gross_salary)     AS total_gross,
                SUM(pr.total_deductions) AS total_deductions,
                SUM(pr.net_pay)          AS total_net
            ')
            ->groupBy('pp.month', 'pp.period_label')
            ->orderBy('pp.month')
            ->get()
            ->keyBy('month');

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[] = $rows->get($m) ?? (object)[
                'month'            => $m,
                'period_label'     => now()->setMonth($m)->format('M'),
                'has_data'         => 0,
                'total_gross'      => 0,
                'total_deductions' => 0,
                'total_net'        => 0,
            ];
        }

        return $months;
    }

    // ─── Auto-generate payroll records ────────────────────────────
    private function generatePayrollForPeriod(PayrollPeriod $period): void
    {
        Employee::where('is_active', 1)->where('salary', '>', 0)
            ->each(function ($emp) use ($period) {
                $data = PayrollRecord::computeFromSalary((float) $emp->salary);
                PayrollRecord::updateOrCreate(
                    ['period_id' => $period->period_id, 'employee_id' => $emp->employee_id],
                    $data
                );
            });
        $period->update(['status' => 'FINALIZED']);
    }
}