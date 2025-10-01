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
        Schema::create('stock_ledgers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->morphs('source'); // source_id, source_type (Purchase, Order, Transfer, Adjustment, Count)
            $table->decimal('qty_change', 14, 3); // + IN, - OUT
            $table->string('reason', 30); // purchase, order, transfer_in, transfer_out, adjust, count
            $table->timestamp('occurred_at');
            $table->timestamps();
            $table->index(['branch_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_ledgers');
    }
};
