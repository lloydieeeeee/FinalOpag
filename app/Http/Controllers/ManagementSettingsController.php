<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ManagementSettingsController extends Controller
{
    /* ─── INDEX ─── */
    public function index()
    {
        return redirect()->route('settings.leaveType');
    }

    /* ══════════════════════════════════
       LEAVE TYPE
    ══════════════════════════════════ */
    public function leaveType()
    {
        $leaveTypes = LeaveType::orderBy('leave_type_id')->get();
        return view('management.leave-type', compact('leaveTypes'));
    }

    public function storeLeaveType(Request $request)
    {
        $v = Validator::make($request->all(), [
            'type_name'         => 'required|string|max:80',
            'type_code'         => 'required|string|max:20|unique:leave_type,type_code',
            'is_accrual_based'  => 'nullable|boolean',
            'accrual_rate'      => 'nullable|numeric|min:0',
            'max_days'          => 'nullable|numeric|min:0.5',
            'notice_days'       => 'nullable|integer|min:0',
            'allow_past_filing' => 'nullable|boolean',
        ], [
            'max_days.min' => 'Annual limit must be at least 0.5. Leave blank for unlimited.',
        ]);
        
        if ($v->fails()) {
            return response()->json(['success' => false, 'message' => $v->errors()->first()], 422);
        }
        
        $lt = LeaveType::create([
            'type_name'         => $request->type_name,
            'type_code'         => strtoupper($request->type_code),
            'is_accrual_based'  => $request->boolean('is_accrual_based'),
            'accrual_rate'      => $request->boolean('is_accrual_based') ? $request->accrual_rate : null,
            'max_days'          => $request->filled('max_days') ? $request->max_days : null,
            'notice_days'       => $request->filled('notice_days') ? $request->notice_days : null,
            'allow_past_filing' => $request->boolean('allow_past_filing'),
            'is_active'         => 1,
        ]);
        
        return response()->json(['success' => true, 'data' => $lt, 'message' => 'Leave type added successfully.']);
    }

    public function updateLeaveType(Request $request, $id)
    {
        $lt = LeaveType::findOrFail($id);
        $v  = Validator::make($request->all(), [
            'type_name'         => 'required|string|max:80',
            'type_code'         => 'required|string|max:20|unique:leave_type,type_code,' . $id . ',leave_type_id',
            'is_accrual_based'  => 'nullable|boolean',
            'accrual_rate'      => 'nullable|numeric|min:0',
            'max_days'          => 'nullable|numeric|min:0.5',
            'notice_days'       => 'nullable|integer|min:0',
            'allow_past_filing' => 'nullable|boolean',
        ], [
            'max_days.min' => 'Annual limit must be at least 0.5. Leave blank for unlimited.',
        ]);
        
        if ($v->fails()) {
            return response()->json(['success' => false, 'message' => $v->errors()->first()], 422);
        }
        
        $lt->update([
            'type_name'         => $request->type_name,
            'type_code'         => strtoupper($request->type_code),
            'is_accrual_based'  => $request->boolean('is_accrual_based'),
            'accrual_rate'      => $request->boolean('is_accrual_based') ? $request->accrual_rate : null,
            'max_days'          => $request->filled('max_days') ? $request->max_days : null,
            'notice_days'       => $request->filled('notice_days') ? $request->notice_days : null,
            'allow_past_filing' => $request->boolean('allow_past_filing'),
        ]);
        
        return response()->json(['success' => true, 'data' => $lt, 'message' => 'Leave type updated successfully.']);
    }

    public function toggleLeaveType($id)
    {
        $lt            = LeaveType::findOrFail($id);
        $lt->is_active = $lt->is_active ? 0 : 1;
        $lt->save();
        return response()->json(['success' => true, 'is_active' => (bool) $lt->is_active]);
    }

    public function destroyLeaveType($id)
    {
        LeaveType::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Leave type deleted.']);
    }

    /* ══════════════════════════════════
       DEPARTMENT
    ══════════════════════════════════ */
    public function department()
    {
        $departments = Department::orderBy('department_id')->get();
        return view('management.department', compact('departments'));
    }

    public function storeDepartment(Request $request)
    {
        $v = Validator::make($request->all(), [
            'department_name' => [
                'required', 'string', 'max:100',
                Rule::unique('department', 'department_name')
                    ->where(function ($query) use ($request) {
                        return $query->whereRaw('LOWER(department_name) = ?', [strtolower(trim($request->department_name))]);
                    }),
            ],
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean'
        ], [
            'department_name.unique' => 'A department with that name already exists.',
        ]);
        
        if ($v->fails()) {
            return response()->json(['success' => false, 'message' => $v->errors()->first()], 422);
        }
        
        $dept = Department::create([
            'department_name' => trim($request->department_name),
            'description'     => $request->description ? trim($request->description) : null,
            'is_active'       => $request->has('is_active') ? $request->is_active : 1,
        ]);
        
        return response()->json(['success' => true, 'data' => $dept, 'message' => 'Department added successfully.']);
    }

    public function updateDepartment(Request $request, $id)
    {
        $dept = Department::findOrFail($id);
        $v    = Validator::make($request->all(), [
            'department_name' => [
                'required', 'string', 'max:100',
                Rule::unique('department', 'department_name')
                    ->ignore($id, 'department_id')
                    ->where(function ($query) use ($request) {
                        return $query->whereRaw('LOWER(department_name) = ?', [strtolower(trim($request->department_name))]);
                    }),
            ],
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean'
        ], [
            'department_name.unique' => 'A department with that name already exists.',
        ]);
        
        if ($v->fails()) {
            return response()->json(['success' => false, 'message' => $v->errors()->first()], 422);
        }
        
        $dept->update([
            'department_name' => trim($request->department_name),
            'description'     => $request->description ? trim($request->description) : null,
            'is_active'       => $request->has('is_active') ? $request->is_active : $dept->is_active,
        ]);
        
        return response()->json(['success' => true, 'data' => $dept, 'message' => 'Department updated successfully.']);
    }

    public function toggleDepartment($id)
    {
        $dept            = Department::findOrFail($id);
        $dept->is_active = $dept->is_active ? 0 : 1;
        $dept->save();
        return response()->json(['success' => true, 'is_active' => (bool) $dept->is_active]);
    }

    public function destroyDepartment($id)
    {
        try {
            Department::findOrFail($id)->delete();
            return response()->json(['success' => true, 'message' => 'Department deleted.']);
        } catch (\Illuminate\Database\QueryException $e) {
            // Check for integrity constraint violation (Code 23000)
            if ($e->getCode() == 23000) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Cannot delete this department because there are employees currently assigned to it. Please reassign those employees first.'
                ]);
            }
            
            return response()->json([
                'success' => false, 
                'message' => 'An unexpected error occurred while deleting the department.'
            ]);
        }
    }

    /* ══════════════════════════════════
       POSITION
    ══════════════════════════════════ */
    public function position()
    {
        $positions = Position::orderBy('position_id')->get();
        return view('management.position', compact('positions'));
    }

    public function storePosition(Request $request)
    {
        $v = Validator::make($request->all(), [
            'position_name' => [
                'required', 'string', 'max:100',
                Rule::unique('position', 'position_name')
                    ->where(function ($query) use ($request) {
                        return $query->whereRaw('LOWER(position_name) = ?', [strtolower(trim($request->position_name))]);
                    }),
            ],
            'position_code' => [
                'required', 'string', 'max:20',
                Rule::unique('position', 'position_code')
                    ->where(function ($query) use ($request) {
                        return $query->whereRaw('LOWER(position_code) = ?', [strtolower(trim($request->position_code))]);
                    }),
            ],
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean'
        ], [
            'position_name.unique' => 'A position with that name already exists.',
            'position_code.unique' => 'A position with that code already exists.',
        ]);
        
        if ($v->fails()) {
            return response()->json(['success' => false, 'message' => $v->errors()->first()], 422);
        }
        
        $pos = Position::create([
            'position_name' => trim($request->position_name),
            'position_code' => strtoupper(trim($request->position_code)),
            'description'   => $request->description ? trim($request->description) : null,
            'is_active'     => $request->has('is_active') ? $request->is_active : 1,
        ]);
        
        return response()->json(['success' => true, 'data' => $pos, 'message' => 'Position added successfully.']);
    }

    public function updatePosition(Request $request, $id)
    {
        $pos = Position::findOrFail($id);
        $v   = Validator::make($request->all(), [
            'position_name' => [
                'required', 'string', 'max:100',
                Rule::unique('position', 'position_name')
                    ->ignore($id, 'position_id')
                    ->where(function ($query) use ($request) {
                        return $query->whereRaw('LOWER(position_name) = ?', [strtolower(trim($request->position_name))]);
                    }),
            ],
            'position_code' => [
                'required', 'string', 'max:20',
                Rule::unique('position', 'position_code')
                    ->ignore($id, 'position_id')
                    ->where(function ($query) use ($request) {
                        return $query->whereRaw('LOWER(position_code) = ?', [strtolower(trim($request->position_code))]);
                    }),
            ],
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean'
        ], [
            'position_name.unique' => 'A position with that name already exists.',
            'position_code.unique' => 'A position with that code already exists.',
        ]);
        
        if ($v->fails()) {
            return response()->json(['success' => false, 'message' => $v->errors()->first()], 422);
        }
        
        $pos->update([
            'position_name' => trim($request->position_name),
            'position_code' => strtoupper(trim($request->position_code)),
            'description'   => $request->description ? trim($request->description) : null,
            'is_active'     => $request->has('is_active') ? $request->is_active : $pos->is_active,
        ]);
        
        return response()->json(['success' => true, 'data' => $pos, 'message' => 'Position updated successfully.']);
    }
    
    public function togglePosition($id)
    {
        $pos            = Position::findOrFail($id);
        $pos->is_active = $pos->is_active ? 0 : 1;
        $pos->save();
        return response()->json(['success' => true, 'is_active' => (bool) $pos->is_active]);
    }

    public function destroyPosition($id)
    {
        try {
            Position::findOrFail($id)->delete();
            return response()->json(['success' => true, 'message' => 'Position deleted.']);
        } catch (\Illuminate\Database\QueryException $e) {
            // Check for integrity constraint violation (Code 23000)
            if ($e->getCode() == 23000) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Cannot delete this position because there are employees currently holding it. Please reassign those employees first.'
                ]);
            }

            return response()->json([
                'success' => false, 
                'message' => 'An unexpected error occurred while deleting the position.'
            ]);
        }
    }

    /* ══════════════════════════════════════════════════════
       GENERIC HELPERS
    ══════════════════════════════════════════════════════ */
    private function optionIndex(string $table, string $view)
    {
        $options = DB::table($table)->orderBy('sort_order')->orderBy('id')->get();
        return view('management.' . $view, compact('options'));
    }

    private function optionStore(Request $request, string $table)
    {
        $v = Validator::make($request->all(), ['label' => 'required|string|max:200']);
        if ($v->fails()) {
            return response()->json(['success' => false, 'message' => $v->errors()->first()], 422);
        }
        $max = DB::table($table)->max('sort_order') ?? -1;
        $id  = DB::table($table)->insertGetId([
            'label'      => $request->label,
            'sort_order' => $max + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['success' => true, 'data' => ['id' => $id], 'message' => 'Option added successfully.']);
    }

    private function optionUpdate(Request $request, string $table, int $id)
    {
        $v = Validator::make($request->all(), ['label' => 'required|string|max:200']);
        if ($v->fails()) {
            return response()->json(['success' => false, 'message' => $v->errors()->first()], 422);
        }
        DB::table($table)->where('id', $id)->update(['label' => $request->label, 'updated_at' => now()]);
        return response()->json(['success' => true, 'message' => 'Option updated successfully.']);
    }

    private function optionDestroy(string $table, int $id)
    {
        DB::table($table)->where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Option deleted.']);
    }

    /* ══════════════════════════════════
       COMMUTATION
    ══════════════════════════════════ */
    public function commutation()                          { return $this->optionIndex('commutation_options', 'commutation'); }
    public function storeCommutation(Request $r)           { return $this->optionStore($r, 'commutation_options'); }
    public function updateCommutation(Request $r, $id)     { return $this->optionUpdate($r, 'commutation_options', $id); }
    public function destroyCommutation($id)                { return $this->optionDestroy('commutation_options', $id); }

    /* ══════════════════════════════════
       RECOMMENDATION
    ══════════════════════════════════ */
    public function recommendation()                       { return $this->optionIndex('recommendation_options', 'recommendation'); }
    public function storeRecommendation(Request $r)        { return $this->optionStore($r, 'recommendation_options'); }
    public function updateRecommendation(Request $r, $id)  { return $this->optionUpdate($r, 'recommendation_options', $id); }
    public function destroyRecommendation($id)             { return $this->optionDestroy('recommendation_options', $id); }

    /* ══════════════════════════════════
       SIGNATORY
    ══════════════════════════════════ */
    public function signatory()
    {
        $options = DB::table('signatory_options')->orderBy('sort_order')->orderBy('id')->get();
        return view('management.signatory', compact('options'));
    }

    public function storeSignatory(Request $r)
    {
        $v = Validator::make($r->all(), [
            'label'     => 'required|string|max:200',
            'full_name' => 'required|string|max:200',
            'title'     => 'required|string|max:200',
        ]);
        if ($v->fails()) return response()->json(['success' => false, 'message' => $v->errors()->first()], 422);
        $max = DB::table('signatory_options')->max('sort_order') ?? -1;
        $id  = DB::table('signatory_options')->insertGetId([
            'label'      => $r->label,
            'full_name'  => $r->full_name,
            'title'      => $r->title,
            'sort_order' => $max + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['success' => true, 'data' => ['id' => $id], 'message' => 'Signatory added successfully.']);
    }

    public function updateSignatory(Request $r, $id)
    {
        $v = Validator::make($r->all(), [
            'label'     => 'required|string|max:200',
            'full_name' => 'required|string|max:200',
            'title'     => 'required|string|max:200',
        ]);
        if ($v->fails()) return response()->json(['success' => false, 'message' => $v->errors()->first()], 422);
        DB::table('signatory_options')->where('id', $id)->update([
            'label'      => $r->label,
            'full_name'  => $r->full_name,
            'title'      => $r->title,
            'updated_at' => now(),
        ]);
        return response()->json(['success' => true, 'message' => 'Signatory updated successfully.']);
    }

    public function destroySignatory($id) { return $this->optionDestroy('signatory_options', $id); }
    
    /* ══════════════════════════════════
       ROLE
    ══════════════════════════════════ */
    public function role()                          { return $this->optionIndex('role_options', 'role'); }
    public function storeRole(Request $r)           { return $this->optionStore($r, 'role_options'); }
    public function updateRole(Request $r, $id)     { return $this->optionUpdate($r, 'role_options', $id); }
    public function destroyRole($id)                { return $this->optionDestroy('role_options', $id); }

    /* ══════════════════════════════════════════════════════
       LEAVE APPLICATION ENFORCEMENT
       ══════════════════════════════════════════════════════ */
       
    public static function checkMaxDays(int $leaveTypeId, float $requestedDays): ?string
    {
        $lt = LeaveType::find($leaveTypeId);
        if (! $lt) {
            return 'Invalid leave type selected.';
        }
        if ($lt->max_days !== null && $requestedDays > (float) $lt->max_days) {
            return "This leave type has an annual limit of {$lt->max_days} day(s). You requested {$requestedDays} day(s).";
        }
        return null; // OK
    }

    /**
     * Checks if the start date complies with the filing notice rules.
     * Call this in your LeaveApplicationController before saving.
     */
    public static function checkFilingNotice(int $leaveTypeId, $startDate): ?string
    {
        $lt = LeaveType::find($leaveTypeId);
        if (! $lt) {
            return 'Invalid leave type selected.';
        }

        // If emergency/past filing is explicitly allowed, return OK immediately
        if ($lt->allow_past_filing) {
            return null;
        }

        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $today = now()->startOfDay();

        // If not allowed to file past dates, reject any date before today
        if ($start->lessThan($today)) {
            return "This leave type does not allow retrospective or past filing.";
        }

        // Check prior notice requirements (if set)
        if ($lt->notice_days !== null && $lt->notice_days > 0) {
            $requiredDate = $today->copy()->addDays($lt->notice_days);
            
            if ($start->lessThan($requiredDate)) {
                return "{$lt->type_name} requires at least {$lt->notice_days} day(s) prior notice. Please select a date on or after {$requiredDate->format('M d, Y')}.";
            }
        }

        return null; // OK
    }
}