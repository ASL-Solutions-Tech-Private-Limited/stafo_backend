<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('help_contents', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('help_contents', function (Blueprint $table) {
            $table->dropColumn('file_path');
        });
    }
};