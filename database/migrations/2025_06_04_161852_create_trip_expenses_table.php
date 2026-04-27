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
        Schema::create('trip_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trip_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('expense_type')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->text('note')->nullable();
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('company_details')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_expenses');
    }
};
