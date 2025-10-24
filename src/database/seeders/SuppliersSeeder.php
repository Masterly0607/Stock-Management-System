<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuppliersSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('suppliers')->updateOrInsert(['code' => 'SUP-ACME'], [
            'name' => 'Acme Care Co.',
            'phone' => '012345678',
            'email' => 'sales@acme.co',
            'tax_id' => 'TIN-9988',
            'contact_name' => 'Mr. Dara',
            'address' => '#12, St 2004, PP',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
