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
        Schema::create('sellable_items', function (Blueprint $table) {
            $table->id('item_id');

            $table->foreignId('menu_item_id')
                ->nullable()
                ->constrained('menu_items', 'menu_item_id')
                ->restrictOnDelete();

            $table->string('item_name', 150);
            $table->enum('item_type', ['cafe', 'laundry']);
            $table->decimal('current_price', 12, 2);

            $table->boolean('counts_as_load')->default(false);
            $table->boolean('counts_as_piece')->default(false);
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellable_items');
    }
};
