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
            ['institution_id' => $institutionId, 'name' => 'Analgesics', 'description' => 'Medications for pain relief', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Antibiotics', 'description' => 'Medications for treating bacterial infections', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Anti-inflammatories', 'description' => 'Medications to reduce inflammation', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Antipyretics', 'description' => 'Medications to reduce fever', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Cardiovascular', 'description' => 'Medications for the cardiovascular system', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Digestive', 'description' => 'Medications for the digestive system', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Endocrine', 'description' => 'Medications for hormonal disorders', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Neurological', 'description' => 'Medications for the nervous system', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Respiratory', 'description' => 'Medications for the respiratory system', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Dermatological', 'description' => 'Medications for the skin', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Ophthalmological', 'description' => 'Medications for the eyes', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Otological', 'description' => 'Medications for the ears', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Urological', 'description' => 'Medications for the urinary system', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Gynecological', 'description' => 'Medications for the female reproductive system', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Hematological', 'description' => 'Medications for the blood', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Antifungals', 'description' => 'Medications for fungal infections', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Antivirals', 'description' => 'Medications for viral infections', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Antiparasitics', 'description' => 'Medications for parasites', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Vaccines', 'description' => 'Vaccines and immunizations', 'created_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Supplements', 'description' => 'Vitamins and nutritional supplements', 'created_at' => now()],
        ];

        DB::table('medication_categories')->insert($categories);
    }
}
