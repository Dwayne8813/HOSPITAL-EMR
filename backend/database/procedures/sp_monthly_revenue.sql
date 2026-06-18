-- Procedimiento para reporte de ingresos mensuales
-- Genera un análisis completo de ingresos por período

DELIMITER //

CREATE PROCEDURE sp_monthly_revenue(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_service_category VARCHAR(50)
)
BEGIN
    -- Resumen mensual de ingresos
    SELECT
        DATE_FORMAT(invoice_date, '%Y-%m') AS revenue_month,
        YEAR(invoice_date) AS revenue_year,
        MONTH(invoice_date) AS revenue_month_num,
        MONTHNAME(invoice_date) AS month_name,
        COUNT(DISTINCT id) AS total_invoices,
        COUNT(DISTINCT patient_id) AS unique_patients,
        COALESCE(SUM(subtotal), 0) AS total_subtotal,
        COALESCE(SUM(discount), 0) AS total_discounts,
        COALESCE(SUM(tax_amount), 0) AS total_taxes,
        COALESCE(SUM(total_amount), 0) AS total_revenue,
        COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END), 0) AS collected_amount,
        COALESCE(SUM(CASE WHEN payment_status = 'pending' THEN total_amount ELSE 0 END), 0) AS pending_amount,
        ROUND(AVG(total_amount), 2) AS avg_invoice_value,
        ROUND(
            COALESCE(SUM(total_amount), 0) / NULLIF(COUNT(DISTINCT patient_id), 0),
            2
        ) AS revenue_per_patient,
        -- Comparación con mes anterior
        LAG(COALESCE(SUM(total_amount), 0)) OVER (ORDER BY DATE_FORMAT(invoice_date, '%Y-%m')) AS prev_month_revenue,
        ROUND(
            (COALESCE(SUM(total_amount), 0) -
             LAG(COALESCE(SUM(total_amount), 0)) OVER (ORDER BY DATE_FORMAT(invoice_date, '%Y-%m'))) /
            NULLIF(LAG(COALESCE(SUM(total_amount), 0)) OVER (ORDER BY DATE_FORMAT(invoice_date, '%Y-%m')), 0) * 100,
            2
        ) AS growth_percentage
    FROM invoices
    WHERE invoice_date BETWEEN p_start_date AND p_end_date
        AND payment_status != 'cancelled'
    GROUP BY DATE_FORMAT(invoice_date, '%Y-%m'), YEAR(invoice_date), MONTH(invoice_date), MONTHNAME(invoice_date)
    ORDER BY revenue_month;

    -- Ingresos por categoría de servicio
    SELECT
        sc.category AS service_category,
        COUNT(ii.id) AS items_sold,
        COALESCE(SUM(ii.quantity), 0) AS total_quantity,
        COALESCE(SUM(ii.total_price), 0) AS total_revenue,
        ROUND(
            COALESCE(SUM(ii.total_price), 0) * 100.0 /
            NULLIF((SELECT SUM(ii2.total_price) FROM invoice_items ii2 INNER JOIN invoices i2 ON ii2.invoice_id = i2.id WHERE i2.invoice_date BETWEEN p_start_date AND p_end_date AND i2.payment_status != 'cancelled'), 0),
            2
        ) AS percentage_of_total,
        ROUND(AVG(ii.unit_price), 2) AS avg_unit_price,
        COUNT(DISTINCT i.patient_id) AS unique_patients
    FROM invoice_items ii
    INNER JOIN invoices i ON ii.invoice_id = i.id
    INNER JOIN service_catalog sc ON ii.service_id = sc.id
    WHERE i.invoice_date BETWEEN p_start_date AND p_end_date
        AND i.payment_status != 'cancelled'
        AND (p_service_category IS NULL OR sc.category = p_service_category)
    GROUP BY sc.category
    ORDER BY total_revenue DESC;

    -- Top 15 servicios más rentables
    SELECT
        sc.name AS service_name,
        sc.code AS service_code,
        sc.category AS service_category,
        sc.price AS standard_price,
        COUNT(ii.id) AS times_sold,
        COALESCE(SUM(ii.quantity), 0) AS total_quantity,
        COALESCE(SUM(ii.total_price), 0) AS total_revenue,
        COALESCE(SUM(ii.discount), 0) AS total_discounts,
        COUNT(DISTINCT i.patient_id) AS unique_patients,
        ROUND(AVG(ii.unit_price), 2) AS avg_selling_price,
        ROUND(
            (sc.price - AVG(ii.unit_price)) / NULLIF(sc.price, 0) * 100,
            2
        ) AS avg_discount_percentage
    FROM invoice_items ii
    INNER JOIN invoices i ON ii.invoice_id = i.id
    INNER JOIN service_catalog sc ON ii.service_id = sc.id
    WHERE i.invoice_date BETWEEN p_start_date AND p_end_date
        AND i.payment_status != 'cancelled'
        AND (p_service_category IS NULL OR sc.category = p_service_category)
    GROUP BY sc.id, sc.name, sc.code, sc.category, sc.price
    ORDER BY total_revenue DESC
    LIMIT 15;

    -- Ingresos por método de pago
    SELECT
        pm.name AS payment_method,
        COUNT(p.id) AS transaction_count,
        COALESCE(SUM(p.amount), 0) AS total_collected,
        ROUND(AVG(p.amount), 2) AS avg_transaction,
        ROUND(
            COALESCE(SUM(p.amount), 0) * 100.0 /
            NULLIF((SELECT SUM(amount) FROM payments WHERE payment_date BETWEEN p_start_date AND CONCAT(p_end_date, ' 23:59:59')), 0),
            2
        ) AS percentage_of_total,
        MIN(p.payment_date) AS first_transaction,
        MAX(p.payment_date) AS last_transaction
    FROM payments p
    INNER JOIN payment_methods pm ON p.payment_method_id = pm.id
    WHERE p.payment_date BETWEEN p_start_date AND CONCAT(p_end_date, ' 23:59:59')
    GROUP BY pm.id, pm.name
    ORDER BY total_collected DESC;

    -- Tendencia diaria de ingresos
    SELECT
        DATE(i.invoice_date) AS revenue_date,
        DAYNAME(DATE(i.invoice_date)) AS day_name,
        COUNT(DISTINCT i.id) AS invoices_count,
        COUNT(DISTINCT i.patient_id) AS patients_count,
        COALESCE(SUM(i.total_amount), 0) AS daily_revenue,
        COALESCE(SUM(CASE WHEN i.payment_status = 'paid' THEN i.total_amount ELSE 0 END), 0) AS collected,
        COALESCE(SUM(CASE WHEN i.payment_status = 'pending' THEN i.total_amount ELSE 0 END), 0) AS pending,
        ROUND(AVG(i.total_amount), 2) AS avg_invoice_value
    FROM invoices i
    WHERE i.invoice_date BETWEEN p_start_date AND p_end_date
        AND i.payment_status != 'cancelled'
    GROUP BY DATE(i.invoice_date), DAYNAME(DATE(i.invoice_date))
    ORDER BY revenue_date;

    -- Comparativa con período anterior
    SELECT
        'Período Actual' AS period,
        COUNT(DISTINCT id) AS total_invoices,
        COALESCE(SUM(total_amount), 0) AS total_revenue,
        COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END), 0) AS collected,
        ROUND(AVG(total_amount), 2) AS avg_invoice_value
    FROM invoices
    WHERE invoice_date BETWEEN p_start_date AND p_end_date
        AND payment_status != 'cancelled'

    UNION ALL

    SELECT
        'Período Anterior' AS period,
        COUNT(DISTINCT id) AS total_invoices,
        COALESCE(SUM(total_amount), 0) AS total_revenue,
        COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END), 0) AS collected,
        ROUND(AVG(total_amount), 2) AS avg_invoice_value
    FROM invoices
    WHERE invoice_date BETWEEN DATE_SUB(p_start_date, INTERVAL DATEDIFF(p_end_date, p_start_date) DAY) AND DATE_SUB(p_start_date, INTERVAL 1 DAY)
        AND payment_status != 'cancelled';
END //

DELIMITER ;
