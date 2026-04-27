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
            // Add business_type_id as a foreign key referring to the id in business_types table
            $table->unsignedBigInteger('business_type_id')->nullable()->after('company_type');

            // Add a foreign key constraint
            $table->foreign('business_type_id')->references('id')->on('business_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('company_details', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['business_type_id']);

            // Drop the business_type_id column
            $table->dropColumn('business_type_id');
        });
    }
};