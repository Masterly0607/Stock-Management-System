<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $super = Role::firstOrCreate(['name' => 'Super Admin']);
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $dist  = Role::firstOrCreate(['name' => 'Distributor']);

        $perms = [
            'manage users',
            'manage products',
            'manage suppliers',
            'manage transfers',
            'manage stock',
            'view reports',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $super->syncPermissions(Permission::all());

        $admin->syncPermissions([
            'manage users',
            'manage transfers',
            'manage stock',
            'view reports',
        ]);

        $dist->syncPermissions([]);
    }
}
