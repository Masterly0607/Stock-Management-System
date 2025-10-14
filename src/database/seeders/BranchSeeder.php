<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Province;
use App\Models\District;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $hq = Branch::firstOrCreate(['code' => 'HQ'], ['name' => 'Head Office', 'type' => 'HQ', 'province_id' => null, 'district_id' => null]);

        $pp = Province::where('code', 'PP')->first();
        $sr = Province::where('code', 'SR')->first();

        Branch::firstOrCreate(['code' => 'PP-PROV'], ['name' => 'Phnom Penh Province Branch', 'type' => 'PROVINCE', 'province_id' => $pp?->id]);
        Branch::firstOrCreate(['code' => 'SR-PROV'], ['name' => 'Siem Reap Province Branch', 'type' => 'PROVINCE', 'province_id' => $sr?->id]);

        $pp_dp = District::where('code', 'PP-DP')->first();
        $pp_7m = District::where('code', 'PP-7M')->first();
        $sr_smc = District::where('code', 'SR-SMC')->first();

        Branch::firstOrCreate(['code' => 'PP-DP-01'], ['name' => 'Daun Penh Distributor', 'type' => 'DISTRICT', 'province_id' => $pp?->id, 'district_id' => $pp_dp?->id]);
        Branch::firstOrCreate(['code' => 'PP-7M-01'], ['name' => '7 Makara Distributor', 'type' => 'DISTRICT', 'province_id' => $pp?->id, 'district_id' => $pp_7m?->id]);
        Branch::firstOrCreate(['code' => 'SR-SMC-01'], ['name' => 'Siem Reap City Distributor', 'type' => 'DISTRICT', 'province_id' => $sr?->id, 'district_id' => $sr_smc?->id]);
    }
}
