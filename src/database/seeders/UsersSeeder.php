<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $super = User::updateOrCreate(
            ['email' => 'super@itc.test'],
            ['name' => 'Super Admin', 'password' => Hash::make('password')]
        );
        $super->syncRoles(['Super Admin']);

        $admin = User::updateOrCreate(
            ['email' => 'admin@branch.test'],
            ['name' => 'Branch Admin', 'password' => Hash::make('password'), 'branch_id' => 1]
        );
        $admin->syncRoles(['Admin']);
    }
}
