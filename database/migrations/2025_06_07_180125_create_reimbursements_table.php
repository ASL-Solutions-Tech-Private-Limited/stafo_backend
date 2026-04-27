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
        Schema::create('reimbursements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('status')->default('pending'); // status can be 'pending', 'approved', 'rejected'
            $table->string('receipt_file')->nullable(); // Path to the receipt file
            $table->date('date')->nullable(); // Date of the reimbursement request
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('company_details')->onDelete('cascade'); // foreign key constraint
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade'); // foreign key constraint
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reimbursements');
    }
};
