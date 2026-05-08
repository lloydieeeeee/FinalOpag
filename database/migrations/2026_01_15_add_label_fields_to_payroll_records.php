<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 2026_01_18_add_label_fields_to_payroll_records.php
//
// SAFE VERSION: Each column is only added when it does not already exist.
// This prevents "Duplicate column" errors if the base-table migration already
// included some of these columns, and also lets you re-run safely.

return new class extends Migration
{
    public function up(): void
    {
        // FIX: table is named 'payroll_record' (singular) in this database
        Schema::table('payroll_record', function (Blueprint $table) {

            if (!Schema::hasColumn('payroll_record', 'label_pera')) {
                $table->string('label_pera', 100)->nullable()->after('allowance_pera');
            }

            if (!Schema::hasColumn('payroll_record', 'label_rata')) {
                $table->string('label_rata', 100)->nullable()->after('allowance_rata');
            }

            if (!Schema::hasColumn('payroll_record', 'label_ta')) {
                $table->string('label_ta', 100)->nullable()->after('allowance_ta');
            }

            if (!Schema::hasColumn('payroll_record', 'label_allowance_other')) {
                $table->string('label_allowance_other', 100)->nullable()->after('allowance_other');
            }

            if (!Schema::hasColumn('payroll_record', 'label_loan_lbp')) {
                $table->string('label_loan_lbp', 100)->nullable()->after('loan_lbp');
            }

            if (!Schema::hasColumn('payroll_record', 'label_loan_dbp')) {
                $table->string('label_loan_dbp', 100)->nullable()->after('loan_dbp');
            }

            if (!Schema::hasColumn('payroll_record', 'label_loan_cngwmpc')) {
                $table->string('label_loan_cngwmpc', 100)->nullable()->after('loan_cngwmpc');
            }

            if (!Schema::hasColumn('payroll_record', 'label_loan_paracle')) {
                $table->string('label_loan_paracle', 100)->nullable()->after('loan_paracle');
            }

            // overpayment_label – required for dynamic label on PDF
            if (!Schema::hasColumn('payroll_record', 'overpayment_label')) {
                $table->string('overpayment_label', 100)->nullable()->after('overpayment');
            }

            // other_deduction_label – same pattern
            if (!Schema::hasColumn('payroll_record', 'other_deduction_label')) {
                $table->string('other_deduction_label', 100)->nullable()->after('other_deduction');
            }
        });
    }

    public function down(): void
    {
        $columns = [
            'label_pera', 'label_rata', 'label_ta', 'label_allowance_other',
            'label_loan_lbp', 'label_loan_dbp', 'label_loan_cngwmpc', 'label_loan_paracle',
            'overpayment_label', 'other_deduction_label',
        ];

        Schema::table('payroll_record', function (Blueprint $table) use ($columns) {
            // Only drop columns that actually exist (safe rollback)
            $existing = array_filter($columns, fn($c) => Schema::hasColumn('payroll_record', $c));
            if (!empty($existing)) {
                $table->dropColumn(array_values($existing));
            }
        });
    }
};