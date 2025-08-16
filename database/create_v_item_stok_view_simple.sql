-- Create simplified v_item_stok view for Kopmensa
-- This view provides basic stock information with outlet status

-- Drop existing view if exists
DROP VIEW IF EXISTS `v_item_stok`;

-- Create the simplified view
CREATE VIEW `v_item_stok` AS 
SELECT 
    i.id AS id_item,
    g.id AS id_gudang,
    g.nama AS gudang,
    g.status_otl,
    i.kode,
    i.item,
    COALESCE(s.jml, 0) AS so,
    COALESCE(s.jml, 0) AS stok_masuk,
    0 AS stok_keluar,
    COALESCE(s.jml, 0) AS sisa
FROM tbl_m_item_stok s
JOIN tbl_m_item i ON i.id = s.id_item
JOIN tbl_m_gudang g ON g.id = s.id_gudang
WHERE s.status = '1' AND i.status = '1' AND g.status_hps = '0'
ORDER BY i.item, g.nama;

-- Test the view
-- SELECT * FROM v_item_stok LIMIT 5;

-- Verify the view was created successfully
-- SHOW CREATE VIEW v_item_stok;
