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
            $table->string('status')->nullable()->after('geo_status');
            $table->enum('is_verified', ['Yes', 'No'])->default('No');
            $table->longText('pan_response')->nullable();
            $table->longText('aadhar_response')->nullable();
            $table->longText('voter_response')->nullable();
            $table->longText('dl_response')->nullable();
            $table->longText('uan_response')->nullable();
            $table->longText('face_response')->nullable();
            $table->longText('address_response')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('is_verified');
            $table->dropColumn('pan_response');
            $table->dropColumn('aadhar_response');
            $table->dropColumn('voter_response');
            $table->dropColumn('dl_response');
            $table->dropColumn('uan_response');
            $table->dropColumn('face_response');
            $table->dropColumn('address_response');
        });
    }
};
