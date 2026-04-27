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
        Schema::create('expense_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expense_id')->nullable();
            $table->unsignedBigInteger('expenseform_id')->nullable();
            $table->string('expense_value')->nullable();
            $table->timestamps();
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade'); // foreign key constraint
            $table->foreign('expenseform_id')->references('id')->on('expenseforms')->onDelete('cascade'); // foreign key constraint
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_details');
    }
};
