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
            // A00-B99: Enfermedades infecciosas
            ['category_id' => $categories['A00-B99'], 'code' => 'A09', 'name' => 'Gastroenteritis y colitis de origen infeccioso', 'description' => 'Diarrea infecciosa', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['A00-B99'], 'code' => 'B34.9', 'name' => 'Infección viral de sitio no especificado', 'description' => 'Infección viral general', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['A00-B99'], 'code' => 'J06.9', 'name' => 'Gripe no especificada', 'description' => 'Infección respiratoria aguda', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['A00-B99'], 'code' => 'L30.9', 'name' => 'Dermatitis no especificada', 'description' => 'Infección de piel', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['A00-B99'], 'code' => 'N39.0', 'name' => 'Infección de vías urinarias', 'description' => 'ITU', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // C00-D49: Neoplasias
            ['category_id' => $categories['C00-D49'], 'code' => 'C34.9', 'name' => 'Neoplasia maligna de bronquios y pulmón', 'description' => 'Cáncer de pulmón', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['C00-D49'], 'code' => 'C50.9', 'name' => 'Neoplasia maligna de mama', 'description' => 'Cáncer de mama', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['C00-D49'], 'code' => 'C53.9', 'name' => 'Neoplasia maligna del cuello uterino', 'description' => 'Cáncer cervicouterino', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['C00-D49'], 'code' => 'D48.9', 'name' => 'Neoplasia de comportamiento incierto', 'description' => 'Tumor de comportamiento incierto', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // D50-D89: Enfermedades de la sangre
            ['category_id' => $categories['D50-D89'], 'code' => 'D50.9', 'name' => 'Anemia por deficiencia de hierro', 'description' => 'Anemia ferropénica', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['D50-D89'], 'code' => 'D64.9', 'name' => 'Anemia no especificada', 'description' => 'Anemia general', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['D50-D89'], 'code' => 'D69.6', 'name' => 'Trombocitopenia', 'description' => 'Plaquetas bajas', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // E00-E89: Enfermedades endocrinas
            ['category_id' => $categories['E00-E89'], 'code' => 'E11.9', 'name' => 'Diabetes mellitus tipo 2', 'description' => 'Diabetes tipo 2', 'gender' => null, 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['E00-E89'], 'code' => 'E10.9', 'name' => 'Diabetes mellitus tipo 1', 'description' => 'Diabetes tipo 1', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['E00-E89'], 'code' => 'E78.5', 'name' => 'Hiperlipidemia', 'description' => 'Colesterol y triglicéridos altos', 'gender' => null, 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['E00-E89'], 'code' => 'E03.9', 'name' => 'Hipotiroidismo no especificado', 'description' => 'Tiroides baja', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['E00-E89'], 'code' => 'E05.9', 'name' => 'Hipertiroidismo no especificado', 'description' => 'Tiroides alta', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['E00-E89'], 'code' => 'E11.65', 'name' => 'Diabetes mellitus tipo 2 con hiperglucemia', 'description' => 'Diabetes descompensada', 'gender' => null, 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['E00-E89'], 'code' => 'E11.2', 'name' => 'Diabetes mellitus tipo 2 con complicación renal', 'description' => 'Diabetes con nefropatía', 'gender' => null, 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['E00-E89'], 'code' => 'E11.4', 'name' => 'Diabetes mellitus tipo 2 con complicación neurológica', 'description' => 'Diabetes con neuropatía', 'gender' => null, 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],

            // F01-F99: Trastornos mentales
            ['category_id' => $categories['F01-F99'], 'code' => 'F32.9', 'name' => 'Episodio depresivo mayor', 'description' => 'Depresión', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['F01-F99'], 'code' => 'F41.1', 'name' => 'Trastorno de ansiedad generalizada', 'description' => 'Ansiedad', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['F01-F99'], 'code' => 'F41.0', 'name' => 'Crisis de angustia', 'description' => 'Ataque de pánico', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['F01-F99'], 'code' => 'F10.2', 'name' => 'Trastorno por uso de alcohol', 'description' => 'Alcoholismo', 'gender' => 'M', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['F01-F99'], 'code' => 'F43.1', 'name' => 'Trastorno de estrés postraumático', 'description' => 'TEPT', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['F01-F99'], 'code' => 'F50.0', 'name' => 'Anorexia nerviosa', 'description' => 'Trastorno alimenticio', 'gender' => 'F', 'age_range' => 'Adolescentes', 'is_active' => true, 'created_at' => now()],

            // G00-G99: Enfermedades del sistema nervioso
            ['category_id' => $categories['G00-G99'], 'code' => 'G43.9', 'name' => 'Migraña no especificada', 'description' => 'Dolor de cabeza severo', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['G00-G99'], 'code' => 'G40.9', 'name' => 'Epilepsia no especificada', 'description' => 'Crisis epilépticas', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['G00-G99'], 'code' => 'G56.0', 'name' => 'Síndrome del túnel carpiano', 'description' => 'Neuropatía compresión', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['G00-G99'], 'code' => 'G47.0', 'name' => 'Trastornos del insomnio', 'description' => 'Insomnio', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['G00-G99'], 'code' => 'G54.0', 'name' => 'Parálisis de braquial plexo', 'description' => 'Dolor de hombro', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // H00-H59: Enfermedades del ojo
            ['category_id' => $categories['H00-H59'], 'code' => 'H40.1', 'name' => 'Glaucoma de ángulo abierto', 'description' => 'Presión ocular alta', 'gender' => null, 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['H00-H59'], 'code' => 'H25.9', 'name' => 'Catarata senil', 'description' => 'Catarata', 'gender' => null, 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['H00-H59'], 'code' => 'H10.9', 'name' => 'Conjuntivitis no especificada', 'description' => 'Ojo rojo', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // H60-H95: Enfermedades del oído
            ['category_id' => $categories['H60-H95'], 'code' => 'H66.9', 'name' => 'Otalgia no especificada', 'description' => 'Dolor de oído', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['H60-H95'], 'code' => 'H90.3', 'name' => 'Hipoacusia neurosensorial', 'description' => 'Pérdida auditiva', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // I00-I99: Enfermedades del sistema circulatorio
            ['category_id' => $categories['I00-I99'], 'code' => 'I10', 'name' => 'Hipertensión esencial primaria', 'description' => 'Hipertensión arterial', 'gender' => null, 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['I00-I99'], 'code' => 'I11.9', 'name' => 'Enfermedad cardíaca hipertensiva', 'description' => 'Hipertensión con afectación cardíaca', 'gender' => null, 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['I00-I99'], 'code' => 'I21.9', 'name' => 'Infarto agudo de miocardio', 'description' => 'Infarto', 'gender' => 'M', 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['I00-I99'], 'code' => 'I50.9', 'name' => 'Insuficiencia cardíaca', 'description' => 'Corazón de fallo', 'gender' => null, 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['I00-I99'], 'code' => 'I63.9', 'name' => 'Infarto cerebral', 'description' => 'Ictus', 'gender' => null, 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['I00-I99'], 'code' => 'I48', 'name' => 'Fibrilación auricular', 'description' => 'Arritmia cardíaca', 'gender' => null, 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['I00-I99'], 'code' => 'I73.9', 'name' => 'Enfermedad vascular periférica', 'description' => 'Insuficiencia venosa', 'gender' => null, 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['I00-I99'], 'code' => 'I25.1', 'name' => 'Enfermedad arterial coronaria', 'description' => 'Cardiopatía isquémica', 'gender' => null, 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],

            // J00-J99: Enfermedades del sistema respiratorio
            ['category_id' => $categories['J00-J99'], 'code' => 'J00', 'name' => 'Rinofaringitis aguda', 'description' => 'Resfriado común', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['J00-J99'], 'code' => 'J06.9', 'name' => 'Infección aguda de las vías respiratorias', 'description' => 'IRA', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['J00-J99'], 'code' => 'J18.9', 'name' => 'Neumonía no especificada', 'description' => 'Neumonía', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['J00-J99'], 'code' => 'J20.9', 'name' => 'Bronquitis aguda', 'description' => 'Bronquitis', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['J00-J99'], 'code' => 'J45.9', 'name' => 'Asma no especificada', 'description' => 'Asma', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['J00-J99'], 'code' => 'J44.1', 'name' => 'Enfermedad pulmonar obstructiva crónica', 'description' => 'EPOC', 'gender' => 'M', 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['J00-J99'], 'code' => 'J02.9', 'name' => 'Faringitis aguda', 'description' => 'Dolor de garganta', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['J00-J99'], 'code' => 'J01.9', 'name' => 'Sinusitis aguda', 'description' => 'Sinusitis', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // K00-K95: Enfermedades del sistema digestivo
            ['category_id' => $categories['K00-K95'], 'code' => 'K29.7', 'name' => 'Gastritis no especificada', 'description' => 'Gastritis', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['K00-K95'], 'code' => 'K21.0', 'name' => 'Enfermedad por reflujo gastroesofágico', 'description' => 'Reflujo', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['K00-K95'], 'code' => 'K80.2', 'name' => 'Cálculos biliares', 'description' => 'Colelitiasis', 'gender' => 'F', 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['K00-K95'], 'code' => 'K35.9', 'name' => 'Apendicitis aguda', 'description' => 'Apendicitis', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['K00-K95'], 'code' => 'K59.0', 'name' => 'Estreñimiento', 'description' => 'Estreñimiento', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['K00-K95'], 'code' => 'K58.9', 'name' => 'Síndrome del intestino irritable', 'description' => 'SII', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['K00-K95'], 'code' => 'K92.2', 'name' => 'Hemorragia gastrointestinal', 'description' => 'Hematemesis/melena', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['K00-K95'], 'code' => 'K76.0', 'name' => 'Hígado graso', 'description' => 'Hepatopatía grasa no alcohólica', 'gender' => null, 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],

            // L00-L99: Enfermedades de la piel
            ['category_id' => $categories['L00-L99'], 'code' => 'L20.9', 'name' => 'Dermatitis atópica', 'description' => 'Eccema', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['L00-L99'], 'code' => 'L40.0', 'name' => 'Psoriasis vulgar', 'description' => 'Psoriasis', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['L00-L99'], 'code' => 'L70.0', 'name' => 'Acné vulgar', 'description' => 'Acné', 'gender' => null, 'age_range' => 'Adolescentes', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['L00-L99'], 'code' => 'L50.9', 'name' => 'Urticaria', 'description' => 'Urticaria', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // M00-M99: Enfermedades del sistema osteomuscular
            ['category_id' => $categories['M00-M99'], 'code' => 'M54.5', 'name' => 'Lumbago', 'description' => 'Dolor lumbar bajo', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['M00-M99'], 'code' => 'M79.3', 'name' => 'Fascitis', 'description' => 'Dolor muscular', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['M00-M99'], 'code' => 'M17.9', 'name' => 'Artrosis de rodilla', 'description' => 'Gonartrosis', 'gender' => 'F', 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['M00-M99'], 'code' => 'M75.1', 'name' => 'Hombro doloroso', 'description' => 'Capsulitis adhesiva', 'gender' => null, 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['M00-M99'], 'code' => 'M54.2', 'name' => 'Cervicalgia', 'description' => 'Dolor de cuello', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['M00-M99'], 'code' => 'M79.6', 'name' => 'Dolor en extremidad', 'description' => 'Dolor de pierna', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['M00-M99'], 'code' => 'M47.9', 'name' => 'Espondiloartrosis', 'description' => 'Dolor de espalda', 'gender' => null, 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['M00-M99'], 'code' => 'M77.1', 'name' => 'Epicondilitis lateral', 'description' => 'Codo de tenista', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // N00-N99: Enfermedades del sistema genitourinario
            ['category_id' => $categories['N00-N99'], 'code' => 'N39.0', 'name' => 'Infección de vías urinarias', 'description' => 'ITU', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['N00-N99'], 'code' => 'N18.9', 'name' => 'Insuficiencia renal crónica', 'description' => 'Enfermedad renal crónica', 'gender' => null, 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['N00-N99'], 'code' => 'N20.0', 'name' => 'Cálculo renal', 'description' => 'Litiasis renal', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['N00-N99'], 'code' => 'N40', 'name' => 'Hiperplasia prostática benigna', 'description' => 'Próstata agrandada', 'gender' => 'M', 'age_range' => 'Adultos', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['N00-N99'], 'code' => 'N92.1', 'name' => 'Exceso de menstruación regular', 'description' => 'Menorragia', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['N00-N99'], 'code' => 'N94.4', 'name' => 'Dismenorrea primaria', 'description' => 'Dolor menstrual', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // O00-O9A: Embarazo, parto y puerperio
            ['category_id' => $categories['O00-O9A'], 'code' => 'O80.0', 'name' => 'Parto espontáneo cefálico', 'description' => 'Parto normal', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['O00-O9A'], 'code' => 'O13.9', 'name' => 'Hipertensión gestacional', 'description' => 'Preeclampsia', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['O00-O9A'], 'code' => 'O24.4', 'name' => 'Diabetes mellitus en el embarazo', 'description' => 'Diabetes gestacional', 'gender' => 'F', 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // P00-P96: Periodo perinatal
            ['category_id' => $categories['P00-P96'], 'code' => 'P07.1', 'name' => 'Bajo peso al nacer', 'description' => 'Prematuridad', 'gender' => null, 'age_range' => 'Neonatal', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['P00-P96'], 'code' => 'P22.9', 'name' => 'Asfixia perinatal', 'description' => 'Sufrimiento fetal', 'gender' => null, 'age_range' => 'Neonatal', 'is_active' => true, 'created_at' => now()],

            // Q00-Q99: Malformaciones congénitas
            ['category_id' => $categories['Q00-Q99'], 'code' => 'Q21.1', 'name' => 'Comunicación interventricular', 'description' => 'Falla septal ventricular', 'gender' => null, 'age_range' => 'Neonatal', 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['Q00-Q99'], 'code' => 'Q53.9', 'name' => 'Criptorquidia', 'description' => 'Testículo no descendido', 'gender' => 'M', 'age_range' => 'Pediátrico', 'is_active' => true, 'created_at' => now()],

            // R00-R99: Síntomas y signos
            ['category_id' => $categories['R00-R99'], 'code' => 'R50.9', 'name' => 'Fiebre no especificada', 'description' => 'Fiebre', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['R00-R99'], 'code' => 'R51', 'name' => 'Cefalea', 'description' => 'Dolor de cabeza', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['R00-R99'], 'code' => 'R10.4', 'name' => 'Dolor abdominal agudo', 'description' => 'Dolor abdominal', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['R00-R99'], 'code' => 'R55', 'name' => 'Síncope y colapso', 'description' => 'Mareo/desmayo', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['R00-R99'], 'code' => 'R06.0', 'name' => 'Disnea', 'description' => 'Dificultad respiratoria', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['R00-R99'], 'code' => 'R11.0', 'name' => 'Náusea', 'description' => 'Náusea', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['R00-R99'], 'code' => 'R11.1', 'name' => 'Vómito', 'description' => 'Vómito', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['R00-R99'], 'code' => 'R63.0', 'name' => 'Anorexia', 'description' => 'Pérdida de apetito', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['R00-R99'], 'code' => 'R10.0', 'name' => 'Distensión abdominal', 'description' => 'Hinchazón abdominal', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['R00-R99'], 'code' => 'R04.2', 'name' => 'Epistaxis', 'description' => 'Sangrado nasal', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // S00-T88: Lesiones
            ['category_id' => $categories['S00-T88'], 'code' => 'S52.5', 'name' => 'Fractura del antebrazo', 'description' => 'Fractura de radio/ulna', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['S00-T88'], 'code' => 'S82.1', 'name' => 'Fractura de tibia', 'description' => 'Fractura de pierna', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['S00-T88'], 'code' => 'T78.2', 'name' => 'Shock anafiláctico', 'description' => 'Reacción alérgica severa', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['S00-T88'], 'code' => 'T14.9', 'name' => 'Envenenamiento no especificado', 'description' => 'Intoxicación', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // V00-Y99: Causas externas
            ['category_id' => $categories['V00-Y99'], 'code' => 'V89.0', 'name' => 'Accidente de tránsito', 'description' => 'Accidente vehicular', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['V00-Y99'], 'code' => 'W19', 'name' => 'Caída no especificada', 'description' => 'Caída accidental', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],

            // Z00-Z99: Contacto con servicios de salud
            ['category_id' => $categories['Z00-Z99'], 'code' => 'Z00.0', 'name' => 'Examen general', 'description' => 'Control médico general', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['Z00-Z99'], 'code' => 'Z01.7', 'name' => 'Examen de laboratorio', 'description' => 'Estudio de laboratorio', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['Z00-Z99'], 'code' => 'Z23', 'name' => 'Vacunación', 'description' => 'Inmunización', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
            ['category_id' => $categories['Z00-Z99'], 'code' => 'Z72.8', 'name' => 'Otros problemas relacionados con el estilo de vida', 'description' => 'Factores de riesgo', 'gender' => null, 'age_range' => null, 'is_active' => true, 'created_at' => now()],
        ];

        DB::table('cie10_codes')->insert($codes);
    }
}
