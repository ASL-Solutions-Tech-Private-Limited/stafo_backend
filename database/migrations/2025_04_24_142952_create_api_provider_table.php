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
        Schema::create('api_provider', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('api_id')->nullable();
            $table->unsignedBigInteger('operator_id')->nullable();
            $table->string('api_code')->nullable();
            $table->string('api_provider_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_provider');
    }
};
