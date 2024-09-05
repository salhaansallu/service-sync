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
        Schema::create('p_o_s_settings', function (Blueprint $table) {
            $table->id();
            $table->string('pos_code');
            $table->string('qr_code', 10)->nullable();
            $table->string('datetime', 10)->nullable();
            $table->string('industry', 10)->nullable();
            $table->string('title', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_o_s_settings');
    }
};
