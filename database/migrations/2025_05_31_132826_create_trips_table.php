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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade')->nullable();
            $table->foreignId('driver_id')->constrained('employees')->onDelete('cascade')->nullable(); 
            $table->foreignId('company_id')->constrained('company_details')->onDelete('cascade')->nullable(); 
            $table->string('start_location')->nullable();
            $table->string('end_location')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
