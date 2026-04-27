<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->string('mobile_no')->nullable();
            $table->string('otp')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->dropForeign(['proprietor_id']);
            $table->dropColumn('proprietor_id');
        });
    }

    public function down()
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->dropColumn('mobile_no');
            $table->dropColumn('otp');
            $table->dropUnique(['email']);
            $table->dropColumn('password');
            $table->foreign('proprietor_id')->references('id')->on('proprietors')->onDelete('cascade');
            $table->unsignedBigInteger('proprietor_id')->nullable(); // Assuming it was an unsigned big integer
        });
    }
};
