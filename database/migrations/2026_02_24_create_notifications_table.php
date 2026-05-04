<?php
// database/migrations/xxxx_xx_xx_create_notifications_table.php
// Run: php artisan migrate

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('notification_id');
            $table->unsignedInteger('recipient_employee_id');   // who sees it
            $table->unsignedInteger('actor_employee_id')->nullable(); // who triggered it
            $table->enum('type', [
                'LEAVE_STATUS_CHANGED',
                'LEAVE_SUBMITTED',
                'LEAVE_APPROVED',
                'LEAVE_REJECTED',
                'GENERAL',
            ])->default('GENERAL');
            $table->string('title', 120);
            $table->text('body')->nullable();
            $table->string('url', 300)->nullable();             // redirect URL
            $table->enum('reference_type', [
                'LEAVE_APPLICATION', 'HALF_DAY', 'EMPLOYEE', 'OTHER'
            ])->nullable();
            $table->unsignedInteger('reference_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('recipient_employee_id');
            $table->index(['recipient_employee_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};