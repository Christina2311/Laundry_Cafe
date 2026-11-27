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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id('expense_id');

            $table->foreignId('expense_category_id')
                ->constrained('expense_categories', 'expense_category_id')
                ->restrictOnDelete();

            $table->foreignId('recorded_by')
                ->constrained('users', 'id')
                ->restrictOnDelete();

            $table->text('expense_description');
            $table->decimal('amount', 12, 2);
            $table->dateTime('expense_date')->useCurrent();

            $table->index('expense_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
