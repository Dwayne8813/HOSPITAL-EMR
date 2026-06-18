<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $boliviaId = DB::table('countries')->where('code', 'BO')->value('id');

        $departments = [
            ['country_id' => $boliviaId, 'name' => 'Chuquisaca', 'code' => 'CHU'],
            ['country_id' => $boliviaId, 'name' => 'La Paz', 'code' => 'LPZ'],
            ['country_id' => $boliviaId, 'name' => 'Cochabamba', 'code' => 'CBB'],
            ['country_id' => $boliviaId, 'name' => 'Oruro', 'code' => 'ORU'],
            ['country_id' => $boliviaId, 'name' => 'Potosí', 'code' => 'PTS'],
            ['country_id' => $boliviaId, 'name' => 'Tarija', 'code' => 'TJA'],
            ['country_id' => $boliviaId, 'name' => 'Santa Cruz', 'code' => 'SCZ'],
            ['country_id' => $boliviaId, 'name' => 'Beni', 'code' => 'BEN'],
            ['country_id' => $boliviaId, 'name' => 'Pando', 'code' => 'PAN'],
        ];

        DB::table('departments')->insert($departments);
    }
}
