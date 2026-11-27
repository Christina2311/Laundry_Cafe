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
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id('purchase_detail_id');

            $table->foreignId('purchase_id')
                ->constrained('purchases', 'purchase_id')
                ->restrictOnDelete();

            $table->foreignId('inventory_item_id')
                ->constrained('inventory_items', 'inventory_item_id')
                ->restrictOnDelete();

            $table->decimal('quantity_purchased', 15, 4);

            $table->foreignId('purchase_unit_id')
                ->constrained('measurement_units', 'unit_id')
                ->restrictOnDelete();

            $table->decimal('conversion_factor', 18, 8)
                ->default(1);

            $table->decimal('base_quantity', 15, 4);
            $table->decimal('unit_cost', 12, 2);
            $table->decimal('line_total', 12, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_details');
    }
};
