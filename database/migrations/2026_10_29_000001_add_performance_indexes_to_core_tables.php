<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations to add high-performance composite indexes.
     */
    public function up(): void
    {
        // 1. attendances
        if (Schema::hasTable('attendances')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->index(['company_id', 'date'], 'idx_attendances_company_date');
                $table->index(['employee_id', 'date'], 'idx_attendances_employee_date');
            });
        }

        // 2. employee_punches
        if (Schema::hasTable('employee_punches')) {
            Schema::table('employee_punches', function (Blueprint $table) {
                $table->index(['employee_id', 'punch_in'], 'idx_punches_employee_punchin');
                $table->index(['punch_in', 'punch_out'], 'idx_punches_open_status');
            });
        }

        // 3. employee_geo_locations
        if (Schema::hasTable('employee_geo_locations')) {
            Schema::table('employee_geo_locations', function (Blueprint $table) {
                $table->index(['employee_id', 'created_at'], 'idx_geo_employee_created');
            });
        }

        // 4. chats
        if (Schema::hasTable('chats')) {
            Schema::table('chats', function (Blueprint $table) {
                $table->index(['company_id', 'created_at'], 'idx_chats_company_created');
                $table->index('sender', 'idx_chats_sender');
                $table->index('receiver', 'idx_chats_receiver');
            });
        }

        // 5. trips
        if (Schema::hasTable('trips')) {
            Schema::table('trips', function (Blueprint $table) {
                $table->index(['company_id', 'created_at'], 'idx_trips_company_created');
                $table->index(['employee_id', 'created_at'], 'idx_trips_employee_created');
            });
        }

        // 6. trip_logs
        if (Schema::hasTable('trip_logs')) {
            Schema::table('trip_logs', function (Blueprint $table) {
                $table->index(['trip_id', 'action_type', 'timestamp'], 'idx_trip_logs_lookup');
            });
        }

        // 7. trip_expenses
        if (Schema::hasTable('trip_expenses')) {
            Schema::table('trip_expenses', function (Blueprint $table) {
                $table->index(['trip_id', 'driver_id'], 'idx_trip_expenses_lookup');
            });
        }

        // 8. employees
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->index(['company_id', 'status'], 'idx_employees_company_status');
                $table->index(['company_id', 'emp_id'], 'idx_employees_company_empid');
            });
        }

        // 9. employee_leaves
        if (Schema::hasTable('employee_leaves')) {
            Schema::table('employee_leaves', function (Blueprint $table) {
                $table->index(['company_id', 'employee_id', 'status'], 'idx_leaves_company_emp_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('attendances')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropIndex('idx_attendances_company_date');
                $table->dropIndex('idx_attendances_employee_date');
            });
        }

        if (Schema::hasTable('employee_punches')) {
            Schema::table('employee_punches', function (Blueprint $table) {
                $table->dropIndex('idx_punches_employee_punchin');
                $table->dropIndex('idx_punches_open_status');
            });
        }

        if (Schema::hasTable('employee_geo_locations')) {
            Schema::table('employee_geo_locations', function (Blueprint $table) {
                $table->dropIndex('idx_geo_employee_created');
            });
        }

        if (Schema::hasTable('chats')) {
            Schema::table('chats', function (Blueprint $table) {
                $table->dropIndex('idx_chats_company_created');
                $table->dropIndex('idx_chats_sender');
                $table->dropIndex('idx_chats_receiver');
            });
        }

        if (Schema::hasTable('trips')) {
            Schema::table('trips', function (Blueprint $table) {
                $table->dropIndex('idx_trips_company_created');
                $table->dropIndex('idx_trips_employee_created');
            });
        }

        if (Schema::hasTable('trip_logs')) {
            Schema::table('trip_logs', function (Blueprint $table) {
                $table->dropIndex('idx_trip_logs_lookup');
            });
        }

        if (Schema::hasTable('trip_expenses')) {
            Schema::table('trip_expenses', function (Blueprint $table) {
                $table->dropIndex('idx_trip_expenses_lookup');
            });
        }

        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropIndex('idx_employees_company_status');
                $table->dropIndex('idx_employees_company_empid');
            });
        }

        if (Schema::hasTable('employee_leaves')) {
            Schema::table('employee_leaves', function (Blueprint $table) {
                $table->dropIndex('idx_leaves_company_emp_status');
            });
        }
    }
};
