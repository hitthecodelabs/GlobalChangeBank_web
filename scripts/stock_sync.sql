-- Stock synchronization workflow for SAP feeds.
--
-- This script recreates the workflow that refreshes the stock staging
-- tables used by the point-of-sale front end.  It expects two CSV files:
--   * sl_stock_totales.csv
--   * sl_stock_por_bodega.csv
-- located in the directory referenced by @csv_path.
--
-- Usage example:
--   mysql --local-infile=1 -u <user> -p pos \
--     --execute="SET @csv_path='/opt/pos_sync'; SOURCE scripts/stock_sync.sql;"
--
-- Both the totals and warehouse-level feeds are loaded, validated, and the
-- supporting views are rebuilt so the application reflects the updated
-- quantities.

SET NAMES utf8mb4;
SET SQL_SAFE_UPDATES = 0;

-- Make sure the staging tables exist.
CREATE TABLE IF NOT EXISTS sap_stock_total (
  ItemCode   VARCHAR(64) NOT NULL,
  InStock    DECIMAL(18,4) NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (ItemCode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS sap_stock_bodega (
  ItemCode   VARCHAR(64) NOT NULL,
  Warehouse  VARCHAR(32) NOT NULL,
  InStock    DECIMAL(18,4) NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (ItemCode, Warehouse),
  KEY idx_wh (Warehouse),
  KEY idx_item (ItemCode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Clean existing records so the CSV import is idempotent.
TRUNCATE sap_stock_total;
TRUNCATE sap_stock_bodega;

-- Import SAP totals per item.
SET @totales = CONCAT(@csv_path, '/sl_stock_totales.csv');
LOAD DATA LOCAL INFILE @totales
INTO TABLE sap_stock_total
FIELDS TERMINATED BY ',' ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES
(@ItemCode, @InStockTotal)
SET ItemCode = TRIM(@ItemCode),
    InStock  = CAST(NULLIF(TRIM(@InStockTotal),'') AS DECIMAL(18,4));

-- Import SAP stock by warehouse.
SET @por_bodega = CONCAT(@csv_path, '/sl_stock_por_bodega.csv');
LOAD DATA LOCAL INFILE @por_bodega
INTO TABLE sap_stock_bodega
FIELDS TERMINATED BY ',' ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES
(@ItemCode, @Warehouse, @InStock)
SET ItemCode  = TRIM(@ItemCode),
    Warehouse = TRIM(@Warehouse),
    InStock   = CAST(NULLIF(TRIM(@InStock),'') AS DECIMAL(18,4));

-- Quick validations: record counts and totals must align.
SELECT COUNT(*) AS items_total FROM sap_stock_total;
SELECT COUNT(DISTINCT ItemCode) AS items_con_bodegas FROM sap_stock_bodega;

SELECT
  (SELECT SUM(InStock) FROM sap_stock_total) AS total_reportado,
  (SELECT SUM(x.sum_b)
     FROM (SELECT ItemCode, SUM(InStock) AS sum_b
             FROM sap_stock_bodega
            GROUP BY ItemCode) x)          AS total_por_bodegas;

SELECT *
FROM (
  SELECT
    t.ItemCode,
    t.InStock AS total_reportado,
    COALESCE(SUM(b.InStock),0) AS suma_bodegas,
    (t.InStock - COALESCE(SUM(b.InStock),0)) AS diff
  FROM sap_stock_total t
  LEFT JOIN sap_stock_bodega b USING (ItemCode)
  GROUP BY t.ItemCode, t.InStock
) x
WHERE x.diff <> 0
ORDER BY ABS(x.diff) DESC
LIMIT 50;

-- Rebuild views consumed by the front end.
CREATE OR REPLACE VIEW vw_sap_stock_pivot AS
SELECT
  ItemCode,
  SUM(InStock) AS stock_total_bodegas,
  SUM(CASE WHEN Warehouse='101' THEN InStock ELSE 0 END) AS wh_101,
  SUM(CASE WHEN Warehouse='108' THEN InStock ELSE 0 END) AS wh_108,
  SUM(CASE WHEN Warehouse='110' THEN InStock ELSE 0 END) AS wh_110,
  SUM(CASE WHEN Warehouse='111' THEN InStock ELSE 0 END) AS wh_111
FROM sap_stock_bodega
GROUP BY ItemCode;

CREATE OR REPLACE VIEW vw_pos_productos_stock AS
SELECT
  p.id,
  p.codigo                         AS sku,
  p.descripcion                    AS nombre,
  c.categoria,
  p.precio_venta,
  COALESCE(st.InStock, 0)              AS stock_total_sap,
  COALESCE(sp.stock_total_bodegas, 0)  AS stock_sum_bodegas,
  COALESCE(sp.stock_total_bodegas, st.InStock, 0) AS stock_para_front,
  CASE WHEN COALESCE(st.InStock,0) = COALESCE(sp.stock_total_bodegas,0) THEN 1 ELSE 0 END AS stock_consistente
FROM pos_productos_sap p
JOIN pos_categorias_sap c ON c.id = p.id_categoria
LEFT JOIN sap_stock_total st ON st.ItemCode = p.codigo
LEFT JOIN (
  SELECT ItemCode, SUM(InStock) AS stock_total_bodegas
  FROM sap_stock_bodega
  GROUP BY ItemCode
) sp ON sp.ItemCode = p.codigo;

-- Optional synchronization for legacy consumers that still read
-- pos_productos_sap.stock_total directly.
UPDATE pos_productos_sap p
LEFT JOIN sap_stock_total st ON st.ItemCode = p.codigo
LEFT JOIN (
  SELECT ItemCode, SUM(InStock) AS sum_b
  FROM sap_stock_bodega
  GROUP BY ItemCode
) b ON b.ItemCode = p.codigo
SET p.stock_total = COALESCE(b.sum_b, st.InStock, 0);

-- Diagnostics for the "999" placeholder issue.
SELECT COUNT(*) AS productos_999_en_vista
FROM vw_pos_productos_stock
WHERE stock_para_front = 999;

SELECT COUNT(*) AS productos_999_en_tabla
FROM pos_productos_sap
WHERE stock_total = 999;

-- Summary the front end uses to display availability ratios.
SELECT
  COUNT(*) AS productos_pos,
  SUM(stock_para_front > 0) AS con_stock,
  ROUND(100*SUM(stock_para_front > 0)/COUNT(*),2) AS pct_con_stock
FROM vw_pos_productos_stock;

-- Sample payload for debugging.
SELECT sku, stock_total_sap, stock_sum_bodegas, stock_para_front
FROM vw_pos_productos_stock
WHERE sku IN ('580101','580107','580201','580222','699023','834127','G2431226050','BK2.2017');

SELECT * FROM vw_sap_stock_pivot
WHERE ItemCode IN ('580101','699023');
