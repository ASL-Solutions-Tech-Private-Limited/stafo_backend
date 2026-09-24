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
        if (!Schema::hasTable('designations')) {
            Schema::create('designations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->index();
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('status')->default(1);
                $table->timestamps();

                $table->foreign('company_id')->references('id')->on('company_details')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designations');
    }
};
