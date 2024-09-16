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
            $table->string('bill_no');
            $table->string('pos_code');
            $table->string('payment_method')->nullable();
            $table->string('delivery')->nullable();
            $table->float('charge', 10, 2)->nullable();
            $table->string('tracking_code')->nullable();
            $table->string('status');
            $table->text('invoice')->nullable();
            $table->text('note')->nullable();
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
