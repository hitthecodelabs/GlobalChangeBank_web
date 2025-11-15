# Stock Synchronization Playbook

This document explains how to refresh the POS stock indicators so the web
front end stops showing the `999` placeholder and instead mirrors the
quantities delivered by SAP.  The process mirrors the terminal session that
was previously run manually and packages it into a repeatable workflow.

## Prerequisites

* Access to the `pos` database with `LOAD DATA LOCAL INFILE` enabled.
* Two CSV exports produced by SAP:
  * `sl_stock_totales.csv`
  * `sl_stock_por_bodega.csv`
* A directory (for example `/opt/pos_sync`) that contains both CSV files.

## Steps

1. **Upload the CSV exports** to the target directory on the application
   server (e.g. `/opt/pos_sync`).
2. **Run the synchronization script** using the MariaDB client.  Update
   the username, password, host, and CSV directory as needed:

   ```bash
   mysql --local-infile=1 \
     -h <host> \
     -u <user> -p \
     pos \
     --execute="SET @csv_path='/opt/pos_sync'; SOURCE scripts/stock_sync.sql;"
   ```

3. **Verify the output** of the script:
   * The totals reported by `sap_stock_total` and the sum of
     `sap_stock_bodega` must be identical.
   * The diagnostics for `productos_999_en_vista` and
     `productos_999_en_tabla` should both return `0`, confirming that no
     product is stuck with the placeholder quantity.
   * The sample payload at the end of the script shows what the backend
     sends to the front end for a handful of SKUs.

4. **Refresh the caches** of the web application (if any) so it picks up the
   latest data from `vw_pos_productos_stock.stock_para_front`.

## Troubleshooting

* If the CSV path is wrong you will see `ERROR 2 (HY000): File not found`. Set
  `@csv_path` to the directory that actually contains the files.
* When the totals do not match, inspect the diagnostic query output to locate
  the items with mismatched quantities before refreshing the front end.
* If the front end continues to display `999`, confirm that it reads either
  `vw_pos_productos_stock.stock_para_front` or `pos_productos_sap.stock_total`.
  Both values are synchronized by the script.
