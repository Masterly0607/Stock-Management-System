<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Unit;
use App\Models\Product;
use App\Models\StockLevel;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $hqId = Branch::where('code', 'HQ')->value('id');
        $unitId = Unit::where('name', 'Piece')->value('id');

        $products = Product::whereIn('sku', ['COC-1L', 'SHAM-500'])->get();

        foreach ($products as $product) {
            StockLevel::firstOrCreate(
                ['branch_id' => $hqId, 'product_id' => $product->id, 'unit_id' => $unitId],
                ['on_hand' => 1000, 'reserved' => 0]
            );
        }
    }
}
