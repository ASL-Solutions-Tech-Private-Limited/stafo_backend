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
        Schema::table('employee_geo_locations', function (Blueprint $table) {
            $table->string('battery_status')->nullable()->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_geo_locations', function (Blueprint $table) {
            // Drop the column if it exists
            if (Schema::hasColumn('employee_geo_locations', 'battery_status')) {
                $table->dropColumn('battery_status');
            }
        });
    }
};
