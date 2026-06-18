<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicationCategorySeeder extends Seeder
{
    public function run(): void
    {
        $institutionId = DB::table('institutions')->first()->id;

        $categories = [
            ['institution_id' => $institutionId, 'name' => 'Analgésicos', 'description' => 'Medicamentos para el alivio del dolor', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Antibióticos', 'description' => 'Medicamentos para tratamiento de infecciones bacterianas', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Antiinflamatorios', 'description' => 'Medicamentos para reducir la inflamación', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Antipiréticos', 'description' => 'Medicamentos para reducir la fiebre', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Cardiovasculares', 'description' => 'Medicamentos para el sistema cardiovascular', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Digestivos', 'description' => 'Medicamentos para el sistema digestivo', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Endocrinos', 'description' => 'Medicamentos para trastornos hormonales', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Neurológicos', 'description' => 'Medicamentos para el sistema nervioso', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Respiratorios', 'description' => 'Medicamentos para el sistema respiratorio', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Dermatológicos', 'description' => 'Medicamentos para la piel', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Oftalmológicos', 'description' => 'Medicamentos para los ojos', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Otológicos', 'description' => 'Medicamentos para los oídos', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Urológicos', 'description' => 'Medicamentos para el sistema urinario', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Ginecológicos', 'description' => 'Medicamentos para el sistema reproductor femenino', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Hematológicos', 'description' => 'Medicamentos para la sangre', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Antifúngicos', 'description' => 'Medicamentos para infecciones por hongos', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Antivirales', 'description' => 'Medicamentos para infecciones virales', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Antiparasitarios', 'description' => 'Medicamentos para parásitos', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Vacunas', 'description' => 'Vacunas e inmunizaciones', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Suplementos', 'description' => 'Vitaminas y suplementos nutricionales', 'created_at' => now()],
        ];

        DB::table('medication_categories')->insert($categories);
    }
}
