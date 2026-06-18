<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Cie10CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'A00-B99', 'name' => 'Enfermedades infecciosas y parasitarias', 'description' => 'Enfermedades causadas por agentes biológicos', 'created_at' => now()],
            ['code' => 'C00-D49', 'name' => 'Neoplasias', 'description' => 'Tumores y cánceres', 'created_at' => now()],
            ['code' => 'D50-D89', 'name' => 'Enfermedades de la sangre y de los órganos hematopoyéticos', 'description' => 'Trastornos sanguíneos', 'created_at' => now()],
            ['code' => 'E00-E89', 'name' => 'Enfermedades endocrinas, nutricionales y metabólicas', 'description' => 'Trastornos hormonales y metabólicos', 'created_at' => now()],
            ['code' => 'F01-F99', 'name' => 'Trastornos mentales y del comportamiento', 'description' => 'Enfermedades psiquiátricas y neurológicas', 'created_at' => now()],
            ['code' => 'G00-G99', 'name' => 'Enfermedades del sistema nervioso', 'description' => 'Patologías del sistema nervioso central y periférico', 'created_at' => now()],
            ['code' => 'H00-H59', 'name' => 'Enfermedades del ojo y sus anexos', 'description' => 'Patologías oculares', 'created_at' => now()],
            ['code' => 'H60-H95', 'name' => 'Enfermedades del oído y de la apófisis mastoides', 'description' => 'Patologías del oído', 'created_at' => now()],
            ['code' => 'I00-I99', 'name' => 'Enfermedades del sistema circulatorio', 'description' => 'Cardiopatías y enfermedades vasculares', 'created_at' => now()],
            ['code' => 'J00-J99', 'name' => 'Enfermedades del sistema respiratorio', 'description' => 'Patologías respiratorias', 'created_at' => now()],
            ['code' => 'K00-K95', 'name' => 'Enfermedades del sistema digestivo', 'description' => 'Patologías gastrointestinales', 'created_at' => now()],
            ['code' => 'L00-L99', 'name' => 'Enfermedades de la piel y del tejido subcutáneo', 'description' => 'Dermatopatías', 'created_at' => now()],
            ['code' => 'M00-M99', 'name' => 'Enfermedades del sistema osteomuscular y del tejido conjuntivo', 'description' => 'Patologías óseas y articulares', 'created_at' => now()],
            ['code' => 'N00-N99', 'name' => 'Enfermedades del sistema genitourinario', 'description' => 'Patologías urológicas y ginecológicas', 'created_at' => now()],
            ['code' => 'O00-O9A', 'name' => 'Embarazo, parto y puerperio', 'description' => 'Complicaciones del embarazo y parto', 'created_at' => now()],
            ['code' => 'P00-P96', 'name' => 'Ciertas afecciones originadas en el periodo perinatal', 'description' => 'Patologías neonatales', 'created_at' => now()],
            ['code' => 'Q00-Q99', 'name' => 'Malformaciones congénitas, deformidades y anomalías cromosómicas', 'description' => 'Defectos de nacimiento', 'created_at' => now()],
            ['code' => 'R00-R99', 'name' => 'Síntomas, signos y hallazgos anormales', 'description' => 'Resultados de laboratorio y estudios anormales', 'created_at' => now()],
            ['code' => 'S00-T88', 'name' => 'Lesiones, envenenamiento y ciertas otras consecuencias de causas externas', 'description' => 'Traumatismos y intoxicaciones', 'created_at' => now()],
            ['code' => 'V00-Y99', 'name' => 'Causas externas de morbilidad y mortalidad', 'description' => 'Accidentes y violencia', 'created_at' => now()],
            ['code' => 'Z00-Z99', 'name' => 'Factores que influyen en el estado de salud y contacto con servicios de salud', 'description' => 'Códigos de contato y motivos de consulta', 'created_at' => now()],
        ];

        DB::table('cie10_categories')->insert($categories);
    }
}
