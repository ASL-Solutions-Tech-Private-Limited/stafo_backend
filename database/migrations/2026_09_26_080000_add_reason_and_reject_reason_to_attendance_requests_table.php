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
        Schema::table('attendance_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_requests', 'reason')) {
                $table->text('reason')->nullable()->after('out_time');
            }
            if (!Schema::hasColumn('attendance_requests', 'reject_reason')) {
                $table->text('reject_reason')->nullable()->after('reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_requests', function (Blueprint $table) {
            if (Schema::hasColumn('attendance_requests', 'reason')) {
                $table->dropColumn('reason');
            }
            if (Schema::hasColumn('attendance_requests', 'reject_reason')) {
                $table->dropColumn('reject_reason');
            }
        });
    }
};
