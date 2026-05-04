<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name', 100);
            $table->string('type', 20)->default('Fixed');
            $table->string('rate', 50)->nullable();
            $table->decimal('rate_value', 10, 4)->default(0.0000);
            $table->enum('rate_type', ['percent', 'flat'])->default('flat');
            $table->decimal('limit_amount', 12, 2)->nullable();
            $table->string('status', 100)->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('is_deducted')->default(1);
            $table->enum('entry_kind', ['deduction', 'addition'])->default('deduction');
            $table->integer('sort_order')->unsigned()->default(0);
            $table->timestamps();

            // Self-referencing foreign key for nested deductions
            $table->foreign('parent_id')
                  ->references('id')->on('payroll_deductions')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payroll_deductions');
    }
};