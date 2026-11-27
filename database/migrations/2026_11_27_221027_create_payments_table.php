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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');

            $table->foreignId('order_id')
                ->unique()
                ->constrained('orders', 'order_id')
                ->restrictOnDelete();

            $table->enum('payment_method', [
                'cash',
                'gcash',
                'maya',
                'bank_transfer',
            ]);

            $table->decimal('amount_paid', 12, 2);

            $table->enum('payment_status', [
                'pending',
                'verified',
                'rejected',
            ])->default('pending');

            $table->string('receipt_image_path')->nullable();

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users', 'id')
                ->restrictOnDelete();

            $table->dateTime('payment_date')->useCurrent();
            $table->dateTime('verification_date')->nullable();

            $table->index('payment_date');
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
