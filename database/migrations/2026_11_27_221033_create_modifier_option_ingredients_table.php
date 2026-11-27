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
        Schema::create('modifier_option_ingredients', function (Blueprint $table) {
            $table->id('modifier_ingredient_id');

            $table->foreignId('modifier_option_id')
                ->constrained('modifier_options', 'modifier_option_id')
                ->cascadeOnDelete();

            $table->foreignId('inventory_item_id')
                ->constrained('inventory_items', 'inventory_item_id')
                ->restrictOnDelete();

            $table->decimal('quantity_required', 15, 4);

            $table->foreignId('unit_id')
                ->constrained('measurement_units', 'unit_id')
                ->restrictOnDelete();

            $table->unique(
                ['modifier_option_id', 'inventory_item_id'],
                'modifier_ingredient_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modifier_option_ingredients');
    }
};
