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
        Schema::create('spare_sale_histories', function (Blueprint $table) {
            $table->id();
            $table->string("spare_code")->nullable();
            $table->string("spare_name")->nullable();
            $table->string("spare_id")->nullable();
            $table->string("cost")->nullable();
            $table->string("qty")->nullable();
            $table->text("pos_code");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_sale_histories');
    }
};
