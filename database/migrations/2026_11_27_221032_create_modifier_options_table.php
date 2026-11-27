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
        Schema::create('modifier_options', function (Blueprint $table) {
            $table->id('modifier_option_id');

            $table->foreignId('modifier_group_id')
                ->constrained('modifier_groups', 'modifier_group_id')
                ->restrictOnDelete();

            $table->string('option_name', 100);
            $table->decimal('additional_price', 12, 2)->default(0);

            $table->unique([
                'modifier_group_id',
                'option_name'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modifier_options');
    }
};
