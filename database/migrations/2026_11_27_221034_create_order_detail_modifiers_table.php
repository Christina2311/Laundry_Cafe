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
        Schema::create('order_detail_modifiers', function (Blueprint $table) {
            $table->id('order_detail_modifier_id');

            $table->foreignId('order_detail_id')
                ->constrained('order_details', 'order_detail_id')
                ->restrictOnDelete();

            $table->foreignId('modifier_option_id')
                ->constrained('modifier_options', 'modifier_option_id')
                ->restrictOnDelete();

            $table->decimal('additional_price', 12, 2);
            $table->decimal('quantity', 15, 4)->default(1);

            $table->unique(
                ['order_detail_id', 'modifier_option_id'],
                'order_detail_modifier_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_detail_modifiers');
    }
};
