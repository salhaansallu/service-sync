<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('p_o_s_settings', function (Blueprint $table) {
            $table->string('referral_coupons_enabled', 10)->default('active')->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('p_o_s_settings', function (Blueprint $table) {
            $table->dropColumn('referral_coupons_enabled');
        });
    }
};
