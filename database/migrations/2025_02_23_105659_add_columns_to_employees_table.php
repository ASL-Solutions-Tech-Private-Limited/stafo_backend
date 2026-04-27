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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('aadhar')->nullable();
            $table->string('pan')->nullable();
            $table->string('voter')->nullable();
            $table->string('driving_license')->nullable();
            $table->string('uan')->nullable();
            $table->enum('aadhar_verify', ['Yes', 'No'])->default('No');
            $table->enum('pan_verify', ['Yes', 'No'])->default('No');
            $table->enum('voter_verify', ['Yes', 'No'])->default('No');
            $table->enum('dl_verify', ['Yes', 'No'])->default('No');
            $table->enum('uan_verify', ['Yes', 'No'])->default('No');
            $table->enum('face_verify', ['Yes', 'No'])->default('No');
            $table->enum('address_verify', ['Yes', 'No'])->default('No');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('aadhar');
            $table->dropColumn('pan');
            $table->dropColumn('voter');
            $table->dropColumn('driving_license');
            $table->dropColumn('uan');
            $table->dropColumn('aadhar_verify');
            $table->dropColumn('pan_verify');
            $table->dropColumn('voter_verify');
            $table->dropColumn('dl_verify');
            $table->dropColumn('uan_verify');
            $table->dropColumn('face_verify');
            $table->dropColumn('address_verify');
        });
    }
};
