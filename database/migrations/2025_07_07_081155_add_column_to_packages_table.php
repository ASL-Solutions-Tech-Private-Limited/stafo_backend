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
        Schema::table('packages', function (Blueprint $table) {
            $table->float('monthly_price')->after('discount_price')->default(0.00);
            $table->float('quarterly_price')->after('monthly_price')->default(0.00);
            $table->float('halfyearly_price')->after('quarterly_price')->default(0.00);
            $table->float('yearly_price')->after('halfyearly_price')->default(0.00);
            $table->float('monthly_discount_price')->after('yearly_price')->default(0.00);
            $table->float('quarterly_discount_price')->after('monthly_discount_price')->default(0.00);
            $table->float('halfyearly_discount_price')->after('quarterly_discount_price')->default(0.00);
            $table->float('yearly_discount_price')->after('halfyearly_discount_price')->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn([
                'monthly_price',
                'quarterly_price',
                'halfyearly_price',
                'yearly_price',
                'monthly_discount_price',
                'quarterly_discount_price',
                'halfyearly_discount_price',
                'yearly_discount_price'
            ]);
        });
    }
};
