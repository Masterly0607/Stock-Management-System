<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();

            $table->enum('reason', ['DAMAGE', 'EXPIRE', 'MANUAL']);
            $table->enum('status', ['DRAFT', 'POSTED'])->default('DRAFT');

            $table->string('ref_no')->nullable();        // ← added (optional reference)
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // ← optional

            $table->timestamps();
            $table->index(['branch_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adjustments');
    }
};
