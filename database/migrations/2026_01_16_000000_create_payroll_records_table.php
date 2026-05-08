<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only create if it doesn't already exist (safe to re-run)
        if (!Schema::hasTable('payroll_records')) {
            Schema::create('payroll_records', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('period_id');
                $table->unsignedBigInteger('employee_id');
                $table->string('designation', 50)->nullable();

                // ── Earnings ────────────────────────────────────────────
                $table->decimal('gross_salary', 12, 2)->default(0);

                // ── GSIS (Employee Share) ────────────────────────────────
                $table->decimal('gsis_ee',          10, 2)->default(0);
                $table->decimal('gsis_govt',         10, 2)->default(0);
                $table->decimal('gsis_ec',           10, 2)->default(0);
                $table->decimal('gsis_policy',       10, 2)->default(0);
                $table->decimal('gsis_emergency',    10, 2)->default(0);
                $table->decimal('gsis_real_estate',  10, 2)->default(0);
                $table->decimal('gsis_mpl',          10, 2)->default(0);
                $table->decimal('gsis_mpl_lite',     10, 2)->default(0);
                $table->decimal('gsis_gfal',         10, 2)->default(0);
                $table->decimal('gsis_computer',     10, 2)->default(0);
                $table->decimal('gsis_conso',        10, 2)->default(0);

                // ── Pag-IBIG ─────────────────────────────────────────────
                $table->decimal('pagibig_ee',        10, 2)->default(0);
                $table->decimal('pagibig_govt',      10, 2)->default(0);
                $table->decimal('pagibig_mpl',       10, 2)->default(0);
                $table->decimal('pagibig_calamity',  10, 2)->default(0);

                // ── PhilHealth ───────────────────────────────────────────
                $table->decimal('philhealth_ee',     10, 2)->default(0);
                $table->decimal('philhealth_govt',   10, 2)->default(0);

                // ── Withholding Tax ──────────────────────────────────────
                $table->decimal('withholding_tax',   10, 2)->default(0);

                // ── Loans ────────────────────────────────────────────────
                $table->decimal('loan_dbp',          10, 2)->default(0);
                $table->string('label_loan_dbp',    100)->nullable();
                $table->decimal('loan_lbp',          10, 2)->default(0);
                $table->string('label_loan_lbp',    100)->nullable();
                $table->decimal('loan_cngwmpc',      10, 2)->default(0);
                $table->string('label_loan_cngwmpc', 100)->nullable();
                $table->decimal('loan_paracle',      10, 2)->default(0);
                $table->string('label_loan_paracle', 100)->nullable();

                // ── CNG/WMPC Breakdown ───────────────────────────────────
                $table->decimal('cng_capital_share',  10, 2)->default(0);
                $table->decimal('cng_kiddie_savings', 10, 2)->default(0);
                $table->decimal('cng_savings',         10, 2)->default(0);
                $table->decimal('cng_regular_loan',   10, 2)->default(0);
                $table->decimal('cng_crisis_loan',    10, 2)->default(0);
                $table->decimal('cng_coop_canteen',   10, 2)->default(0);
                $table->decimal('cng_coop_store',     10, 2)->default(0);
                $table->decimal('cng_calamity_loan',  10, 2)->default(0);
                $table->decimal('cng_abuloy',          10, 2)->default(0);
                $table->decimal('cng_handog',          10, 2)->default(0);
                $table->decimal('cng_b2b_loan',       10, 2)->default(0);
                $table->decimal('cng_petty_cash',     10, 2)->default(0);
                $table->decimal('cng_commodity_loan', 10, 2)->default(0);

                // ── Other Deductions ─────────────────────────────────────
                $table->decimal('overpayment',       10, 2)->default(0);
                $table->string('overpayment_label', 100)->nullable();
                $table->decimal('other_deduction',   10, 2)->default(0);
                $table->string('other_deduction_label', 100)->nullable();

                // ── Allowances ───────────────────────────────────────────
                $table->decimal('allowance_pera',    10, 2)->default(0);
                $table->string('label_pera',        100)->nullable();
                $table->decimal('allowance_rata',    10, 2)->default(0);
                $table->string('label_rata',        100)->nullable();
                $table->decimal('allowance_ta',      10, 2)->default(0);
                $table->string('label_ta',          100)->nullable();
                $table->decimal('allowance_other',   10, 2)->default(0);
                $table->string('label_allowance_other', 100)->nullable();

                // ── Totals ───────────────────────────────────────────────
                $table->decimal('total_deductions',  12, 2)->default(0);
                $table->decimal('total_allowances',  12, 2)->default(0);
                $table->decimal('net_pay',           12, 2)->default(0);

                // ── Dynamic deductions (JSON) ────────────────────────────
                $table->json('dynamic_deductions')->nullable();

                // ── Remittance hide flags ────────────────────────────────
                $table->boolean('hide_gsis')->default(false);
                $table->boolean('hide_pagibig')->default(false);
                $table->boolean('hide_philhealth')->default(false);

                $table->timestamps();

                $table->unique(['period_id', 'employee_id']);
                $table->index('employee_id');
                $table->index('period_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_records');
    }
};