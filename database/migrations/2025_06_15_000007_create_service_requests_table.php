<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_id')->unique();
            $table->string('type', 50); // web-design, web-seo, pos-system, mobile-app
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 20);
            $table->text('message');
            $table->string('status', 50)->default('pending'); // pending, in-progress, completed
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
