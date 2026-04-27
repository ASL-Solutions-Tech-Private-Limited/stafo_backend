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
        Schema::table('trip_expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_id')->after('company_id')->nullable();

            // If you want to add a foreign key constraint (optional):
             $table->foreign('driver_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('trip_expenses', function (Blueprint $table) {
            $table->dropColumn('driver_id');
        });
    }
};
