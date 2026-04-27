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
            $table->integer('max_employee_add')->default(0);
            $table->integer('employee_added')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_details', function (Blueprint $table) {          
            $table->dropColumn('max_employee_add');
            $table->dropColumn('employee_added');
        });
    }
};
