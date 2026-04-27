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
        // Adding the `company_id` column to the `shifts` table
        Schema::table('shifts', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->after('end_time'); // This will add company_id after 'end_time'

            // Adding foreign key constraint referencing company_details table
            //$table->foreign('company_id')
                //->references('id') // id of company_details table
                //->on('company_details') // foreign table
                //->onDelete('cascade'); // If the company is deleted, related shifts will also be deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Dropping the foreign key and the column if the migration is rolled back
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};