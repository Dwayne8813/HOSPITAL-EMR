<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingSeeder extends Seeder
{
    public function run(): void
    {
        $institutionId = DB::table('institutions')->first()->id;

        $buildings = [
            [
                'institution_id' => $institutionId,
                'name' => 'Main Building',
                'floors' => 3,
                'description' => 'Main hospital building with clinics, emergency department, and inpatient care',
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'institution_id' => $institutionId,
                'name' => 'Emergency Building',
                'floors' => 2,
                'description' => 'Building dedicated to emergency services and triage',
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'institution_id' => $institutionId,
                'name' => 'Laboratory Building',
                'floors' => 1,
                'description' => 'Building for clinical analysis and diagnostic imaging',
                'status' => 'active',
                'created_at' => now(),
            ],
        ];

        DB::table('buildings')->insert($buildings);
    }
}
