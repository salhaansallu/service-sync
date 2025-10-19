<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email')->unique();
            $table->string('role', 20)->default('customer')->after('type');
            $table->boolean('phone_verified')->default(false)->after('phone');
            $table->json('notification_preferences')->nullable()->after('phone_verified');
            $table->unsignedBigInteger('customer_id')->nullable()->after('id');
            
            // Make email nullable since we're using phone for auth
            $table->string('email')->nullable()->change();
            
            // Add foreign key to customers table
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['phone', 'role', 'phone_verified', 'notification_preferences', 'customer_id']);
        });
    }
};
