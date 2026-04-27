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
        Schema::create('task_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');            
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->text('comments')->nullable();
            $table->string('status')->default('active'); // Status of the comment, e.g., active, deleted
            $table->timestamps();
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('company_details')->onDelete('set null');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->index(['task_id', 'employee_id'], 'task_employee_index'); // Index for faster querying

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_comments');
    }
};
