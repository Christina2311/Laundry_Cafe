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
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->id('recipe_ingredient_id');

            $table->foreignId('recipe_id')
                ->constrained('recipes', 'recipe_id')
                ->cascadeOnDelete();

            $table->foreignId('inventory_item_id')
                ->constrained('inventory_items', 'inventory_item_id')
                ->restrictOnDelete();

            $table->decimal('quantity_required', 15, 4);

            $table->foreignId('unit_id')
                ->constrained('measurement_units', 'unit_id')
                ->restrictOnDelete();

            $table->unique(['recipe_id', 'inventory_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_ingredients');
    }
};
