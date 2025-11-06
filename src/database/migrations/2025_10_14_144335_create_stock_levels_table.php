<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_levels', function (Blueprint $t) {
            $t->id();
            $t->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->decimal('qty', 18, 3)->default(0);
            $t->timestamps();

            $t->unique(['branch_id', 'product_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('stock_levels');
    }
};
