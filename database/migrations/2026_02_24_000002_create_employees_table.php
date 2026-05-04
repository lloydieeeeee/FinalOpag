<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('employee_id');
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->date('birthday')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('address')->nullable();
            $table->date('hire_date')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->decimal('salary', 12, 2)->nullable();
            $table->timestamps();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->foreign('position_id')
                  ->references('position_id')
                  ->on('positions')
                  ->nullOnDelete();
        });
    }

    public function down(): void {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
        });
        Schema::dropIfExists('employees');
    }
};