<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warranty_records', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('bill_number');
            $table->string('phone_number', 20);
            $table->string('product_name');
            $table->date('purchase_date');
            $table->date('expiry_date');
            $table->string('coverage_type'); // Full Warranty, Limited Warranty, etc.
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['serial_number', 'bill_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranty_records');
    }
};
