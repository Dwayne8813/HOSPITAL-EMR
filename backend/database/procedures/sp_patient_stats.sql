-- Procedimiento para estadísticas de pacientes
-- Genera un análisis completo de la población de pacientes

DELIMITER //

CREATE PROCEDURE sp_patient_stats(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_department_id INT,
    IN p_city_id INT
)
BEGIN
    -- Resumen general de pacientes
    SELECT
        'RESUMEN DE PACIENTES' AS report_section,
        COUNT(*) AS total_patients,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active_patients,
        SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) AS inactive_patients,
        SUM(CASE WHEN status = 'deceased' THEN 1 ELSE 0 END) AS deceased_patients,
        SUM(CASE WHEN gender = 'M' THEN 1 ELSE 0 END) AS male_patients,
        SUM(CASE WHEN gender = 'F' THEN 1 ELSE 0 END) AS female_patients,
        ROUND(AVG(TIMESTAMPDIFF(YEAR, birth_date, CURDATE())), 1) AS avg_age,
        MIN(TIMESTAMPDIFF(YEAR, birth_date, CURDATE())) AS min_age,
        MAX(TIMESTAMPDIFF(YEAR, birth_date, CURDATE())) AS max_age,
        SUM(CASE WHEN insurance_company IS NOT NULL AND insurance_company != '' THEN 1 ELSE 0 END) AS insured_patients,
        ROUND(
            SUM(CASE WHEN insurance_company IS NOT NULL AND insurance_company != '' THEN 1 ELSE 0 END) * 100.0 /
            NULLIF(COUNT(*), 0), 2
        ) AS insurance_rate
    FROM patients
    WHERE created_at BETWEEN p_start_date AND CONCAT(p_end_date, ' 23:59:59')
        AND (p_department_id IS NULL OR department_id = p_department_id)
        AND (p_city_id IS NULL OR city_id = p_city_id);

    -- Distribución por grupo etario
    SELECT
        CASE
            WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 0 AND 5 THEN '0-5 años'
            WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 6 AND 12 THEN '6-12 años'
            WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 13 AND 17 THEN '13-17 años'
            WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 30 THEN '18-30 años'
            WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 31 AND 45 THEN '31-45 años'
            WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 46 AND 60 THEN '46-60 años'
            WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) > 60 THEN 'Más de 60 años'
            ELSE 'No especificado'
        END AS age_group,
        COUNT(*) AS patient_count,
        ROUND(
            COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*) FROM patients WHERE status = 'active'), 0),
            2
        ) AS percentage,
        SUM(CASE WHEN gender = 'M' THEN 1 ELSE 0 END) AS male_count,
        SUM(CASE WHEN gender = 'F' THEN 1 ELSE 0 END) AS female_count,
        ROUND(AVG(TIMESTAMPDIFF(YEAR, birth_date, CURDATE())), 1) AS avg_age_in_group
    FROM patients
    WHERE status = 'active'
        AND (p_department_id IS NULL OR department_id = p_department_id)
        AND (p_city_id IS NULL OR city_id = p_city_id)
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

    -- Distribución por tipo de sangre
    SELECT
        blood_type,
        COUNT(*) AS patient_count,
        ROUND(
            COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*) FROM patients WHERE status = 'active' AND blood_type IS NOT NULL), 0),
            2
        ) AS percentage,
        SUM(CASE WHEN gender = 'M' THEN 1 ELSE 0 END) AS male_count,
        SUM(CASE WHEN gender = 'F' THEN 1 ELSE 0 END) AS female_count
    FROM patients
    WHERE status = 'active'
        AND blood_type IS NOT NULL
        AND (p_department_id IS NULL OR department_id = p_department_id)
        AND (p_city_id IS NULL OR city_id = p_city_id)
    GROUP BY blood_type
    ORDER BY patient_count DESC;

    -- Distribución por estado civil
    SELECT
        CASE civil_status
            WHEN 'single' THEN 'Soltero/a'
            WHEN 'married' THEN 'Casado/a'
            WHEN 'divorced' THEN 'Divorciado/a'
            WHEN 'widowed' THEN 'Viudo/a'
            WHEN 'cohabiting' THEN 'Unión libre'
            ELSE 'No especificado'
        END AS civil_status_name,
        COUNT(*) AS patient_count,
        ROUND(
            COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*) FROM patients WHERE status = 'active'), 0),
            2
        ) AS percentage,
        ROUND(AVG(TIMESTAMPDIFF(YEAR, birth_date, CURDATE())), 1) AS avg_age
    FROM patients
    WHERE status = 'active'
        AND (p_department_id IS NULL OR department_id = p_department_id)
        AND (p_city_id IS NULL OR city_id = p_city_id)
    GROUP BY civil_status
    ORDER BY patient_count DESC;

    -- Top 10 ciudades de origen
    SELECT
        c.name AS city_name,
        d.name AS department_name,
        COUNT(*) AS patient_count,
        ROUND(
            COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*) FROM patients WHERE status = 'active'), 0),
            2
        ) AS percentage
    FROM patients p
    INNER JOIN cities c ON p.city_id = c.id
    INNER JOIN provinces pr ON c.province_id = pr.id
    INNER JOIN departments d ON pr.department_id = d.id
    WHERE p.status = 'active'
        AND (p_department_id IS NULL OR p.department_id = p_department_id)
        AND (p_city_id IS NULL OR p.city_id = p_city_id)
    GROUP BY c.id, c.name, d.name
    ORDER BY patient_count DESC
    LIMIT 10;

    -- Pacientes por seguro médico
    SELECT
        COALESCE(insurance_company, 'Sin seguro') AS insurance_provider,
        COUNT(*) AS patient_count,
        ROUND(
            COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*) FROM patients WHERE status = 'active'), 0),
            2
        ) AS percentage,
        ROUND(AVG(TIMESTAMPDIFF(YEAR, birth_date, CURDATE())), 1) AS avg_age
    FROM patients
    WHERE status = 'active'
        AND (p_department_id IS NULL OR department_id = p_department_id)
        AND (p_city_id IS NULL OR city_id = p_city_id)
    GROUP BY insurance_company
    ORDER BY patient_count DESC
    LIMIT 15;

    -- Tendencia de registros mensuales
    SELECT
        DATE_FORMAT(created_at, '%Y-%m') AS registration_month,
        COUNT(*) AS new_patients,
        SUM(CASE WHEN gender = 'M' THEN 1 ELSE 0 END) AS new_male,
        SUM(CASE WHEN gender = 'F' THEN 1 ELSE 0 END) AS new_female,
        ROUND(AVG(TIMESTAMPDIFF(YEAR, birth_date, CURDATE())), 1) AS avg_age,
        SUM(CASE WHEN insurance_company IS NOT NULL AND insurance_company != '' THEN 1 ELSE 0 END) AS new_insured
    FROM patients
    WHERE created_at BETWEEN p_start_date AND CONCAT(p_end_date, ' 23:59:59')
        AND (p_department_id IS NULL OR department_id = p_department_id)
        AND (p_city_id IS NULL OR city_id = p_city_id)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY registration_month;

    -- Alergias más comunes
    SELECT
        pa.allergy,
        COUNT(*) AS patient_count,
        COUNT(DISTINCT pa.patient_id) AS unique_patients
    FROM patient_allergies pa
    INNER JOIN patients p ON pa.patient_id = p.id
    WHERE p.status = 'active'
        AND (p_department_id IS NULL OR p.department_id = p_department_id)
        AND (p_city_id IS NULL OR p.city_id = p_city_id)
    GROUP BY pa.allergy
    ORDER BY patient_count DESC
    LIMIT 15;
END //

DELIMITER ;
