-- Procedimiento para reporte de cartera y morosidad
-- Genera un reporte de facturas pendientes de pago

DELIMITER //

CREATE PROCEDURE sp_cash_arrears(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_min_days_overdue INT,
    IN p_max_days_overdue INT
)
BEGIN
    -- Resumen general de cartera
    SELECT
        'RESUMEN CARTERA' AS report_section,
        COUNT(*) AS total_pending_invoices,
        COUNT(DISTINCT patient_id) AS unique_patients,
        COALESCE(SUM(total_amount), 0) AS total_pending_amount,
        COALESCE(AVG(total_amount), 0) AS avg_invoice_amount,
        COALESCE(MIN(total_amount), 0) AS min_invoice_amount,
        COALESCE(MAX(total_amount), 0) AS max_invoice_amount,
        ROUND(AVG(DATEDIFF(CURDATE(), invoice_date)), 0) AS avg_days_overdue
    FROM invoices
    WHERE payment_status IN ('pending', 'partial')
        AND invoice_date BETWEEN p_start_date AND p_end_date;

    -- Distribución por días de mora
    SELECT
        CASE
            WHEN DATEDIFF(CURDATE(), invoice_date) BETWEEN 0 AND 30 THEN '1-30 días'
            WHEN DATEDIFF(CURDATE(), invoice_date) BETWEEN 31 AND 60 THEN '31-60 días'
            WHEN DATEDIFF(CURDATE(), invoice_date) BETWEEN 61 AND 90 THEN '61-90 días'
            WHEN DATEDIFF(CURDATE(), invoice_date) BETWEEN 91 AND 120 THEN '91-120 días'
            WHEN DATEDIFF(CURDATE(), invoice_date) > 120 THEN 'Más de 120 días'
            ELSE 'Vencida'
        END AS aging_bucket,
        COUNT(*) AS invoice_count,
        COALESCE(SUM(total_amount), 0) AS total_amount,
        COUNT(DISTINCT patient_id) AS unique_patients,
        ROUND(
            COALESCE(SUM(total_amount), 0) * 100.0 /
            NULLIF((SELECT SUM(total_amount) FROM invoices WHERE payment_status IN ('pending', 'partial')), 0),
            2
        ) AS percentage
    FROM invoices
    WHERE payment_status IN ('pending', 'partial')
        AND invoice_date BETWEEN p_start_date AND p_end_date
    GROUP BY aging_bucket
    ORDER BY
        CASE aging_bucket
            WHEN '1-30 días' THEN 1
            WHEN '31-60 días' THEN 2
            WHEN '61-90 días' THEN 3
            WHEN '91-120 días' THEN 4
            WHEN 'Más de 120 días' THEN 5
            ELSE 6
        END;

    -- Top deudores
    SELECT
        p.id AS patient_id,
        CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
        p.ci AS patient_ci,
        p.phone AS patient_phone,
        p.email AS patient_email,
        COUNT(i.id) AS pending_invoices,
        COALESCE(SUM(i.total_amount), 0) AS total_debt,
        MIN(i.invoice_date) AS oldest_invoice_date,
        MAX(i.invoice_date) AS newest_invoice_date,
        ROUND(AVG(DATEDIFF(CURDATE(), i.invoice_date)), 0) AS avg_days_overdue,
        MAX(DATEDIFF(CURDATE(), i.invoice_date)) AS max_days_overdue
    FROM invoices i
    INNER JOIN patients p ON i.patient_id = p.id
    WHERE i.payment_status IN ('pending', 'partial')
        AND i.invoice_date BETWEEN p_start_date AND p_end_date
    GROUP BY p.id, p.first_name, p.last_name, p.ci, p.phone, p.email
    HAVING total_debt > 0
    ORDER BY total_debt DESC
    LIMIT 20;

    -- Facturas por mes de emisión
    SELECT
        DATE_FORMAT(invoice_date, '%Y-%m') AS invoice_month,
        COUNT(*) AS total_invoices,
        COALESCE(SUM(total_amount), 0) AS total_amount,
        COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END), 0) AS paid_amount,
        COALESCE(SUM(CASE WHEN payment_status IN ('pending', 'partial') THEN total_amount ELSE 0 END), 0) AS pending_amount,
        ROUND(
            COALESCE(SUM(CASE WHEN payment_status IN ('pending', 'partial') THEN total_amount ELSE 0 END), 0) * 100.0 /
            NULLIF(SUM(total_amount), 0), 2
        ) AS collection_rate
    FROM invoices
    WHERE invoice_date BETWEEN p_start_date AND p_end_date
    GROUP BY DATE_FORMAT(invoice_date, '%Y-%m')
    ORDER BY invoice_month DESC;

    -- Pacientes con mayor morosidad
    SELECT
        p.id AS patient_id,
        CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
        p.ci AS patient_ci,
        p.phone AS patient_phone,
        COUNT(i.id) AS historical_pending,
        COALESCE(SUM(i.total_amount), 0) AS historical_amount,
        SUM(CASE WHEN i.payment_status = 'paid' THEN 1 ELSE 0 END) AS paid_invoices,
        ROUND(
            SUM(CASE WHEN i.payment_status = 'paid' THEN 1 ELSE 0 END) * 100.0 /
            NULLIF(COUNT(i.id), 0), 2
        ) AS payment_rate
    FROM invoices i
    INNER JOIN patients p ON i.patient_id = p.id
    WHERE i.invoice_date BETWEEN DATE_SUB(p_start_date, INTERVAL 1 YEAR) AND p_end_date
    GROUP BY p.id, p.first_name, p.last_name, p.ci, p.phone
    HAVING historical_pending > 2 AND payment_rate < 50
    ORDER BY historical_amount DESC
    LIMIT 15;
END //

DELIMITER ;
