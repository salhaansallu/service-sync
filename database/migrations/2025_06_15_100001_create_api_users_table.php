<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20)->unique();
            $table->string('email')->nullable();
            $table->string('password');
            $table->string('role', 20)->default('customer'); // customer, admin
            $table->boolean('phone_verified')->default(false);
            $table->json('notification_preferences')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            // Foreign key to link with existing customers
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            
            // Indexes
            $table->index('phone');
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_users');
    }
};
