<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add foreign key to branch_id
            $table->unsignedBigInteger('branch_id')->change();  // Ensure it's an unsigned big integer
            $table->foreign('branch_id')  // Define foreign key
                ->references('id')  // Reference the 'id' column in the 'branches' table
                ->on('branches')  // Specify the 'branches' table
                ->onDelete('cascade');  // Optionally, delete employees if the branch is deleted
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['branch_id']);
        });
    }
}