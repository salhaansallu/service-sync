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
        Schema::create('china_orders', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_no');
            $table->string('bill_no')->nullable();
            $table->string('panel_no')->nullable();
            $table->float('price')->nullable();
            $table->string('qty')->nullable();
            $table->string('pcb_no')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('pos_code')->nullable();
            $table->string('customer')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('china_orders');
    }
};
