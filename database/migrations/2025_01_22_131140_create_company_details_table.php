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
        Schema::create('company_details', function (Blueprint $table) {
            $table->id(); 
            //$table->unsignedBigInteger('proprietor_id');
            $table->string('company_name', 255)->nullable();
            $table->string('company_type', 100)->nullable();
            $table->string('registration_number', 100)->unique()->nullable();
            $table->string('gst_number', 50)->unique()->nullable();
            $table->string('pan_number', 20)->unique()->nullable();

            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('pin', 10)->nullable();

            // Bank details
            $table->string('bank_name', 150)->nullable();
            $table->string('account_number', 50)->unique()->nullable();
            $table->string('ifsc_code', 20)->nullable();

            $table->integer('no_of_employee')->unsigned()->default(0);
            $table->string('status')->nullable();
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_details');
    }
};
