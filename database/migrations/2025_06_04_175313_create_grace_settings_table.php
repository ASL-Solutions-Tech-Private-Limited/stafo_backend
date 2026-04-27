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
        Schema::create('grace_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();            
            $table->string('name')->nullable();
            $table->string('label')->nullable();
            $table->string('value')->nullable();
            $table->string('status')->nullable();            
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('company_details')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grace_settings');
    }
};
