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
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'commission')) {
                $table->decimal('commission', 12, 2)->default(0.00)->after('amount');
            }
            if (!Schema::hasColumn('transactions', 'bill_amount')) {
                $table->decimal('bill_amount', 12, 2)->nullable()->after('amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'commission')) {
                $table->dropColumn('commission');
            }
            if (Schema::hasColumn('transactions', 'bill_amount')) {
                $table->dropColumn('bill_amount');
            }
        });
    }
};
