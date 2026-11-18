<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicationCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $medications = [
            // Analgesics
            ['name' => 'Acetaminophen', 'generic_name' => 'Acetaminophen', 'category' => 'Analgesics', 'presentation' => 'Tablet', 'concentration' => '500 mg', 'laboratory' => 'Genfar', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ibuprofen', 'generic_name' => 'Ibuprofen', 'category' => 'Anti-inflammatories', 'presentation' => 'Tablet', 'concentration' => '400 mg', 'laboratory' => 'Bago', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Metamizole', 'generic_name' => 'Metamizole', 'category' => 'Analgesics', 'presentation' => 'Tablet', 'concentration' => '500 mg', 'laboratory' => 'Teuto', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Naproxen', 'generic_name' => 'Naproxen', 'category' => 'Anti-inflammatories', 'presentation' => 'Tablet', 'concentration' => '250 mg', 'laboratory' => 'Pfizer', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ketorolac', 'generic_name' => 'Ketorolac', 'category' => 'Analgesics', 'presentation' => 'Injection', 'concentration' => '30 mg/ml', 'laboratory' => 'Roche', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tramadol', 'generic_name' => 'Tramadol', 'category' => 'Analgesics', 'presentation' => 'Tablet', 'concentration' => '50 mg', 'laboratory' => 'Mylan', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Morphine', 'generic_name' => 'Morphine sulfate', 'category' => 'Analgesics', 'presentation' => 'Injection', 'concentration' => '10 mg/ml', 'laboratory' => 'Endo', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Antibiotics
            ['name' => 'Amoxicillin', 'generic_name' => 'Amoxicillin', 'category' => 'Antibiotics', 'presentation' => 'Capsule', 'concentration' => '500 mg', 'laboratory' => 'Genfar', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Azithromycin', 'generic_name' => 'Azithromycin', 'category' => 'Antibiotics', 'presentation' => 'Tablet', 'concentration' => '500 mg', 'laboratory' => 'Pfizer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ciprofloxacin', 'generic_name' => 'Ciprofloxacin', 'category' => 'Antibiotics', 'presentation' => 'Tablet', 'concentration' => '500 mg', 'laboratory' => 'Bayer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ceftriaxone', 'generic_name' => 'Ceftriaxone', 'category' => 'Antibiotics', 'presentation' => 'Injection', 'concentration' => '1 g', 'laboratory' => 'Roche', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Metronidazole', 'generic_name' => 'Metronidazole', 'category' => 'Antibiotics', 'presentation' => 'Tablet', 'concentration' => '500 mg', 'laboratory' => 'Genfar', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Erythromycin', 'generic_name' => 'Erythromycin', 'category' => 'Antibiotics', 'presentation' => 'Tablet', 'concentration' => '500 mg', 'laboratory' => 'Abbott', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Amoxicillin + Clavulanate', 'generic_name' => 'Amoxicillin + Clavulanic acid', 'category' => 'Antibiotics', 'presentation' => 'Tablet', 'concentration' => '875/125 mg', 'laboratory' => 'GlaxoSmithKline', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sulfamethoxazole + Trimethoprim', 'generic_name' => 'Sulfamethoxazole + Trimethoprim', 'category' => 'Antibiotics', 'presentation' => 'Tablet', 'concentration' => '800/160 mg', 'laboratory' => 'Roche', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Cardiovascular
            ['name' => 'Losartan', 'generic_name' => 'Losartan potassium', 'category' => 'Cardiovascular', 'presentation' => 'Tablet', 'concentration' => '50 mg', 'laboratory' => 'Merck', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Enalapril', 'generic_name' => 'Enalapril maleate', 'category' => 'Cardiovascular', 'presentation' => 'Tablet', 'concentration' => '10 mg', 'laboratory' => 'Abbott', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Amlodipine', 'generic_name' => 'Amlodipine besylate', 'category' => 'Cardiovascular', 'presentation' => 'Tablet', 'concentration' => '5 mg', 'laboratory' => 'Pfizer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Atenolol', 'generic_name' => 'Atenolol', 'category' => 'Cardiovascular', 'presentation' => 'Tablet', 'concentration' => '50 mg', 'laboratory' => 'AstraZeneca', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Furosemide', 'generic_name' => 'Furosemide', 'category' => 'Cardiovascular', 'presentation' => 'Tablet', 'concentration' => '40 mg', 'laboratory' => 'Sanofi', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hydrochlorothiazide', 'generic_name' => 'Hydrochlorothiazide', 'category' => 'Cardiovascular', 'presentation' => 'Tablet', 'concentration' => '25 mg', 'laboratory' => 'Merck', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Digoxin', 'generic_name' => 'Digoxin', 'category' => 'Cardiovascular', 'presentation' => 'Tablet', 'concentration' => '0.25 mg', 'laboratory' => 'GSK', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Warfarin', 'generic_name' => 'Warfarin sodium', 'category' => 'Cardiovascular', 'presentation' => 'Tablet', 'concentration' => '5 mg', 'laboratory' => 'Pfizer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Aspirin', 'generic_name' => 'Acetylsalicylic acid', 'category' => 'Cardiovascular', 'presentation' => 'Tablet', 'concentration' => '100 mg', 'laboratory' => 'Bayer', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Clopidogrel', 'generic_name' => 'Clopidogrel', 'category' => 'Cardiovascular', 'presentation' => 'Tablet', 'concentration' => '75 mg', 'laboratory' => 'Sanofi', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Digestive
            ['name' => 'Omeprazole', 'generic_name' => 'Omeprazole', 'category' => 'Digestive', 'presentation' => 'Capsule', 'concentration' => '20 mg', 'laboratory' => 'AstraZeneca', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ranitidine', 'generic_name' => 'Ranitidine', 'category' => 'Digestive', 'presentation' => 'Tablet', 'concentration' => '150 mg', 'laboratory' => 'GSK', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Domperidone', 'generic_name' => 'Domperidone', 'category' => 'Digestive', 'presentation' => 'Tablet', 'concentration' => '10 mg', 'laboratory' => 'Janssen', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Metoclopramide', 'generic_name' => 'Metoclopramide', 'category' => 'Digestive', 'presentation' => 'Tablet', 'concentration' => '10 mg', 'laboratory' => 'Abbott', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Buscopan', 'generic_name' => 'Hyoscine butylbromide', 'category' => 'Digestive', 'presentation' => 'Tablet', 'concentration' => '10 mg', 'laboratory' => 'Boehringer', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lactulose', 'generic_name' => 'Lactulose', 'category' => 'Digestive', 'presentation' => 'Oral solution', 'concentration' => '667 mg/ml', 'laboratory' => 'Abbott', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Activated charcoal', 'generic_name' => 'Activated charcoal', 'category' => 'Digestive', 'presentation' => 'Powder', 'concentration' => '250 mg', 'laboratory' => 'Laboratorys', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Endocrine
            ['name' => 'Metformin', 'generic_name' => 'Metformin hydrochloride', 'category' => 'Endocrine', 'presentation' => 'Tablet', 'concentration' => '850 mg', 'laboratory' => 'Merck', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Glibenclamide', 'generic_name' => 'Glibenclamide', 'category' => 'Endocrine', 'presentation' => 'Tablet', 'concentration' => '5 mg', 'laboratory' => 'Merck', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Levothyroxine', 'generic_name' => 'Levothyroxine sodium', 'category' => 'Endocrine', 'presentation' => 'Tablet', 'concentration' => '50 mcg', 'laboratory' => 'Merck', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'NPH insulin', 'generic_name' => 'Human NPH insulin', 'category' => 'Endocrine', 'presentation' => 'Injection', 'concentration' => '100 UI/ml', 'laboratory' => 'NovoNordisk', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Regular insulin', 'generic_name' => 'Human regular insulin', 'category' => 'Endocrine', 'presentation' => 'Injection', 'concentration' => '100 UI/ml', 'laboratory' => 'NovoNordisk', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Neurological
            ['name' => 'Diazepam', 'generic_name' => 'Diazepam', 'category' => 'Neurological', 'presentation' => 'Tablet', 'concentration' => '5 mg', 'laboratory' => 'Roche', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fluoxetine', 'generic_name' => 'Fluoxetine hydrochloride', 'category' => 'Neurological', 'presentation' => 'Capsule', 'concentration' => '20 mg', 'laboratory' => 'Lilly', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Carbamazepine', 'generic_name' => 'Carbamazepine', 'category' => 'Neurological', 'presentation' => 'Tablet', 'concentration' => '200 mg', 'laboratory' => 'Novartis', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Haloperidol', 'generic_name' => 'Haloperidol', 'category' => 'Neurological', 'presentation' => 'Tablet', 'concentration' => '5 mg', 'laboratory' => 'Janssen', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lithium', 'generic_name' => 'Lithium carbonate', 'category' => 'Neurological', 'presentation' => 'Tablet', 'concentration' => '300 mg', 'laboratory' => 'Abbott', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Respiratory
            ['name' => 'Salbutamol', 'generic_name' => 'Salbutamol', 'category' => 'Respiratory', 'presentation' => 'Inhaler', 'concentration' => '100 mcg', 'laboratory' => 'GSK', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ambroxol', 'generic_name' => 'Ambroxol hydrochloride', 'category' => 'Respiratory', 'presentation' => 'Syrup', 'concentration' => '15 mg/5 ml', 'laboratory' => 'Abbott', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Acetylcysteine', 'generic_name' => 'Acetylcysteine', 'category' => 'Respiratory', 'presentation' => 'Powder', 'concentration' => '600 mg', 'laboratory' => 'Zambon', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dexamethasone', 'generic_name' => 'Dexamethasone', 'category' => 'Respiratory', 'presentation' => 'Injection', 'concentration' => '4 mg/ml', 'laboratory' => 'Pfizer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Prednisone', 'generic_name' => 'Prednisone', 'category' => 'Respiratory', 'presentation' => 'Tablet', 'concentration' => '5 mg', 'laboratory' => 'Genfar', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Dermatological
            ['name' => 'Betamethasone', 'generic_name' => 'Betamethasone', 'category' => 'Dermatological', 'presentation' => 'Cream', 'concentration' => '0.05%', 'laboratory' => 'Schering', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Clotrimazole', 'generic_name' => 'Clotrimazole', 'category' => 'Antifungals', 'presentation' => 'Cream', 'concentration' => '1%', 'laboratory' => 'Bayer', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Miconazole', 'generic_name' => 'Miconazole', 'category' => 'Antifungals', 'presentation' => 'Cream', 'concentration' => '2%', 'laboratory' => 'Janssen', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Urological
            ['name' => 'Tamsulosin', 'generic_name' => 'Tamsulosin', 'category' => 'Urological', 'presentation' => 'Capsule', 'concentration' => '0.4 mg', 'laboratory' => 'Astellas', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sildenafil', 'generic_name' => 'Sildenafil citrate', 'category' => 'Urological', 'presentation' => 'Tablet', 'concentration' => '50 mg', 'laboratory' => 'Pfizer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Gynecological
            ['name' => 'Gestational metformin', 'generic_name' => 'Metformin', 'category' => 'Gynecological', 'presentation' => 'Tablet', 'concentration' => '500 mg', 'laboratory' => 'Merck', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Supplements
            ['name' => 'Folic acid', 'generic_name' => 'Folic acid', 'category' => 'Supplements', 'presentation' => 'Tablet', 'concentration' => '5 mg', 'laboratory' => 'Genfar', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ferrous sulfate', 'generic_name' => 'Ferrous sulfate', 'category' => 'Supplements', 'presentation' => 'Tablet', 'concentration' => '200 mg', 'laboratory' => 'Abbott', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vitamin C', 'generic_name' => 'Ascorbic acid', 'category' => 'Supplements', 'presentation' => 'Tablet', 'concentration' => '500 mg', 'laboratory' => 'Genfar', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vitamin D', 'generic_name' => 'Colecalciferol', 'category' => 'Supplements', 'presentation' => 'Tablet', 'concentration' => '400 UI', 'laboratory' => 'Abbott', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Calcium + Vitamin D', 'generic_name' => 'Calcium carbonate + colecalciferol', 'category' => 'Supplements', 'presentation' => 'Tablet', 'concentration' => '600/400', 'laboratory' => 'Abbott', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Antiparasitics
            ['name' => 'Mebendazole', 'generic_name' => 'Mebendazole', 'category' => 'Antiparasitics', 'presentation' => 'Tablet', 'concentration' => '100 mg', 'laboratory' => 'Janssen', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Albendazole', 'generic_name' => 'Albendazole', 'category' => 'Antiparasitics', 'presentation' => 'Tablet', 'concentration' => '400 mg', 'laboratory' => 'GSK', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ivermectin', 'generic_name' => 'Ivermectin', 'category' => 'Antiparasitics', 'presentation' => 'Tablet', 'concentration' => '6 mg', 'laboratory' => 'Merck', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Antivirals
            ['name' => 'Acyclovir', 'generic_name' => 'Acyclovir', 'category' => 'Antivirals', 'presentation' => 'Tablet', 'concentration' => '400 mg', 'laboratory' => 'GSK', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Hematological
            ['name' => 'Tranexamic acid', 'generic_name' => 'Tranexamic acid', 'category' => 'Hematological', 'presentation' => 'Injection', 'concentration' => '500 mg', 'laboratory' => 'Pfizer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vitamin K', 'generic_name' => 'Phytomenadione', 'category' => 'Hematological', 'presentation' => 'Injection', 'concentration' => '10 mg/ml', 'laboratory' => 'Roche', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Ophthalmological
            ['name' => 'Timolol', 'generic_name' => 'Timolol maleate', 'category' => 'Ophthalmological', 'presentation' => 'Eye drops', 'concentration' => '0.5%', 'laboratory' => 'Merck', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tobramycin', 'generic_name' => 'Tobramycin', 'category' => 'Ophthalmological', 'presentation' => 'Eye drops', 'concentration' => '0.3%', 'laboratory' => 'Alcon', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Other
            ['name' => 'Heparin', 'generic_name' => 'Heparin sodium', 'category' => 'Cardiovascular', 'presentation' => 'Injection', 'concentration' => '5000 UI/ml', 'laboratory' => 'Pfizer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Epinephrine', 'generic_name' => 'Epinephrine', 'category' => 'Cardiovascular', 'presentation' => 'Injection', 'concentration' => '1 mg/ml', 'laboratory' => 'Pfizer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Atropine', 'generic_name' => 'Atropine sulfate', 'category' => 'Cardiovascular', 'presentation' => 'Injection', 'concentration' => '0.5 mg/ml', 'laboratory' => 'Pfizer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('medication_catalog')->insert($medications);
    }
}
