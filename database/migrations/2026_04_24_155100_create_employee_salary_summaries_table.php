<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    if (!Schema::hasTable('employee_salary_summaries')) {
        Schema::create('employee_salary_summaries', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('company_id');
        $table->unsignedBigInteger('employee_id');
        $table->string('employee_name');
        $table->string('salary_month');
        $table->string('salary_year');
        $table->decimal('basic_salary', 10, 2);
        $table->decimal('total_earning', 10, 2)->default(0);
        $table->decimal('total_deduction', 10, 2)->default(0);
        $table->decimal('other_deduction', 10, 2)->default(0);
        $table->decimal('reimbursement', 10, 2)->default(0);
        $table->decimal('net_salary', 10, 2);
        $table->integer('absent_days')->default(0);
        $table->integer('working_days')->default(0);
        $table->string('status')->default('Generated');
        $table->timestamp('generated_date');
        $table->timestamps();
        
        $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        $table->index(['company_id', 'employee_id', 'salary_month', 'salary_year']);
    });
    }
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_salary_summaries');
    }
};
