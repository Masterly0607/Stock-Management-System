<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();

            $table->string('customer_name');
            $table->enum('status', ['DRAFT', 'CONFIRMED', 'PAID', 'DELIVERED', 'CANCELLED'])->default('DRAFT');

            // Pay-before-deliver support
            $table->boolean('requires_prepayment')->default(true);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0); // ← added

            $table->string('currency', 3)->default('USD');

            // Operational timestamps
            $table->timestamp('delivered_at')->nullable(); // ← added
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->index(['branch_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
