<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TriageProtocolSeeder extends Seeder
{
    public function run(): void
    {
        $institutionId = DB::table('institutions')->first()->id;

        $protocols = [
            [
                'institution_id' => $institutionId,
                'name' => 'Nivel I - Resucitación',
                'criteria_json' => json_encode([
                    'signos_vitales' => 'Inestables, compromiso hemodinámico',
                    'condicion' => 'Paciente en estado crítico, sin pulso, sin respiración',
                    'tiempo_respuesta' => 'Inmediato',
                    'ejemplos' => [
                        'Paro cardiorrespiratorio',
                        'Shock severo',
                        'Trauma mayor',
                        'Hemorragia masiva',
                        'Coma profundo',
                    ],
                ]),
                'level' => 1,
                'color_code' => '#FF0000',
                'description' => 'Atención inmediata para pacientes en estado crítico que requieren reanimación y soporte vital.',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'institution_id' => $institutionId,
                'name' => 'Nivel II - Emergency',
                'criteria_json' => json_encode([
                    'signos_vitales' => 'Alterados, riesgo vital potencial',
                    'condicion' => 'Paciente con compromiso significativo',
                    'tiempo_respuesta' => 'Máximo 10 minutos',
                    'ejemplos' => [
                        'Dolor torácico agudo',
                        'Dificultad respiratoria severa',
                        'Convulsiones activas',
                        'Trauma con lesiones significativas',
                        'Shock moderado',
                        'Sangrado activo',
                    ],
                ]),
                'level' => 2,
                'color_code' => '#FF8C00',
                'description' => 'Atención urgente para pacientes con riesgo vital potencial que requieren intervención rápida.',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'institution_id' => $institutionId,
                'name' => 'Nivel III - Urgent Care',
                'criteria_json' => json_encode([
                    'signos_vitales' => 'Relativamente estables, sin riesgo inmediato',
                    'condicion' => 'Paciente con dolor moderado o molestia significativa',
                    'tiempo_respuesta' => 'Máximo 30 minutos',
                    'ejemplos' => [
                        'Dolor abdominal intenso',
                        'Fiebre alta con malestar general',
                        'Heridas que requieren sutura',
                        'Fracturas cerradas',
                        'Vómito y diarrea severos',
                        'Infecciones moderadas',
                    ],
                ]),
                'level' => 3,
                'color_code' => '#FFD700',
                'description' => 'Atención para pacientes con condiciones urgentes que no son inmediatamente potencialmente mortales.',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'institution_id' => $institutionId,
                'name' => 'Nivel IV - Menos Urgente',
                'criteria_json' => json_encode([
                    'signos_vitales' => 'Estables, sin compromiso significativo',
                    'condicion' => 'Paciente con condición no urgente que requiere atención',
                    'tiempo_respuesta' => 'Máximo 60 minutos',
                    'ejemplos' => [
                        'Dolor leve a moderado',
                        'Enfermedades crónicas estables',
                        'Infecciones leves',
                        'Problemas de piel menores',
                        'Medicación de续费',
                        'Solicitud de certificados',
                    ],
                ]),
                'level' => 4,
                'color_code' => '#32CD32',
                'description' => 'Atención para pacientes con condiciones no urgentes que pueden esperar sin riesgo.',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'institution_id' => $institutionId,
                'name' => 'Nivel V - No Urgente',
                'criteria_json' => json_encode([
                    'signos_vitales' => 'Normales, sin alteraciones',
                    'condicion' => 'Paciente sin urgencia médica, atención programada',
                    'tiempo_respuesta' => 'Puede esperar o referir a consulta externa',
                    'ejemplos' => [
                        'Control de enfermedades crónicas',
                        'Solicitud de recetas',
                        'Exámenes de rutina',
                        'Atención psicológica',
                        'Asesoramiento nutricional',
                        'Certificados médicos',
                    ],
                ]),
                'level' => 5,
                'color_code' => '#4169E1',
                'description' => 'Pacientes que no requieren atención de urgencia y pueden ser atendidos en consulta externa.',
                'is_active' => true,
                'created_at' => now(),
            ],
        ];

        DB::table('triage_protocols')->insert($protocols);
    }
}
