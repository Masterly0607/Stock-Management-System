<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $cat = DB::table('categories')->where('slug', 'beverages')->value('id');

        DB::table('products')->upsert([
            ['sku' => 'COLA-330', 'name' => 'Cola 330ml', 'slug' => Str::slug('Cola 330ml'), 'category_id' => $cat, 'unit' => 'can', 'price' => 0.80, 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'WATER-500', 'name' => 'Water 500ml', 'slug' => Str::slug('Water 500ml'), 'category_id' => $cat, 'unit' => 'bottle', 'price' => 0.30, 'created_at' => $now, 'updated_at' => $now],
        ], ['sku'], ['name', 'slug', 'category_id', 'unit', 'price', 'updated_at']);
    }
}
