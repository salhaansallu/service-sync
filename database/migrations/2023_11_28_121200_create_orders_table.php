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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number');
            $table->string('pos_code');
            $table->string('user_id');
            $table->string('customer');
            $table->float('total');
            $table->float('total_cost');
            $table->float('service_charge');
            $table->float('roundup');
            $table->string('payment_method');
            $table->text('invoice')->default("");
            $table->text('qr_code')->default("");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
