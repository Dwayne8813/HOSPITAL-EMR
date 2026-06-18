-- Vista de diagnósticos más frecuentes
-- Muestra los diagnósticos más comunes en el hospital

CREATE OR REPLACE VIEW v_top_diagnoses AS
SELECT
    cc.id AS category_id,
    cc.code AS category_code,
    cc.name AS category_name,
    c.id AS code_id,
    c.code AS diagnosis_code,
    c.name AS diagnosis_name,
    c.gender AS gender_restriction,
    c.age_range,

    -- Conteo total de diagnósticos
    COUNT(d.id) AS total_diagnoses,

    -- Diagnósticos del mes actual
    SUM(CASE
        WHEN MONTH(d.created_at) = MONTH(CURDATE())
        AND YEAR(d.created_at) = YEAR(CURDATE())
        THEN 1 ELSE 0
    END) AS diagnoses_this_month,

    -- Diagnósticos del año actual
    SUM(CASE
        WHEN YEAR(d.created_at) = YEAR(CURDATE())
        THEN 1 ELSE 0
    END) AS diagnoses_this_year,

    -- Pacientes únicos con este diagnóstico
    COUNT(DISTINCT d.patient_id) AS unique_patients,

    -- Porcentaje del total
    ROUND(
        COUNT(d.id) * 100.0 / NULLIF((SELECT COUNT(*) FROM diagnoses), 0),
        2
    ) AS percentage_of_total,

    -- Distribución por género
    SUM(CASE WHEN p.gender = 'M' THEN 1 ELSE 0 END) AS male_patients,
    SUM(CASE WHEN p.gender = 'F' THEN 1 ELSE 0 END) AS female_patients,

    -- Edad promedio de pacientes
    ROUND(AVG(TIMESTAMPDIFF(YEAR, p.birth_date, CURDATE())), 1) AS avg_patient_age,

    -- Tasa de mortalidad (si hay datos)
    ROUND(
        SUM(CASE WHEN d.outcome = 'deceased' THEN 1 ELSE 0 END) * 100.0 /
        NULLIF(COUNT(d.id), 0),
        2
    ) AS mortality_rate,

    -- Hospitalizaciones resultantes
    SUM(CASE WHEN d.hospitalization_id IS NOT NULL THEN 1 ELSE 0 END) AS resulting_hospitalizations,

    -- Promedio de días de hospitalización
    ROUND(AVG(
        CASE
            WHEN h.id IS NOT NULL THEN DATEDIFF(h.actual_discharge_date, h.admission_date)
            ELSE NULL
        END
    ), 1) AS avg_hospitalization_days

FROM diagnoses d
INNER JOIN cie10_codes c ON d.cie10_code_id = c.id
INNER JOIN cie10_categories cc ON c.category_id = cc.id
INNER JOIN patients p ON d.patient_id = p.id
LEFT JOIN hospitalizations h ON d.hospitalization_id = h.id
GROUP BY
    cc.id, cc.code, cc.name,
    c.id, c.code, c.name, c.gender, c.age_range
ORDER BY COUNT(d.id) DESC
LIMIT 50;
