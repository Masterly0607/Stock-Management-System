<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        Role::query()->upsert([
            ['name' => 'Super Admin', 'guard_name' => 'web'],
            ['name' => 'Admin', 'guard_name' => 'web'],
            ['name' => 'Distributor', 'guard_name' => 'web'],
        ], ['name'], ['guard_name']);
    }
}
