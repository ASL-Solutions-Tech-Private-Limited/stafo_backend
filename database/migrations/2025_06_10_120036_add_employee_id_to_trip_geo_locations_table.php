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
            Schema::table('trip_geo_locations', function (Blueprint $table) {
                $table->unsignedBigInteger('driver_id')->nullable()->after('company_id');
                $table->foreign('driver_id')->references('id')->on('employees')->onDelete('set null');
            });
        }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_geo_locations', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn('employee_id');
        });
    }
};
