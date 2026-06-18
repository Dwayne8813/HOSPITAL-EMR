<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'Bolivia', 'code' => 'BO', 'phone_code' => '+591'],
            ['name' => 'Argentina', 'code' => 'AR', 'phone_code' => '+54'],
            ['name' => 'Brasil', 'code' => 'BR', 'phone_code' => '+55'],
            ['name' => 'Chile', 'code' => 'CL', 'phone_code' => '+56'],
            ['name' => 'Paraguay', 'code' => 'PY', 'phone_code' => '+595'],
            ['name' => 'Perú', 'code' => 'PE', 'phone_code' => '+51'],
        ];

        DB::table('countries')->insert($countries);
    }
}
