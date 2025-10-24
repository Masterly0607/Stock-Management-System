<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->upsert([
            ['id' => 101, 'category_id' => 1, 'unit_id' => 1, 'sku' => 'SH-001', 'barcode' => '885000000001', 'name' => 'Shampoo 250ml', 'brand' => 'FreshCare', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 201, 'category_id' => 2, 'unit_id' => 1, 'sku' => 'SP-001', 'barcode' => '885000000101', 'name' => 'Soap Bar', 'brand' => 'CleanMe', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ], ['id'], ['category_id', 'unit_id', 'sku', 'barcode', 'name', 'brand', 'is_active', 'updated_at']);
    }
}
