<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('units')->upsert([
            ['id' => 1, 'name' => 'Piece', 'symbol' => 'pcs', 'base_ratio' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Box', 'symbol' => 'box', 'base_ratio' => 12, 'created_at' => now(), 'updated_at' => now()],
        ], ['id'], ['name', 'symbol', 'base_ratio', 'updated_at']);
    }
}
