-- Vista resumen médico del paciente
-- Muestra información completa del historial médico de un paciente

CREATE OR REPLACE VIEW v_patient_medical_summary AS
SELECT
    p.id AS patient_id,
    p.internal_code,
    p.medical_record_number,
    p.ci,
    CONCAT(p.first_name, ' ', p.last_name, ' ', COALESCE(p.second_last_name, '')) AS full_name,
    p.first_name,
    p.last_name,
    p.second_last_name,
    p.birth_date,
    TIMESTAMPDIFF(YEAR, p.birth_date, CURDATE()) AS age,
    p.gender,
    p.blood_type,
    p.phone,
    p.email,
    p.address,
    p.nationality,
    p.status AS patient_status,

    -- Última consulta
    (SELECT c.consultation_date
     FROM consultations c
     WHERE c.patient_id = p.id
     ORDER BY c.consultation_date DESC
     LIMIT 1) AS last_consultation_date,

    (SELECT c.reason
     FROM consultations c
     WHERE c.patient_id = p.id
     ORDER BY c.consultation_date DESC
     LIMIT 1) AS last_consultation_reason,

    -- Diagnóstico más reciente
    (SELECT cie.name
     FROM diagnoses d
     INNER JOIN cie10_codes cie ON d.cie10_code_id = cie.id
     WHERE d.patient_id = p.id
     ORDER BY d.diagnosis_date DESC
     LIMIT 1) AS last_diagnosis,

    (SELECT cie.code
     FROM diagnoses d
     INNER JOIN cie10_codes cie ON d.cie10_code_id = cie.id
     WHERE d.patient_id = p.id
     ORDER BY d.diagnosis_date DESC
     LIMIT 1) AS last_diagnosis_code,

    -- Total de consultas
    (SELECT COUNT(*)
     FROM consultations c
     WHERE c.patient_id = p.id) AS total_consultations,

    -- Consultas este año
    (SELECT COUNT(*)
     FROM consultations c
     WHERE c.patient_id = p.id
        AND YEAR(c.consultation_date) = YEAR(CURDATE())) AS consultations_this_year,

    -- Total de diagnósticos
    (SELECT COUNT(*)
     FROM diagnoses d
     WHERE d.patient_id = p.id) AS total_diagnoses,

    -- Total de recetas
    (SELECT COUNT(*)
     FROM prescriptions pr
     WHERE pr.patient_id = p.id) AS total_prescriptions,

    -- Total de análisis de laboratorio
    (SELECT COUNT(*)
     FROM lab_orders lo
     WHERE lo.patient_id = p.id) AS total_lab_orders,

    -- Total de hospitalizaciones
    (SELECT COUNT(*)
     FROM hospitalizations h
     WHERE h.patient_id = p.id) AS total_hospitalizations,

    -- Hospitalización actual
    (SELECT h.id
     FROM hospitalizations h
     WHERE h.patient_id = p.id
        AND h.status = 'active'
     LIMIT 1) AS current_hospitalization_id,

    -- Alergias
    (SELECT GROUP_CONCAT(pa.allergy SEPARATOR ', ')
     FROM patient_allergies pa
     WHERE pa.patient_id = p.id) AS allergies,

    -- Medicamentos actuales
    (SELECT GROUP_CONCAT(DISTINCT m.name SEPARATOR ', ')
     FROM prescriptions pr
     INNER JOIN prescription_items pi ON pr.id = pi.prescription_id
     INNER JOIN medications m ON pi.medication_id = m.id
     WHERE pr.patient_id = p.id
        AND pr.status = 'active') AS current_medications,

    -- Antecedentes familiares
    (SELECT GROUP_CONCAT(CONCAT(a.type, ': ', a.description) SEPARATOR '; ')
     FROM antecedents a
     WHERE a.patient_id = p.id
        AND a.type = 'family') AS family_history,

    -- Antecedentes personales
    (SELECT GROUP_CONCAT(CONCAT(a.type, ': ', a.description) SEPARATOR '; ')
     FROM antecedents a
     WHERE a.patient_id = p.id
        AND a.type = 'personal') AS personal_history,

    -- Últimos signos vitales
    (SELECT vs.systolic_pressure
     FROM vital_signs vs
     WHERE vs.patient_id = p.id
     ORDER BY vs.recorded_at DESC
     LIMIT 1) AS last_blood_pressure,

    (SELECT vs.heart_rate
     FROM vital_signs vs
     WHERE vs.patient_id = p.id
     ORDER BY vs.recorded_at DESC
     LIMIT 1) AS last_heart_rate,

    (SELECT vs.temperature
     FROM vital_signs vs
     WHERE vs.patient_id = p.id
     ORDER BY vs.recorded_at DESC
     LIMIT 1) AS last_temperature,

    (SELECT vs.weight
     FROM vital_signs vs
     WHERE vs.patient_id = p.id
     ORDER BY vs.recorded_at DESC
     LIMIT 1) AS last_weight,

    -- Información de seguro
    p.insurance_company,
    p.insurance_number,
    p.insurance_expiry,

    -- Contacto de emergencia
    p.emergency_contact_name,
    p.emergency_contact_phone,

    -- Fechas
    p.created_at AS registration_date,
    p.updated_at AS last_update

FROM patients p
WHERE p.status = 'active';
