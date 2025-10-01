<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuppliersSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        DB::table('suppliers')->upsert([
            ['name' => 'ABC Supply', 'contact_name' => 'Mr. A', 'phone' => '012000111', 'email' => 'abc@supply.test', 'address' => 'Phnom Penh', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'SR Wholesale', 'contact_name' => 'Ms. S', 'phone' => '016222333', 'email' => 'sr@wholesale.test', 'address' => 'Siem Reap', 'created_at' => $now, 'updated_at' => $now],
        ], ['name'], ['contact_name', 'phone', 'email', 'address', 'updated_at']);
    }
}
