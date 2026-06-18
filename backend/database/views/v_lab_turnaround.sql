-- Vista de tiempo de respuesta de laboratorio
-- Muestra el tiempo promedio de procesamiento de análisis

CREATE OR REPLACE VIEW v_lab_turnaround AS
SELECT
    lo.id AS order_id,
    lo.order_number,
    lo.order_date,
    lo.status AS order_status,
    lo.priority,

    -- Tiempo de procesamiento
    CASE
        WHEN lo.status = 'completed' AND lo.completed_at IS NOT NULL THEN
            TIMESTAMPDIFF(HOUR, lo.order_date, lo.completed_at)
        WHEN lo.status = 'in_progress' THEN
            TIMESTAMPDIFF(HOUR, lo.order_date, NOW())
        ELSE NULL
    END AS turnaround_hours,

    CASE
        WHEN lo.status = 'completed' AND lo.completed_at IS NOT NULL THEN
            TIMESTAMPDIFF(MINUTE, lo.order_date, lo.completed_at)
        WHEN lo.status = 'in_progress' THEN
            TIMESTAMPDIFF(MINUTE, lo.order_date, NOW())
        ELSE NULL
    END AS turnaround_minutes,

    -- Tiempo por tipo de análisis
    (SELECT GROUP_CONCAT(DISTINCT lo2.test_type SEPARATOR ', ')
     FROM lab_order_items lo2
     WHERE lo2.order_id = lo.id) AS test_types,

    -- Categoría del análisis
    (SELECT GROUP_CONCAT(DISTINCT lt.category SEPARATOR ', ')
     FROM lab_order_items loi
     INNER JOIN lab_tests lt ON loi.test_id = lt.id
     WHERE loi.order_id = lo.id) AS test_categories,

    -- Información del paciente
    p.id AS patient_id,
    CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
    p.ci AS patient_ci,

    -- Información del médico
    u.id AS doctor_id,
    u.name AS doctor_name,
    s.name AS specialty_name,

    -- Tiempo objetivo según prioridad
    CASE lo.priority
        WHEN 'urgent' THEN 2
        WHEN 'high' THEN 4
        WHEN 'normal' THEN 24
        WHEN 'low' THEN 48
        ELSE 24
    END AS target_hours,

    -- Cumplimiento del tiempo objetivo
    CASE
        WHEN lo.status = 'completed' AND lo.completed_at IS NOT NULL THEN
            CASE
                WHEN TIMESTAMPDIFF(HOUR, lo.order_date, lo.completed_at) <=
                     CASE lo.priority
                         WHEN 'urgent' THEN 2
                         WHEN 'high' THEN 4
                         WHEN 'normal' THEN 24
                         WHEN 'low' THEN 48
                         ELSE 24
                     END THEN 'Cumplido'
                ELSE 'No Cumplido'
            END
        ELSE 'Pendiente'
    END AS sla_status,

    lo.created_at,
    lo.updated_at,
    lo.completed_at

FROM lab_orders lo
INNER JOIN patients p ON lo.patient_id = p.id
INNER JOIN users u ON lo.ordered_by = u.id
LEFT JOIN specialties s ON u.specialty_id = s.id
ORDER BY lo.order_date DESC;
