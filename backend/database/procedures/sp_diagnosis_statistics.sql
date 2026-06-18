-- Procedimiento para estadísticas de diagnósticos
-- Genera un análisis completo de diagnósticos médicos

DELIMITER //

CREATE PROCEDURE sp_diagnosis_statistics(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_age_group VARCHAR(20),
    IN p_gender CHAR(1)
)
BEGIN
    -- Resumen de diagnósticos
    SELECT
        'RESUMEN DE DIAGNÓSTICOS' AS report_section,
        COUNT(*) AS total_diagnoses,
        COUNT(DISTINCT patient_id) AS unique_patients,
        COUNT(DISTINCT cie10_code_id) AS unique_diagnoses,
        ROUND(AVG(TIMESTAMPDIFF(YEAR, p.birth_date, d.diagnosis_date)), 1) AS avg_patient_age,
        SUM(CASE WHEN p.gender = 'M' THEN 1 ELSE 0 END) AS male_diagnoses,
        SUM(CASE WHEN p.gender = 'F' THEN 1 ELSE 0 END) AS female_diagnoses
    FROM diagnoses d
    INNER JOIN patients p ON d.patient_id = p.id
    WHERE d.diagnosis_date BETWEEN p_start_date AND p_end_date
        AND (p_gender IS NULL OR p.gender = p_gender);

    -- Top 20 diagnósticos
    SELECT
        cc.code AS category_code,
        cc.name AS category_name,
        cie.code AS diagnosis_code,
        cie.name AS diagnosis_name,
        COUNT(*) AS total_cases,
        COUNT(DISTINCT d.patient_id) AS unique_patients,
        ROUND(
            COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*) FROM diagnoses WHERE diagnosis_date BETWEEN p_start_date AND p_end_date), 0),
            2
        ) AS percentage,
        ROUND(AVG(TIMESTAMPDIFF(YEAR, p.birth_date, d.diagnosis_date)), 1) AS avg_age,
        SUM(CASE WHEN p.gender = 'M' THEN 1 ELSE 0 END) AS male_cases,
        SUM(CASE WHEN p.gender = 'F' THEN 1 ELSE 0 END) AS female_cases,
        SUM(CASE WHEN d.hospitalization_id IS NOT NULL THEN 1 ELSE 0 END) AS hospitalizations,
        ROUND(
            SUM(CASE WHEN d.hospitalization_id IS NOT NULL THEN 1 ELSE 0 END) * 100.0 /
            NULLIF(COUNT(*), 0), 2
        ) AS hospitalization_rate
    FROM diagnoses d
    INNER JOIN cie10_codes cie ON d.cie10_code_id = cie.id
    INNER JOIN cie10_categories cc ON cie.category_id = cc.id
    INNER JOIN patients p ON d.patient_id = p.id
    WHERE d.diagnosis_date BETWEEN p_start_date AND p_end_date
        AND (p_gender IS NULL OR p.gender = p_gender)
    GROUP BY cc.code, cc.name, cie.code, cie.name
    ORDER BY total_cases DESC
    LIMIT 20;

    -- Distribución por grupo etario
    SELECT
        CASE
            WHEN TIMESTAMPDIFF(YEAR, p.birth_date, d.diagnosis_date) BETWEEN 0 AND 5 THEN '0-5 años'
            WHEN TIMESTAMPDIFF(YEAR, p.birth_date, d.diagnosis_date) BETWEEN 6 AND 12 THEN '6-12 años'
            WHEN TIMESTAMPDIFF(YEAR, p.birth_date, d.diagnosis_date) BETWEEN 13 AND 17 THEN '13-17 años'
            WHEN TIMESTAMPDIFF(YEAR, p.birth_date, d.diagnosis_date) BETWEEN 18 AND 30 THEN '18-30 años'
            WHEN TIMESTAMPDIFF(YEAR, p.birth_date, d.diagnosis_date) BETWEEN 31 AND 45 THEN '31-45 años'
            WHEN TIMESTAMPDIFF(YEAR, p.birth_date, d.diagnosis_date) BETWEEN 46 AND 60 THEN '46-60 años'
            WHEN TIMESTAMPDIFF(YEAR, p.birth_date, d.diagnosis_date) > 60 THEN 'Más de 60 años'
            ELSE 'No especificado'
        END AS age_group,
        COUNT(*) AS total_diagnoses,
        COUNT(DISTINCT d.patient_id) AS unique_patients,
        ROUND(
            COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*) FROM diagnoses WHERE diagnosis_date BETWEEN p_start_date AND p_end_date), 0),
            2
        ) AS percentage
    FROM diagnoses d
    INNER JOIN patients p ON d.patient_id = p.id
    WHERE d.diagnosis_date BETWEEN p_start_date AND p_end_date
        AND (p_gender IS NULL OR p.gender = p_gender)
    GROUP BY age_group
    ORDER BY
        CASE age_group
            WHEN '0-5 años' THEN 1
            WHEN '6-12 años' THEN 2
            WHEN '13-17 años' THEN 3
            WHEN '18-30 años' THEN 4
            WHEN '31-45 años' THEN 5
            WHEN '46-60 años' THEN 6
            WHEN 'Más de 60 años' THEN 7
            ELSE 8
        END;

    -- Diagnósticos por categoría CIE-10
    SELECT
        cc.code AS category_code,
        cc.name AS category_name,
        COUNT(*) AS total_diagnoses,
        COUNT(DISTINCT d.patient_id) AS unique_patients,
        ROUND(AVG(TIMESTAMPDIFF(YEAR, p.birth_date, d.diagnosis_date)), 1) AS avg_age,
        ROUND(
            COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*) FROM diagnoses WHERE diagnosis_date BETWEEN p_start_date AND p_end_date), 0),
            2
        ) AS percentage,
        SUM(CASE WHEN d.hospitalization_id IS NOT NULL THEN 1 ELSE 0 END) AS hospitalizations
    FROM diagnoses d
    INNER JOIN cie10_codes cie ON d.cie10_code_id = cie.id
    INNER JOIN cie10_categories cc ON cie.category_id = cc.id
    INNER JOIN patients p ON d.patient_id = p.id
    WHERE d.diagnosis_date BETWEEN p_start_date AND p_end_date
        AND (p_gender IS NULL OR p.gender = p_gender)
    GROUP BY cc.id, cc.code, cc.name
    ORDER BY total_diagnoses DESC;

    -- Tendencia mensual de diagnósticos
    SELECT
        DATE_FORMAT(d.diagnosis_date, '%Y-%m') AS diagnosis_month,
        COUNT(*) AS total_diagnoses,
        COUNT(DISTINCT d.patient_id) AS unique_patients,
        (SELECT cie.name
         FROM diagnoses d2
         INNER JOIN cie10_codes cie ON d2.cie10_code_id = cie.id
         WHERE MONTH(d2.diagnosis_date) = MONTH(d.diagnosis_date)
            AND YEAR(d2.diagnosis_date) = YEAR(d.diagnosis_date)
         GROUP BY cie.name
         ORDER BY COUNT(*) DESC
         LIMIT 1) AS most_common_diagnosis
    FROM diagnoses d
    WHERE d.diagnosis_date BETWEEN p_start_date AND p_end_date
    GROUP BY DATE_FORMAT(d.diagnosis_date, '%Y-%m')
    ORDER BY diagnosis_month;
END //

DELIMITER ;
