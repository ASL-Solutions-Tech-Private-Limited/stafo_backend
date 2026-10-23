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
        if (!Schema::hasTable('pz_service_category')) {
            Schema::create('pz_service_category', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('service_id')->nullable()->index();
                $table->string('name', 60);
                $table->string('category_icon', 60)->nullable();
                $table->string('category_file_name', 60)->nullable();
                $table->char('category_code', 3);
                $table->double('commission')->nullable();
                $table->enum('isFlat', ['0', '1'])->default('0');
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->string('created_by', 60)->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->string('updated_by', 60)->nullable();
                $table->string('service_code', 60)->index();
                $table->integer('status')->default(0);
                $table->integer('orderBy')->default(0);
            });
        }

        if (!Schema::hasTable('d2_package_commission_tbl')) {
            Schema::create('d2_package_commission_tbl', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('pacid')->nullable();
                $table->bigInteger('company_id')->nullable()->index();
                $table->integer('optid')->nullable();
                $table->integer('billtypeid')->nullable()->index('billtypeid_index');
                $table->bigInteger('gift_id')->nullable();
                $table->float('comper')->default(0);
                $table->float('gstper')->default(0);
                $table->enum('issurcharge', ['1', '0'])->default('0');
                $table->enum('isflat', ['1', '0'])->default('0');
                $table->enum('status', ['1', '0'])->default('1');
                $table->string('insert_date', 40)->nullable();
                $table->string('insert_user', 120)->nullable();
                $table->string('update_date', 40)->nullable();
                $table->string('update_user', 120)->nullable();

                $table->index(['id', 'pacid', 'optid'], 'id_pacid_optid_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('d2_package_commission_tbl');
        Schema::dropIfExists('pz_service_category');
    }
};
