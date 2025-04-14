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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string("item")->nullable();
            $table->float("amount", 15, 2)->nullable();
            $table->float("qty", 15, 2)->nullable();
            $table->integer("supplier_id")->nullable();
            $table->string("payment")->nullable();
            $table->integer("user")->nullable();
            $table->text("note");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
