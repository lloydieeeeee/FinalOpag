<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Extend payroll_record with all columns found in Excel
        Schema::table('payroll_record', function (Blueprint $table) {
            if (!Schema::hasColumn('payroll_record', 'employee_code'))
                $table->string('employee_code', 30)->nullable()->after('employee_id');
            if (!Schema::hasColumn('payroll_record', 'designation'))
                $table->string('designation', 50)->nullable()->after('employee_code');
            if (!Schema::hasColumn('payroll_record', 'gsis_ec'))
                $table->decimal('gsis_ec', 10, 2)->default(0)->after('gsis_govt');
            if (!Schema::hasColumn('payroll_record', 'gsis_real_estate'))
                $table->decimal('gsis_real_estate', 10, 2)->default(0)->after('gsis_ec');
            if (!Schema::hasColumn('payroll_record', 'gsis_conso'))
                $table->decimal('gsis_conso', 10, 2)->default(0)->after('gsis_real_estate');
            if (!Schema::hasColumn('payroll_record', 'gsis_emergency'))
                $table->decimal('gsis_emergency', 10, 2)->default(0)->after('gsis_conso');
            if (!Schema::hasColumn('payroll_record', 'gsis_mpl'))
                $table->decimal('gsis_mpl', 10, 2)->default(0)->after('gsis_emergency');
            if (!Schema::hasColumn('payroll_record', 'gsis_mpl_lite'))
                $table->decimal('gsis_mpl_lite', 10, 2)->default(0)->after('gsis_mpl');
            if (!Schema::hasColumn('payroll_record', 'gsis_gfal'))
                $table->decimal('gsis_gfal', 10, 2)->default(0)->after('gsis_mpl_lite');
            if (!Schema::hasColumn('payroll_record', 'gsis_computer'))
                $table->decimal('gsis_computer', 10, 2)->default(0)->after('gsis_gfal');
            if (!Schema::hasColumn('payroll_record', 'gsis_policy'))
                $table->decimal('gsis_policy', 10, 2)->default(0)->after('gsis_computer');
            if (!Schema::hasColumn('payroll_record', 'pagibig_mpl'))
                $table->decimal('pagibig_mpl', 10, 2)->default(0)->after('pagibig_govt');
            if (!Schema::hasColumn('payroll_record', 'pagibig_calamity'))
                $table->decimal('pagibig_calamity', 10, 2)->default(0)->after('pagibig_mpl');
            if (!Schema::hasColumn('payroll_record', 'philhealth_govt_share'))
                $table->decimal('philhealth_govt_share', 10, 2)->default(0)->after('philhealth_govt');
            if (!Schema::hasColumn('payroll_record', 'overpayment'))
                $table->decimal('overpayment', 10, 2)->default(0)->after('philhealth_govt_share');
            if (!Schema::hasColumn('payroll_record', 'allowance_ta'))
                $table->decimal('allowance_ta', 10, 2)->default(0)->after('allowance_rata');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_record', function (Blueprint $table) {
            $table->dropColumn([
                'employee_code','designation','gsis_ec','gsis_real_estate',
                'gsis_conso','gsis_emergency','gsis_mpl','gsis_mpl_lite',
                'gsis_gfal','gsis_computer','gsis_policy',
                'pagibig_mpl','pagibig_calamity','philhealth_govt_share',
                'overpayment','allowance_ta',
            ]);
        });
    }
};