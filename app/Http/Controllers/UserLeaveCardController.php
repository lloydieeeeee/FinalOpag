<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveCard;
use App\Models\LeaveCardEntry;
use App\Models\LeaveApplication;
use App\Models\LeaveCreditBalance;
use Carbon\Carbon;

class UserLeaveCardController extends Controller
{
    public function index()
    {
        $year = now()->year;
        return view('leave_card.user_card', compact('year'));
    }

    public function show(Request $request)
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'No employee record linked to your account.'], 404);
        }

        $employeeId = $employee->employee_id;
        $year       = (int) $request->query('year', now()->year);

        $vlBalance = LeaveCreditBalance::where('employee_id', $employeeId)
                        ->where('leave_type_id', 1)
                        ->where('year', $year)
                        ->first();

        $slBalance = LeaveCreditBalance::where('employee_id', $employeeId)
                        ->where('leave_type_id', 2)
                        ->where('year', $year)
                        ->first();

        $currentVl = $vlBalance ? (string) $vlBalance->remaining_balance : null;
        $currentSl = $slBalance ? (string) $slBalance->remaining_balance : null;

        /* ── Old balance — reference_year = $year (no - 1) ── */
        $oldBalanceRecord = DB::table('old_balance')
            ->where('employee_id', $employeeId)
            ->where('reference_year', $year)
            ->first();

        $oldBalanceVl = $oldBalanceRecord ? (float) $oldBalanceRecord->old_vl_balance : 0;
        $oldBalanceSl = $oldBalanceRecord ? (float) $oldBalanceRecord->old_sl_balance : 0;

        $card = LeaveCard::where('employee_id', $employeeId)
                         ->where('year', $year)
                         ->first();

        $hasAppDateCol = DB::getSchemaBuilder()->hasColumn('leave_applications', 'application_date');
        $filingDateCol = $hasAppDateCol ? 'application_date' : 'created_at';

        $applications = LeaveApplication::with('leaveType')
            ->where('employee_id', $employeeId)
            ->where(function ($q) use ($year, $filingDateCol) {
                $q->whereYear($filingDateCol, $year)
                  ->orWhereYear('start_date', $year);
            })
            ->orderBy('created_at', 'ASC')
            ->get()
            ->map(function ($app) use ($filingDateCol) {
                $filingDate  = Carbon::parse($app->{$filingDateCol} ?? $app->created_at)->toDateString();
                $filingMonth = (int) Carbon::parse($filingDate)->month;
                return [
                    'leave_id'         => $app->leave_id,
                    'is_half_day'      => false,
                    'half_day_id'      => null,
                    'leave_type'       => $app->leaveType->type_name        ?? '—',
                    'type_code'        => $app->leaveType->type_code         ?? '—',
                    'is_accrual_based' => $app->leaveType->is_accrual_based  ?? 0,
                    'applied_at'       => $filingDate,
                    'month'            => $filingMonth,
                    'start_date'       => $app->start_date ? Carbon::parse($app->start_date)->toDateString() : null,
                    'end_date'         => $app->end_date   ? Carbon::parse($app->end_date)->toDateString()   : null,
                    'no_of_days'       => (float) $app->no_of_days,
                    'details_of_leave' => $app->details_of_leave,
                    'status'           => $app->status,
                    'is_monetization'  => (bool) $app->is_monetization,
                ];
            });

        if (!$card) {
            return response()->json([
                'success'     => false,
                'employee'    => $this->employeePayload($employee),
                'current_vl'  => $currentVl,
                'current_sl'  => $currentSl,
                'old_balance' => [
                    'vl'             => $oldBalanceVl,
                    'sl'             => $oldBalanceSl,
                    'reference_year' => $year,
                    'found'          => (bool) $oldBalanceRecord,
                ],
                'applications' => $applications,
            ]);
        }

        $entries = LeaveCardEntry::where('leave_card_id', $card->leave_card_id)
                        ->orderBy('entry_order')
                        ->get()
                        ->map(function ($e) {
                            $isSep = str_contains($e->date_particulars ?? '', '--- MONTH SEPARATOR ---');
                            return [
                                'entry_id'             => $e->entry_id,
                                'is_separator'         => $isSep,
                                'entry_order'          => $e->entry_order,
                                'month'                => $e->month,
                                'date_particulars'     => $isSep ? null : $e->date_particulars,
                                'earned_vl'            => $e->earned_vl,
                                'earned_sl'            => $e->earned_sl,
                                'taken_vl'             => $e->taken_vl,
                                'taken_sl'             => $e->taken_sl,
                                'leave_wop'            => $e->leave_wop,
                                'tardy_undertime'      => $e->tardy_undertime,
                                'balance_vl'           => $e->balance_vl,
                                'balance_sl'           => $e->balance_sl,
                                'remarks'              => $e->remarks,
                                'status'               => $e->status,
                                'leave_application_id' => $e->leave_application_id,
                                'half_day_id'          => $e->half_day_id ?? null,
                                'is_manual'            => $e->is_manual,
                            ];
                        });

        return response()->json([
            'success'     => true,
            'employee'    => $this->employeePayload($employee),
            'card'        => [
                'leave_card_id' => $card->leave_card_id,
                'opening_vl'    => $card->opening_vl,
                'opening_sl'    => $card->opening_sl,
            ],
            'entries'      => $entries,
            'current_vl'   => $currentVl,
            'current_sl'   => $currentSl,
            'old_balance'  => [
                'vl'             => $oldBalanceVl,
                'sl'             => $oldBalanceSl,
                'reference_year' => $year,
                'found'          => (bool) $oldBalanceRecord,
            ],
            'applications' => $applications,
        ]);
    }

    public function oldBalancePdf(Request $request)
    {
        $employee = Auth::user()->employee;
        if (!$employee) abort(404);

        $year = (int) $request->query('year', now()->year);

        $record = DB::table('old_balance')
            ->where('employee_id', $employee->employee_id)
            ->where('reference_year', $year)
            ->first();

        if (!$record || !$record->pdf_file) {
            abort(404, 'No PDF found for this year.');
        }

        return response($record->pdf_file, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="old_balance_' . $year . '.pdf"',
        ]);
    }

    public function print(Request $request)
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            abort(404, 'No employee record linked to your account.');
        }

        $employeeId = $employee->employee_id;
        $year       = (int) $request->query('year', now()->year);

        $card = LeaveCard::where('employee_id', $employeeId)
                         ->where('year', $year)
                         ->first();

        $entries = $card
            ? LeaveCardEntry::where('leave_card_id', $card->leave_card_id)
                            ->orderBy('entry_order')
                            ->get()
            : collect();

        $vlBalance = LeaveCreditBalance::where('employee_id', $employeeId)
                        ->where('leave_type_id', 1)
                        ->where('year', $year)
                        ->first();

        $slBalance = LeaveCreditBalance::where('employee_id', $employeeId)
                        ->where('leave_type_id', 2)
                        ->where('year', $year)
                        ->first();

        return view('leave_card.print', compact(
            'employee', 'card', 'entries', 'year', 'vlBalance', 'slBalance'
        ));
    }

    private function employeePayload($emp): array
    {
        return [
            'employee_id'           => $emp->employee_id,
            'last_name'             => $emp->last_name,
            'first_name'            => $emp->first_name,
            'middle_name'           => $emp->middle_name,
            'formatted_employee_id' => $emp->formatted_employee_id ?? $emp->employee_id,
            'position_name'         => $emp->position->position_name    ?? '—',
            'department_name'       => $emp->department->department_name ?? '—',
        ];
    }
}