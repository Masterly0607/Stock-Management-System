<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockLevelsSeeder extends Seeder
{
    public function run(): void
    {
        $branches = DB::table('branches')->pluck('id');
        $products = DB::table('products')->pluck('id');
        $units    = DB::table('units')->whereIn('id', [1])->pluck('id'); // keep base unit rows

        $rows = [];
        foreach ($branches as $b) {
            foreach ($products as $p) {
                foreach ($units as $u) {
                    $rows[] = [
                        'branch_id' => $b,
                        'product_id' => $p,
                        'unit_id' => $u,
                        'on_hand' => 0,
                        'reserved' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
        }
        collect($rows)->chunk(500)->each(
            fn($chunk) =>
            DB::table('stock_levels')->upsert($chunk->toArray(), ['branch_id', 'product_id', 'unit_id'], ['on_hand', 'reserved', 'updated_at'])
        );
    }
}
