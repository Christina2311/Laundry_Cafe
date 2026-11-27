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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');

            $table->foreignId('processed_by')
                ->constrained('users', 'id')
                ->restrictOnDelete();

            $table->foreignId('discount_id')
                ->nullable()
                ->constrained('discounts', 'discount_id')
                ->restrictOnDelete();

            $table->dateTime('order_datetime')->useCurrent();

            $table->enum('order_status', [
                'pending',
                'completed',
                'cancelled',
            ])->default('pending');

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);

            $table->text('notes')->nullable();

            $table->index('order_datetime');
            $table->index('order_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
