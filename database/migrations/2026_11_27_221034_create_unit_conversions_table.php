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
        Schema::create('unit_conversions', function (Blueprint $table) {
            $table->id('conversion_id');

            $table->foreignId('inventory_item_id')
                ->constrained('inventory_items', 'inventory_item_id')
                ->restrictOnDelete();

            $table->foreignId('from_unit_id')
                ->constrained('measurement_units', 'unit_id')
                ->restrictOnDelete();

            $table->foreignId('to_unit_id')
                ->constrained('measurement_units', 'unit_id')
                ->restrictOnDelete();

            $table->decimal('conversion_factor', 18, 8);

            $table->unique(
                ['inventory_item_id', 'from_unit_id', 'to_unit_id'],
                'inventory_unit_conversion_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_conversions');
    }
};
