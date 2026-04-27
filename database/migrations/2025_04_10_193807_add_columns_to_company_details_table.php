<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->integer('package_id')->nullable();
            $table->float('package_price')->nullable();
            $table->date('subscription_start')->nullable();
            $table->date('subscription_end')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->dropColumn('package_id');
            $table->dropColumn('package_price');
            $table->dropColumn('subscription_start');
            $table->dropColumn('subscription_end');
        });
    }
};
