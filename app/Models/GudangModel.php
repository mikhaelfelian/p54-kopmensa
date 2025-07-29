<?php

namespace App\Models;

use CodeIgniter\Model;

class GudangModel extends Model
{
    protected $table            = 'tbl_m_gudang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['created_at','updated_at','deleted_at','id_user','kode', 'nama', 'deskripsi', 'status', 'status_hps', 'status_gd', 'status_otl'];

    // Pengaturan tanggal
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validasi
    protected $validationRules = [
        'nama'       => 'required|max_length[160]',
        'kode'       => 'permit_empty|max_length[160]',
        'status'     => 'permit_empty|in_list[0,1]',
        'status_hps' => 'permit_empty|in_list[0,1]',
        'status_gd'  => 'permit_empty|in_list[0,1]',
        'status_otl' => 'permit_empty|in_list[0,1]',
    ];

    /**
     * Menghasilkan kode unik untuk gudang
     * Format: GDG-001, GDG-002, dll
     */
    public function generateKode($status_otl = null)
    {
        // SAP format code: 1xxx for Gudang, 2xxx for Outlet
        $typeDigit  = ($status_otl === '1') ? '2' : '1';
        $prefix     = $typeDigit;

        // Find the last code for this type
        $lastKode = $this->select('kode')
            ->like('kode', $prefix, 'after')
            ->orderBy('kode', 'DESC')
            ->first();

        if (!$lastKode) {
            // Start from 1001 or 2001 depending on type
            $startNumber = ($typeDigit === '2') ? 2001 : 1001;
            return (string)$startNumber;
        }

        // Extract the numeric part and increment
        $lastNumber = (int) $lastKode->kode;
        $newNumber = $lastNumber + 1;

        // Ensure the new code starts with the correct type digit
        if (substr((string)$newNumber, 0, 1) !== $typeDigit) {
            $newNumber = ($typeDigit === '2') ? 2001 : 1001;
        }

        return (string)$newNumber;
    }

