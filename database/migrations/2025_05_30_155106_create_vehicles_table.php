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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_no')->unique();         // Vehicle Number
            $table->string('vehicle_type')->nullable();                         // Type (e.g. Truck, Car)
            $table->string('fuel')->nullable();                         // Fuel type (e.g. Diesel, Petrol, EV)
            $table->decimal('load_capacity', 8, 2)->nullable(); // Load in tons/kg/etc.
            $table->integer('speedometer')->default(0);     // Speedometer reading (KM)
            $table->string('rc_upload_path')->nullable();   // Path to uploaded RC file
            $table->unsignedBigInteger('employee_id')->nullable(); // Assigned driver (foreign key)
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active'); // Current status
            $table->integer('km_travelled')->default(0);    // Total KM travelled
            $table->timestamps();

            // Optional: add driver foreign key constraint if there's a drivers table
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
