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
            // Main Building
            ['building_id' => $buildings['Main Building'], 'name' => 'Planta Baja', 'number' => 0, 'description' => 'Recepción, admisión, emergencias y consultorios generales', 'status' => 'active', 'created_at' => now()],
            ['building_id' => $buildings['Main Building'], 'name' => 'Piso 1', 'number' => 1, 'description' => 'Consultorios especializados y laboratorio', 'status' => 'active', 'created_at' => now()],
            ['building_id' => $buildings['Main Building'], 'name' => 'Piso 2', 'number' => 2, 'description' => 'Hospitalization y quirófanos', 'status' => 'active', 'created_at' => now()],

            // Emergency Building
            ['building_id' => $buildings['Emergency Building'], 'name' => 'Planta Baja', 'number' => 0, 'description' => 'Triage y atención de emergencia', 'status' => 'active', 'created_at' => now()],
            ['building_id' => $buildings['Emergency Building'], 'name' => 'Piso 1', 'number' => 1, 'description' => 'Sala de observación y procedure rooms', 'status' => 'active', 'created_at' => now()],

            // Laboratory Building
            ['building_id' => $buildings['Laboratory Building'], 'name' => 'Planta Baja', 'number' => 0, 'description' => 'Laboratory clínico y imagenología', 'status' => 'active', 'created_at' => now()],
        ];

        DB::table('floors')->insert($floors);
    }
}
