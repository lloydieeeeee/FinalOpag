<?php
// database/migrations/xxxx_xx_xx_add_status_columns_to_leave_application.php
// Run: php artisan migrate

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Alter the status ENUM to include new values
        DB::statement("
            ALTER TABLE `leave_application`
            MODIFY COLUMN `status`
                enum('PENDING','RECEIVED','ON-PROCESS','APPROVED','REJECTED','CANCELLED')
                NOT NULL DEFAULT 'PENDING'
        ");

        // 2. Add reject_reason, approved_by, approved_at if they don't exist
        Schema::table('leave_application', function (Blueprint $table) {
            if (!Schema::hasColumn('leave_application', 'reject_reason')) {
                $table->text('reject_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('leave_application', 'approved_by')) {
                $table->unsignedInteger('approved_by')->nullable()->after('reject_reason');
            }
            if (!Schema::hasColumn('leave_application', 'approved_at')) {
                $table->datetime('approved_at')->nullable()->after('approved_by');
            }
        });

        // 3. Fix the broken column name "`updated at`" (has a space) → `updated_at`
        //    Only runs if the bad column exists
        $columns = DB::select("SHOW COLUMNS FROM `leave_application`");
        $hasSpaced = collect($columns)->contains(fn($c) => $c->Field === 'updated at');
        if ($hasSpaced) {
            DB::statement("
                ALTER TABLE `leave_application`
                CHANGE `updated at` `updated_at`
                    timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
            ");
        }
    }

    public function down(): void
    {
        // Revert ENUM back to original
        DB::statement("
            ALTER TABLE `leave_application`
            MODIFY COLUMN `status`
                enum('PENDING','APPROVED','REJECTED','CANCELLED')
                NOT NULL DEFAULT 'PENDING'
        ");

        Schema::table('leave_application', function (Blueprint $table) {
            $table->dropColumn(['reject_reason', 'approved_by', 'approved_at']);
        });
    }
};