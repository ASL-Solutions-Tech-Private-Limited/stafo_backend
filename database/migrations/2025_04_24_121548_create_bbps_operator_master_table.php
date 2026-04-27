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
        Schema::create('bbps_operator_master', function (Blueprint $table) {
            $table->id();
            $table->string('InCode', 50)->nullable();
            $table->string('operator_code')->nullable();
            $table->string('name')->nullable();
            $table->string('service')->default('BBPS');
            $table->string('category')->nullable();
            $table->string('APICode')->default('Billavenue');
            $table->integer('viewbill')->nullable();
            $table->string('blr_coverage')->nullable();
            $table->string('regex', 305)->nullable();
            $table->string('displayname')->nullable();
            $table->string('ad1_d_name')->nullable();
            $table->string('ad1_name')->nullable();
            $table->string('ad1_regex')->nullable();
            $table->string('ad2_d_name')->nullable();
            $table->string('ad2_name')->nullable();
            $table->string('ad2_regex')->nullable();
            $table->string('ad3_d_name')->nullable();
            $table->string('ad3_name')->nullable();
            $table->string('ad3_regex')->nullable();
            $table->string('mode', 50)->nullable();
            $table->text('message')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('update_by')->nullable();
            $table->dateTime('update_dttm')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bbps_operator_master');
    }
};