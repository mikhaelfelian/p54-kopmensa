-- Create v_item_stok view for Kopmensa
-- This view provides comprehensive stock movement and balance information

-- Drop existing view if exists
DROP VIEW IF EXISTS `v_item_stok`;

-- Create the view
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_item_stok` AS 
WITH
/* ============================ *
   v_item_stok (LIVE, all items x gudang)
   - stok_masuk : beli + retur jual + manual/mutasi masuk (all-time)
   - stok_keluar: jual + retur beli + manual/mutasi keluar (all-time)
   - so         : pergerakan SETELAH SO (after-SO net, bisa +/-)
   - sisa       : stok real = baseline SO + so  (bisa minus)
 * ============================ */
-- pasangan item×gudang dari master stok
dim AS (
  SELECT DISTINCT s.id_item, s.id_gudang
  FROM tbl_m_item_stok s
  -- WHERE s.status = '1'   -- aktif saja? silakan buka jika perlu
),

/* ===== SO terakhir per item×gudang (qty murni) ===== */
so_one AS (
  SELECT id_item, id_gudang, jml AS so_qty, created_at AS so_ts
  FROM (
    SELECT h.*,
           ROW_NUMBER() OVER (
             PARTITION BY h.id_item, h.id_gudang
             ORDER BY h.created_at DESC, h.id DESC
           ) AS rn
    FROM tbl_m_item_hist h
    WHERE h.status='6'
  ) t
  WHERE rn=1
),

/* ===== PEMBELIAN (anti dobel nomor nota) ===== */
beli_hdr_can AS (
  SELECT MIN(id) AS id, no_nota
  FROM tbl_trans_beli
  GROUP BY no_nota
),
beli_per_nota AS (
  SELECT bd.id_item, bd.id_gudang, b.no_nota,
         MIN(COALESCE(bd.created_at, b.created_at)) AS ts,
         SUM(COALESCE(NULLIF(bd.jml_diterima,0), bd.jml, 0)) AS qty
  FROM tbl_trans_beli_det bd
  JOIN beli_hdr_can bc ON bc.id = bd.id_pembelian
  JOIN tbl_trans_beli b ON b.id = bc.id
  GROUP BY bd.id_item, bd.id_gudang, b.no_nota
),

/* ===== PENJUALAN (anti dobel nomor nota) ===== */
jual_hdr_can AS (
  SELECT MIN(id) AS id, no_nota
  FROM tbl_trans_jual
  GROUP BY no_nota
),
jual_per_nota AS (
  SELECT jd.id_item, j.id_gudang, j.no_nota,
         MIN(COALESCE(j.created_at, jd.created_at)) AS ts,
         SUM(CASE WHEN COALESCE(jd.status,1)=1 THEN jd.jml ELSE 0 END) AS qty
  FROM tbl_trans_jual_det jd
  JOIN jual_hdr_can jc ON jc.id = jd.id_penjualan
  JOIN tbl_trans_jual j ON j.id = jc.id
  GROUP BY jd.id_item, j.id_gudang, j.no_nota
),

/* ===== MANUAL MASUK dari HISTORI (status=2) ===== */
hist2 AS (
  SELECT h.id_item, h.id_gudang, h.created_at AS ts, h.jml AS qty
  FROM tbl_m_item_hist h
  WHERE h.status='2'
),

/* ===== MUTASI =====
   - keluar (asal) dari histori status=7 (gudang = h.id_gudang)
   - masuk (tujuan) dari HISTORI status=7 JOIN header mutasi utk ambil id_gd_tujuan
*/
mutasi_out AS (
  SELECT h.id_item,
         h.id_gudang       AS id_gudang,
         h.created_at      AS ts,
         h.jml             AS qty
  FROM tbl_m_item_hist h
  WHERE h.status='7'
),
mutasi_in AS (
  SELECT h.id_item,
         m.id_gd_tujuan    AS id_gudang,
         h.created_at      AS ts,
         h.jml             AS qty
  FROM tbl_m_item_hist h
  JOIN tbl_trans_mutasi m
       ON m.id = h.id_mutasi      -- <<== ganti ke kolom FK sebenarnya jika berbeda
  WHERE h.status='7'
),

/* ===== REKAP ALL-TIME ===== */
masuk_all AS (  -- pembelian + manual + mutasi masuk
  SELECT d.id_item, d.id_gudang,
         COALESCE(b.qty,0) + COALESCE(h2.qty,0) + COALESCE(mi.qty,0) AS qty
  FROM dim d
  LEFT JOIN (SELECT id_item,id_gudang,SUM(qty) qty FROM beli_per_nota GROUP BY 1,2) b
         ON b.id_item=d.id_item AND b.id_gudang=d.id_gudang
  LEFT JOIN (SELECT id_item,id_gudang,SUM(qty) qty FROM hist2 GROUP BY 1,2) h2
         ON h2.id_item=d.id_item AND h2.id_gudang=d.id_gudang
  LEFT JOIN (SELECT id_item,id_gudang,SUM(qty) qty FROM mutasi_in GROUP BY 1,2) mi
         ON mi.id_item=d.id_item AND mi.id_gudang=d.id_gudang
),
keluar_all AS ( -- penjualan + mutasi keluar
  SELECT d.id_item, d.id_gudang,
         COALESCE(j.qty,0) + COALESCE(mo.qty,0) AS qty
  FROM dim d
  LEFT JOIN (SELECT id_item,id_gudang,SUM(qty) qty FROM jual_per_nota GROUP BY 1,2) j
         ON j.id_item=d.id_item AND j.id_gudang=d.id_gudang
  LEFT JOIN (SELECT id_item,id_gudang,SUM(qty) qty FROM mutasi_out GROUP BY 1,2) mo
         ON mo.id_item=d.id_item AND mo.id_gudang=d.id_gudang
),

/* ===== PERGERAKAN SETELAH SO (dipakai untuk 'sisa') ===== */
masuk_after AS (
  SELECT d.id_item, d.id_gudang,
         /* pembelian setelah SO */
         COALESCE((
           SELECT SUM(CASE WHEN so.so_ts IS NULL OR bp.ts > so.so_ts THEN bp.qty ELSE 0 END)
           FROM beli_per_nota bp
           LEFT JOIN so_one so ON so.id_item=d.id_item AND so.id_gudang=d.id_gudang
           WHERE bp.id_item=d.id_item AND bp.id_gudang=d.id_gudang
         ),0)
       + /* manual(2) setelah SO */
         COALESCE((
           SELECT SUM(CASE WHEN so.so_ts IS NULL OR h.ts > so.so_ts THEN h.qty ELSE 0 END)
           FROM hist2 h
           LEFT JOIN so_one so ON so.id_item=d.id_item AND so.id_gudang=d.id_gudang
           WHERE h.id_item=d.id_item AND h.id_gudang=d.id_gudang
         ),0)
       + /* mutasi MASUK (tujuan) setelah SO */
         COALESCE((
           SELECT SUM(CASE WHEN so.so_ts IS NULL OR mi.ts > so.so_ts THEN mi.qty ELSE 0 END)
           FROM mutasi_in mi
           LEFT JOIN so_one so ON so.id_item=d.id_item AND so.id_gudang=d.id_gudang
           WHERE mi.id_item=d.id_item AND mi.id_gudang=d.id_gudang
         ),0) AS qty
  FROM dim d
),
keluar_after AS (
  SELECT d.id_item, d.id_gudang,
         /* penjualan setelah SO */
         COALESCE((
           SELECT SUM(CASE WHEN so.so_ts IS NULL OR jp.ts > so.so_ts THEN jp.qty ELSE 0 END)
           FROM jual_per_nota jp
           LEFT JOIN so_one so ON so.id_item=d.id_item AND so.id_gudang=d.id_gudang
           WHERE jp.id_item=d.id_item AND jp.id_gudang=d.id_gudang
         ),0)
       + /* mutasi KELUAR (asal) setelah SO */
         COALESCE((
           SELECT SUM(CASE WHEN so.so_ts IS NULL OR mo.ts > so.so_ts THEN mo.qty ELSE 0 END)
           FROM mutasi_out mo
           LEFT JOIN so_one so ON so.id_item=d.id_item AND so.id_gudang=d.id_gudang
           WHERE mo.id_item=d.id_item AND mo.id_gudang=d.id_gudang
         ),0) AS qty
  FROM dim d
)

SELECT
  i.id   AS id_item,
  g.id   AS id_gudang,
  g.nama AS gudang,
  g.status_otl,
  i.kode,
  i.item,

  /* SO murni */
  COALESCE(so.so_qty,0)        AS so,

  /* rekap all-time untuk info */
  COALESCE(ma.qty,0)           AS stok_masuk,   -- beli + manual(2) + mutasi masuk
  COALESCE(ka.qty,0)           AS stok_keluar,  -- jual + mutasi keluar

  /* stok real-time */
  CASE
    WHEN so.so_ts IS NULL
      THEN COALESCE(ma.qty,0) - COALESCE(ka.qty,0)
    ELSE COALESCE(so.so_qty,0)
       +  COALESCE(mf.qty,0)   -- masuk setelah SO
       -  COALESCE(kf.qty,0)   -- keluar setelah SO
  END AS sisa

FROM dim d
JOIN tbl_m_item   i ON i.id = d.id_item
JOIN tbl_m_gudang g ON g.id = d.id_gudang
LEFT JOIN so_one      so ON so.id_item=d.id_item AND so.id_gudang=d.id_gudang
LEFT JOIN masuk_all   ma ON ma.id_item=d.id_item AND ma.id_gudang=d.id_gudang
LEFT JOIN keluar_all  ka ON ka.id_item=d.id_item AND ka.id_gudang=d.id_gudang
LEFT JOIN masuk_after mf ON mf.id_item=d.id_item AND mf.id_gudang=d.id_gudang
LEFT JOIN keluar_after kf ON kf.id_item=d.id_item AND kf.id_gudang=d.id_gudang
ORDER BY i.item, g.nama;

-- Add comments to the view
-- COMMENT ON VIEW v_item_stok IS 'Comprehensive view of item stock information combining stock, item, warehouse, outlet, category, brand, unit, supplier, and user data';

-- Create indexes on the underlying tables for better performance
-- Note: These indexes should already exist, but adding them here for reference
-- CREATE INDEX IF NOT EXISTS idx_item_stok_item ON tbl_m_item_stok(id_item);
-- CREATE INDEX IF NOT EXISTS idx_item_stok_gudang ON tbl_m_item_stok(id_gudang);
-- CREATE INDEX IF NOT EXISTS idx_item_stok_status ON tbl_m_item_stok(status);
-- CREATE INDEX IF NOT EXISTS idx_item_stok_created ON tbl_m_item_stok(created_at);
-- CREATE INDEX IF NOT EXISTS idx_item_hist_item ON tbl_m_item_hist(id_item);
-- CREATE INDEX IF NOT EXISTS idx_item_hist_gudang ON tbl_m_item_hist(id_gudang);
-- CREATE INDEX IF NOT EXISTS idx_item_hist_status ON tbl_m_item_hist(status);
-- CREATE INDEX IF NOT EXISTS idx_item_hist_created ON tbl_m_item_hist(created_at);
