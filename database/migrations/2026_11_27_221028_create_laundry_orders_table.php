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
        Schema::create('laundry_orders', function (Blueprint $table) {
            $table->id('laundry_order_id');

            $table->foreignId('order_id')
                ->unique()
                ->constrained('orders', 'order_id')
                ->restrictOnDelete();

            $table->string('customer_name', 150);

            $table->unsignedInteger('total_loads')->default(0);
            $table->unsignedInteger('total_pieces')->default(0);

            $table->dateTime('requested_pickup_datetime')
                ->nullable();

            $table->enum('laundry_status', [
                'pending',
                'washing',
                'drying',
                'folding',
                'ready_for_pickup',
                'claimed',
                'cancelled',
            ])->default('pending');

            $table->text('special_instructions')->nullable();

            $table->index('laundry_status');
            $table->index('requested_pickup_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laundry_orders');
    }
};
