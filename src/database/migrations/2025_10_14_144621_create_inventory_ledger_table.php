<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_ledger', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->foreignId('branch_id')->constrained()->cascadeOnDelete();

            // Movement types (string enum-style)
            $t->string('movement', 32)->index(); // IN, OUT, TRANSFER_IN, TRANSFER_OUT, SALE_OUT, ADJUST_IN, ADJUST_OUT
            $t->decimal('qty', 18, 3);
            $t->decimal('balance_after', 18, 3);

            // Idempotency keys to tie back to source doc
            $t->string('source_type', 64);
            $t->unsignedBigInteger('source_id');
            $t->unsignedBigInteger('source_line')->default(0);

            $t->timestamp('posted_at');
            $t->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();

            $t->string('hash', 128)->nullable();
            $t->timestamps();

            $t->unique(['source_type', 'source_id', 'source_line', 'branch_id', 'product_id', 'movement'], 'ledger_unique_source_line');
            $t->index(['product_id', 'branch_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('inventory_ledger');
    }
};
