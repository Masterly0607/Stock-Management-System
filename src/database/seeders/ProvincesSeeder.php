<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvincesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        DB::table('provinces')->upsert([
            ['name' => 'Phnom Penh', 'code' => 'PP', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Siem Reap',  'code' => 'SR', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Battambang', 'code' => 'BTB', 'created_at' => $now, 'updated_at' => $now],
        ], ['name'], ['code', 'updated_at']);
    }
}
