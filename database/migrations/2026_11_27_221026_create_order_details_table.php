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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id('order_detail_id');

            $table->foreignId('order_id')
                ->constrained('orders', 'order_id')
                ->restrictOnDelete();

            $table->foreignId('item_id')
                ->constrained('sellable_items', 'item_id')
                ->restrictOnDelete();

            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('line_total', 12, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
