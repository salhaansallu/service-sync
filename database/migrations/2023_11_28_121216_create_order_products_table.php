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
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->string("order_id");
            $table->string("pro_name");
            $table->string("sku");
            $table->string("qty");
            $table->float("cost");
            $table->float("price");
            $table->float("discount");
            $table->string("discount_mod");
            $table->float("discounted_price");
            $table->string("pos_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
