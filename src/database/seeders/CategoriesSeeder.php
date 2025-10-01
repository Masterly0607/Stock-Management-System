<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $cats = collect(['Beverages', 'Snacks', 'Household'])->map(fn($n) => [
            'name' => $n,
            'slug' => Str::slug($n),
            'created_at' => $now,
            'updated_at' => $now
        ])->all();

        DB::table('categories')->upsert($cats, ['slug'], ['name', 'updated_at']);
    }
}
