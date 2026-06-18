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
                'name' => 'Edificio Principal',
                'floors' => 3,
                'description' => 'Edificio principal del hospital con consultorios, urgencias y hospitalización',
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'institution_id' => $institutionId,
                'name' => 'Edificio de Emergencias',
                'floors' => 2,
                'description' => 'Edificio dedicado a servicios de emergencia y triage',
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'institution_id' => $institutionId,
                'name' => 'Edificio de Laboratorio',
                'floors' => 1,
                'description' => 'Edificio de análisis clínicos y diagnóstico por imagen',
                'status' => 'active',
                'created_at' => now(),
            ],
        ];

        DB::table('buildings')->insert($buildings);
    }
}
