<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Stock level = How many items are in stock?, Where are they?, How many are already promised to customers?
    public function up(): void
    {
        Schema::create('stock_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->decimal('on_hand', 18, 3)->default(0); //  number of product that available in stock
            $table->decimal('reserved', 18, 3)->default(0); // for customer orders but not yet delivered
            $table->timestamps();
            $table->unique(['branch_id', 'product_id', 'unit_id']); // Why we combine them: Because none of those columns alone can uniquely identify a stock record — but together, they do. 
            // 1. If only branch_id was unique, You could store only one record per branch.
            // 2. If only product_id was unique: You could store only one record per product in the whole system, even if you have multiple branches.
            // 3. If only unit_id was unique: You couldn’t even have multiple units (like kg, box, piece) for the same product.


            $table->index(['branch_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_levels');
    }
};
