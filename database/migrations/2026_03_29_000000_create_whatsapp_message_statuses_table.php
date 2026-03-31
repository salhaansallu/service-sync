<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_message_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('bill_no');
            $table->string('mobile_no');
            $table->enum('status', ['Not sent', 'accepted']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_message_statuses');
    }
};
