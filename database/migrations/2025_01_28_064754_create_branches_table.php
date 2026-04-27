<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('branch_name')->nullable();
            $table->text('branch_address')->nullable();
            $table->boolean('status')->default(1); // 1 for Active, 0 for Inactive
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('company_id')->references('id')->on('company_details')->onDelete('cascade');

        });
    }

    public function down()
    {
        Schema::dropIfExists('branches');
    }
}