    /**
     * Mendapatkan level stok untuk suatu item di semua gudang
     */
    public function getItemStocks($item_id)
    {
        return $this->db->table('tbl_m_gudang')
            ->select('
                tbl_m_gudang.nama as gudang,
                COALESCE(tbl_m_item_stok.jml, 0) as stok
            ')
            ->join('tbl_m_item_stok', 'tbl_m_item_stok.id_gudang = tbl_m_gudang.id AND tbl_m_item_stok.id_item = ' . $item_id, 'left')
            ->get()
            ->getResult();
    }
    
    /**
     * Mendapatkan semua outlets (gudang dengan status_otl = '1')
     */
    public function getOutlets()
    {
        return $this->where('status_otl', '1')
                    ->where('status', '1') // hanya yang aktif
                    ->where('status_hps', '0') // tidak terhapus
                    ->orderBy('nama', 'ASC')
                    ->findAll();
    }
    
    /**
     * Mendapatkan semua warehouses (gudang dengan status_otl = '0' atau null)
     */
    public function getWarehouses()
    {
        return $this->groupStart()
                    ->where('status_otl', '0')
                    ->orWhere('status_otl', null)
                    ->groupEnd()
                    ->where('status', '1') // hanya yang aktif
                    ->where('status_hps', '0') // tidak terhapus
                    ->orderBy('nama', 'ASC')
                    ->findAll();
    }
    
    /**
     * Mendapatkan item stok di outlets saja
     */
    public function getItemStocksInOutlets($item_id = null)
    {
        $builder = $this->db->table('tbl_m_gudang')
            ->select('
                tbl_m_gudang.id,
                tbl_m_gudang.kode,
                tbl_m_gudang.nama as gudang,
                tbl_m_item_stok.id_item,
                tbl_m_item.item,
                tbl_m_item.barcode,
                COALESCE(tbl_m_item_stok.jml, 0) as stok,
                tbl_m_item.harga_beli,
                tbl_m_item.harga_jual
            ')
            ->join('tbl_m_item_stok', 'tbl_m_item_stok.id_gudang = tbl_m_gudang.id', 'inner')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'inner')
            ->where('tbl_m_gudang.status_otl', '1') // filter outlets only
            ->where('tbl_m_gudang.status', '1') // gudang aktif
            ->where('tbl_m_gudang.status_hps', '0') // tidak terhapus
            ->where('tbl_m_item.status', '1'); // item aktif
        
        if ($item_id) {
            $builder->where('tbl_m_item_stok.id_item', $item_id);
        }
        
        return $builder->orderBy('tbl_m_gudang.nama', 'ASC')
                      ->orderBy('tbl_m_item.item', 'ASC')
                      ->get()
                      ->getResult();
    }
    
    /**
     * Mendapatkan stok item berdasarkan filter outlets dengan pagination
     */
    public function getItemStocksInOutletsPaginated($filters = [])
    {
        $builder = $this->db->table('tbl_m_gudang')
            ->select('
                tbl_m_item.id,
                tbl_m_gudang.id as gudang_id,
                tbl_m_gudang.kode as gudang_kode,
                tbl_m_gudang.nama as gudang_nama,
                tbl_m_item_stok.id_item,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_item.barcode,
                tbl_m_item.deskripsi,
                tbl_m_item.status,
                tbl_m_item.foto,
                tbl_m_kategori.kategori,
                tbl_m_merk.merk,
                COALESCE(tbl_m_item_stok.jml, 0) as stok,
                tbl_m_item.harga_beli,
                tbl_m_item.harga_jual,
                tbl_m_item.jml_min as stok_min
            ')
            ->join('tbl_m_item_stok', 'tbl_m_item_stok.id_gudang = tbl_m_gudang.id', 'inner')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'inner')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->where('tbl_m_gudang.status_otl', '1') // filter outlets only
            ->where('tbl_m_gudang.status', '1') // gudang aktif
            ->where('tbl_m_gudang.status_hps', '0') // tidak terhapus
            ->where('tbl_m_item.status', '1'); // item aktif
        
        // Apply filters
        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $builder->groupStart()
                   ->like('tbl_m_item.item', $keyword)
                   ->orLike('tbl_m_item.kode', $keyword)
                   ->orLike('tbl_m_item.barcode', $keyword)
                   ->groupEnd();
        }
        
        if (isset($filters['kategori']) && $filters['kategori'] !== '') {
            $builder->where('tbl_m_item.id_kategori', $filters['kategori']);
        }
        
        if (isset($filters['merk']) && $filters['merk'] !== '') {
            $builder->where('tbl_m_item.id_merk', $filters['merk']);
        }
        
        if (isset($filters['stok']) && $filters['stok'] !== '') {
            $builder->where('tbl_m_item.status_stok', $filters['stok']);
        }
        
        // Stock level filters
        if (!empty($filters['min_stok_operator']) && isset($filters['min_stok_value'])) {
            $operator = $filters['min_stok_operator'];
            $value = $filters['min_stok_value'];
            $builder->where("tbl_m_item_stok.jml $operator", $value);
        }
        
        // Price filters - from tbl_m_item
        if (!empty($filters['harga_beli_operator']) && !empty($filters['harga_beli_value'])) {
            $operator = $filters['harga_beli_operator'];
            $value = str_replace(['.', ','], '', $filters['harga_beli_value']); // remove formatting
            $builder->where("tbl_m_item.harga_beli $operator", $value);
        }
        
        if (!empty($filters['harga_jual_operator']) && !empty($filters['harga_jual_value'])) {
            $operator = $filters['harga_jual_operator'];
            $value = str_replace(['.', ','], '', $filters['harga_jual_value']); // remove formatting
            $builder->where("tbl_m_item.harga_jual $operator", $value);
        }
        
        return $builder->orderBy('tbl_m_gudang.nama', 'ASC')
                      ->orderBy('tbl_m_item.item', 'ASC');
    }
    
