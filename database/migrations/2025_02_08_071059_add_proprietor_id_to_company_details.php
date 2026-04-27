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
            // Add the proprietor_id column after the company_name column
            $table->unsignedBigInteger('proprietor_id')->nullable()->after('company_name');

            // Add foreign key constraint
            $table->foreign('proprietor_id')
                ->references('id') // Column in the proprietor_details table
                ->on('proprietor_details') // Table name for the proprietors
                ->onDelete('cascade'); // Action when the referenced proprietor is deleted (optional)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('company_details', function (Blueprint $table) {
            // Drop the foreign key and column
            $table->dropForeign(['proprietor_id']);
            $table->dropColumn('proprietor_id');
        });
    }
};