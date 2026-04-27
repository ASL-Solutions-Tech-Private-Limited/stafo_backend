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
            Schema::table('trip_logs', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')
                    ->nullable()
                    ->after('trip_id');
               $table->foreign('company_id')->references('id')->on('company_details')->onDelete('cascade');

            });
        }

    /**
     * Reverse the migrations.
     */
       public function down()
        {
            Schema::table('trip_logs', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }
};
