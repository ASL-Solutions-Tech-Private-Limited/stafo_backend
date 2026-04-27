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
        Schema::table('employees', function (Blueprint $table) {
            
            $table->string('device_name')->nullable()->after('fcm_token');
            $table->string('android_version')->nullable()->after('device_name');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop the columns if they exist
            if (Schema::hasColumn('employees', 'device_name')) {
                $table->dropColumn('device_name');
            }
            if (Schema::hasColumn('employees', 'android_version')) {
                $table->dropColumn('android_version');
            }
        });
    }
};
