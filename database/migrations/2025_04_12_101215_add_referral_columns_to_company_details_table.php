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
            $table->string('referral_code')->unique()->nullable()->after('password');
            $table->unsignedBigInteger('referred_by')->nullable()->after('referral_code');

            $table->foreign('referred_by')->references('id')->on('company_details')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn('referred_by');
            $table->dropColumn('referral_code');
        });
    }
};