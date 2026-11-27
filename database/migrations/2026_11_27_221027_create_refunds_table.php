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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id('refund_id');

            $table->foreignId('payment_id')
                ->constrained('payments', 'payment_id')
                ->restrictOnDelete();

            $table->foreignId('processed_by')
                ->constrained('users', 'id')
                ->restrictOnDelete();

            $table->decimal('refund_amount', 12, 2);
            $table->text('refund_reason');
            $table->dateTime('refund_date')->useCurrent();

            $table->index('refund_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
