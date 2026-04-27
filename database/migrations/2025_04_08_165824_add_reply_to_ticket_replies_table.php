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
        Schema::table('ticket_replies', function (Blueprint $table) {
            // Add the 'reply' column after 'message'
            $table->text('reply')->nullable()->after('message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('ticket_replies', function (Blueprint $table) {
            // Drop the 'reply' column if the migration is rolled back
            $table->dropColumn('reply');
        });
    }
};
