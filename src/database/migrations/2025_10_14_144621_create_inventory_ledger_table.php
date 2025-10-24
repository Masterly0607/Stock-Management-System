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
        Schema::create('inventory_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->enum('txn_type', ['PURCHASE_IN', 'TRANSFER_OUT', 'TRANSFER_IN', 'SALE_OUT', 'ADJUST_IN', 'ADJUST_OUT', 'COUNT_SET']);
            $table->decimal('qty_delta', 18, 3);               // +IN / -OUT
            $table->decimal('balance_after', 18, 3);           // snapshot after post
            $table->string('reference_type');                // e.g. 'PurchaseOrder'
            $table->unsignedBigInteger('reference_id');      // e.g. 700
            $table->timestamp('posted_at');
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['branch_id', 'product_id', 'posted_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_ledger');
    }
};
