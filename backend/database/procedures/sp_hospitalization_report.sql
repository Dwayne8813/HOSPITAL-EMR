-- Procedimiento para reporte de hospitalizaciones
-- Genera un reporte completo de hospitalizaciones por período

DELIMITER //

CREATE PROCEDURE sp_hospitalization_report(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_department_id INT,
    IN p_doctor_id INT
)
BEGIN
    -- Reporte resumen
    SELECT
        'RESUMEN' AS report_section,
        COUNT(*) AS total_hospitalizations,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active,
        SUM(CASE WHEN status = 'discharged' THEN 1 ELSE 0 END) AS discharged,
        SUM(CASE WHEN status = 'transferred' THEN 1 ELSE 0 END) AS transferred,
        SUM(CASE WHEN status = 'deceased' THEN 1 ELSE 0 END) AS deceased,
        ROUND(AVG(
            CASE
                WHEN actual_discharge_date IS NOT NULL THEN
                    DATEDIFF(actual_discharge_date, admission_date)
                WHEN status = 'active' THEN
                    DATEDIFF(CURDATE(), admission_date)
                ELSE NULL
            END
        ), 1) AS avg_stay_days,
        COUNT(DISTINCT patient_id) AS unique_patients
    FROM hospitalizations
    WHERE admission_date BETWEEN p_start_date AND CONCAT(p_end_date, ' 23:59:59')
        AND (p_department_id IS NULL OR department_id = p_department_id)
        AND (p_doctor_id IS NULL OR doctor_id = p_doctor_id);

    -- Hospitalizaciones por diagnóstico
    SELECT
        cie.code AS diagnosis_code,
        cie.name AS diagnosis_name,
        COUNT(*) AS total_cases,
        ROUND(AVG(
            CASE
                WHEN h.actual_discharge_date IS NOT NULL THEN
                    DATEDIFF(h.actual_discharge_date, h.admission_date)
                ELSE NULL
            END
        ), 1) AS avg_stay_days,
        SUM(CASE WHEN h.status = 'deceased' THEN 1 ELSE 0 END) AS deaths,
        ROUND(
            SUM(CASE WHEN h.status = 'deceased' THEN 1 ELSE 0 END) * 100.0 /
            NULLIF(COUNT(*), 0), 2
        ) AS mortality_rate
    FROM hospitalizations h
    INNER JOIN diagnoses d ON h.id = d.hospitalization_id
    INNER JOIN cie10_codes cie ON d.cie10_code_id = cie.id
    WHERE h.admission_date BETWEEN p_start_date AND CONCAT(p_end_date, ' 23:59:59')
        AND (p_department_id IS NULL OR h.department_id = p_department_id)
        AND (p_doctor_id IS NULL OR h.doctor_id = p_doctor_id)
    GROUP BY cie.code, cie.name
    ORDER BY total_cases DESC;

    -- Hospitalizaciones por médico
    SELECT
        u.id AS doctor_id,
        u.name AS doctor_name,
        s.name AS specialty_name,
        COUNT(*) AS total_hospitalizations,
        ROUND(AVG(
            CASE
                WHEN h.actual_discharge_date IS NOT NULL THEN
                    DATEDIFF(h.actual_discharge_date, h.admission_date)
                ELSE NULL
            END
        ), 1) AS avg_stay_days,
        SUM(CASE WHEN h.status = 'active' THEN 1 ELSE 0 END) AS currently_active,
        SUM(CASE WHEN h.status = 'deceased' THEN 1 ELSE 0 END) AS deaths
    FROM hospitalizations h
    INNER JOIN users u ON h.doctor_id = u.id
    LEFT JOIN specialties s ON u.specialty_id = s.id
    WHERE h.admission_date BETWEEN p_start_date AND CONCAT(p_end_date, ' 23:59:59')
        AND (p_department_id IS NULL OR h.department_id = p_department_id)
        AND (p_doctor_id IS NULL OR h.doctor_id = p_doctor_id)
    GROUP BY u.id, u.name, s.name
    ORDER BY total_hospitalizations DESC;

    -- Ingresos por hospitalización
    SELECT
        DATE(h.admission_date) AS admission_date,
        COUNT(*) AS admissions,
        COUNT(DISTINCT h.patient_id) AS unique_patients,
        COALESCE(SUM(
            CASE
                WHEN h.actual_discharge_date IS NOT NULL THEN
                    DATEDIFF(h.actual_discharge_date, h.admission_date) * COALESCE(b.daily_rate, 0)
                WHEN h.status = 'active' THEN
                    DATEDIFF(CURDATE(), h.admission_date) * COALESCE(b.daily_rate, 0)
                ELSE 0
            END
        ), 0) AS estimated_revenue
    FROM hospitalizations h
    LEFT JOIN beds b ON h.bed_id = b.id
    WHERE h.admission_date BETWEEN p_start_date AND CONCAT(p_end_date, ' 23:59:59')
        AND (p_department_id IS NULL OR h.department_id = p_department_id)
        AND (p_doctor_id IS NULL OR h.doctor_id = p_doctor_id)
    GROUP BY DATE(h.admission_date)
    ORDER BY admission_date;
END //

DELIMITER ;
