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
        // Goal: Each company location (HQ or branch). Example: HQ, Phnom Penh Branch.
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['HQ', 'PROVINCE', 'DISTRICT'])->default('HQ');
            $table->foreignId('province_id')->nullable()->constrained()->restrictOnDelete(); // restrictOnDelete = prevents deleting a parent record if it still has related child records.
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete(); // nullOnDelete = If the parent record is deleted, set the foreign key in the child record to NULL instead of deleting it or throwing an error.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
