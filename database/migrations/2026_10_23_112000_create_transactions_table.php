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
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('user_type', 50)->nullable();
                $table->string('txnid')->nullable()->index();
                $table->decimal('amount', 12, 2)->default(0.00);
                $table->text('product_info')->nullable();
                $table->string('status', 50)->default('pending')->index();
                $table->string('payu_payment_id')->nullable()->index();
                $table->text('hash_received')->nullable();
                $table->text('hash_calculated')->nullable();
                $table->longText('payu_response')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
