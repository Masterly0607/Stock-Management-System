<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            ProvincesSeeder::class,
            DistrictsSeeder::class,
            BranchesSeeder::class,
            CategoriesSeeder::class,
            ProductsSeeder::class,
            SuppliersSeeder::class,
            DistributorsSeeder::class,
            UsersSeeder::class,
            TestDataSeeder::class,
        ]);
    }
}
