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
        Schema::table('employee_documents', function (Blueprint $table) {
            // Adding employee_id after document_type_id
            $table->unsignedBigInteger('employee_id')->after('document_type_id');

            // Setting the foreign key constraint
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('employee_documents', function (Blueprint $table) {
            // Dropping the foreign key and column
            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');
        });
    }
};