<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $hqId     = Branch::where('code', 'HQ')->value('id');
        $ppProvId = Branch::where('code', 'PP-PROV')->value('id');
        $dpId     = Branch::where('code', 'PP-DP-01')->value('id');

        $super = User::firstOrCreate(['email' => 'superadmin@example.com'], [
            'name' => 'Super Admin',
            'password' => Hash::make('1234'),
            'branch_id' => $hqId,
            'status' => true,
        ]);
        $super->assignRole('Super Admin');

        $admin = User::firstOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Province Admin',
            'password' => Hash::make('1234'),
            'branch_id' => $ppProvId,
            'status' => true,
        ]);
        $admin->assignRole('Admin');

        $dist = User::firstOrCreate(['email' => 'distributor@example.com'], [
            'name' => 'Daun Penh Distributor',
            'password' => Hash::make('1234'),
            'branch_id' => $dpId,
            'status' => true,
        ]);
        $dist->assignRole('Distributor');
    }
}
