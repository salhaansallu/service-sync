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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string("sku");
            $table->string("pro_name");
            $table->float("price")->nullable();
            $table->float("cost");
            $table->string("qty")->default('0');
            $table->integer("category")->nullable();
            $table->text("pro_image");
            $table->string("pos_code");
            $table->string("supplier")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
