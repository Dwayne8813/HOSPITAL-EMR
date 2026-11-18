<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Cie10CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'A00-B99', 'name' => 'Infectious and parasitic diseases', 'description' => 'Diseases caused by biological agents', 'created_at' => now()],
            ['code' => 'C00-D49', 'name' => 'Neoplasms', 'description' => 'Tumors and cancers', 'created_at' => now()],
            ['code' => 'D50-D89', 'name' => 'Diseases of the blood and blood-forming organs', 'description' => 'Blood disorders', 'created_at' => now()],
            ['code' => 'E00-E89', 'name' => 'Endocrine, nutritional and metabolic diseases', 'description' => 'Hormonal and metabolic disorders', 'created_at' => now()],
            ['code' => 'F01-F99', 'name' => 'Mental and behavioral disorders', 'description' => 'Psychiatric and neurological diseases', 'created_at' => now()],
            ['code' => 'G00-G99', 'name' => 'Diseases of the nervous system', 'description' => 'Disorders of the central and peripheral nervous system', 'created_at' => now()],
            ['code' => 'H00-H59', 'name' => 'Diseases of the eye and adnexa', 'description' => 'Eye disorders', 'created_at' => now()],
            ['code' => 'H60-H95', 'name' => 'Diseases of the ear and mastoid process', 'description' => 'Ear disorders', 'created_at' => now()],
            ['code' => 'I00-I99', 'name' => 'Diseases of the circulatory system', 'description' => 'Heart and vascular diseases', 'created_at' => now()],
            ['code' => 'J00-J99', 'name' => 'Diseases of the respiratory system', 'description' => 'Respiratory disorders', 'created_at' => now()],
            ['code' => 'K00-K95', 'name' => 'Diseases of the digestive system', 'description' => 'Gastrointestinal disorders', 'created_at' => now()],
            ['code' => 'L00-L99', 'name' => 'Diseases of the skin and subcutaneous tissue', 'description' => 'Skin diseases', 'created_at' => now()],
            ['code' => 'M00-M99', 'name' => 'Diseases of the musculoskeletal system and connective tissue', 'description' => 'Bone and joint disorders', 'created_at' => now()],
            ['code' => 'N00-N99', 'name' => 'Diseases of the genitourinary system', 'description' => 'Urological and gynecological disorders', 'created_at' => now()],
            ['code' => 'O00-O9A', 'name' => 'Pregnancy, childbirth and the puerperium', 'description' => 'Complications of pregnancy and childbirth', 'created_at' => now()],
            ['code' => 'P00-P96', 'name' => 'Certain conditions originating in the perinatal period', 'description' => 'Neonatal disorders', 'created_at' => now()],
            ['code' => 'Q00-Q99', 'name' => 'Congenital malformations, deformations and chromosomal abnormalities', 'description' => 'Birth defects', 'created_at' => now()],
            ['code' => 'R00-R99', 'name' => 'Symptoms, signs and abnormal clinical findings', 'description' => 'Abnormal laboratory and study findings', 'created_at' => now()],
            ['code' => 'S00-T88', 'name' => 'Injury, poisoning and certain other consequences of external causes', 'description' => 'Trauma and poisoning', 'created_at' => now()],
            ['code' => 'V00-Y99', 'name' => 'External causes of morbidity and mortality', 'description' => 'Accidents and violence', 'created_at' => now()],
            ['code' => 'Z00-Z99', 'name' => 'Factors influencing health status and contact with health services', 'description' => 'Contact codes and reasons for consultation', 'created_at' => now()],
        ];

        DB::table('cie10_categories')->insert($categories);
    }
}
