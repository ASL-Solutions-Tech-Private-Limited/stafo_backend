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
        Schema::create('proprietor_details', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('mobile', 15)->unique()->nullable();
            $table->string('email', 255)->unique()->nullable();
            $table->string('aadhar', 20)->unique()->nullable();
            $table->string('pan', 20)->unique()->nullable();

            // Current Address Details
            $table->string('current_address', 255)->nullable();
            $table->string('current_city', 100)->nullable();
            $table->string('current_state', 100)->nullable();
            $table->string('current_country', 100)->nullable();
            $table->string('current_pin', 10)->nullable();

            // Proprietor Address Details
            $table->string('p_address', 255)->nullable();
            $table->string('p_city', 100)->nullable();
            $table->string('p_state', 100)->nullable();
            $table->string('p_country', 100)->nullable();
            $table->string('p_pin', 10)->nullable();
            $table->string('status')->nullable();
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proprietor_details');
    }
};
