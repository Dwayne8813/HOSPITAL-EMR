-- Vista de citas pendientes
-- Muestra todas las citas programadas o confirmadas para los próximos días

CREATE OR REPLACE VIEW v_pending_appointments AS
SELECT
    a.id AS appointment_id,
    a.appointment_date,
    a.start_time,
    a.end_time,
    a.status,
    a.reason,
    a.notes,

    -- Información del paciente
    p.id AS patient_id,
    CONCAT(p.first_name, ' ', p.last_name, ' ', COALESCE(p.second_last_name, '')) AS patient_name,
    p.ci AS patient_ci,
    p.phone AS patient_phone,
    p.email AS patient_email,
    p.gender AS patient_gender,
    TIMESTAMPDIFF(YEAR, p.birth_date, CURDATE()) AS patient_age,

    -- Información del médico
    u.id AS doctor_id,
    u.name AS doctor_name,
    u.email AS doctor_email,
    u.phone AS doctor_phone,

    -- Información de la especialidad
    s.name AS specialty_name,

    -- Tiempo de espera estimado
    CASE
        WHEN a.appointment_date = CURDATE() AND a.start_time > TIME(NOW()) THEN 'Hoy'
        WHEN a.appointment_date = CURDATE() AND a.start_time <= TIME(NOW()) THEN 'Retrasada'
        WHEN a.appointment_date < CURDATE() THEN 'Vencida'
        WHEN DATEDIFF(a.appointment_date, CURDATE()) = 1 THEN 'Mañana'
        WHEN DATEDIFF(a.appointment_date, CURDATE()) BETWEEN 2 AND 7 THEN 'Esta semana'
        ELSE 'Próxima semana'
    END AS urgency,

    -- Días hasta la cita
    DATEDIFF(a.appointment_date, CURDATE()) AS days_until_appointment,

    a.created_at,
    a.updated_at

FROM appointments a
INNER JOIN patients p ON a.patient_id = p.id
INNER JOIN users u ON a.doctor_id = u.id
LEFT JOIN specialties s ON a.specialty_id = s.id
WHERE a.status IN ('scheduled', 'confirmed')
    AND a.appointment_date >= CURDATE()
    AND p.status = 'active'
    AND u.is_active = 1
ORDER BY a.appointment_date ASC, a.start_time ASC;
