<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_access', function (Blueprint $table) {
            $table->bigIncrements('user_access_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('user_access')->default('employee');
            $table->timestamps();
        });

        Schema::table('user_access', function (Blueprint $table) {
            $table->foreign('employee_id')
                  ->references('employee_id')
                  ->on('employees')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::table('user_access', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
        });
        Schema::dropIfExists('user_access');
    }
};