-- Procedimiento para estadísticas de citas médicas
-- Genera un análisis completo del rendimiento de citas

DELIMITER //

CREATE PROCEDURE sp_appointment_stats(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_doctor_id INT,
    IN p_specialty_id INT
)
BEGIN
    -- Resumen general de citas
    SELECT
        'RESUMEN DE CITAS' AS report_section,
        COUNT(*) AS total_appointments,
        SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END) AS scheduled,
        SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled,
        SUM(CASE WHEN status = 'no_show' THEN 1 ELSE 0 END) AS no_shows,
        COUNT(DISTINCT patient_id) AS unique_patients,
        COUNT(DISTINCT doctor_id) AS active_doctors,
        ROUND(
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) * 100.0 /
            NULLIF(COUNT(*), 0), 2
        ) AS completion_rate,
        ROUND(
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) * 100.0 /
            NULLIF(COUNT(*), 0), 2
        ) AS cancellation_rate,
        ROUND(
            SUM(CASE WHEN status = 'no_show' THEN 1 ELSE 0 END) * 100.0 /
            NULLIF(COUNT(*), 0), 2
        ) AS no_show_rate,
        ROUND(AVG(TIMESTAMPDIFF(MINUTE,
            CONCAT(appointment_date, ' ', start_time),
            CONCAT(appointment_date, ' ', end_time)
        )), 0) AS avg_duration_minutes
    FROM appointments
    WHERE appointment_date BETWEEN p_start_date AND p_end_date
        AND (p_doctor_id IS NULL OR doctor_id = p_doctor_id)
        AND (p_specialty_id IS NULL OR specialty_id = p_specialty_id);

    -- Rendimiento por médico
    SELECT
        u.id AS doctor_id,
        u.name AS doctor_name,
        s.name AS specialty_name,
        COUNT(*) AS total_appointments,
        SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) AS completed,
        SUM(CASE WHEN a.status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled,
        SUM(CASE WHEN a.status = 'no_show' THEN 1 ELSE 0 END) AS no_shows,
        COUNT(DISTINCT a.patient_id) AS unique_patients,
        ROUND(
            SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) * 100.0 /
            NULLIF(COUNT(*), 0), 2
        ) AS completion_rate,
        ROUND(AVG(TIMESTAMPDIFF(MINUTE,
            CONCAT(a.appointment_date, ' ', a.start_time),
            CONCAT(a.appointment_date, ' ', a.end_time)
        )), 0) AS avg_duration_minutes,
        -- Citas por día promedio
        ROUND(COUNT(*) / NULLIF(DATEDIFF(p_end_date, p_start_date) + 1, 0), 1) AS avg_appointments_per_day
    FROM appointments a
    INNER JOIN users u ON a.doctor_id = u.id
    LEFT JOIN specialties s ON u.specialty_id = s.id
    WHERE a.appointment_date BETWEEN p_start_date AND p_end_date
        AND (p_doctor_id IS NULL OR a.doctor_id = p_doctor_id)
        AND (p_specialty_id IS NULL OR a.specialty_id = p_specialty_id)
    GROUP BY u.id, u.name, s.name
    ORDER BY total_appointments DESC;

    -- Distribución por día de la semana
    SELECT
        DAYNAME(appointment_date) AS day_name,
        DAYOFWEEK(appointment_date) AS day_number,
        COUNT(*) AS total_appointments,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled,
        SUM(CASE WHEN status = 'no_show' THEN 1 ELSE 0 END) AS no_shows,
        COUNT(DISTINCT doctor_id) AS active_doctors,
        ROUND(AVG(TIMESTAMPDIFF(MINUTE,
            CONCAT(appointment_date, ' ', start_time),
            CONCAT(appointment_date, ' ', end_time)
        )), 0) AS avg_duration_minutes
    FROM appointments
    WHERE appointment_date BETWEEN p_start_date AND p_end_date
        AND (p_doctor_id IS NULL OR doctor_id = p_doctor_id)
        AND (p_specialty_id IS NULL OR specialty_id = p_specialty_id)
    GROUP BY DAYNAME(appointment_date), DAYOFWEEK(appointment_date)
    ORDER BY day_number;

    -- Distribución por hora del día
    SELECT
        start_time AS appointment_hour,
        COUNT(*) AS total_appointments,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled,
        ROUND(
            COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*) FROM appointments WHERE appointment_date BETWEEN p_start_date AND p_end_date), 0),
            2
        ) AS percentage,
        COUNT(DISTINCT doctor_id) AS active_doctors
    FROM appointments
    WHERE appointment_date BETWEEN p_start_date AND p_end_date
        AND (p_doctor_id IS NULL OR doctor_id = p_doctor_id)
        AND (p_specialty_id IS NULL OR specialty_id = p_specialty_id)
    GROUP BY start_time
    ORDER BY start_time;

    -- Citas por especialidad
    SELECT
        s.name AS specialty_name,
        COUNT(*) AS total_appointments,
        SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) AS completed,
        SUM(CASE WHEN a.status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled,
        SUM(CASE WHEN a.status = 'no_show' THEN 1 ELSE 0 END) AS no_shows,
        COUNT(DISTINCT a.patient_id) AS unique_patients,
        COUNT(DISTINCT a.doctor_id) AS doctors_count,
        ROUND(
            SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) * 100.0 /
            NULLIF(COUNT(*), 0), 2
        ) AS completion_rate
    FROM appointments a
    INNER JOIN specialties s ON a.specialty_id = s.id
    WHERE a.appointment_date BETWEEN p_start_date AND p_end_date
        AND (p_doctor_id IS NULL OR a.doctor_id = p_doctor_id)
        AND (p_specialty_id IS NULL OR a.specialty_id = p_specialty_id)
    GROUP BY s.id, s.name
    ORDER BY total_appointments DESC;

    -- Tendencia diaria de citas
    SELECT
        appointment_date,
        DAYNAME(appointment_date) AS day_name,
        COUNT(*) AS total_appointments,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled,
        SUM(CASE WHEN status = 'no_show' THEN 1 ELSE 0 END) AS no_shows,
        COUNT(DISTINCT doctor_id) AS active_doctors,
        ROUND(
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) * 100.0 /
            NULLIF(COUNT(*), 0), 2
        ) AS completion_rate
    FROM appointments
    WHERE appointment_date BETWEEN p_start_date AND p_end_date
        AND (p_doctor_id IS NULL OR doctor_id = p_doctor_id)
        AND (p_specialty_id IS NULL OR specialty_id = p_specialty_id)
    GROUP BY appointment_date
    ORDER BY appointment_date;

    -- Razones de cancelación más comunes
    SELECT
        cancellation_reason,
        COUNT(*) AS cancellation_count,
        ROUND(
            COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*) FROM appointments WHERE status = 'cancelled' AND appointment_date BETWEEN p_start_date AND p_end_date), 0),
            2
        ) AS percentage,
        COUNT(DISTINCT doctor_id) AS affected_doctors
    FROM appointments
    WHERE status = 'cancelled'
        AND appointment_date BETWEEN p_start_date AND p_end_date
        AND cancellation_reason IS NOT NULL
        AND cancellation_reason != ''
        AND (p_doctor_id IS NULL OR doctor_id = p_doctor_id)
        AND (p_specialty_id IS NULL OR specialty_id = p_specialty_id)
    GROUP BY cancellation_reason
    ORDER BY cancellation_count DESC
    LIMIT 10;

    -- Pacientes frecuentes (más de 3 citas)
    SELECT
        p.id AS patient_id,
        CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
        p.ci AS patient_ci,
        COUNT(*) AS total_appointments,
        COUNT(DISTINCT a.doctor_id) AS different_doctors,
        COUNT(DISTINCT a.specialty_id) AS different_specialties,
        MIN(a.appointment_date) AS first_appointment,
        MAX(a.appointment_date) AS last_appointment,
        ROUND(AVG(TIMESTAMPDIFF(DAY, MIN(a.appointment_date), MAX(a.appointment_date)) / NULLIF(COUNT(*) - 1, 0)), 1) AS avg_days_between
    FROM appointments a
    INNER JOIN patients p ON a.patient_id = p.id
    WHERE a.appointment_date BETWEEN p_start_date AND p_end_date
        AND (p_doctor_id IS NULL OR a.doctor_id = p_doctor_id)
        AND (p_specialty_id IS NULL OR a.specialty_id = p_specialty_id)
    GROUP BY p.id, p.first_name, p.last_name, p.ci
    HAVING total_appointments > 3
    ORDER BY total_appointments DESC
    LIMIT 20;
END //

DELIMITER ;
