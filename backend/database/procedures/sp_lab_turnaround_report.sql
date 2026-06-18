-- Procedimiento para reporte de tiempo de respuesta de laboratorio
-- Genera un análisis del rendimiento del laboratorio

DELIMITER //

CREATE PROCEDURE sp_lab_turnaround_report(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_test_category VARCHAR(50),
    IN p_priority VARCHAR(20)
)
BEGIN
    -- Resumen general del laboratorio
    SELECT
        'RESUMEN LABORATORIO' AS report_section,
        COUNT(*) AS total_orders,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_orders,
        SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress_orders,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_orders,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_orders,
        ROUND(AVG(
            CASE
                WHEN status = 'completed' AND completed_at IS NOT NULL THEN
                    TIMESTAMPDIFF(HOUR, order_date, completed_at)
                ELSE NULL
            END
        ), 1) AS avg_turnaround_hours,
        ROUND(AVG(
            CASE
                WHEN status = 'completed' AND completed_at IS NOT NULL THEN
                    TIMESTAMPDIFF(MINUTE, order_date, completed_at)
                ELSE NULL
            END
        ), 0) AS avg_turnaround_minutes,
        MIN(
            CASE
                WHEN status = 'completed' AND completed_at IS NOT NULL THEN
                    TIMESTAMPDIFF(HOUR, order_date, completed_at)
                ELSE NULL
            END
        ) AS min_turnaround_hours,
        MAX(
            CASE
                WHEN status = 'completed' AND completed_at IS NOT NULL THEN
                    TIMESTAMPDIFF(HOUR, order_date, completed_at)
                ELSE NULL
            END
        ) AS max_turnaround_hours
    FROM lab_orders
    WHERE order_date BETWEEN p_start_date AND CONCAT(p_end_date, ' 23:59:59')
        AND (p_priority IS NULL OR priority = p_priority);

    -- Tiempo de respuesta por tipo de análisis
    SELECT
        lt.category AS test_category,
        lt.name AS test_name,
        COUNT(lo.id) AS total_orders,
        ROUND(AVG(
            CASE
                WHEN lo.status = 'completed' AND lo.completed_at IS NOT NULL THEN
                    TIMESTAMPDIFF(HOUR, lo.order_date, lo.completed_at)
                ELSE NULL
            END
        ), 1) AS avg_turnaround_hours,
        ROUND(AVG(
            CASE
                WHEN lo.status = 'completed' AND lo.completed_at IS NOT NULL THEN
                    TIMESTAMPDIFF(MINUTE, lo.order_date, lo.completed_at)
                ELSE NULL
            END
        ), 0) AS avg_turnaround_minutes,
        MIN(
            CASE
                WHEN lo.status = 'completed' AND lo.completed_at IS NOT NULL THEN
                    TIMESTAMPDIFF(HOUR, lo.order_date, lo.completed_at)
                ELSE NULL
            END
        ) AS min_turnaround_hours,
        MAX(
            CASE
                WHEN lo.status = 'completed' AND lo.completed_at IS NOT NULL THEN
                    TIMESTAMPDIFF(HOUR, lo.order_date, lo.completed_at)
                ELSE NULL
            END
        ) AS max_turnaround_hours,
        SUM(CASE WHEN lo.priority = 'urgent' THEN 1 ELSE 0 END) AS urgent_orders,
        SUM(CASE WHEN lo.priority = 'high' THEN 1 ELSE 0 END) AS high_priority_orders,
        SUM(CASE WHEN lo.priority = 'normal' THEN 1 ELSE 0 END) AS normal_orders,
        -- Cumplimiento de SLA
        ROUND(
            SUM(CASE
                WHEN lo.status = 'completed' AND lo.completed_at IS NOT NULL THEN
                    CASE
                        WHEN TIMESTAMPDIFF(HOUR, lo.order_date, lo.completed_at) <=
                             CASE lo.priority
                                 WHEN 'urgent' THEN 2
                                 WHEN 'high' THEN 4
                                 WHEN 'normal' THEN 24
                                 WHEN 'low' THEN 48
                                 ELSE 24
                             END THEN 1
                        ELSE 0
                    END
                ELSE 0
            END) * 100.0 /
            NULLIF(SUM(CASE WHEN lo.status = 'completed' THEN 1 ELSE 0 END), 0),
            2
        ) AS sla_compliance_rate
    FROM lab_orders lo
    INNER JOIN lab_order_items loi ON lo.id = loi.order_id
    INNER JOIN lab_tests lt ON loi.test_id = lt.id
    WHERE lo.order_date BETWEEN p_start_date AND CONCAT(p_end_date, ' 23:59:59')
        AND (p_test_category IS NULL OR lt.category = p_test_category)
        AND (p_priority IS NULL OR lo.priority = p_priority)
    GROUP BY lt.category, lt.name
    ORDER BY avg_turnaround_hours DESC;

    -- Rendimiento por prioridad
    SELECT
        priority,
        COUNT(*) AS total_orders,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_orders,
        ROUND(AVG(
            CASE
                WHEN status = 'completed' AND completed_at IS NOT NULL THEN
                    TIMESTAMPDIFF(HOUR, order_date, completed_at)
                ELSE NULL
            END
        ), 1) AS avg_turnaround_hours,
        CASE priority
            WHEN 'urgent' THEN 2
            WHEN 'high' THEN 4
            WHEN 'normal' THEN 24
            WHEN 'low' THEN 48
        END AS target_hours,
        ROUND(
            SUM(CASE
                WHEN status = 'completed' AND completed_at IS NOT NULL THEN
                    CASE
                        WHEN TIMESTAMPDIFF(HOUR, order_date, completed_at) <=
                             CASE priority
                                 WHEN 'urgent' THEN 2
                                 WHEN 'high' THEN 4
                                 WHEN 'normal' THEN 24
                                 WHEN 'low' THEN 48
                             END THEN 1
                        ELSE 0
                    END
                ELSE 0
            END) * 100.0 /
            NULLIF(SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END), 0),
            2
        ) AS sla_compliance_rate
    FROM lab_orders
    WHERE order_date BETWEEN p_start_date AND CONCAT(p_end_date, ' 23:59:59')
    GROUP BY priority
    ORDER BY
        CASE priority
            WHEN 'urgent' THEN 1
            WHEN 'high' THEN 2
            WHEN 'normal' THEN 3
            WHEN 'low' THEN 4
        END;

    -- Tendencia diaria
    SELECT
        DATE(order_date) AS order_date,
        DAYNAME(DATE(order_date)) AS day_name,
        COUNT(*) AS total_orders,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_orders,
        ROUND(AVG(
            CASE
                WHEN status = 'completed' AND completed_at IS NOT NULL THEN
                    TIMESTAMPDIFF(HOUR, order_date, completed_at)
                ELSE NULL
            END
        ), 1) AS avg_turnaround_hours
    FROM lab_orders
    WHERE order_date BETWEEN p_start_date AND CONCAT(p_end_date, ' 23:59:59')
    GROUP BY DATE(order_date), DAYNAME(DATE(order_date))
    ORDER BY order_date;

    -- Horas pico de actividad
    SELECT
        HOUR(order_date) AS order_hour,
        COUNT(*) AS total_orders,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_orders,
        ROUND(AVG(
            CASE
                WHEN status = 'completed' AND completed_at IS NOT NULL THEN
                    TIMESTAMPDIFF(MINUTE, order_date, completed_at)
                ELSE NULL
            END
        ), 0) AS avg_turnaround_minutes
    FROM lab_orders
    WHERE order_date BETWEEN p_start_date AND CONCAT(p_end_date, ' 23:59:59')
    GROUP BY HOUR(order_date)
    ORDER BY total_orders DESC;
END //

DELIMITER ;
