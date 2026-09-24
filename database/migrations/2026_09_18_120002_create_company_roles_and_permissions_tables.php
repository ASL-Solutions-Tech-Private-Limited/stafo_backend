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
        if (!Schema::hasTable('company_roles')) {
            Schema::create('company_roles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->index();
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('status')->default(1);
                $table->timestamps();

                $table->foreign('company_id')->references('id')->on('company_details')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('company_role_permissions')) {
            Schema::create('company_role_permissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_role_id')->index();
                $table->string('permission_key');
                $table->timestamps();

                $table->foreign('company_role_id')->references('id')->on('company_roles')->onDelete('cascade');
                $table->unique(['company_role_id', 'permission_key']);
            });
        }

        if (!Schema::hasTable('employee_permission_overrides')) {
            Schema::create('employee_permission_overrides', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id')->index();
                $table->string('permission_key');
                $table->boolean('is_granted')->default(1);
                $table->timestamps();

                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
                $table->unique(['employee_id', 'permission_key']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_permission_overrides');
        Schema::dropIfExists('company_role_permissions');
        Schema::dropIfExists('company_roles');
    }
};
