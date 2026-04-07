<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_quotations', function (Blueprint $table) {
            $table->id();
            $table->string('sq_no')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->json('items');
            $table->decimal('total', 10, 2)->default(0);
            $table->text('note')->nullable();
            $table->string('pos_code');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_quotations');
    }
};
