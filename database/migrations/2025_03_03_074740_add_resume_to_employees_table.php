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
        Schema::table('employees', function (Blueprint $table) {
            // Add the 'resume' column to the 'employees' table
            $table->string('resume')->nullable()->after('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop the 'resume' column if rolling back the migration
            $table->dropColumn('resume');
        });
    }
};