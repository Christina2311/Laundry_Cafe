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
        Schema::create('laundry_status_histories', function (Blueprint $table) {
            $table->id('history_id');

            $table->foreignId('laundry_order_id')
                ->constrained('laundry_orders', 'laundry_order_id')
                ->restrictOnDelete();

            $table->foreignId('changed_by')
                ->constrained('users', 'id')
                ->restrictOnDelete();

            $table->string('previous_status', 50)->nullable();
            $table->string('new_status', 50);

            $table->dateTime('changed_at')->useCurrent();

            $table->text('remarks')->nullable();

            $table->index([
                'laundry_order_id',
                'changed_at'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laundry_status_histories');
    }
};
