<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('pos_code');
            $table->string('code', 32);
            $table->string('referrer_phone', 30);
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['active', 'redeemed', 'paid', 'cancelled'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->unique(['pos_code', 'code']);
            $table->index(['pos_code', 'referrer_phone']);
        });

        Schema::create('referral_coupon_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_coupon_id')->constrained('referral_coupons');
            $table->string('pos_code');
            $table->string('bill_no');
            $table->unsignedBigInteger('redeemed_by')->nullable();
            $table->timestamp('redeemed_at');
            $table->unsignedBigInteger('paid_by')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->unique('referral_coupon_id');
            $table->index(['pos_code', 'bill_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_coupon_redemptions');
        Schema::dropIfExists('referral_coupons');
    }
};
