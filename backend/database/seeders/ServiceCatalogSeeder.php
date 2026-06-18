<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $institutionId = DB::table('institutions')->first()->id;

        $services = [
            // Consultas
            ['institution_id' => $institutionId, 'name' => 'Consulta General', 'category' => 'Consultas', 'code' => 'CON-001', 'description' => 'Consulta médica general', 'price' => 150.00, 'insurance_price' => 100.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Consulta Especialista', 'category' => 'Consultas', 'code' => 'CON-002', 'description' => 'Consulta con médico especialista', 'price' => 250.00, 'insurance_price' => 180.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Consulta de Control', 'category' => 'Consultas', 'code' => 'CON-003', 'description' => 'Consulta de seguimiento', 'price' => 100.00, 'insurance_price' => 70.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Consulta Pediátrica', 'category' => 'Consultas', 'code' => 'CON-004', 'description' => 'Consulta médica para niños', 'price' => 180.00, 'insurance_price' => 120.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Consulta Ginecológica', 'category' => 'Consultas', 'code' => 'CON-005', 'description' => 'Consulta ginecológica', 'price' => 200.00, 'insurance_price' => 150.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Emergencia
            ['institution_id' => $institutionId, 'name' => 'Emergencia General', 'category' => 'Emergencia', 'code' => 'EMR-001', 'description' => 'Atención de emergencia general', 'price' => 200.00, 'insurance_price' => 150.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Triage', 'category' => 'Emergencia', 'code' => 'EMR-002', 'description' => 'Clasificación de pacientes', 'price' => 50.00, 'insurance_price' => 30.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Urgencia', 'category' => 'Emergencia', 'code' => 'EMR-003', 'description' => 'Atención de urgencia', 'price' => 180.00, 'insurance_price' => 120.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Laboratorio
            ['institution_id' => $institutionId, 'name' => 'Hemograma Completo', 'category' => 'Laboratorio', 'code' => 'LAB-001', 'description' => 'Análisis de sangre completo', 'price' => 50.00, 'insurance_price' => 35.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Glicemia', 'category' => 'Laboratorio', 'code' => 'LAB-002', 'description' => 'Determinación de glucosa', 'price' => 25.00, 'insurance_price' => 15.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Colesterol Total', 'category' => 'Laboratorio', 'code' => 'LAB-003', 'description' => 'Determinación de colesterol', 'price' => 40.00, 'insurance_price' => 25.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Triglicéridos', 'category' => 'Laboratorio', 'code' => 'LAB-004', 'description' => 'Determinación de triglicéridos', 'price' => 40.00, 'insurance_price' => 25.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Creatinina', 'category' => 'Laboratorio', 'code' => 'LAB-005', 'description' => 'Determinación de creatinina', 'price' => 35.00, 'insurance_price' => 20.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Urianálisis', 'category' => 'Laboratorio', 'code' => 'LAB-006', 'description' => 'Análisis completo de orina', 'price' => 30.00, 'insurance_price' => 20.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Hemograma con Eritrosedimentación', 'category' => 'Laboratorio', 'code' => 'LAB-007', 'description' => 'Hemograma completo con VSG', 'price' => 65.00, 'insurance_price' => 45.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Prueba de Embarazo', 'category' => 'Laboratorio', 'code' => 'LAB-008', 'description' => 'Determinación de beta-HCG', 'price' => 45.00, 'insurance_price' => 30.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'HbA1c', 'category' => 'Laboratorio', 'code' => 'LAB-009', 'description' => 'Hemoglobina glucosilada', 'price' => 80.00, 'insurance_price' => 55.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Perfil Lipídico', 'category' => 'Laboratorio', 'code' => 'LAB-010', 'description' => 'Perfil completo de lípidos', 'price' => 90.00, 'insurance_price' => 60.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Perfil Hepático', 'category' => 'Laboratorio', 'code' => 'LAB-011', 'description' => 'Función hepática completa', 'price' => 120.00, 'insurance_price' => 80.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Examen de Heces', 'category' => 'Laboratorio', 'code' => 'LAB-012', 'description' => 'Coprocultivo y parasitoscópico', 'price' => 35.00, 'insurance_price' => 25.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Gasometría Arterial', 'category' => 'Laboratorio', 'code' => 'LAB-013', 'description' => 'Análisis de gases sanguíneos', 'price' => 75.00, 'insurance_price' => 50.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Imagenología
            ['institution_id' => $institutionId, 'name' => 'Radiografía de Tórax', 'category' => 'Imagenología', 'code' => 'IMG-001', 'description' => 'Radiografía simple de tórax', 'price' => 120.00, 'insurance_price' => 80.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Radiografía de Abdomen', 'category' => 'Imagenología', 'code' => 'IMG-002', 'description' => 'Radiografía simple de abdomen', 'price' => 120.00, 'insurance_price' => 80.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Ecografía Abdominal', 'category' => 'Imagenología', 'code' => 'IMG-003', 'description' => 'Ecografía de abdomen completo', 'price' => 200.00, 'insurance_price' => 140.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Ecografía Obstétrica', 'category' => 'Imagenología', 'code' => 'IMG-004', 'description' => 'Ecografía de control prenatal', 'price' => 200.00, 'insurance_price' => 140.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Ecocardiograma', 'category' => 'Imagenología', 'code' => 'IMG-005', 'description' => 'Ecocardiograma transtorácico', 'price' => 350.00, 'insurance_price' => 250.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Tomografía', 'category' => 'Imagenología', 'code' => 'IMG-006', 'description' => 'Tomografía computarizada', 'price' => 500.00, 'insurance_price' => 350.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Resonancia Magnética', 'category' => 'Imagenología', 'code' => 'IMG-007', 'description' => 'Resonancia magnética nuclear', 'price' => 800.00, 'insurance_price' => 550.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Mamografía', 'category' => 'Imagenología', 'code' => 'IMG-008', 'description' => 'Estudio mamográfico', 'price' => 250.00, 'insurance_price' => 175.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Hospitalización
            ['institution_id' => $institutionId, 'name' => 'Habitación Individual', 'category' => 'Hospitalización', 'code' => 'HOS-001', 'description' => 'Habitación individual por día', 'price' => 350.00, 'insurance_price' => 250.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Habitación Compartida', 'category' => 'Hospitalización', 'code' => 'HOS-002', 'description' => 'Cama en habitación compartida por día', 'price' => 200.00, 'insurance_price' => 140.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'UCI', 'category' => 'Hospitalización', 'code' => 'HOS-003', 'description' => 'Unidad de Cuidados Intensivos por día', 'price' => 800.00, 'insurance_price' => 550.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Sala de Recuperación', 'category' => 'Hospitalización', 'code' => 'HOS-004', 'description' => 'Sala de recuperación post-operatoria', 'price' => 250.00, 'insurance_price' => 175.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Procedimientos
            ['institution_id' => $institutionId, 'name' => 'Nebulización', 'category' => 'Procedimientos', 'code' => 'PRO-001', 'description' => 'Nebulización bronquial', 'price' => 50.00, 'insurance_price' => 35.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Intravenosa', 'category' => 'Procedimientos', 'code' => 'PRO-002', 'description' => 'Instalación de vía IV', 'price' => 40.00, 'insurance_price' => 25.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Sutura', 'category' => 'Procedimientos', 'code' => 'PRO-003', 'description' => 'Sutura de heridas', 'price' => 80.00, 'insurance_price' => 55.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Curación', 'category' => 'Procedimientos', 'code' => 'PRO-004', 'description' => 'Curación y vendaje', 'price' => 60.00, 'insurance_price' => 40.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Electrocardiograma', 'category' => 'Procedimientos', 'code' => 'PRO-005', 'description' => 'ECG de 12 derivaciones', 'price' => 80.00, 'insurance_price' => 55.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Endoscopia', 'category' => 'Procedimientos', 'code' => 'PRO-006', 'description' => 'Endoscopia digestiva alta', 'price' => 400.00, 'insurance_price' => 280.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Colonoscopia', 'category' => 'Procedimientos', 'code' => 'PRO-007', 'description' => 'Colonoscopia diagnóstica', 'price' => 450.00, 'insurance_price' => 315.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Cirugía
            ['institution_id' => $institutionId, 'name' => 'Apendicectomía', 'category' => 'Cirugía', 'code' => 'CIR-001', 'description' => 'Cirugía de apendicectomía', 'price' => 2500.00, 'insurance_price' => 1750.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Colecistectomía', 'category' => 'Cirugía', 'code' => 'CIR-002', 'description' => 'Cirugía de vesícula', 'price' => 3500.00, 'insurance_price' => 2450.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Cesárea', 'category' => 'Cirugía', 'code' => 'CIR-003', 'description' => 'Parto por cesárea', 'price' => 3000.00, 'insurance_price' => 2100.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Consultas Especializadas
            ['institution_id' => $institutionId, 'name' => 'Consulta Cardiológica', 'category' => 'Consultas', 'code' => 'CON-006', 'description' => 'Consulta con cardiólogo', 'price' => 300.00, 'insurance_price' => 210.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Consulta Neurológica', 'category' => 'Consultas', 'code' => 'CON-007', 'description' => 'Consulta con neurólogo', 'price' => 300.00, 'insurance_price' => 210.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Consulta Dermatológica', 'category' => 'Consultas', 'code' => 'CON-008', 'description' => 'Consulta con dermatólogo', 'price' => 250.00, 'insurance_price' => 175.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Consulta Oftalmológica', 'category' => 'Consultas', 'code' => 'CON-009', 'description' => 'Consulta con oftalmólogo', 'price' => 250.00, 'insurance_price' => 175.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Consulta Otorrinolaringológica', 'category' => 'Consultas', 'code' => 'CON-010', 'description' => 'Consulta con otorrinolaringólogo', 'price' => 250.00, 'insurance_price' => 175.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Consulta Urológica', 'category' => 'Consultas', 'code' => 'CON-011', 'description' => 'Consulta con urólogo', 'price' => 250.00, 'insurance_price' => 175.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Consulta Gastroenterológica', 'category' => 'Consultas', 'code' => 'CON-012', 'description' => 'Consulta con gastroenterólogo', 'price' => 280.00, 'insurance_price' => 195.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('service_catalog')->insert($services);
    }
}
