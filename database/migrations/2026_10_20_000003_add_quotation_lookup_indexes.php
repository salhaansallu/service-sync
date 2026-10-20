<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repairs', function (Blueprint $table) {
            $table->index(['pos_code', 'bill_no'], 'repairs_pos_bill_index');
        });
        Schema::table('quotations', function (Blueprint $table) {
            $table->index(['pos_code', 'id'], 'quotations_pos_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropIndex('quotations_pos_id_index');
        });
        Schema::table('repairs', function (Blueprint $table) {
            $table->dropIndex('repairs_pos_bill_index');
        });
    }
};
