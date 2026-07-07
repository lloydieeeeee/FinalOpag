<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leave_card_entry', function (Blueprint $table) {
            $table->unsignedBigInteger('manual_leave_type_id')->nullable()->after('leave_application_id');
            $table->decimal('manual_days_taken', 8, 3)->nullable()->after('manual_leave_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('leave_card_entry', function (Blueprint $table) {
            $table->dropColumn(['manual_leave_type_id', 'manual_days_taken']);
        });
    }
};