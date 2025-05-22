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
        Schema::create('product_purchases', function (Blueprint $table) {
            $table->id();
            $table->string("purshace_no");
            $table->text("products");
            $table->float("total", 15, 2)->default('0');
            $table->float("pending", 15, 2)->default('0');
            $table->float("cbm_price", 15, 2)->default('0');
            $table->float("qty", 15, 2)->default('0');
            $table->string("currency", 10)->default('LKR');
            $table->float("shipping_charge", 15, 2)->default('0');
            $table->float("total_in_currency", 15, 2)->default('0');
            $table->integer("supplier_id")->nullable();
            $table->integer("shipper_id")->nullable();
            $table->text("note");
            $table->string("status")->default('pending');
            $table->string("stock_updated")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_purchases');
    }
};
