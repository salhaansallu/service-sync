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
        Schema::create('pos_data', function (Blueprint $table) {
            $table->id();
            $table->string("pos_code");
            $table->string("admin_id");
            $table->string("company_name");
            $table->string("industry");
            $table->string("country");
            $table->string("city");
            $table->string("plan");
            $table->string("status")->nullable();
            $table->string("expiry_date")->nullable();
            $table->string("currency")->default("LKR");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_data');
    }
};
