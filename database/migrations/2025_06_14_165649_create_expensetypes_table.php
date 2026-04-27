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
        Schema::create('expensetypes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('name')->nullable(); // Name of the expense type
            $table->string('description')->nullable(); // Description of the expense type
            $table->string('is_document_req')->nullable(); // Name of the expense type
            $table->string('status')->default('Active'); // Status can be 'active', 'inactive'            
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('company_details')->onDelete('cascade'); // foreign key constraint
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expensetypes');
    }
};
