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
            $table->bigInteger('aadhar')->after('business_type_id')->nullable();
            $table->enum('aadhar_verify', ['Yes', 'No'])->default('No')->after('device_id');
            $table->longText('aadhar_response')->after('is_verified')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->dropColumn('aadhar');
            $table->dropColumn('aadhar_verify');
            $table->dropColumn('aadhar_response');
        });
    }
};
