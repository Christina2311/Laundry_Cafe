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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id('inventory_item_id');

            $table->foreignId('category_id')
                ->constrained('inventory_categories', 'category_id')
                ->restrictOnDelete();

            $table->foreignId('base_unit_id')
                ->constrained('measurement_units', 'unit_id')
                ->restrictOnDelete();

            $table->string('item_name', 150);
            $table->decimal('current_stock', 15, 4)->default(0);
            $table->decimal('reorder_level', 15, 4)->default(0);
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
