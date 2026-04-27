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
        Schema::table('company_details', function (Blueprint $table) {
            // Add the company_code column with default value null
            $table->string('company_code')->nullable()->after('company_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('company_details', function (Blueprint $table) {
            // Drop the company_code column
            $table->dropColumn('company_code');
        });
    }
};