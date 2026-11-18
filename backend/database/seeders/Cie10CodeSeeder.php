<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Cie10CodeSeeder extends Seeder
{
    public function run(): void
    {
        $categories = DB::table('cie10_categories')->pluck('id', 'code')->toArray();

        $codes = [
            // A00-B99: Infectious diseases
            ['category_id' => $categories['A00-B99'], 'code' => 'A09', 'name' => 'Infectious gastroenteritis and colitis', 'description' => 'Infectious diarrhea', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['A00-B99'], 'code' => 'B34.9', 'name' => 'Viral infection of unspecified site', 'description' => 'General viral infection', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['A00-B99'], 'code' => 'J06.9', 'name' => 'Influenza, unspecified', 'description' => 'Acute respiratory infection', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['A00-B99'], 'code' => 'L30.9', 'name' => 'Dermatitis, unspecified', 'description' => 'Skin infection', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['A00-B99'], 'code' => 'N39.0', 'name' => 'Urinary tract infection', 'description' => 'UTI', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // C00-D49: Neoplasms
            ['category_id' => $categories['C00-D49'], 'code' => 'C34.9', 'name' => 'Malignant neoplasm of bronchus and lung', 'description' => 'Lung cancer', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['C00-D49'], 'code' => 'C50.9', 'name' => 'Malignant neoplasm of breast', 'description' => 'Breast cancer', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['C00-D49'], 'code' => 'C53.9', 'name' => 'Malignant neoplasm of uterine cervix', 'description' => 'Cervical cancer', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['C00-D49'], 'code' => 'D48.9', 'name' => 'Neoplasm of uncertain behavior', 'description' => 'Tumor of uncertain behavior', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // D50-D89: Diseases of the blood
            ['category_id' => $categories['D50-D89'], 'code' => 'D50.9', 'name' => 'Iron deficiency anemia', 'description' => 'Iron deficiency anemia', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['D50-D89'], 'code' => 'D64.9', 'name' => 'Anemia, unspecified', 'description' => 'General anemia', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['D50-D89'], 'code' => 'D69.6', 'name' => 'Thrombocytopenia', 'description' => 'Low platelets', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // E00-E89: Endocrine diseases
            ['category_id' => $categories['E00-E89'], 'code' => 'E11.9', 'name' => 'Type 2 diabetes mellitus', 'description' => 'Type 2 diabetes', 'gender' => null, 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['E00-E89'], 'code' => 'E10.9', 'name' => 'Type 1 diabetes mellitus', 'description' => 'Type 1 diabetes', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['E00-E89'], 'code' => 'E78.5', 'name' => 'Hyperlipidemia', 'description' => 'High cholesterol and triglycerides', 'gender' => null, 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['E00-E89'], 'code' => 'E03.9', 'name' => 'Hypothyroidism, unspecified', 'description' => 'Low thyroid', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['E00-E89'], 'code' => 'E05.9', 'name' => 'Hyperthyroidism, unspecified', 'description' => 'High thyroid', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['E00-E89'], 'code' => 'E11.65', 'name' => 'Type 2 diabetes mellitus with hyperglycemia', 'description' => 'Uncontrolled diabetes', 'gender' => null, 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['E00-E89'], 'code' => 'E11.2', 'name' => 'Type 2 diabetes mellitus with renal complications', 'description' => 'Diabetes with nephropathy', 'gender' => null, 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['E00-E89'], 'code' => 'E11.4', 'name' => 'Type 2 diabetes mellitus with neurological complications', 'description' => 'Diabetes with neuropathy', 'gender' => null, 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],

            // F01-F99: Mental disorders
            ['category_id' => $categories['F01-F99'], 'code' => 'F32.9', 'name' => 'Major depressive episode', 'description' => 'Depression', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['F01-F99'], 'code' => 'F41.1', 'name' => 'Generalized anxiety disorder', 'description' => 'Anxiety', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['F01-F99'], 'code' => 'F41.0', 'name' => 'Panic attack', 'description' => 'Panic attack', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['F01-F99'], 'code' => 'F10.2', 'name' => 'Alcohol use disorder', 'description' => 'Alcoholism', 'gender' => 'M', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['F01-F99'], 'code' => 'F43.1', 'name' => 'Post-traumatic stress disorder', 'description' => 'PTSD', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['F01-F99'], 'code' => 'F50.0', 'name' => 'Anorexia nervosa', 'description' => 'Eating disorder', 'gender' => 'F', 'age_range' => 'Adolescents', 'is_active' => true, 'created_at' => now()],

            // G00-G99: Diseases of the nervous system
            ['category_id' => $categories['G00-G99'], 'code' => 'G43.9', 'name' => 'Migraine, unspecified', 'description' => 'Severe headache', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['G00-G99'], 'code' => 'G40.9', 'name' => 'Epilepsy, unspecified', 'description' => 'Seizures', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['G00-G99'], 'code' => 'G56.0', 'name' => 'Carpal tunnel syndrome', 'description' => 'Compression neuropathy', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['G00-G99'], 'code' => 'G47.0', 'name' => 'Insomnia disorders', 'description' => 'Insomnia', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['G00-G99'], 'code' => 'G54.0', 'name' => 'Brachial plexus palsy', 'description' => 'Shoulder pain', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // H00-H59: Diseases of the eye
            ['category_id' => $categories['H00-H59'], 'code' => 'H40.1', 'name' => 'Open-angle glaucoma', 'description' => 'High intraocular pressure', 'gender' => null, 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['H00-H59'], 'code' => 'H25.9', 'name' => 'Senile cataract', 'description' => 'Cataract', 'gender' => null, 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['H00-H59'], 'code' => 'H10.9', 'name' => 'Conjunctivitis, unspecified', 'description' => 'Red eye', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // H60-H95: Diseases of the ear
            ['category_id' => $categories['H60-H95'], 'code' => 'H66.9', 'name' => 'Ear pain, unspecified', 'description' => 'Ear pain', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['H60-H95'], 'code' => 'H90.3', 'name' => 'Sensorineural hearing loss', 'description' => 'Hearing loss', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // I00-I99: Diseases of the circulatory system
            ['category_id' => $categories['I00-I99'], 'code' => 'I10', 'name' => 'Essential (primary) hypertension', 'description' => 'Arterial hypertension', 'gender' => null, 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['I00-I99'], 'code' => 'I11.9', 'name' => 'Hypertensive heart disease', 'description' => 'Hypertension with heart involvement', 'gender' => null, 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['I00-I99'], 'code' => 'I21.9', 'name' => 'Acute myocardial infarction', 'description' => 'Heart attack', 'gender' => 'M', 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['I00-I99'], 'code' => 'I50.9', 'name' => 'Heart failure', 'description' => 'Heart failure', 'gender' => null, 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['I00-I99'], 'code' => 'I63.9', 'name' => 'Cerebral infarction', 'description' => 'Stroke', 'gender' => null, 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['I00-I99'], 'code' => 'I48', 'name' => 'Atrial fibrillation', 'description' => 'Cardiac arrhythmia', 'gender' => null, 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['I00-I99'], 'code' => 'I73.9', 'name' => 'Peripheral vascular disease', 'description' => 'Venous insufficiency', 'gender' => null, 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['I00-I99'], 'code' => 'I25.1', 'name' => 'Coronary artery disease', 'description' => 'Ischemic heart disease', 'gender' => null, 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],

            // J00-J99: Diseases of the respiratory system
            ['category_id' => $categories['J00-J99'], 'code' => 'J00', 'name' => 'Acute nasopharyngitis', 'description' => 'Common cold', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['J00-J99'], 'code' => 'J06.9', 'name' => 'Acute respiratory tract infection', 'description' => 'ARI', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['J00-J99'], 'code' => 'J18.9', 'name' => 'Pneumonia, unspecified', 'description' => 'Pneumonia', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['J00-J99'], 'code' => 'J20.9', 'name' => 'Acute bronchitis', 'description' => 'Bronchitis', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['J00-J99'], 'code' => 'J45.9', 'name' => 'Asthma, unspecified', 'description' => 'Asthma', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['J00-J99'], 'code' => 'J44.1', 'name' => 'Chronic obstructive pulmonary disease', 'description' => 'COPD', 'gender' => 'M', 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['J00-J99'], 'code' => 'J02.9', 'name' => 'Acute pharyngitis', 'description' => 'Sore throat', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['J00-J99'], 'code' => 'J01.9', 'name' => 'Acute sinusitis', 'description' => 'Sinusitis', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // K00-K95: Diseases of the digestive system
            ['category_id' => $categories['K00-K95'], 'code' => 'K29.7', 'name' => 'Gastritis, unspecified', 'description' => 'Gastritis', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['K00-K95'], 'code' => 'K21.0', 'name' => 'Gastroesophageal reflux disease', 'description' => 'Reflux', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['K00-K95'], 'code' => 'K80.2', 'name' => 'Gallstones', 'description' => 'Cholelithiasis', 'gender' => 'F', 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['K00-K95'], 'code' => 'K35.9', 'name' => 'Acute appendicitis', 'description' => 'Appendicitis', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['K00-K95'], 'code' => 'K59.0', 'name' => 'Constipation', 'description' => 'Constipation', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['K00-K95'], 'code' => 'K58.9', 'name' => 'Irritable bowel syndrome', 'description' => 'IBS', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['K00-K95'], 'code' => 'K92.2', 'name' => 'Gastrointestinal hemorrhage', 'description' => 'Hematemesis/melena', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['K00-K95'], 'code' => 'K76.0', 'name' => 'Fatty liver', 'description' => 'Nonalcoholic fatty liver disease', 'gender' => null, 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],

            // L00-L99: Diseases of the skin
            ['category_id' => $categories['L00-L99'], 'code' => 'L20.9', 'name' => 'Atopic dermatitis', 'description' => 'Eczema', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['L00-L99'], 'code' => 'L40.0', 'name' => 'Psoriasis vulgaris', 'description' => 'Psoriasis', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['L00-L99'], 'code' => 'L70.0', 'name' => 'Acne vulgaris', 'description' => 'Acne', 'gender' => null, 'age_range' => 'Adolescents', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['L00-L99'], 'code' => 'L50.9', 'name' => 'Urticaria', 'description' => 'Urticaria', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // M00-M99: Diseases of the musculoskeletal system
            ['category_id' => $categories['M00-M99'], 'code' => 'M54.5', 'name' => 'Low back pain', 'description' => 'Low back pain', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['M00-M99'], 'code' => 'M79.3', 'name' => 'Fasciitis', 'description' => 'Muscle pain', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['M00-M99'], 'code' => 'M17.9', 'name' => 'Knee osteoarthritis', 'description' => 'Gonarthrosis', 'gender' => 'F', 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['M00-M99'], 'code' => 'M75.1', 'name' => 'Painful shoulder', 'description' => 'Adhesive capsulitis', 'gender' => null, 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['M00-M99'], 'code' => 'M54.2', 'name' => 'Neck pain', 'description' => 'Neck pain', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['M00-M99'], 'code' => 'M79.6', 'name' => 'Limb pain', 'description' => 'Leg pain', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['M00-M99'], 'code' => 'M47.9', 'name' => 'Spondyloarthrosis', 'description' => 'Back pain', 'gender' => null, 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['M00-M99'], 'code' => 'M77.1', 'name' => 'Lateral epicondylitis', 'description' => 'Tennis elbow', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // N00-N99: Diseases of the genitourinary system
            ['category_id' => $categories['N00-N99'], 'code' => 'N39.0', 'name' => 'Urinary tract infection', 'description' => 'UTI', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['N00-N99'], 'code' => 'N18.9', 'name' => 'Chronic renal failure', 'description' => 'Chronic kidney disease', 'gender' => null, 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['N00-N99'], 'code' => 'N20.0', 'name' => 'Kidney stone', 'description' => 'Renal lithiasis', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['N00-N99'], 'code' => 'N40', 'name' => 'Benign prostatic hyperplasia', 'description' => 'Enlarged prostate', 'gender' => 'M', 'age_range' => 'Adults', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['N00-N99'], 'code' => 'N92.1', 'name' => 'Excessive regular menstruation', 'description' => 'Menorrhagia', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['N00-N99'], 'code' => 'N94.4', 'name' => 'Primary dysmenorrhea', 'description' => 'Menstrual pain', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // O00-O9A: Pregnancy, childbirth and the puerperium
            ['category_id' => $categories['O00-O9A'], 'code' => 'O80.0', 'name' => 'Spontaneous cephalic delivery', 'description' => 'Normal delivery', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['O00-O9A'], 'code' => 'O13.9', 'name' => 'Gestational hypertension', 'description' => 'Preeclampsia', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['O00-O9A'], 'code' => 'O24.4', 'name' => 'Diabetes mellitus in pregnancy', 'description' => 'Gestational diabetes', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // P00-P96: Perinatal period
            ['category_id' => $categories['P00-P96'], 'code' => 'P07.1', 'name' => 'Low birth weight', 'description' => 'Prematurity', 'gender' => null, 'age_range' => 'Neonatal', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['P00-P96'], 'code' => 'P22.9', 'name' => 'Perinatal asphyxia', 'description' => 'Fetal distress', 'gender' => null, 'age_range' => 'Neonatal', 'is_active' => true, 'created_at' => now()],

            // Q00-Q99: Congenital malformations
            ['category_id' => $categories['Q00-Q99'], 'code' => 'Q21.1', 'name' => 'Ventricular septal defect', 'description' => 'Ventricular septal defect', 'gender' => null, 'age_range' => 'Neonatal', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['Q00-Q99'], 'code' => 'Q53.9', 'name' => 'Cryptorchidism', 'description' => 'Undescended testicle', 'gender' => 'M', 'age_range' => 'Pediatric', 'is_active' => true, 'created_at' => now()],

            // R00-R99: Symptoms and signs
            ['category_id' => $categories['R00-R99'], 'code' => 'R50.9', 'name' => 'Fever, unspecified', 'description' => 'Fever', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['R00-R99'], 'code' => 'R51', 'name' => 'Headache', 'description' => 'Headache', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['R00-R99'], 'code' => 'R10.4', 'name' => 'Acute abdominal pain', 'description' => 'Abdominal pain', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['R00-R99'], 'code' => 'R55', 'name' => 'Syncope and collapse', 'description' => 'Dizziness/fainting', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['R00-R99'], 'code' => 'R06.0', 'name' => 'Dyspnea', 'description' => 'Difficulty breathing', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['R00-R99'], 'code' => 'R11.0', 'name' => 'Nausea', 'description' => 'Nausea', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['R00-R99'], 'code' => 'R11.1', 'name' => 'Vomiting', 'description' => 'Vomiting', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['R00-R99'], 'code' => 'R63.0', 'name' => 'Anorexia', 'description' => 'Loss of appetite', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['R00-R99'], 'code' => 'R10.0', 'name' => 'Abdominal distension', 'description' => 'Abdominal swelling', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['R00-R99'], 'code' => 'R04.2', 'name' => 'Epistaxis', 'description' => 'Nosebleed', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // S00-T88: Injuries
            ['category_id' => $categories['S00-T88'], 'code' => 'S52.5', 'name' => 'Fracture of forearm', 'description' => 'Radius/ulna fracture', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['S00-T88'], 'code' => 'S82.1', 'name' => 'Tibia fracture', 'description' => 'Leg fracture', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['S00-T88'], 'code' => 'T78.2', 'name' => 'Anaphylactic shock', 'description' => 'Severe allergic reaction', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['S00-T88'], 'code' => 'T14.9', 'name' => 'Poisoning, unspecified', 'description' => 'Poisoning', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // V00-Y99: External causes
            ['category_id' => $categories['V00-Y99'], 'code' => 'V89.0', 'name' => 'Traffic accident', 'description' => 'Motor vehicle accident', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['V00-Y99'], 'code' => 'W19', 'name' => 'Fall, unspecified', 'description' => 'Accidental fall', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // Z00-Z99: Contact with health services
            ['category_id' => $categories['Z00-Z99'], 'code' => 'Z00.0', 'name' => 'General examination', 'description' => 'General medical check-up', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['Z00-Z99'], 'code' => 'Z01.7', 'name' => 'Laboratory examination', 'description' => 'Laboratory test', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['Z00-Z99'], 'code' => 'Z23', 'name' => 'Vaccination', 'description' => 'Immunization', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['Z00-Z99'], 'code' => 'Z72.8', 'name' => 'Other problems related to lifestyle', 'description' => 'Risk factors', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
        ];

        DB::table('cie10_codes')->insert($codes);
    }
}
