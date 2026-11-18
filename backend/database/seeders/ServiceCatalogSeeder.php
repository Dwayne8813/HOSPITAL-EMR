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
            // Consultations
            ['institution_id' => $institutionId, 'name' => 'General Consultation', 'category' => 'Consultations', 'code' => 'CON-001', 'description' => 'General medical consultation', 'price' => 150.00, 'insurance_price' => 100.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Specialist Consultation', 'category' => 'Consultations', 'code' => 'CON-002', 'description' => 'Consultation with a specialist doctor', 'price' => 250.00, 'insurance_price' => 180.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Follow-up Consultation', 'category' => 'Consultations', 'code' => 'CON-003', 'description' => 'Follow-up consultation', 'price' => 100.00, 'insurance_price' => 70.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Pediatric Consultation', 'category' => 'Consultations', 'code' => 'CON-004', 'description' => 'Medical consultation for children', 'price' => 180.00, 'insurance_price' => 120.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Gynecological Consultation', 'category' => 'Consultations', 'code' => 'CON-005', 'description' => 'Gynecological consultation', 'price' => 200.00, 'insurance_price' => 150.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Emergency
            ['institution_id' => $institutionId, 'name' => 'General Emergency', 'category' => 'Emergency', 'code' => 'EMR-001', 'description' => 'General emergency care', 'price' => 200.00, 'insurance_price' => 150.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Triage', 'category' => 'Emergency', 'code' => 'EMR-002', 'description' => 'Patient triage', 'price' => 50.00, 'insurance_price' => 30.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Urgent Care', 'category' => 'Emergency', 'code' => 'EMR-003', 'description' => 'Urgent care', 'price' => 180.00, 'insurance_price' => 120.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Laboratory
            ['institution_id' => $institutionId, 'name' => 'Complete Blood Count', 'category' => 'Laboratory', 'code' => 'LAB-001', 'description' => 'Complete blood analysis', 'price' => 50.00, 'insurance_price' => 35.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Blood Glucose', 'category' => 'Laboratory', 'code' => 'LAB-002', 'description' => 'Blood glucose measurement', 'price' => 25.00, 'insurance_price' => 15.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Total Cholesterol', 'category' => 'Laboratory', 'code' => 'LAB-003', 'description' => 'Cholesterol measurement', 'price' => 40.00, 'insurance_price' => 25.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Triglycerides', 'category' => 'Laboratory', 'code' => 'LAB-004', 'description' => 'Triglyceride measurement', 'price' => 40.00, 'insurance_price' => 25.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Creatinine', 'category' => 'Laboratory', 'code' => 'LAB-005', 'description' => 'Creatinine measurement', 'price' => 35.00, 'insurance_price' => 20.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Urinalysis', 'category' => 'Laboratory', 'code' => 'LAB-006', 'description' => 'Complete urinalysis', 'price' => 30.00, 'insurance_price' => 20.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Complete Blood Count with ESR', 'category' => 'Laboratory', 'code' => 'LAB-007', 'description' => 'Complete blood count with ESR', 'price' => 65.00, 'insurance_price' => 45.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Pregnancy Test', 'category' => 'Laboratory', 'code' => 'LAB-008', 'description' => 'Beta-HCG measurement', 'price' => 45.00, 'insurance_price' => 30.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'HbA1c', 'category' => 'Laboratory', 'code' => 'LAB-009', 'description' => 'Glycated hemoglobin', 'price' => 80.00, 'insurance_price' => 55.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Lipid Profile', 'category' => 'Laboratory', 'code' => 'LAB-010', 'description' => 'Complete lipid profile', 'price' => 90.00, 'insurance_price' => 60.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Liver Function Panel', 'category' => 'Laboratory', 'code' => 'LAB-011', 'description' => 'Complete liver function panel', 'price' => 120.00, 'insurance_price' => 80.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Stool Test', 'category' => 'Laboratory', 'code' => 'LAB-012', 'description' => 'Stool culture and parasite exam', 'price' => 35.00, 'insurance_price' => 25.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Arterial Blood Gas', 'category' => 'Laboratory', 'code' => 'LAB-013', 'description' => 'Blood gas analysis', 'price' => 75.00, 'insurance_price' => 50.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Imaging
            ['institution_id' => $institutionId, 'name' => 'Chest X-Ray', 'category' => 'Imaging', 'code' => 'IMG-001', 'description' => 'Chest X-ray', 'price' => 120.00, 'insurance_price' => 80.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Abdominal X-Ray', 'category' => 'Imaging', 'code' => 'IMG-002', 'description' => 'Abdominal X-ray', 'price' => 120.00, 'insurance_price' => 80.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Abdominal Ultrasound', 'category' => 'Imaging', 'code' => 'IMG-003', 'description' => 'Complete abdominal ultrasound', 'price' => 200.00, 'insurance_price' => 140.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Obstetric Ultrasound', 'category' => 'Imaging', 'code' => 'IMG-004', 'description' => 'Prenatal ultrasound', 'price' => 200.00, 'insurance_price' => 140.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Ecocardiograma', 'category' => 'Imaging', 'code' => 'IMG-005', 'description' => 'Transthoracic echocardiogram', 'price' => 350.00, 'insurance_price' => 250.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'CT Scan', 'category' => 'Imaging', 'code' => 'IMG-006', 'description' => 'Computed tomography', 'price' => 500.00, 'insurance_price' => 350.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'MRI', 'category' => 'Imaging', 'code' => 'IMG-007', 'description' => 'Magnetic resonance imaging', 'price' => 800.00, 'insurance_price' => 550.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Mammogram', 'category' => 'Imaging', 'code' => 'IMG-008', 'description' => 'Mammography study', 'price' => 250.00, 'insurance_price' => 175.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Hospitalization
            ['institution_id' => $institutionId, 'name' => 'Private Room', 'category' => 'Hospitalization', 'code' => 'HOS-001', 'description' => 'Private room per day', 'price' => 350.00, 'insurance_price' => 250.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Shared Room', 'category' => 'Hospitalization', 'code' => 'HOS-002', 'description' => 'Bed in a shared room per day', 'price' => 200.00, 'insurance_price' => 140.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'ICU', 'category' => 'Hospitalization', 'code' => 'HOS-003', 'description' => 'Intensive Care Unit per day', 'price' => 800.00, 'insurance_price' => 550.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Recovery Room', 'category' => 'Hospitalization', 'code' => 'HOS-004', 'description' => 'Post-operative recovery room', 'price' => 250.00, 'insurance_price' => 175.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Procedures
            ['institution_id' => $institutionId, 'name' => 'Nebulization', 'category' => 'Procedures', 'code' => 'PRO-001', 'description' => 'Bronchial nebulization', 'price' => 50.00, 'insurance_price' => 35.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'IV Therapy', 'category' => 'Procedures', 'code' => 'PRO-002', 'description' => 'IV line placement', 'price' => 40.00, 'insurance_price' => 25.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Suture', 'category' => 'Procedures', 'code' => 'PRO-003', 'description' => 'Wound suturing', 'price' => 80.00, 'insurance_price' => 55.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Wound Dressing', 'category' => 'Procedures', 'code' => 'PRO-004', 'description' => 'Wound care and dressing', 'price' => 60.00, 'insurance_price' => 40.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Electrocardiogram', 'category' => 'Procedures', 'code' => 'PRO-005', 'description' => '12-lead ECG', 'price' => 80.00, 'insurance_price' => 55.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Endoscopy', 'category' => 'Procedures', 'code' => 'PRO-006', 'description' => 'Upper GI endoscopy', 'price' => 400.00, 'insurance_price' => 280.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Colonoscopy', 'category' => 'Procedures', 'code' => 'PRO-007', 'description' => 'Diagnostic colonoscopy', 'price' => 450.00, 'insurance_price' => 315.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Surgery
            ['institution_id' => $institutionId, 'name' => 'Appendectomy', 'category' => 'Surgery', 'code' => 'CIR-001', 'description' => 'Appendectomy surgery', 'price' => 2500.00, 'insurance_price' => 1750.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Cholecystectomy', 'category' => 'Surgery', 'code' => 'CIR-002', 'description' => 'Gallbladder surgery', 'price' => 3500.00, 'insurance_price' => 2450.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Cesarean Section', 'category' => 'Surgery', 'code' => 'CIR-003', 'description' => 'Cesarean delivery', 'price' => 3000.00, 'insurance_price' => 2100.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Consultations Especializadas
            ['institution_id' => $institutionId, 'name' => 'Cardiology Consultation', 'category' => 'Consultations', 'code' => 'CON-006', 'description' => 'Consultation with a cardiologist', 'price' => 300.00, 'insurance_price' => 210.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Neurology Consultation', 'category' => 'Consultations', 'code' => 'CON-007', 'description' => 'Consultation with a neurologist', 'price' => 300.00, 'insurance_price' => 210.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Dermatology Consultation', 'category' => 'Consultations', 'code' => 'CON-008', 'description' => 'Consultation with a dermatologist', 'price' => 250.00, 'insurance_price' => 175.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Ophthalmology Consultation', 'category' => 'Consultations', 'code' => 'CON-009', 'description' => 'Consultation with an ophthalmologist', 'price' => 250.00, 'insurance_price' => 175.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'ENT Consultation', 'category' => 'Consultations', 'code' => 'CON-010', 'description' => 'Consultation with an ENT specialist', 'price' => 250.00, 'insurance_price' => 175.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Urology Consultation', 'category' => 'Consultations', 'code' => 'CON-011', 'description' => 'Consultation with a urologist', 'price' => 250.00, 'insurance_price' => 175.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['institution_id' => $institutionId, 'name' => 'Gastroenterology Consultation', 'category' => 'Consultations', 'code' => 'CON-012', 'description' => 'Consultation with a gastroenterologist', 'price' => 280.00, 'insurance_price' => 195.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('service_catalog')->insert($services);
    }
}
