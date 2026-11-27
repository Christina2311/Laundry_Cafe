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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id('purchase_id');

            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained('suppliers', 'supplier_id')
                ->restrictOnDelete();

            $table->foreignId('recorded_by')
                ->constrained('users', 'id')
                ->restrictOnDelete();

            $table->enum('source_type', [
                'supplier',
                'store',
                'other',
            ]);

            $table->string('source_name', 150)->nullable();

            $table->dateTime('purchase_date')->useCurrent();

            $table->enum('purchase_status', [
                'pending',
                'received',
                'cancelled',
            ])->default('pending');

            $table->decimal('total_amount', 12, 2)->default(0);

            $table->index('purchase_date');
            $table->index('purchase_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
