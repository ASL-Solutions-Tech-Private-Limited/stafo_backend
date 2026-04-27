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
            $table->enum('pan_verify', ['Yes', 'No'])->default('No');
            $table->enum('registration_verify', ['Yes', 'No'])->default('No');
            $table->enum('gstn_verify', ['Yes', 'No'])->default('No');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->dropColumn('pan_verify');
            $table->dropColumn('registration_verify');
            $table->dropColumn('gstn_verify');
        });
    }
};
