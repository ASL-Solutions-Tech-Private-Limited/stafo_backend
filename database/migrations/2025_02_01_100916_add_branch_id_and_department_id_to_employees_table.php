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
            // 'company_id' ke baad branch_id aur department_id ko add karna
            $table->unsignedBigInteger('company_id')->after('emp_id');
            $table->unsignedBigInteger('branch_id')->after('company_id');
            $table->unsignedBigInteger('department_id')->after('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('branch_id');
            $table->dropColumn('department_id');
        });
    }
};