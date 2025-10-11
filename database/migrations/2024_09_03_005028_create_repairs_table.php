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
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->string("bill_no");
            $table->string("model_no")->nullable();
            $table->string("serial_no")->nullable();
            $table->text("fault")->nullable();
            $table->string("has_multiple_fault")->nullable();
            $table->text("multiple_fault")->nullable();
            $table->text("note")->nullable();
            $table->float("advance", 10, 2)->default(0);
            $table->float("total", 10, 2)->default(0);
            $table->float("cost", 10, 2)->default(0);
            $table->float("delivery", 10, 2)->default(0);
            $table->string("customer");
            $table->string("partner");
            $table->string("cashier");
            $table->string("techie")->nullable();
            $table->string("spares")->nullable();
            $table->string("status", 100);
            $table->string("pos_code");
            $table->text("invoice")->nullable();
            $table->string("type")->nullable();
            $table->text("products")->nullable();
            $table->string("parent")->nullable();
            $table->timestamp("paid_date")->nullable();
            $table->timestamp("repaired_date")->nullable();
            $table->integer("warranty")->default(0);
            $table->text("signature")->nullable();
            $table->float("commission", 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
