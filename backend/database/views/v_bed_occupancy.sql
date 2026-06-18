-- Vista de ocupación de camas
-- Muestra el estado actual de todas las camas del hospital

CREATE OR REPLACE VIEW v_bed_occupancy AS
SELECT
    b.id AS bed_id,
    b.number AS bed_number,
    b.bed_type,
    b.status AS bed_status,
    b.daily_rate,

    -- Información de la habitación
    r.id AS room_id,
    r.number AS room_number,
    r.name AS room_name,
    r.room_type,
    r.capacity AS room_capacity,

    -- Información del piso
    f.name AS floor_name,
    f.number AS floor_number,

    -- Información del edificio
    bu.name AS building_name,

    -- Información del paciente (si está ocupada)
    h.id AS hospitalization_id,
    p.id AS patient_id,
    CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
    p.ci AS patient_ci,
    h.admission_date,
    h.expected_discharge_date,
    DATEDIFF(CURDATE(), h.admission_date) AS days_hospitalized,

    -- Información del médico
    u.name AS doctor_name,
    u.id AS doctor_id,

    -- Diagnóstico
    h.admission_diagnosis,
    h.status AS hospitalization_status,

    -- Días de estadía estimados
    CASE
        WHEN h.expected_discharge_date IS NOT NULL THEN
            DATEDIFF(h.expected_discharge_date, h.admission_date)
        ELSE NULL
    END AS estimated_stay_days,

    -- Costo acumulado estimado
    ROUND(
        DATEDIFF(CURDATE(), h.admission_date) * b.daily_rate, 2
    ) AS estimated_cost,

    b.updated_at AS last_update

FROM beds b
INNER JOIN rooms r ON b.room_id = r.id
INNER JOIN floors f ON r.floor_id = f.id
INNER JOIN buildings bu ON f.building_id = bu.id
LEFT JOIN hospitalizations h ON b.id = h.bed_id AND h.status = 'active'
LEFT JOIN patients p ON h.patient_id = p.id
LEFT JOIN users u ON h.doctor_id = u.id
WHERE b.status != 'maintenance'
ORDER BY
    bu.name,
    f.number,
    r.number,
    b.number;
