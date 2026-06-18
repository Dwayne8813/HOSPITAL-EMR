-- Vista de ingresos diarios
-- Muestra los ingresos por día con desglose por categorías

CREATE OR REPLACE VIEW v_daily_revenue AS
SELECT
    DATE(i.invoice_date) AS revenue_date,

    -- Resumen de facturación
    COUNT(DISTINCT i.id) AS total_invoices,
    COUNT(DISTINCT i.patient_id) AS unique_patients,
    COALESCE(SUM(i.subtotal), 0) AS total_subtotal,
    COALESCE(SUM(i.discount), 0) AS total_discounts,
    COALESCE(SUM(i.tax_amount), 0) AS total_taxes,
    COALESCE(SUM(i.total_amount), 0) AS total_revenue,

    -- Ingresos por estado de pago
    COALESCE(SUM(CASE WHEN i.payment_status = 'paid' THEN i.total_amount ELSE 0 END), 0) AS paid_amount,
    COALESCE(SUM(CASE WHEN i.payment_status = 'partial' THEN i.total_amount ELSE 0 END), 0) AS partial_amount,
    COALESCE(SUM(CASE WHEN i.payment_status = 'pending' THEN i.total_amount ELSE 0 END), 0) AS pending_amount,
    COALESCE(SUM(CASE WHEN i.payment_status = 'cancelled' THEN i.total_amount ELSE 0 END), 0) AS cancelled_amount,

    -- Conteo por estado de pago
    SUM(CASE WHEN i.payment_status = 'paid' THEN 1 ELSE 0 END) AS paid_invoices,
    SUM(CASE WHEN i.payment_status = 'partial' THEN 1 ELSE 0 END) AS partial_invoices,
    SUM(CASE WHEN i.payment_status = 'pending' THEN 1 ELSE 0 END) AS pending_invoices,
    SUM(CASE WHEN i.payment_status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_invoices,

    -- Promedio por factura
    ROUND(
        COALESCE(SUM(i.total_amount), 0) / NULLIF(COUNT(DISTINCT i.id), 0), 2
    ) AS avg_invoice_amount,

    -- Promedio por paciente
    ROUND(
        COALESCE(SUM(i.total_amount), 0) / NULLIF(COUNT(DISTINCT i.patient_id), 0), 2
    ) AS avg_revenue_per_patient,

    -- Impuesto promedio
    ROUND(AVG(i.tax_amount), 2) AS avg_tax_amount,

    -- Porcentaje de impuestos
    CASE
        WHEN SUM(i.subtotal) > 0 THEN
            ROUND((SUM(i.tax_amount) / SUM(i.subtotal)) * 100, 2)
        ELSE 0
    END AS tax_percentage,

    -- Día de la semana
    DAYNAME(DATE(i.invoice_date)) AS day_of_week,
    -- Número del día de la semana
    DAYOFWEEK(DATE(i.invoice_date)) AS day_number

FROM invoices i
WHERE i.invoice_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY DATE(i.invoice_date)
ORDER BY DATE(i.invoice_date) DESC;
