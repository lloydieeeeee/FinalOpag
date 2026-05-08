<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Original migration: 2026_01_18_add_label_fields_to_payroll_records.php
// This file extends that migration by adding the missing overpayment_label
// column that both PayrollController and PayslipController now reference.
// Run: php artisan migrate

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_records', function (Blueprint $table) {
            // ── Original label columns (kept exactly as-is) ───────────────
            $table->string('label_pera',            100)->nullable()->after('allowance_pera');
            $table->string('label_rata',            100)->nullable()->after('allowance_rata');
            $table->string('label_ta',              100)->nullable()->after('allowance_ta');
            $table->string('label_allowance_other', 100)->nullable()->after('allowance_other');
            $table->string('label_loan_lbp',        100)->nullable()->after('loan_lbp');
            $table->string('label_loan_dbp',        100)->nullable()->after('loan_dbp');
            $table->string('label_loan_cngwmpc',    100)->nullable()->after('loan_cngwmpc');
            $table->string('label_loan_paracle',    100)->nullable()->after('loan_paracle');

            // ── FIX 2: Added missing overpayment_label column ─────────────
            // Without this column the dynamic Overpayment label typed in the
            // frontend cannot be persisted, so it always shows "Overpayment"
            // (hardcoded) in the printed PDF instead of the custom text.
            $table->string('overpayment_label',     100)->nullable()->after('overpayment');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_records', function (Blueprint $table) {
            $table->dropColumn([
                'label_pera', 'label_rata', 'label_ta', 'label_allowance_other',
                'label_loan_lbp', 'label_loan_dbp', 'label_loan_cngwmpc', 'label_loan_paracle',
                // FIX 2: also drop on rollback
                'overpayment_label',
            ]);
        });
    }
};