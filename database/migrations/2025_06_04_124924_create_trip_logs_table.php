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
        Schema::create('trip_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trip_id'); // 👈 Add this line
           $table->enum('action_type', ['start', 'pause', 'resume', 'end']);
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->decimal('speed', 8, 2)->nullable();
            $table->decimal('odometer', 10, 2)->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_logs');
    }
};
