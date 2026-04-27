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
        Schema::table('employees', function (Blueprint $table) {
            // Add the 'device_status' column after the 'status' column
            $table->string('device_status')->nullable()->after('status');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop the 'device_status' column if the migration is rolled back
            $table->dropColumn('device_status');
        });
    }
};