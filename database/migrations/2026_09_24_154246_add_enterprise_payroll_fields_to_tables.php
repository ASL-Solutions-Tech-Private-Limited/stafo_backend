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
        if (Schema::hasTable('employee_salary_summaries')) {
            Schema::table('employee_salary_summaries', function (Blueprint $table) {
                if (!Schema::hasColumn('employee_salary_summaries', 'arrears')) {
                    $table->decimal('arrears', 10, 2)->default(0)->after('reimbursement');
                }
                if (!Schema::hasColumn('employee_salary_summaries', 'bonus')) {
                    $table->decimal('bonus', 10, 2)->default(0)->after('arrears');
                }
                if (!Schema::hasColumn('employee_salary_summaries', 'ctc')) {
                    $table->decimal('ctc', 12, 2)->default(0)->after('gross_salary');
                }
                if (!Schema::hasColumn('employee_salary_summaries', 'gratuity')) {
                    $table->decimal('gratuity', 10, 2)->default(0)->after('esi_employer');
                }
                if (!Schema::hasColumn('employee_salary_summaries', 'tax_regime')) {
                    $table->string('tax_regime', 20)->default('new')->after('tds_amount');
                }
                if (!Schema::hasColumn('employee_salary_summaries', 'sandwich_deduction')) {
                    $table->decimal('sandwich_deduction', 10, 2)->default(0)->after('other_deduction');
                }
            });
        }

        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (!Schema::hasColumn('employees', 'tax_regime')) {
                    $table->string('tax_regime', 20)->default('new')->after('salary');
                }
                if (!Schema::hasColumn('employees', 'tax_declarations')) {
                    $table->text('tax_declarations')->nullable()->after('tax_regime');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('employee_salary_summaries')) {
            Schema::table('employee_salary_summaries', function (Blueprint $table) {
                $columns = ['arrears', 'bonus', 'ctc', 'gratuity', 'tax_regime', 'sandwich_deduction'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('employee_salary_summaries', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                $columns = ['tax_regime', 'tax_declarations'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('employees', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
