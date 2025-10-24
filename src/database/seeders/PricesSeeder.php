<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PricesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('prices')->upsert([
            ['id' => 1, 'product_id' => 101, 'province_id' => null, 'unit_id' => 1, 'price' => 3.50, 'currency' => 'USD', 'starts_at' => now(), 'ends_at' => null, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'product_id' => 201, 'province_id' => null, 'unit_id' => 1, 'price' => 1.00, 'currency' => 'USD', 'starts_at' => now(), 'ends_at' => null, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ], ['id'], ['product_id', 'province_id', 'unit_id', 'price', 'currency', 'starts_at', 'ends_at', 'is_active', 'updated_at']);
    }
}
