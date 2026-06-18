-- Vista de medicamentos con bajo stock
-- Muestra medicamentos que están por debajo del nivel mínimo de stock

CREATE OR REPLACE VIEW v_low_stock_medications AS
SELECT
    m.id AS medication_id,
    m.name AS medication_name,
    m.generic_name,
    m.category,
    m.presentation,
    m.concentration,
    m.laboratory,
    m.current_stock,
    m.min_stock,
    (m.min_stock - m.current_stock) AS stock_deficit,
    m.unit_price,
    ROUND((m.min_stock - m.current_stock) * m.unit_price, 2) AS reorder_cost,
    m.status,
    mc.name AS category_name,

    -- Nivel de urgencia del stock
    CASE
        WHEN m.current_stock = 0 THEN 'CRITICO'
        WHEN m.current_stock <= (m.min_stock * 0.5) THEN 'MUY BAJO'
        WHEN m.current_stock <= m.min_stock THEN 'BAJO'
        ELSE 'NORMAL'
    END AS stock_level,

    -- Porcentaje de stock restante
    CASE
        WHEN m.min_stock > 0 THEN ROUND((m.current_stock / m.min_stock) * 100, 1)
        ELSE 100
    END AS stock_percentage,

    m.updated_at AS last_update

FROM medications m
LEFT JOIN medication_categories mc ON m.category = mc.name
WHERE m.status = 'active'
    AND m.current_stock <= m.min_stock
ORDER BY
    CASE
        WHEN m.current_stock = 0 THEN 1
        WHEN m.current_stock <= (m.min_stock * 0.5) THEN 2
        ELSE 3
    END,
    (m.min_stock - m.current_stock) DESC;
