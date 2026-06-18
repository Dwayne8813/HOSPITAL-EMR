-- Procedimiento para reporte de ventas
-- Genera un reporte de servicios y productos vendidos

DELIMITER //

CREATE PROCEDURE sp_sales_report(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_category VARCHAR(50)
)
BEGIN
    -- Resumen de ventas
    SELECT
        'RESUMEN DE VENTAS' AS report_section,
        COUNT(DISTINCT i.id) AS total_invoices,
        COUNT(DISTINCT i.patient_id) AS unique_patients,
        COALESCE(SUM(i.subtotal), 0) AS total_subtotal,
        COALESCE(SUM(i.discount), 0) AS total_discounts,
        COALESCE(SUM(i.tax_amount), 0) AS total_taxes,
        COALESCE(SUM(i.total_amount), 0) AS total_revenue,
        ROUND(AVG(i.total_amount), 2) AS avg_invoice_value,
        SUM(CASE WHEN i.payment_status = 'paid' THEN 1 ELSE 0 END) AS paid_invoices,
        SUM(CASE WHEN i.payment_status = 'pending' THEN 1 ELSE 0 END) AS pending_invoices,
        SUM(CASE WHEN i.payment_status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_invoices
    FROM invoices i
    WHERE i.invoice_date BETWEEN p_start_date AND p_end_date;

    -- Ventas por categoría de servicio
    SELECT
        sc.category AS service_category,
        COUNT(ii.id) AS items_sold,
        COUNT(DISTINCT i.patient_id) AS unique_patients,
        COALESCE(SUM(ii.quantity), 0) AS total_quantity,
        COALESCE(SUM(ii.total_price), 0) AS total_revenue,
        ROUND(AVG(ii.unit_price), 2) AS avg_unit_price,
        ROUND(
            COALESCE(SUM(ii.total_price), 0) * 100.0 /
            NULLIF((SELECT SUM(ii2.total_price) FROM invoice_items ii2 INNER JOIN invoices i2 ON ii2.invoice_id = i2.id WHERE i2.invoice_date BETWEEN p_start_date AND p_end_date), 0),
            2
        ) AS percentage_of_total
    FROM invoice_items ii
    INNER JOIN invoices i ON ii.invoice_id = i.id
    INNER JOIN service_catalog sc ON ii.service_id = sc.id
    WHERE i.invoice_date BETWEEN p_start_date AND p_end_date
        AND (p_category IS NULL OR sc.category = p_category)
    GROUP BY sc.category
    ORDER BY total_revenue DESC;

    -- Top servicios vendidos
    SELECT
        sc.name AS service_name,
        sc.code AS service_code,
        sc.category AS service_category,
        sc.price AS unit_price,
        COUNT(ii.id) AS times_sold,
        COALESCE(SUM(ii.quantity), 0) AS total_quantity,
        COALESCE(SUM(ii.total_price), 0) AS total_revenue,
        COUNT(DISTINCT i.patient_id) AS unique_patients
    FROM invoice_items ii
    INNER JOIN invoices i ON ii.invoice_id = i.id
    INNER JOIN service_catalog sc ON ii.service_id = sc.id
    WHERE i.invoice_date BETWEEN p_start_date AND p_end_date
        AND (p_category IS NULL OR sc.category = p_category)
    GROUP BY sc.id, sc.name, sc.code, sc.category, sc.price
    ORDER BY total_revenue DESC
    LIMIT 20;

    -- Ventas por método de pago
    SELECT
        pm.name AS payment_method,
        COUNT(p.id) AS transaction_count,
        COALESCE(SUM(p.amount), 0) AS total_collected,
        ROUND(AVG(p.amount), 2) AS avg_transaction_amount,
        ROUND(
            COALESCE(SUM(p.amount), 0) * 100.0 /
            NULLIF((SELECT SUM(amount) FROM payments WHERE payment_date BETWEEN p_start_date AND CONCAT(p_end_date, ' 23:59:59')), 0),
            2
        ) AS percentage_of_total
    FROM payments p
    INNER JOIN payment_methods pm ON p.payment_method_id = pm.id
    WHERE p.payment_date BETWEEN p_start_date AND CONCAT(p_end_date, ' 23:59:59')
    GROUP BY pm.id, pm.name
    ORDER BY total_collected DESC;

    -- Tendencia diaria de ventas
    SELECT
        DATE(i.invoice_date) AS sale_date,
        DAYNAME(DATE(i.invoice_date)) AS day_name,
        COUNT(DISTINCT i.id) AS invoices_count,
        COUNT(DISTINCT i.patient_id) AS patients_count,
        COALESCE(SUM(i.total_amount), 0) AS daily_revenue,
        COALESCE(SUM(CASE WHEN i.payment_status = 'paid' THEN i.total_amount ELSE 0 END), 0) AS collected_amount,
        COALESCE(SUM(CASE WHEN i.payment_status = 'pending' THEN i.total_amount ELSE 0 END), 0) AS pending_amount
    FROM invoices i
    WHERE i.invoice_date BETWEEN p_start_date AND p_end_date
    GROUP BY DATE(i.invoice_date), DAYNAME(DATE(i.invoice_date))
    ORDER BY sale_date;

    -- Descuentos aplicados
    SELECT
        i.invoice_number,
        i.invoice_date,
        CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
        i.subtotal,
        i.discount,
        ROUND((i.discount / i.subtotal) * 100, 2) AS discount_percentage,
        i.total_amount,
        da.authorized_by,
        da.reason AS discount_reason
    FROM invoices i
    INNER JOIN patients p ON i.patient_id = p.id
    LEFT JOIN discount_authorizations da ON i.id = da.invoice_id
    WHERE i.invoice_date BETWEEN p_start_date AND p_end_date
        AND i.discount > 0
    ORDER BY i.discount DESC
    LIMIT 20;
END //

DELIMITER ;
