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
        Schema::create('salarytypes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('salary_type')->nullable();
            $table->string('salary_type_description')->nullable();
            $table->float('amount', 8, 2)->nullable();
            $table->enum('amount_type', ['Percentage', 'Flat'])->default('Flat');
            $table->enum('status', ['0', '1'])->default('1');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('company_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salarytypes');
    }
};
