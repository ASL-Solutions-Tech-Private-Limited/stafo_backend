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
        Schema::table('company_details', function (Blueprint $table) {
            $table->string('device_name')->nullable()->after('fcm_token');
            $table->string('android_version')->nullable()->after('device_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_details', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'device_name')) {
                $table->dropColumn('device_name');
            }
            if (Schema::hasColumn('employees', 'android_version')) {
                $table->dropColumn('android_version');
            }
        });
    }
};
