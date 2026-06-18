-- Vista mensual de reporte de farmacia
-- Muestra movimientos de medicamentos por mes

CREATE OR REPLACE VIEW v_monthly_pharmacy_report AS
SELECT
    DATE_FORMAT(md.created_at, '%Y-%m') AS report_month,
    YEAR(md.created_at) AS report_year,
    MONTH(md.created_at) AS report_month_num,

    -- Resumen de dispensación
    COUNT(DISTINCT md.id) AS total_dispensations,
    COUNT(DISTINCT md.prescription_id) AS prescriptions_dispensed,
    COUNT(DISTINCT md.patient_id) AS unique_patients,

    -- Medicamentos dispensados
    SUM(md.quantity) AS total_medications_dispensed,
    COUNT(DISTINCT md.medication_id) AS unique_medications,

    -- Montos
    COALESCE(SUM(md.total_price), 0) AS total_revenue,
    ROUND(AVG(md.total_price), 2) AS avg_dispensation_value,
    ROUND(
        COALESCE(SUM(md.total_price), 0) / NULLIF(COUNT(DISTINCT md.patient_id), 0),
        2
    ) AS avg_revenue_per_patient,

    -- Medicamentos más dispensados
    (SELECT m.name
     FROM medication_dispensations md2
     INNER JOIN medications m ON md2.medication_id = m.id
     WHERE MONTH(md2.created_at) = MONTH(md.created_at)
        AND YEAR(md2.created_at) = YEAR(md.created_at)
     GROUP BY m.name
     ORDER BY SUM(md2.quantity) DESC
     LIMIT 1) AS most_dispensed_medication,

    -- Top 5 categorías
    (SELECT GROUP_CONCAT(DISTINCT m.category ORDER BY COUNT(*) DESC SEPARATOR ', ')
     FROM medication_dispensations md3
     INNER JOIN medications m ON md3.medication_id = m.id
     WHERE MONTH(md3.created_at) = MONTH(md.created_at)
        AND YEAR(md3.created_at) = YEAR(md.created_at)
     GROUP BY m.category
     LIMIT 5) AS top_categories,

    -- Con receta vs sin receta
    SUM(CASE WHEN md.requires_prescription = 1 THEN 1 ELSE 0 END) AS with_prescription,
    SUM(CASE WHEN md.requires_prescription = 0 THEN 1 ELSE 0 END) AS without_prescription,

    -- Promedio de medicamentos por dispensación
    ROUND(
        SUM(md.quantity) / NULLIF(COUNT(DISTINCT md.id), 0),
        1
    ) AS avg_medications_per_dispensation,

    -- Días con más actividad
    (SELECT DAYNAME(md4.created_at)
     FROM medication_dispensations md4
     WHERE MONTH(md4.created_at) = MONTH(md.created_at)
        AND YEAR(md4.created_at) = YEAR(md.created_at)
     GROUP BY DAYNAME(md4.created_at)
     ORDER BY COUNT(*) DESC
     LIMIT 1) AS busiest_day,

    -- Hora pico
    (SELECT HOUR(md5.created_at)
     FROM medication_dispensations md5
     WHERE MONTH(md5.created_at) = MONTH(md.created_at)
        AND YEAR(md5.created_at) = YEAR(md.created_at)
     GROUP BY HOUR(md5.created_at)
     ORDER BY COUNT(*) DESC
     LIMIT 1) AS peak_hour

FROM medication_dispensations md
WHERE md.created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
GROUP BY DATE_FORMAT(md.created_at, '%Y-%m'), YEAR(md.created_at), MONTH(md.created_at)
ORDER BY report_month DESC;
