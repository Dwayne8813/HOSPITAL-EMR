<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicationCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $medications = [
            // Analgésicos
            ['name' => 'Paracetamol', 'generic_name' => 'Acetaminofén', 'category' => 'Analgésicos', 'presentation' => 'Tableta', 'concentration' => '500 mg', 'laboratory' => 'Genfar', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ibuprofeno', 'generic_name' => 'Ibuprofeno', 'category' => 'Antiinflamatorios', 'presentation' => 'Tableta', 'concentration' => '400 mg', 'laboratory' => 'Bago', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dipirona', 'generic_name' => 'Metamizol', 'category' => 'Analgésicos', 'presentation' => 'Tableta', 'concentration' => '500 mg', 'laboratory' => 'Teuto', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Naproxeno', 'generic_name' => 'Naproxeno', 'category' => 'Antiinflamatorios', 'presentation' => 'Tableta', 'concentration' => '250 mg', 'laboratory' => 'Pfizer', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ketorolaco', 'generic_name' => 'Ketorolaco', 'category' => 'Analgésicos', 'presentation' => 'Inyectable', 'concentration' => '30 mg/ml', 'laboratory' => 'Roche', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tramadol', 'generic_name' => 'Tramadol', 'category' => 'Analgésicos', 'presentation' => 'Tableta', 'concentration' => '50 mg', 'laboratory' => 'Mylan', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Morfina', 'generic_name' => 'Sulfato de morfina', 'category' => 'Analgésicos', 'presentation' => 'Inyectable', 'concentration' => '10 mg/ml', 'laboratory' => 'Endo', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Antibióticos
            ['name' => 'Amoxicilina', 'generic_name' => 'Amoxicilina', 'category' => 'Antibióticos', 'presentation' => 'Cápsula', 'concentration' => '500 mg', 'laboratory' => 'Genfar', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Azitromicina', 'generic_name' => 'Azitromicina', 'category' => 'Antibióticos', 'presentation' => 'Tableta', 'concentration' => '500 mg', 'laboratory' => 'Pfizer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ciprofloxacino', 'generic_name' => 'Ciprofloxacino', 'category' => 'Antibióticos', 'presentation' => 'Tableta', 'concentration' => '500 mg', 'laboratory' => 'Bayer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ceftriaxona', 'generic_name' => 'Ceftriaxona', 'category' => 'Antibióticos', 'presentation' => 'Inyectable', 'concentration' => '1 g', 'laboratory' => 'Roche', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Metronidazol', 'generic_name' => 'Metronidazol', 'category' => 'Antibióticos', 'presentation' => 'Tableta', 'concentration' => '500 mg', 'laboratory' => 'Genfar', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Eritromicina', 'generic_name' => 'Eritromicina', 'category' => 'Antibióticos', 'presentation' => 'Tableta', 'concentration' => '500 mg', 'laboratory' => 'Abbott', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Amoxicilina + Clavulánico', 'generic_name' => 'Amoxicilina + Ácido Clavulánico', 'category' => 'Antibióticos', 'presentation' => 'Tableta', 'concentration' => '875/125 mg', 'laboratory' => 'GlaxoSmithKline', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sulfametoxazol + Trimetoprim', 'generic_name' => 'Sulfametoxazol + Trimetoprim', 'category' => 'Antibióticos', 'presentation' => 'Tableta', 'concentration' => '800/160 mg', 'laboratory' => 'Roche', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Cardiovasculares
            ['name' => 'Losartán', 'generic_name' => 'Losartán Potásico', 'category' => 'Cardiovasculares', 'presentation' => 'Tableta', 'concentration' => '50 mg', 'laboratory' => 'Merck', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Enalapril', 'generic_name' => 'Enalapril Maleato', 'category' => 'Cardiovasculares', 'presentation' => 'Tableta', 'concentration' => '10 mg', 'laboratory' => 'Abbott', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Amlodipino', 'generic_name' => 'Amlodipino Besilato', 'category' => 'Cardiovasculares', 'presentation' => 'Tableta', 'concentration' => '5 mg', 'laboratory' => 'Pfizer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Atenolol', 'generic_name' => 'Atenolol', 'category' => 'Cardiovasculares', 'presentation' => 'Tableta', 'concentration' => '50 mg', 'laboratory' => 'AstraZeneca', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Furosemida', 'generic_name' => 'Furosemida', 'category' => 'Cardiovasculares', 'presentation' => 'Tableta', 'concentration' => '40 mg', 'laboratory' => 'Sanofi', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hidroclorotiazida', 'generic_name' => 'Hidroclorotiazida', 'category' => 'Cardiovasculares', 'presentation' => 'Tableta', 'concentration' => '25 mg', 'laboratory' => 'Merck', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Digoxina', 'generic_name' => 'Digoxina', 'category' => 'Cardiovasculares', 'presentation' => 'Tableta', 'concentration' => '0.25 mg', 'laboratory' => 'GSK', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Varfarina', 'generic_name' => 'Varfarina Sódica', 'category' => 'Cardiovasculares', 'presentation' => 'Tableta', 'concentration' => '5 mg', 'laboratory' => 'Pfizer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Aspirina', 'generic_name' => 'Ácido Acetilsalicílico', 'category' => 'Cardiovasculares', 'presentation' => 'Tableta', 'concentration' => '100 mg', 'laboratory' => 'Bayer', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Clopidogrel', 'generic_name' => 'Clopidogrel', 'category' => 'Cardiovasculares', 'presentation' => 'Tableta', 'concentration' => '75 mg', 'laboratory' => 'Sanofi', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Digestivos
            ['name' => 'Omeprazol', 'generic_name' => 'Omeprazol', 'category' => 'Digestivos', 'presentation' => 'Cápsula', 'concentration' => '20 mg', 'laboratory' => 'AstraZeneca', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ranitidina', 'generic_name' => 'Ranitidina', 'category' => 'Digestivos', 'presentation' => 'Tableta', 'concentration' => '150 mg', 'laboratory' => 'GSK', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Domperidona', 'generic_name' => 'Domperidona', 'category' => 'Digestivos', 'presentation' => 'Tableta', 'concentration' => '10 mg', 'laboratory' => 'Janssen', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Metoclopramida', 'generic_name' => 'Metoclopramida', 'category' => 'Digestivos', 'presentation' => 'Tableta', 'concentration' => '10 mg', 'laboratory' => 'Abbott', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Buscapina', 'generic_name' => 'Hioscina Butilbromuro', 'category' => 'Digestivos', 'presentation' => 'Tableta', 'concentration' => '10 mg', 'laboratory' => 'Boehringer', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lactulosa', 'generic_name' => 'Lactulosa', 'category' => 'Digestivos', 'presentation' => 'Solución oral', 'concentration' => '667 mg/ml', 'laboratory' => 'Abbott', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Carbón Activado', 'generic_name' => 'Carbón Activado', 'category' => 'Digestivos', 'presentation' => 'Polvo', 'concentration' => '250 mg', 'laboratory' => 'Laboratorios', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Endocrinos
            ['name' => 'Metformina', 'generic_name' => 'Metformina Clorhidrato', 'category' => 'Endocrinos', 'presentation' => 'Tableta', 'concentration' => '850 mg', 'laboratory' => 'Merck', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Glibenclamida', 'generic_name' => 'Glibenclamida', 'category' => 'Endocrinos', 'presentation' => 'Tableta', 'concentration' => '5 mg', 'laboratory' => 'Merck', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Levotiroxina', 'generic_name' => 'Levotiroxina Sódica', 'category' => 'Endocrinos', 'presentation' => 'Tableta', 'concentration' => '50 mcg', 'laboratory' => 'Merck', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Insulina NPH', 'generic_name' => 'Insulina Humana NPH', 'category' => 'Endocrinos', 'presentation' => 'Inyectable', 'concentration' => '100 UI/ml', 'laboratory' => 'NovoNordisk', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Insulina Regular', 'generic_name' => 'Insulina Humana Regular', 'category' => 'Endocrinos', 'presentation' => 'Inyectable', 'concentration' => '100 UI/ml', 'laboratory' => 'NovoNordisk', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Neurológicos
            ['name' => 'Diazepam', 'generic_name' => 'Diazepam', 'category' => 'Neurológicos', 'presentation' => 'Tableta', 'concentration' => '5 mg', 'laboratory' => 'Roche', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fluoxetina', 'generic_name' => 'Fluoxetina Clorhidrato', 'category' => 'Neurológicos', 'presentation' => 'Cápsula', 'concentration' => '20 mg', 'laboratory' => 'Lilly', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Carbamazepina', 'generic_name' => 'Carbamazepina', 'category' => 'Neurológicos', 'presentation' => 'Tableta', 'concentration' => '200 mg', 'laboratory' => 'Novartis', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Haloperidol', 'generic_name' => 'Haloperidol', 'category' => 'Neurológicos', 'presentation' => 'Tableta', 'concentration' => '5 mg', 'laboratory' => 'Janssen', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Litio', 'generic_name' => 'Carbonato de Litio', 'category' => 'Neurológicos', 'presentation' => 'Tableta', 'concentration' => '300 mg', 'laboratory' => 'Abbott', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Respiratorios
            ['name' => 'Salbutamol', 'generic_name' => 'Salbutamol', 'category' => 'Respiratorios', 'presentation' => 'Inhalador', 'concentration' => '100 mcg', 'laboratory' => 'GSK', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ambroxol', 'generic_name' => 'Ambroxol Clorhidrato', 'category' => 'Respiratorios', 'presentation' => 'Jarabe', 'concentration' => '15 mg/5 ml', 'laboratory' => 'Abbott', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Acetilcisteína', 'generic_name' => 'Acetilcisteína', 'category' => 'Respiratorios', 'presentation' => 'Polvo', 'concentration' => '600 mg', 'laboratory' => 'Zambon', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dexametasona', 'generic_name' => 'Dexametasona', 'category' => 'Respiratorios', 'presentation' => 'Inyectable', 'concentration' => '4 mg/ml', 'laboratory' => 'Pfizer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Prednisona', 'generic_name' => 'Prednisona', 'category' => 'Respiratorios', 'presentation' => 'Tableta', 'concentration' => '5 mg', 'laboratory' => 'Genfar', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Dermatológicos
            ['name' => 'Betametasona', 'generic_name' => 'Betametasona', 'category' => 'Dermatológicos', 'presentation' => 'Crema', 'concentration' => '0.05%', 'laboratory' => 'Schering', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Clotrimazol', 'generic_name' => 'Clotrimazol', 'category' => 'Antifúngicos', 'presentation' => 'Crema', 'concentration' => '1%', 'laboratory' => 'Bayer', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Miconazol', 'generic_name' => 'Miconazol', 'category' => 'Antifúngicos', 'presentation' => 'Crema', 'concentration' => '2%', 'laboratory' => 'Janssen', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Urológicos
            ['name' => 'Tamsulosina', 'generic_name' => 'Tamsulosina', 'category' => 'Urológicos', 'presentation' => 'Cápsula', 'concentration' => '0.4 mg', 'laboratory' => 'Astellas', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sildenafilo', 'generic_name' => 'Sildenafilo Citrato', 'category' => 'Urológicos', 'presentation' => 'Tableta', 'concentration' => '50 mg', 'laboratory' => 'Pfizer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Ginecológicos
            ['name' => 'Metformina Gestacional', 'generic_name' => 'Metformina', 'category' => 'Ginecológicos', 'presentation' => 'Tableta', 'concentration' => '500 mg', 'laboratory' => 'Merck', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Suplementos
            ['name' => 'Ácido Fólico', 'generic_name' => 'Ácido Fólico', 'category' => 'Suplementos', 'presentation' => 'Tableta', 'concentration' => '5 mg', 'laboratory' => 'Genfar', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sulfato Ferroso', 'generic_name' => 'Sulfato Ferroso', 'category' => 'Suplementos', 'presentation' => 'Tableta', 'concentration' => '200 mg', 'laboratory' => 'Abbott', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vitamina C', 'generic_name' => 'Ácido Ascórbico', 'category' => 'Suplementos', 'presentation' => 'Tableta', 'concentration' => '500 mg', 'laboratory' => 'Genfar', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vitamina D', 'generic_name' => 'Colecalciferol', 'category' => 'Suplementos', 'presentation' => 'Tableta', 'concentration' => '400 UI', 'laboratory' => 'Abbott', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Calcio + Vitamina D', 'generic_name' => 'Carbonato de Calcio + Colecalciferol', 'category' => 'Suplementos', 'presentation' => 'Tableta', 'concentration' => '600/400', 'laboratory' => 'Abbott', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Antiparasitarios
            ['name' => 'Mebendazol', 'generic_name' => 'Mebendazol', 'category' => 'Antiparasitarios', 'presentation' => 'Tableta', 'concentration' => '100 mg', 'laboratory' => 'Janssen', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Albendazol', 'generic_name' => 'Albendazol', 'category' => 'Antiparasitarios', 'presentation' => 'Tableta', 'concentration' => '400 mg', 'laboratory' => 'GSK', 'requires_prescription' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ivermectina', 'generic_name' => 'Ivermectina', 'category' => 'Antiparasitarios', 'presentation' => 'Tableta', 'concentration' => '6 mg', 'laboratory' => 'Merck', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Antivirales
            ['name' => 'Aciclovir', 'generic_name' => 'Aciclovir', 'category' => 'Antivirales', 'presentation' => 'Tableta', 'concentration' => '400 mg', 'laboratory' => 'GSK', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Hematológicos
            ['name' => 'Ácido Tranexámico', 'generic_name' => 'Ácido Tranexámico', 'category' => 'Hematológicos', 'presentation' => 'Inyectable', 'concentration' => '500 mg', 'laboratory' => 'Pfizer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vitamina K', 'generic_name' => 'Fitomenadiona', 'category' => 'Hematológicos', 'presentation' => 'Inyectable', 'concentration' => '10 mg/ml', 'laboratory' => 'Roche', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Oftalmológicos
            ['name' => 'Timolol', 'generic_name' => 'Timolol Maleato', 'category' => 'Oftalmológicos', 'presentation' => 'Colirio', 'concentration' => '0.5%', 'laboratory' => 'Merck', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tobramicina', 'generic_name' => 'Tobramicina', 'category' => 'Oftalmológicos', 'presentation' => 'Colirio', 'concentration' => '0.3%', 'laboratory' => 'Alcon', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Otros
            ['name' => 'Heparina', 'generic_name' => 'Heparina Sódica', 'category' => 'Cardiovasculares', 'presentation' => 'Inyectable', 'concentration' => '5000 UI/ml', 'laboratory' => 'Pfizer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Epinefrina', 'generic_name' => 'Adrenalina', 'category' => 'Cardiovasculares', 'presentation' => 'Inyectable', 'concentration' => '1 mg/ml', 'laboratory' => 'Pfizer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Atropina', 'generic_name' => 'Sulfato de Atropina', 'category' => 'Cardiovasculares', 'presentation' => 'Inyectable', 'concentration' => '0.5 mg/ml', 'laboratory' => 'Pfizer', 'requires_prescription' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('medication_catalog')->insert($medications);
    }
}
