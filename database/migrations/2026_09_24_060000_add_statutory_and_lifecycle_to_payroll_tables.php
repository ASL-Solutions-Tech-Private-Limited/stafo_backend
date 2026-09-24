<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_salary_summaries', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_salary_summaries', 'gross_salary')) {
                $table->decimal('gross_salary', 10, 2)->default(0)->after('net_salary');
            }
            if (!Schema::hasColumn('employee_salary_summaries', 'leave_days')) {
                $table->decimal('leave_days', 5, 1)->default(0)->after('present_days');
            }
            if (!Schema::hasColumn('employee_salary_summaries', 'late_count')) {
                $table->integer('late_count')->default(0)->after('leave_days');
            }
            if (!Schema::hasColumn('employee_salary_summaries', 'halfday_count')) {
                $table->decimal('halfday_count', 5, 1)->default(0)->after('late_count');
            }
            if (!Schema::hasColumn('employee_salary_summaries', 'pf_employee')) {
                $table->decimal('pf_employee', 10, 2)->default(0)->after('total_deduction');
            }
            if (!Schema::hasColumn('employee_salary_summaries', 'pf_employer')) {
                $table->decimal('pf_employer', 10, 2)->default(0)->after('pf_employee');
            }
            if (!Schema::hasColumn('employee_salary_summaries', 'esi_employee')) {
                $table->decimal('esi_employee', 10, 2)->default(0)->after('pf_employer');
            }
            if (!Schema::hasColumn('employee_salary_summaries', 'esi_employer')) {
                $table->decimal('esi_employer', 10, 2)->default(0)->after('esi_employee');
            }
            if (!Schema::hasColumn('employee_salary_summaries', 'pt_amount')) {
                $table->decimal('pt_amount', 10, 2)->default(0)->after('esi_employer');
            }
            if (!Schema::hasColumn('employee_salary_summaries', 'tds_amount')) {
                $table->decimal('tds_amount', 10, 2)->default(0)->after('pt_amount');
            }
            if (!Schema::hasColumn('employee_salary_summaries', 'payment_status')) {
                $table->string('payment_status')->default('Pending')->after('status');
            }
            if (!Schema::hasColumn('employee_salary_summaries', 'payment_mode')) {
                $table->string('payment_mode')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('employee_salary_summaries', 'paid_date')) {
                $table->timestamp('paid_date')->nullable()->after('payment_mode');
            }
            if (!Schema::hasColumn('employee_salary_summaries', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('paid_date');
            }
        });

        Schema::table('employee_salaries', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_salaries', 'payment_type')) {
                $table->string('payment_type')->nullable()->after('salary_type_amount_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_salary_summaries', function (Blueprint $table) {
            $table->dropColumn([
                'gross_salary',
                'leave_days',
                'late_count',
                'halfday_count',
                'pf_employee',
                'pf_employer',
                'esi_employee',
                'esi_employer',
                'pt_amount',
                'tds_amount',
                'payment_status',
                'payment_mode',
                'paid_date',
                'is_locked',
            ]);
        });

        Schema::table('employee_salaries', function (Blueprint $table) {
            $table->dropColumn('payment_type');
        });
    }
};