    /**
     * Mendapatkan stok item berdasarkan filter outlet spesifik dengan pagination
     */
    public function getItemStocksInSpecificOutlet($gudang_id, $filters = [])
    {
        $builder = $this->db->table('tbl_m_gudang')
            ->select('
                tbl_m_item.id,
                tbl_m_gudang.id as gudang_id,
                tbl_m_gudang.kode as gudang_kode,
                tbl_m_gudang.nama as gudang_nama,
                tbl_m_item_stok.id_item,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_item.barcode,
                tbl_m_item.deskripsi,
                tbl_m_item.status,
                tbl_m_item.foto,
                tbl_m_kategori.kategori,
                tbl_m_merk.merk,
                COALESCE(tbl_m_item_stok.jml, 0) as stok,
                tbl_m_item.harga_beli,
                tbl_m_item.harga_jual,
                tbl_m_item.jml_min as stok_min
            ')
            ->join('tbl_m_item_stok', 'tbl_m_item_stok.id_gudang = tbl_m_gudang.id', 'inner')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'inner')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->where('tbl_m_gudang.id', $gudang_id) // filter specific outlet
            ->where('tbl_m_gudang.status_otl', '1') // ensure it's an outlet
            ->where('tbl_m_gudang.status', '1') // gudang aktif
            ->where('tbl_m_gudang.status_hps', '0') // tidak terhapus
            ->where('tbl_m_item.status', '1'); // item aktif
        
        // Apply filters
        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $builder->groupStart()
                   ->like('tbl_m_item.item', $keyword)
                   ->orLike('tbl_m_item.kode', $keyword)
                   ->orLike('tbl_m_item.barcode', $keyword)
                   ->groupEnd();
        }
        
        if (isset($filters['kategori']) && $filters['kategori'] !== '') {
            $builder->where('tbl_m_item.id_kategori', $filters['kategori']);
        }
        
        if (isset($filters['merk']) && $filters['merk'] !== '') {
            $builder->where('tbl_m_item.id_merk', $filters['merk']);
        }
        
        if (isset($filters['stok']) && $filters['stok'] !== '') {
            $builder->where('tbl_m_item.status_stok', $filters['stok']);
        }
        
        // Stock level filters
        if (!empty($filters['min_stok_operator']) && isset($filters['min_stok_value'])) {
            $operator = $filters['min_stok_operator'];
            $value = $filters['min_stok_value'];
            $builder->where("tbl_m_item_stok.jml $operator", $value);
        }
        
        // Price filters - from tbl_m_item
        if (!empty($filters['harga_beli_operator']) && !empty($filters['harga_beli_value'])) {
            $operator = $filters['harga_beli_operator'];
            $value = str_replace(['.', ','], '', $filters['harga_beli_value']); // remove formatting
            $builder->where("tbl_m_item.harga_beli $operator", $value);
        }
        
        if (!empty($filters['harga_jual_operator']) && !empty($filters['harga_jual_value'])) {
            $operator = $filters['harga_jual_operator'];
            $value = str_replace(['.', ','], '', $filters['harga_jual_value']); // remove formatting
            $builder->where("tbl_m_item.harga_jual $operator", $value);
        }
        
        return $builder->orderBy('tbl_m_gudang.nama', 'ASC')
                      ->orderBy('tbl_m_item.item', 'ASC');
    }
    
    /**
     * Mendapatkan total stok item di semua outlets
     */
    public function getTotalItemStockInOutlets($item_id)
    {
        $result = $this->db->table('tbl_m_gudang')
            ->select('SUM(COALESCE(tbl_m_item_stok.jml, 0)) as total_stok')
            ->join('tbl_m_item_stok', 'tbl_m_item_stok.id_gudang = tbl_m_gudang.id', 'left')
            ->where('tbl_m_gudang.status_otl', '1') // outlets only
            ->where('tbl_m_gudang.status', '1') // gudang aktif
            ->where('tbl_m_gudang.status_hps', '0') // tidak terhapus
            ->where('tbl_m_item_stok.id_item', $item_id)
            ->get()
            ->getRow();
        
        return $result ? $result->total_stok : 0;
    }
} 