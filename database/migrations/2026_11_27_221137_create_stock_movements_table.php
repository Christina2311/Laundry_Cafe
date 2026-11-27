<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id('movement_id');

            $table->foreignId('inventory_item_id')
                ->constrained('inventory_items', 'inventory_item_id')
                ->restrictOnDelete();

            $table->foreignId('purchase_detail_id')
                ->nullable()
                ->constrained('purchase_details', 'purchase_detail_id')
                ->restrictOnDelete();

            $table->foreignId('order_detail_id')
                ->nullable()
                ->constrained('order_details', 'order_detail_id')
                ->restrictOnDelete();

            $table->foreignId('laundry_order_id')
                ->nullable()
                ->constrained('laundry_orders', 'laundry_order_id')
                ->restrictOnDelete();

            $table->foreignId('recorded_by')
                ->constrained('users', 'id')
                ->restrictOnDelete();

            $table->enum('movement_type', [
                'in',
                'out',
            ]);

            $table->enum('movement_reason', [
                'purchase',
                'sale',
                'laundry_consumption',
                'cancellation',
                'refund',
                'purchase_reversal',
            ]);

            $table->decimal('quantity_change', 15, 4);

            $table->dateTime('movement_date')->useCurrent();

            $table->text('remarks')->nullable();

            $table->index([
                'inventory_item_id',
                'movement_date'
            ]);

            $table->index('movement_reason');
        });

        DB::statement("
        ALTER TABLE stock_movements
        ADD CONSTRAINT chk_stock_movement_source
        CHECK (
            (
                (purchase_detail_id IS NOT NULL) +
                (order_detail_id IS NOT NULL) +
                (laundry_order_id IS NOT NULL)
            ) = 1
        )
    ");

        DB::statement("
        ALTER TABLE stock_movements
        ADD CONSTRAINT chk_stock_movement_quantity
        CHECK (
            (movement_type = 'in' AND quantity_change > 0)
            OR
            (movement_type = 'out' AND quantity_change < 0)
        )
    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
