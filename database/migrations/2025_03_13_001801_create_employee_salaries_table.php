<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('company_id');
            $table->integer('salary_month')->nullable();
            $table->integer('salary_year')->nullable();
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('gross_salary', 10, 2);
            $table->unsignedBigInteger('salary_type_id');
            $table->decimal('amount', 10, 2);
            $table->decimal('salary_type_amount', 10, 2);
            $table->string('salary_type_amount_type')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('company_details')->onDelete('cascade');
            $table->foreign('salary_type_id')->references('id')->on('salary_types')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_salaries');
    }
};
