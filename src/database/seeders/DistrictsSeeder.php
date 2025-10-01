<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictsSeeder extends Seeder
{
    public function run(): void
    {
        $pp = DB::table('provinces')->where('name', 'Phnom Penh')->value('id');
        $sr = DB::table('provinces')->where('name', 'Siem Reap')->value('id');
        $now = now();

        DB::table('districts')->upsert([
            ['province_id' => $pp, 'name' => 'Chamkar Mon', 'code' => 'CKM', 'created_at' => $now, 'updated_at' => $now],
            ['province_id' => $pp, 'name' => 'Prampir Makara', 'code' => '7MK', 'created_at' => $now, 'updated_at' => $now],
            ['province_id' => $sr, 'name' => 'Siem Reap', 'code' => 'SRK', 'created_at' => $now, 'updated_at' => $now],
        ], ['province_id', 'name'], ['code', 'updated_at']);
    }
}
