<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('branches')->upsert(
            [
                [
                    'code' => 'BR-HQ',
                    'name' => 'HQ',
                    'type' => 'HQ',
                    'province_id' => 1,
                    'district_id' => null,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'code' => 'BR-PP',
                    'name' => 'Phnom Penh Admin',
                    'type' => 'PROVINCE',
                    'province_id' => 1,
                    'district_id' => null,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'code' => 'BR-DNP',
                    'name' => 'Daun Penh Distributor',
                    'type' => 'DISTRICT',
                    'province_id' => 1,
                    'district_id' => 10,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            ['code'], // <-- unique/constraint column(s) to match on
            ['name', 'type', 'province_id', 'district_id', 'is_active', 'updated_at']
        ); // columns to update if exists
    }
}
