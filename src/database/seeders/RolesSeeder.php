<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('roles')) {
            $this->command->warn('Skipping RolesSeeder: roles table not found. Run vendor:publish + migrate first.');
            return;
        }

        foreach (['Super Admin', 'Admin', 'Distributor'] as $name) {
            Role::findOrCreate($name, 'web');
        }
    }
}
