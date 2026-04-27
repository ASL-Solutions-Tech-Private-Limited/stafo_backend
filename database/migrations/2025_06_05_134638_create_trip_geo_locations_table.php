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
        Schema::create('trip_geo_locations', function (Blueprint $table) {
            $table->id();
             $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade')->nullable();
             $table->foreignId('company_id')->constrained('company_details')->onDelete('cascade')->nullable();
             $table->decimal('latitude', 10, 7)->nullable();
             $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_geo_locations');
    }
};
