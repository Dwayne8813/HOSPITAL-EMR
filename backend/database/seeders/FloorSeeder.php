<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FloorSeeder extends Seeder
{
    public function run(): void
    {
        $buildings = DB::table('buildings')->pluck('id', 'name')->toArray();

        $floors = [
            // Edificio Principal
            ['building_id' => $buildings['Edificio Principal'], 'name' => 'Planta Baja', 'number' => 0, 'description' => 'Recepción, admisión, emergencias y consultorios generales', 'status' => 'active', 'created_at' => now()],
            ['building_id' => $buildings['Edificio Principal'], 'name' => 'Piso 1', 'number' => 1, 'description' => 'Consultorios especializados y laboratorio', 'status' => 'active', 'created_at' => now()],
            ['building_id' => $buildings['Edificio Principal'], 'name' => 'Piso 2', 'number' => 2, 'description' => 'Hospitalización y quirófanos', 'status' => 'active', 'created_at' => now()],

            // Edificio de Emergencias
            ['building_id' => $buildings['Edificio de Emergencias'], 'name' => 'Planta Baja', 'number' => 0, 'description' => 'Triage y atención de emergencia', 'status' => 'active', 'created_at' => now()],
            ['building_id' => $buildings['Edificio de Emergencias'], 'name' => 'Piso 1', 'number' => 1, 'description' => 'Sala de observación y procedure rooms', 'status' => 'active', 'created_at' => now()],

            // Edificio de Laboratorio
            ['building_id' => $buildings['Edificio de Laboratorio'], 'name' => 'Planta Baja', 'number' => 0, 'description' => 'Laboratorio clínico y imagenología', 'status' => 'active', 'created_at' => now()],
        ];

        DB::table('floors')->insert($floors);
    }
}
