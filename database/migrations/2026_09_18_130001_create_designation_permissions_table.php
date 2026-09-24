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
        if (!Schema::hasTable('designation_permissions')) {
            Schema::create('designation_permissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('designation_id')->index();
                $table->string('permission_key');
                $table->timestamps();

                $table->foreign('designation_id')->references('id')->on('designations')->onDelete('cascade');
                $table->unique(['designation_id', 'permission_key']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designation_permissions');
    }
};
