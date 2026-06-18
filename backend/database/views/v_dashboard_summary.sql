-- Vista resumen del dashboard principal
-- Muestra estadísticas clave del hospital

CREATE OR REPLACE VIEW v_dashboard_summary AS
SELECT
    -- Total de pacientes
    (SELECT COUNT(*) FROM patients WHERE status = 'active') AS total_patients,
    -- Pacientes nuevos hoy
    (SELECT COUNT(*) FROM patients WHERE DATE(created_at) = CURDATE()) AS new_patients_today,
    -- Pacientes nuevos este mes
    (SELECT COUNT(*) FROM patients WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())) AS new_patients_month,

    -- Total de médicos activos
    (SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug = 'doctor' AND u.is_active = 1) AS total_doctors,

    -- Citas de hoy
    (SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE()) AS appointments_today,
    -- Citas completadas hoy
    (SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE() AND status = 'completed') AS appointments_completed_today,
    -- Citas pendientes hoy
    (SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE() AND status IN ('scheduled', 'confirmed')) AS appointments_pending_today,
    -- Citas canceladas hoy
    (SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE() AND status = 'cancelled') AS appointments_cancelled_today,

    -- Camas totales
    (SELECT COUNT(*) FROM beds WHERE status != 'maintenance') AS total_beds,
    -- Camas ocupadas
    (SELECT COUNT(*) FROM beds WHERE status = 'occupied') AS occupied_beds,
    -- Camas disponibles
    (SELECT COUNT(*) FROM beds WHERE status = 'available') AS available_beds,
    -- Tasa de ocupación
    ROUND(
        (SELECT COUNT(*) FROM beds WHERE status = 'occupied') /
        NULLIF((SELECT COUNT(*) FROM beds WHERE status != 'maintenance'), 0) * 100, 2
    ) AS bed_occupancy_rate,

    -- Hospitalizaciones activas
    (SELECT COUNT(*) FROM hospitalizations WHERE status = 'active') AS active_hospitalizations,
    -- Ingresos hoy
    (SELECT COUNT(*) FROM hospitalizations WHERE DATE(admission_date) = CURDATE()) AS admissions_today,
    -- Altas hoy
    (SELECT COUNT(*) FROM hospitalizations WHERE DATE(actual_discharge_date) = CURDATE()) AS discharges_today,

    -- Ingresos del día (facturación)
    (SELECT COALESCE(SUM(total_amount), 0) FROM invoices WHERE invoice_date = CURDATE() AND payment_status != 'cancelled') AS revenue_today,
    -- Ingresos del mes
    (SELECT COALESCE(SUM(total_amount), 0) FROM invoices WHERE MONTH(invoice_date) = MONTH(CURDATE()) AND YEAR(invoice_date) = YEAR(CURDATE()) AND payment_status != 'cancelled') AS revenue_month,
    -- Ingresos del año
    (SELECT COALESCE(SUM(total_amount), 0) FROM invoices WHERE YEAR(invoice_date) = YEAR(CURDATE()) AND payment_status != 'cancelled') AS revenue_year,

    -- Facturas pendientes de pago
    (SELECT COUNT(*) FROM invoices WHERE payment_status = 'pending') AS pending_invoices,
    -- Monto total pendiente
    (SELECT COALESCE(SUM(total_amount), 0) FROM invoices WHERE payment_status = 'pending') AS pending_amount,

    -- Emergencias hoy
    (SELECT COUNT(*) FROM emergency_triage WHERE DATE(created_at) = CURDATE()) AS emergencies_today,
    -- Emergencias nivel I y II
    (SELECT COUNT(*) FROM emergency_triage WHERE DATE(created_at) = CURDATE() AND level IN (1, 2)) AS critical_emergencies_today,

    -- Análisis de laboratorio pendientes
    (SELECT COUNT(*) FROM lab_orders WHERE status = 'pending') AS pending_lab_orders,
    -- Resultados de laboratorio listos
    (SELECT COUNT(*) FROM lab_orders WHERE status = 'completed' AND DATE(updated_at) = CURDATE()) AS completed_lab_today,

    -- Medicamentos con bajo stock
    (SELECT COUNT(*) FROM medications WHERE current_stock <= min_stock AND status = 'active') AS low_stock_medications,

    -- Fecha y hora del resumen
    NOW() AS summary_date;
