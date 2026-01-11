<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_id')->unique();
            $table->string('user_id')->nullable();
            $table->string('customer_name');
            $table->string('customer_phone', 20);
            $table->string('tv_brand');
            $table->string('tv_model');
            $table->string('issue_type');
            $table->text('issue_description');
            $table->text('address');
            $table->string('pickup_option'); // pickup or drop-off
            $table->string('status', 50)->default('pending'); // pending, confirmed, parts-ordered, in-progress, testing, ready, completed, cancelled
            $table->json('timeline')->nullable(); // Array of status changes with timestamps
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('final_cost', 10, 2)->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index('booking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
