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
        Schema::create('leavetypes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable(); 
            $table->string('name')->nullable();
            $table->integer('no_of_days')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->boolean('status')->default(true); // status column add kiya
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('company_details')->onDelete('cascade'); // foreign key constraint
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leavetypes');
    }
};
