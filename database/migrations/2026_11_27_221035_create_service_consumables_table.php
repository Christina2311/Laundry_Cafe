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
        Schema::create('service_consumables', function (Blueprint $table) {
            $table->id('service_consumable_id');

            $table->foreignId('item_id')
                ->constrained('sellable_items', 'item_id')
                ->restrictOnDelete();

            $table->foreignId('inventory_item_id')
                ->constrained('inventory_items', 'inventory_item_id')
                ->restrictOnDelete();

            $table->decimal('quantity_required', 15, 4);

            $table->foreignId('unit_id')
                ->constrained('measurement_units', 'unit_id')
                ->restrictOnDelete();

            $table->unique(['item_id', 'inventory_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_consumables');
    }
};
