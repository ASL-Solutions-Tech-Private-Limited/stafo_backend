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
        Schema::create('salry_type_packages', function (Blueprint $table) {
            $table->id();
            
            // Foreign key for company
            $table->foreignId('company_id')->constrained('company_details')->onDelete('cascade');
            
            // Package fields
            $table->string('package_name', 255);
            $table->text('package_description')->nullable();
            $table->enum('package_type', ['Basic', 'Standard', 'Premium', 'Custom']);
            
            // Status column
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
