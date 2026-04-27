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
        Schema::create('dc_cc_bbps_sub_category', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('code', 100)->nullable();
            $table->string('img', 255)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('del')->default(1);
            $table->integer('update_by')->nullable();
            $table->dateTime('update_dttm')->nullable();
            $table->integer('order_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('dc_cc_bbps_sub_category');
    }
};