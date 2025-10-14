<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            LocationSeeder::class,
            BranchSeeder::class,
            UserSeeder::class,
            CatalogSeeder::class,
            SupplierSeeder::class,
            StockSeeder::class,
        ]);
    }
}
