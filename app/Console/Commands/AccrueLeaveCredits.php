<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\LeaveCreditBalance;
use App\Models\AccrualLog;

class AccrueLeaveCredits extends Command
{
    protected $signature   = 'leave:accrue {--force : Skip the first-of-month check for testing}';
    protected $description = 'Accrue 1.25 VL and 1.25 SL credits for all active employees on the 1st of every month';

    public function handle(): int
    {
        $today = Carbon::now();

        if (!$this->option('force') && $today->day !== 1) {
            $this->warn("Today is not the 1st of the month. Use --force to run anyway.");
            return self::FAILURE;
        }

        $accrualDate = $today->toDateString();
        $year        = (int) $today->format('Y');
        $month       = (int) $today->format('m');
        $monthLabel  = $today->format('F Y');

        $leaveTypes = LeaveType::where('is_accrual_based', 1)
            ->where('is_active', 1)
            ->get()
            ->keyBy('type_code');

        if ($leaveTypes->isEmpty()) {
            $this->error('No accrual-based leave types found.');
            return self::FAILURE;
        }

        $employees = Employee::where('is_active', 1)->get();

        if ($employees->isEmpty()) {
            $this->warn('No active employees found.');
            return self::SUCCESS;
        }

        $this->info("Starting accrual for {$employees->count()} employee(s) — {$monthLabel}...");

        $accrued = 0;
        $skipped = 0;

        DB::transaction(function () use (
            $employees, $leaveTypes, $accrualDate,
            $year, $month, $monthLabel, &$accrued, &$skipped
        ) {
            foreach ($employees as $employee) {

                // 1. Get or create leave_card for this year
                $leaveCard = DB::table('leave_card')
                    ->where('employee_id', $employee->employee_id)
                    ->where('year', $year)
                    ->first();

                if (!$leaveCard) {
                    $leaveCardId = DB::table('leave_card')->insertGetId([
                        'employee_id' => $employee->employee_id,
                        'year'        => $year,
                        'opening_vl'  => 0,
                        'opening_sl'  => 0,
                        'created_by'  => 0,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                    $this->line("  CARD  Created leave card for {$employee->last_name}, {$employee->first_name} ({$year})");
                } else {
                    $leaveCardId = $leaveCard->leave_card_id;
                }

                // 2. Get current running balances from last entry
                $lastEntry = DB::table('leave_card_entry')
                    ->where('leave_card_id', $leaveCardId)
                    ->orderBy('entry_order', 'desc')
                    ->first();

                $currentBalanceVL = $lastEntry
                    ? (float) $lastEntry->balance_vl
                    : (float) ($leaveCard->opening_vl ?? 0);

                $currentBalanceSL = $lastEntry
                    ? (float) $lastEntry->balance_sl
                    : (float) ($leaveCard->opening_sl ?? 0);

                // 3. Get next entry_order
                $nextOrder = DB::table('leave_card_entry')
                    ->where('leave_card_id', $leaveCardId)
                    ->max('entry_order') ?? -1;
                $nextOrder = (int) $nextOrder + 1;

                // 4. Accrue each leave type
                $vlAccrued = false;
                $slAccrued = false;
                $earnedVL  = 0;
                $earnedSL  = 0;

                foreach ($leaveTypes as $leaveType) {
                    $rate = (float) $leaveType->accrual_rate;
                    $isVL = str_contains(strtolower($leaveType->type_name), 'vacation');
                    $isSL = str_contains(strtolower($leaveType->type_name), 'sick');

                    // Check duplicate accrual this month
                    $alreadyAccrued = AccrualLog::where('employee_id',   $employee->employee_id)
                        ->where('leave_type_id', $leaveType->leave_type_id)
                        ->whereYear('accrual_date',  $year)
                        ->whereMonth('accrual_date', $month)
                        ->exists();

                    if ($alreadyAccrued) {
                        $this->line("  SKIP  {$employee->last_name}, {$employee->first_name} — {$leaveType->type_name} (already accrued)");
                        $skipped++;
                        continue;
                    }

                    // Update credit_balance
                    $balance = LeaveCreditBalance::firstOrCreate(
                        [
                            'employee_id'   => $employee->employee_id,
                            'leave_type_id' => $leaveType->leave_type_id,
                            'year'          => $year,
                        ],
                        [
                            'total_accrued'     => 0,
                            'total_used'        => 0,
                            'remaining_balance' => 0,
                        ]
                    );

                    $balance->total_accrued     += $rate;
                    $balance->remaining_balance += $rate;
                    $balance->save();

                    // Log accrual
                    AccrualLog::create([
                        'employee_id'       => $employee->employee_id,
                        'leave_type_id'     => $leaveType->leave_type_id,
                        'credit_balance_id' => $balance->credit_balance_id,
                        'accrual_date'      => $accrualDate,
                        'days_accrued'      => $rate,
                        'remarks'           => "Monthly accrual for {$monthLabel}",
                    ]);

                    if ($isVL) { $earnedVL = $rate; $vlAccrued = true; }
                    if ($isSL) { $earnedSL = $rate; $slAccrued = true; }

                    $this->info("  OK    {$employee->last_name}, {$employee->first_name} — {$leaveType->type_name} +{$rate}");
                    $accrued++;
                }

                // 5. Write ONE leave_card_entry for both VL + SL
                if ($vlAccrued || $slAccrued) {
                    $newBalanceVL = $currentBalanceVL + $earnedVL;
                    $newBalanceSL = $currentBalanceSL + $earnedSL;

                    DB::table('leave_card_entry')->insert([
                        'leave_card_id'        => $leaveCardId,
                        'entry_order'          => $nextOrder,
                        'month'                => null,
                        'date_particulars'     => $monthLabel,
                        'earned_vl'            => $earnedVL > 0 ? $earnedVL : null,
                        'earned_sl'            => $earnedSL > 0 ? $earnedSL : null,
                        'taken_vl'             => null,
                        'taken_sl'             => null,
                        'leave_wop'            => null,
                        'tardy_undertime'      => null,
                        'balance_vl'           => $newBalanceVL,
                        'balance_sl'           => $newBalanceSL,
                        'remarks'              => 'Accrual',
                        'status'               => 'APPROVED',
                        'leave_application_id' => null,
                        'half_day_id'          => null,
                        'is_manual'            => 0,
                        'created_by'           => 0,
                        'created_at'           => now(),
                        'updated_at'           => now(),
                    ]);

                    $this->line("  CARD  Entry added — VL: {$newBalanceVL} | SL: {$newBalanceSL}");
                }
            }
        });

        $this->newLine();
        $this->info("Done! Accrued: {$accrued} | Skipped: {$skipped}");

        return self::SUCCESS;
    }
}