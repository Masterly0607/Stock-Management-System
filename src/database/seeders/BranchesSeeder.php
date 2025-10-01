<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchesSeeder extends Seeder
{
    public function run(): void
    {
        $ppProv = DB::table('provinces')->where('name', 'Phnom Penh')->value('id');
        $ppDist = DB::table('districts')->where('name', 'Chamkar Mon')->value('id');
        $srProv = DB::table('provinces')->where('name', 'Siem Reap')->value('id');
        $srDist = DB::table('districts')->where('name', 'Siem Reap')->value('id');
        $now = now();

        DB::table('branches')->upsert([
            ['id' => 1, 'name' => 'Main Branch', 'code' => 'MB', 'province_id' => $ppProv, 'district_id' => $ppDist, 'address' => '#1 Phnom Penh', 'phone' => '012345678', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Siem Reap Branch', 'code' => 'SRB', 'province_id' => $srProv, 'district_id' => $srDist, 'address' => '#2 Siem Reap', 'phone' => '017000111', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['name', 'code', 'province_id', 'district_id', 'address', 'phone', 'updated_at']);
    }
}
