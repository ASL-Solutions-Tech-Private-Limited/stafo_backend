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
            if (!Schema::hasColumn('employees', 'designation_id')) {
                $table->unsignedBigInteger('designation_id')->nullable()->after('department_id');
                $table->foreign('designation_id')->references('id')->on('designations')->nullOnDelete();
            }
            if (!Schema::hasColumn('employees', 'company_role_id')) {
                $table->unsignedBigInteger('company_role_id')->nullable()->after('designation_id');
                $table->foreign('company_role_id')->references('id')->on('company_roles')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'company_role_id')) {
                $table->dropForeign(['company_role_id']);
                $table->dropColumn('company_role_id');
            }
            if (Schema::hasColumn('employees', 'designation_id')) {
                $table->dropForeign(['designation_id']);
                $table->dropColumn('designation_id');
            }
        });
    }
};
