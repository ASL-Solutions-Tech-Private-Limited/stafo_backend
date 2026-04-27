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
        Schema::create('company_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('company_details')->onDelete('cascade'); // Foreign key for the company
            $table->foreignId('document_type_id')->constrained('document_types')->onDelete('cascade'); // Foreign key for document type
            $table->string('document')->nullable(); // To store the document file path
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('company_documents');
    }
};