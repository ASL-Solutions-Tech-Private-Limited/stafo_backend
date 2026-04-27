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
        Schema::table('branches', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('branch_address');  // Adjust 'after' as needed
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->integer('radar')->default(0)->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'radar']);
        });
    }
};
