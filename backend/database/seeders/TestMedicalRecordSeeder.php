<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestMedicalRecordSeeder extends Seeder
{
    public function run(): void
    {
        $institutionId = DB::table('institutions')->first()->id;
        $doctorId = 3;
        $patientIds = DB::table('patients')->pluck('id')->toArray();

        $medicalRecords = [
            [
                'institution_id' => $institutionId,
                'patient_id' => $patientIds[4],
                'doctor_id' => $doctorId,
                'consultation_date' => date('Y-m-d', strtotime('-2 days')),
                'record_type' => 'consultation',
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'institution_id' => $institutionId,
                'patient_id' => $patientIds[0],
                'doctor_id' => $doctorId,
                'consultation_date' => date('Y-m-d', strtotime('-7 days')),
                'record_type' => 'consultation',
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('medical_records')->insert($medicalRecords);
    }
}
