<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\District;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $pp = Province::firstOrCreate(['code' => 'PP'], ['name' => 'Phnom Penh']);
        $sr = Province::firstOrCreate(['code' => 'SR'], ['name' => 'Siem Reap']);

        District::firstOrCreate(['code' => 'PP-DP'], ['name' => 'Daun Penh', 'province_id' => $pp->id]);
        District::firstOrCreate(['code' => 'PP-7M'], ['name' => '7 Makara', 'province_id' => $pp->id]);
        District::firstOrCreate(['code' => 'SR-SMC'], ['name' => 'Siem Reap City', 'province_id' => $sr->id]);
    }
}
