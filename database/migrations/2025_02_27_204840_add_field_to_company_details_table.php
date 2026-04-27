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
            $table->enum('is_verified', ['Yes', 'No'])->default('No');
            $table->longText('pan_response')->nullable();
            $table->longText('reg_response')->nullable();
            $table->longText('gst_response')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->dropColumn('is_verified');
            $table->dropColumn('pan_response');
            $table->dropColumn('reg_response');
            $table->dropColumn('gst_response');
        });
    }
};
