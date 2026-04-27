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
        Schema::create('dc_cc_bbps_category', function (Blueprint $table) {
            $table->integer('id')->unsigned(); // not auto-increment
            $table->string('name', 255)->nullable();
            $table->string('code', 100)->nullable();
            $table->string('img', 255)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('del')->default(1);
            $table->integer('update_by')->nullable();
            $table->dateTime('update_dttm')->nullable();
            $table->integer('orderBy')->default(0);

            $table->timestamps(); // created_at and updated_at

            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dc_cc_bbps_category');
    }
};