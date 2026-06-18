<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $institutionId = DB::table('institutions')->first()->id;
        $floors = DB::table('floors')->pluck('id', 'name')->toArray();

        $rooms = [
            // Edificio Principal - Planta Baja
            ['floor_id' => $floors['Planta Baja'], 'institution_id' => $institutionId, 'number' => 'PB-01', 'name' => 'Consulta General 1', 'room_type' => 'shared', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Planta Baja'], 'institution_id' => $institutionId, 'number' => 'PB-02', 'name' => 'Consulta General 2', 'room_type' => 'shared', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Planta Baja'], 'institution_id' => $institutionId, 'number' => 'PB-03', 'name' => 'Consulta Pediátrica', 'room_type' => 'shared', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Planta Baja'], 'institution_id' => $institutionId, 'number' => 'PB-04', 'name' => 'Consulta Ginecológica', 'room_type' => 'shared', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Planta Baja'], 'institution_id' => $institutionId, 'number' => 'PB-05', 'name' => 'Sala de Espera', 'room_type' => 'ward', 'capacity' => 20, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],

            // Edificio Principal - Piso 1
            ['floor_id' => $floors['Piso 1'], 'institution_id' => $institutionId, 'number' => '1-01', 'name' => 'Consulta Cardiología', 'room_type' => 'individual', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Piso 1'], 'institution_id' => $institutionId, 'number' => '1-02', 'name' => 'Consulta Neurología', 'room_type' => 'individual', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Piso 1'], 'institution_id' => $institutionId, 'number' => '1-03', 'name' => 'Consulta Traumatología', 'room_type' => 'individual', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Piso 1'], 'institution_id' => $institutionId, 'number' => '1-04', 'name' => 'Consulta Dermatología', 'room_type' => 'individual', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Piso 1'], 'institution_id' => $institutionId, 'number' => '1-05', 'name' => 'Consulta Oftalmología', 'room_type' => 'individual', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Piso 1'], 'institution_id' => $institutionId, 'number' => '1-06', 'name' => 'Consulta Otorrinolaringología', 'room_type' => 'individual', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Piso 1'], 'institution_id' => $institutionId, 'number' => '1-07', 'name' => 'Consulta Urología', 'room_type' => 'individual', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Piso 1'], 'institution_id' => $institutionId, 'number' => '1-08', 'name' => 'Consulta Gastroenterología', 'room_type' => 'individual', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],

            // Edificio Principal - Piso 2
            ['floor_id' => $floors['Piso 2'], 'institution_id' => $institutionId, 'number' => '2-01', 'name' => 'Habitación 201', 'room_type' => 'individual', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Piso 2'], 'institution_id' => $institutionId, 'number' => '2-02', 'name' => 'Habitación 202', 'room_type' => 'individual', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Piso 2'], 'institution_id' => $institutionId, 'number' => '2-03', 'name' => 'Habitación 203', 'room_type' => 'shared', 'capacity' => 2, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Piso 2'], 'institution_id' => $institutionId, 'number' => '2-04', 'name' => 'Habitación 204', 'room_type' => 'shared', 'capacity' => 2, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Piso 2'], 'institution_id' => $institutionId, 'number' => '2-05', 'name' => 'Habitación 205', 'room_type' => 'ward', 'capacity' => 4, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Piso 2'], 'institution_id' => $institutionId, 'number' => '2-06', 'name' => 'Habitación 206', 'room_type' => 'ward', 'capacity' => 4, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Piso 2'], 'institution_id' => $institutionId, 'number' => '2-07', 'name' => 'UCI', 'room_type' => 'icu', 'capacity' => 6, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Piso 2'], 'institution_id' => $institutionId, 'number' => '2-08', 'name' => 'Sala Quirúrgica 1', 'room_type' => 'surgery', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Piso 2'], 'institution_id' => $institutionId, 'number' => '2-09', 'name' => 'Sala Quirúrgica 2', 'room_type' => 'surgery', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Piso 2'], 'institution_id' => $institutionId, 'number' => '2-10', 'name' => 'Sala Recuperación', 'room_type' => 'recovery', 'capacity' => 4, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],

            // Edificio de Emergencias - Planta Baja
            ['floor_id' => $floors['Planta Baja'], 'institution_id' => $institutionId, 'number' => 'EM-01', 'name' => 'Triage 1', 'room_type' => 'emergency', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Planta Baja'], 'institution_id' => $institutionId, 'number' => 'EM-02', 'name' => 'Triage 2', 'room_type' => 'emergency', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Planta Baja'], 'institution_id' => $institutionId, 'number' => 'EM-03', 'name' => 'Consulta Emergencia 1', 'room_type' => 'emergency', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Planta Baja'], 'institution_id' => $institutionId, 'number' => 'EM-04', 'name' => 'Consulta Emergencia 2', 'room_type' => 'emergency', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Planta Baja'], 'institution_id' => $institutionId, 'number' => 'EM-05', 'name' => 'Sala de Curaciones', 'room_type' => 'emergency', 'capacity' => 2, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Planta Baja'], 'institution_id' => $institutionId, 'number' => 'EM-06', 'name' => 'Sala de Observación', 'room_type' => 'ward', 'capacity' => 8, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],

            // Edificio de Emergencias - Piso 1
            ['floor_id' => $floors['Piso 1'], 'institution_id' => $institutionId, 'number' => 'EM1-01', 'name' => 'Sala de Procedimientos', 'room_type' => 'emergency', 'capacity' => 2, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Piso 1'], 'institution_id' => $institutionId, 'number' => 'EM1-02', 'name' => 'Sala de Rayos X', 'room_type' => 'emergency', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Piso 1'], 'institution_id' => $institutionId, 'number' => 'EM1-03', 'name' => 'Sala de Observación Extendida', 'room_type' => 'ward', 'capacity' => 6, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],

            // Edificio de Laboratorio - Planta Baja
            ['floor_id' => $floors['Planta Baja'], 'institution_id' => $institutionId, 'number' => 'LB-01', 'name' => 'Hematología', 'room_type' => 'shared', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Planta Baja'], 'institution_id' => $institutionId, 'number' => 'LB-02', 'name' => 'Química Sanguínea', 'room_type' => 'shared', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Planta Baja'], 'institution_id' => $institutionId, 'number' => 'LB-03', 'name' => 'Microbiología', 'room_type' => 'shared', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Planta Baja'], 'institution_id' => $institutionId, 'number' => 'LB-04', 'name' => 'Radiología', 'room_type' => 'shared', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['floor_id' => $floors['Planta Baja'], 'institution_id' => $institutionId, 'number' => 'LB-05', 'name' => 'Ecografía', 'room_type' => 'shared', 'capacity' => 1, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('rooms')->insert($rooms);
    }
}
