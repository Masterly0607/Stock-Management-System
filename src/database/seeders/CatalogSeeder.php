<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Price;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $bev = Category::firstOrCreate(['code' => 'BEV'], ['name' => 'Beverages', 'is_active' => true]);
        $hbc = Category::firstOrCreate(['code' => 'HBC'], ['name' => 'Health & Beauty', 'is_active' => true]);

        $pc  = Unit::firstOrCreate(['name' => 'Piece'], ['symbol' => 'pc', 'base_ratio' => 1]);
        $box = Unit::firstOrCreate(['name' => 'Box'], ['symbol' => 'box', 'base_ratio' => 1]);

        $p1 = Product::firstOrCreate(['sku' => 'COC-1L'], [
            'name' => 'Coca Cola 1L',
            'barcode' => '1234567890001',
            'category_id' => $bev->id,
            'unit_base_id' => $pc->id,
            'brand' => 'Coca Cola',
            'is_active' => true,
        ]);

        $p2 = Product::firstOrCreate(['sku' => 'SHAM-500'], [
            'name' => 'Shampoo 500ml',
            'barcode' => '1234567890002',
            'category_id' => $hbc->id,
            'unit_base_id' => $pc->id,
            'brand' => 'Shine',
            'is_active' => true,
        ]);

        Price::firstOrCreate(['product_id' => $p1->id, 'province_id' => null, 'unit_id' => $pc->id, 'price' => 1.50, 'currency' => 'USD', 'is_active' => true]);
        Price::firstOrCreate(['product_id' => $p2->id, 'province_id' => null, 'unit_id' => $pc->id, 'price' => 3.20, 'currency' => 'USD', 'is_active' => true]);
    }
}
