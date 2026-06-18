-- Vista de estadísticas de hospitalización
-- Muestra métricas de hospitalización por período

CREATE OR REPLACE VIEW v_hospitalization_stats AS
SELECT
    DATE_FORMAT(h.admission_date, '%Y-%m') AS report_month,
    YEAR(h.admission_date) AS report_year,
    MONTH(h.admission_date) AS report_month_num,

    -- Conteo de hospitalizaciones
    COUNT(*) AS total_hospitalizations,

    -- Hospitalizaciones activas
    SUM(CASE WHEN h.status = 'active' THEN 1 ELSE 0 END) AS active_hospitalizations,

    -- Altas completadas
    SUM(CASE WHEN h.status = 'discharged' THEN 1 ELSE 0 END) AS completed_hospitalizations,

    -- Transferencias
    SUM(CASE WHEN h.status = 'transferred' THEN 1 ELSE 0 END) AS transferred_hospitalizations,

    -- Defunciones
    SUM(CASE WHEN h.status = 'deceased' THEN 1 ELSE 0 END) AS deceased_hospitalizations,

    -- Pacientes únicos
    COUNT(DISTINCT h.patient_id) AS unique_patients,

    -- Tiempo promedio de hospitalización (días)
    ROUND(AVG(
        CASE
            WHEN h.actual_discharge_date IS NOT NULL THEN
                DATEDIFF(h.actual_discharge_date, h.admission_date)
            WHEN h.status = 'active' THEN
                DATEDIFF(CURDATE(), h.admission_date)
            ELSE NULL
        END
    ), 1) AS avg_length_of_stay,

    -- Tiempo máximo de hospitalización
    MAX(
        CASE
            WHEN h.actual_discharge_date IS NOT NULL THEN
                DATEDIFF(h.actual_discharge_date, h.admission_date)
            WHEN h.status = 'active' THEN
                DATEDIFF(CURDATE(), h.admission_date)
            ELSE 0
        END
    ) AS max_length_of_stay,

    -- Tiempo mínimo de hospitalización
    MIN(
        CASE
            WHEN h.actual_discharge_date IS NOT NULL THEN
                DATEDIFF(h.actual_discharge_date, h.admission_date)
            ELSE NULL
        END
    ) AS min_length_of_stay,

    -- Ocupación promedio de camas
    ROUND(AVG(
        CASE
            WHEN h.status = 'active' THEN 1
            ELSE 0
        END
    ) * 100, 2) AS avg_bed_utilization,

    -- Tasa de mortalidad
    ROUND(
        SUM(CASE WHEN h.status = 'deceased' THEN 1 ELSE 0 END) * 100.0 /
        NULLIF(COUNT(*), 0),
        2
    ) AS mortality_rate,

    -- Tasa de readmisión (pacientes con más de 1 hospitalización en 30 días)
    (SELECT COUNT(DISTINCT h2.patient_id)
     FROM hospitalizations h2
     WHERE h2.patient_id IN (
         SELECT h3.patient_id
         FROM hospitalizations h3
         WHERE YEAR(h3.admission_date) = YEAR(h.admission_date)
            AND MONTH(h3.admission_date) = MONTH(h.admission_date)
     )
     AND h2.admission_date > h.admission_date
     AND DATEDIFF(h2.admission_date, h.admission_date) <= 30
    ) AS readmissions_within_30_days,

    -- Ingresos por hospitalización
    COALESCE(SUM(
        CASE
            WHEN h.actual_discharge_date IS NOT NULL THEN
                DATEDIFF(h.actual_discharge_date, h.admission_date) * b.daily_rate
            WHEN h.status = 'active' THEN
                DATEDIFF(CURDATE(), h.admission_date) * b.daily_rate
            ELSE 0
        END
    ), 0) AS total_revenue,

    -- Costo promedio por hospitalización
    ROUND(AVG(
        CASE
            WHEN h.actual_discharge_date IS NOT NULL THEN
                DATEDIFF(h.actual_discharge_date, h.admission_date) * b.daily_rate
            WHEN h.status = 'active' THEN
                DATEDIFF(CURDATE(), h.admission_date) * b.daily_rate
            ELSE NULL
        END
    ), 2) AS avg_cost_per_hospitalization,

    -- Diagnósticos principales
    (SELECT GROUP_CONCAT(DISTINCT cie.name SEPARATOR ', ')
     FROM diagnoses d
     INNER JOIN cie10_codes cie ON d.cie10_code_id = cie.id
     WHERE d.hospitalization_id IN (
         SELECT h4.id
         FROM hospitalizations h4
         WHERE YEAR(h4.admission_date) = YEAR(h.admission_date)
            AND MONTH(h4.admission_date) = MONTH(h.admission_date)
     )
     GROUP BY cie.name
     ORDER BY COUNT(*) DESC
     LIMIT 5) AS top_diagnoses,

    -- Distribución por género
    ROUND(
        SUM(CASE WHEN p.gender = 'M' THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(*), 0),
        1
    ) AS male_percentage,
    ROUND(
        SUM(CASE WHEN p.gender = 'F' THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(*), 0),
        1
    ) AS female_percentage,

    -- Edad promedio
    ROUND(AVG(TIMESTAMPDIFF(YEAR, p.birth_date, h.admission_date)), 1) AS avg_patient_age

FROM hospitalizations h
INNER JOIN patients p ON h.patient_id = p.id
LEFT JOIN beds b ON h.bed_id = b.id
WHERE h.admission_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
GROUP BY DATE_FORMAT(h.admission_date, '%Y-%m'), YEAR(h.admission_date), MONTH(h.admission_date)
ORDER BY report_month DESC;
