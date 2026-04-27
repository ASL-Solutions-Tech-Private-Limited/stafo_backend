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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->integer('phone')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->nullable();
            $table->string('lead_from')->nullable();
            $table->date('start_date')->nullable();
            $table->date('next_date')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('company_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
