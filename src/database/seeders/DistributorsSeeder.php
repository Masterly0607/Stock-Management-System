<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistributorsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        DB::table('distributors')->upsert([
            ['name' => 'Retail Partner A', 'contact_name' => 'Mr. R', 'phone' => '010111222', 'email' => 'retailA@test.com', 'address' => 'Phnom Penh', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Retail Partner B', 'contact_name' => 'Ms. B', 'phone' => '012333444', 'email' => 'retailB@test.com', 'address' => 'Siem Reap', 'created_at' => $now, 'updated_at' => $now],
        ], ['name'], ['contact_name', 'phone', 'email', 'address', 'updated_at']);
    }
}
