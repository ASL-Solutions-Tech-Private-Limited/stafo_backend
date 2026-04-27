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
        Schema::create('device_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->nullable(); // Foreign key for employee (nullable in case of company)
            $table->unsignedBigInteger('company_id')->nullable();  // Foreign key for company (nullable in case of employee)
            $table->string('employee_device_id')->nullable(); // Device ID for employee
            $table->string('company_device_id')->nullable();  // Device ID for company
            $table->timestamps();

            // Add foreign key relationships
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('company_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_sessions');
    }
};