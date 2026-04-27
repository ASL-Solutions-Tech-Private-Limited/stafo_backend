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
        // Drop the existing foreign key that points to company_details
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['company_id']); // Drop old foreign key
        });

        // Add a new foreign key that points to proprietor_details
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('company_id')
                ->references('id')
                ->on('proprietor_details') // Updated to proprietor_details
                ->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Drop the newly created foreign key if we rollback
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });

        // Restore the old foreign key pointing to company_details
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('company_id')
                ->references('id')
                ->on('company_details')
                ->onDelete('cascade');
        });
    }
};