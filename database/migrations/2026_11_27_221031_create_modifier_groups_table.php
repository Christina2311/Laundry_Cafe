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
        Schema::create('modifier_groups', function (Blueprint $table) {
            $table->id('modifier_group_id');

            $table->foreignId('item_id')
                ->constrained('sellable_items', 'item_id')
                ->restrictOnDelete();

            $table->string('group_name', 100);
            $table->boolean('is_required')->default(false);

            $table->unique(['item_id', 'group_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modifier_groups');
    }
};
