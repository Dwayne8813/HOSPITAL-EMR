<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestAppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $institutionId = DB::table('institutions')->first()->id;
        $doctorId = 3;
        $specialtyId = DB::table('specialties')->where('slug', 'medicina-general')->first()->id;
        $patientIds = DB::table('patients')->pluck('id')->toArray();

        $appointments = [
            [
                'institution_id' => $institutionId,
                'patient_id' => $patientIds[0],
                'doctor_id' => $doctorId,
                'specialty_id' => $specialtyId,
                'appointment_date' => date('Y-m-d', strtotime('+1 day')),
                'start_time' => '08:00',
                'end_time' => '08:30',
                'status' => 'scheduled',
                'reason' => 'Control de presión arterial',
                'notes' => 'Seguimiento mensual',
                'confirmed_at' => null,
                'cancelled_at' => null,
                'cancellation_reason' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'institution_id' => $institutionId,
                'patient_id' => $patientIds[1],
                'doctor_id' => $doctorId,
                'specialty_id' => $specialtyId,
                'appointment_date' => date('Y-m-d', strtotime('+2 days')),
                'start_time' => '09:00',
                'end_time' => '09:30',
                'status' => 'confirmed',
                'reason' => 'Dolor de estómago recurrente',
                'notes' => null,
                'confirmed_at' => now(),
                'cancelled_at' => null,
                'cancellation_reason' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'institution_id' => $institutionId,
                'patient_id' => $patientIds[2],
                'doctor_id' => $doctorId,
                'specialty_id' => $specialtyId,
                'appointment_date' => date('Y-m-d', strtotime('+3 days')),
                'start_time' => '10:00',
                'end_time' => '10:30',
                'status' => 'scheduled',
                'reason' => 'Examen general de salud',
                'notes' => 'Paciente requiere ayuno',
                'confirmed_at' => null,
                'cancelled_at' => null,
                'cancellation_reason' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'institution_id' => $institutionId,
                'patient_id' => $patientIds[3],
                'doctor_id' => $doctorId,
                'specialty_id' => $specialtyId,
                'appointment_date' => date('Y-m-d', strtotime('+5 days')),
                'start_time' => '11:00',
                'end_time' => '11:30',
                'status' => 'scheduled',
                'reason' => 'Revisión de resultados de laboratorio',
                'notes' => null,
                'confirmed_at' => null,
                'cancelled_at' => null,
                'cancellation_reason' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'institution_id' => $institutionId,
                'patient_id' => $patientIds[4],
                'doctor_id' => $doctorId,
                'specialty_id' => $specialtyId,
                'appointment_date' => date('Y-m-d', strtotime('-2 days')),
                'start_time' => '08:30',
                'end_time' => '09:00',
                'status' => 'completed',
                'reason' => 'Consulta por gripe',
                'notes' => 'Se recetó medicamento para el resfriado',
                'confirmed_at' => now(),
                'cancelled_at' => null,
                'cancellation_reason' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('appointments')->insert($appointments);
    }
}
