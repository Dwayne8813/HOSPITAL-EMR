<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SpecialtySeeder extends Seeder
{
    public function run(): void
    {
        $specialties = [
            ['name' => 'Medicina General', 'slug' => 'medicina-general', 'description' => 'Atención médica primaria y preventiva', 'icon' => 'stethoscope', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Medicina Interna', 'slug' => 'medicina-interna', 'description' => 'Diagnóstico y tratamiento de enfermedades del adulto', 'icon' => 'heart-pulse', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pediatría', 'slug' => 'pediatria', 'description' => 'Atención médica de niños y adolescentes', 'icon' => 'baby', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ginecología', 'slug' => 'ginecologia', 'description' => 'Salud femenina y sistema reproductor', 'icon' => 'heart', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Obstetricia', 'slug' => 'obstetricia', 'description' => 'Embarazo, parto y puerperio', 'icon' => 'baby-carriage', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cardiología', 'slug' => 'cardiologia', 'description' => 'Enfermedades del corazón y sistema circulatorio', 'icon' => 'heart', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Neurología', 'slug' => 'neurologia', 'description' => 'Enfermedades del sistema nervioso', 'icon' => 'brain', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Traumatología', 'slug' => 'traumatologia', 'description' => 'Lesiones del sistema musculoesquelético', 'icon' => 'bone', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ortopedia', 'slug' => 'ortopedia', 'description' => 'Tratamiento de enfermedades óseas y articulares', 'icon' => 'bone', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dermatología', 'slug' => 'dermatologia', 'description' => 'Enfermedades de la piel', 'icon' => 'hand', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Psiquiatría', 'slug' => 'psiquiatria', 'description' => 'Trastornos mentales y del comportamiento', 'icon' => 'brain', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Psicología', 'slug' => 'psicologia', 'description' => 'Salud mental y terapia psicológica', 'icon' => 'brain', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Endocrinología', 'slug' => 'endocrinologia', 'description' => 'Enfermedades hormonales y metabólicas', 'icon' => 'flask', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Nefrología', 'slug' => 'nefrologia', 'description' => 'Enfermedades renales', 'icon' => 'filter', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Urología', 'slug' => 'urologia', 'description' => 'Enfermedades del sistema urinario', 'icon' => 'droplet', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Oftalmología', 'slug' => 'oftalmologia', 'description' => 'Enfermedades de los ojos', 'icon' => 'eye', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Otorrinolaringología', 'slug' => 'otorrinolaringologia', 'description' => 'Enfermedades del oído, nariz y garganta', 'icon' => 'ear', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Oncología', 'slug' => 'oncologia', 'description' => 'Diagnóstico y tratamiento del cáncer', 'icon' => 'ribbon', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Odontología', 'slug' => 'odontologia', 'description' => 'Salud bucal y dental', 'icon' => 'tooth', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Anestesiología', 'slug' => 'anestesiologia', 'description' => 'Anestesia y manejo del dolor', 'icon' => 'syringe', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cirugía General', 'slug' => 'cirugia-general', 'description' => 'Procedimientos quirúrgicos generales', 'icon' => 'scissors', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Radiología', 'slug' => 'radiologia', 'description' => 'Diagnóstico por imagen', 'icon' => 'x-ray', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Laboratorio Clínico', 'slug' => 'laboratorio', 'description' => 'Análisis clínicos y diagnóstico de laboratorio', 'icon' => 'flask', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Farmacia', 'slug' => 'farmacia', 'description' => 'Dispensación y gestión de medicamentos', 'icon' => 'pill', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Nutrición', 'slug' => 'nutricion', 'description' => 'Asesoramiento nutricional y dietético', 'icon' => 'apple', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fisioterapia', 'slug' => 'fisioterapia', 'description' => 'Rehabilitación física', 'icon' => 'activity', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gastroenterología', 'slug' => 'gastroenterologia', 'description' => 'Enfermedades del sistema digestivo', 'icon' => 'stomach', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pneumología', 'slug' => 'pneumologia', 'description' => 'Enfermedades respiratorias', 'icon' => 'wind', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Reumatología', 'slug' => 'reumatologia', 'description' => 'Enfermedades reumáticas y autoinmunes', 'icon' => 'hand', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hematología', 'slug' => 'hematologia', 'description' => 'Enfermedades de la sangre', 'icon' => 'droplet', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('specialties')->insert($specialties);
    }
}
