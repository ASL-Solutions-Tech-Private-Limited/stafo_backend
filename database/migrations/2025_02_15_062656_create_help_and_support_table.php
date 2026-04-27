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
        Schema::create('help_and_support', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('company_details')->onDelete('set null');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('contact_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('help_and_support');
    }
};