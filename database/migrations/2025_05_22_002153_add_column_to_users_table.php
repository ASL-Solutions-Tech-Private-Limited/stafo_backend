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
        Schema::table('shifts', function (Blueprint $table) {
            $table->boolean('sunday')->default(false);
            $table->boolean('monday')->default(true);
            $table->boolean('tuesday')->default(true);
            $table->boolean('wednesday')->default(true);
            $table->boolean('thursday')->default(true);
            $table->boolean('friday')->default(true);
            $table->boolean('saturday')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            //
            // Drop the columns if they exist
            if (Schema::hasColumn('shifts', 'sunday')) {
                $table->dropColumn('sunday');
            }
            if (Schema::hasColumn('shifts', 'monday')) {
                $table->dropColumn('monday');
            }
            if (Schema::hasColumn('shifts', 'tuesday')) {
                $table->dropColumn('tuesday');
            }
            if (Schema::hasColumn('shifts', 'wednesday')) {
                $table->dropColumn('wednesday');
            }
            if (Schema::hasColumn('shifts', 'thursday')) {
                $table->dropColumn('thursday');
            }
            if (Schema::hasColumn('shifts', 'friday')) {
                $table->dropColumn('friday');
            }
            if (Schema::hasColumn('shifts', 'saturday')) {
                $table->dropColumn('saturday');
            }
       
        });
    }
};
