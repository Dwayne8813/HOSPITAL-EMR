<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BedSeeder extends Seeder
{
    public function run(): void
    {
        $institutionId = DB::table('institutions')->first()->id;
        $rooms = DB::table('rooms')->pluck('id', 'number')->toArray();

        $beds = [];
        $bedCount = 0;

        // Habitaciones individuales - 1 cama cada una
        $individualRooms = ['2-01', '2-02'];
        foreach ($individualRooms as $roomNumber) {
            $bedCount++;
            $beds[] = [
                'room_id' => $rooms[$roomNumber],
                'institution_id' => $institutionId,
                'number' => 'CAMA-' . str_pad($bedCount, 3, '0', STR_PAD_LEFT),
                'bed_type' => 'standard',
                'status' => 'available',
                'daily_rate' => 350.00,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Habitaciones compartidas - 2 camas cada una
        $sharedRooms = ['2-03', '2-04'];
        foreach ($sharedRooms as $roomNumber) {
            for ($i = 1; $i <= 2; $i++) {
                $bedCount++;
                $beds[] = [
                    'room_id' => $rooms[$roomNumber],
                    'institution_id' => $institutionId,
                    'number' => 'CAMA-' . str_pad($bedCount, 3, '0', STR_PAD_LEFT),
                    'bed_type' => 'standard',
                    'status' => 'available',
                    'daily_rate' => 200.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Habitaciones tipo ward - 4 camas cada una
        $wardRooms = ['2-05', '2-06'];
        foreach ($wardRooms as $roomNumber) {
            for ($i = 1; $i <= 4; $i++) {
                $bedCount++;
                $beds[] = [
                    'room_id' => $rooms[$roomNumber],
                    'institution_id' => $institutionId,
                    'number' => 'CAMA-' . str_pad($bedCount, 3, '0', STR_PAD_LEFT),
                    'bed_type' => 'standard',
                    'status' => 'available',
                    'daily_rate' => 150.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // UCI - 6 camas ICU
        for ($i = 1; $i <= 6; $i++) {
            $bedCount++;
            $beds[] = [
                'room_id' => $rooms['2-07'],
                'institution_id' => $institutionId,
                'number' => 'CAMA-' . str_pad($bedCount, 3, '0', STR_PAD_LEFT),
                'bed_type' => 'icu',
                'status' => 'available',
                'daily_rate' => 800.00,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Sala de Recuperación - 4 camas
        for ($i = 1; $i <= 4; $i++) {
            $bedCount++;
            $beds[] = [
                'room_id' => $rooms['2-10'],
                'institution_id' => $institutionId,
                'number' => 'CAMA-' . str_pad($bedCount, 3, '0', STR_PAD_LEFT),
                'bed_type' => 'standard',
                'status' => 'available',
                'daily_rate' => 250.00,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Sala de Observación Emergencias - 8 camas
        for ($i = 1; $i <= 8; $i++) {
            $bedCount++;
            $beds[] = [
                'room_id' => $rooms['EM-06'],
                'institution_id' => $institutionId,
                'number' => 'CAMA-' . str_pad($bedCount, 3, '0', STR_PAD_LEFT),
                'bed_type' => 'standard',
                'status' => 'available',
                'daily_rate' => 180.00,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Sala de Observación Extendida - 6 camas
        for ($i = 1; $i <= 6; $i++) {
            $bedCount++;
            $beds[] = [
                'room_id' => $rooms['EM1-03'],
                'institution_id' => $institutionId,
                'number' => 'CAMA-' . str_pad($bedCount, 3, '0', STR_PAD_LEFT),
                'bed_type' => 'standard',
                'status' => 'available',
                'daily_rate' => 180.00,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('beds')->insert($beds);
    }
}
