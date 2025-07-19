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
                tbl_m_gudang.gudang,
                COALESCE(tbl_m_item_stok.jml, 0) as stok
            ')
            ->join('tbl_m_item_stok', 'tbl_m_item_stok.id_gudang = tbl_m_gudang.id AND tbl_m_item_stok.id_item = ' . $item_id, 'left')
            ->get()
            ->getResult();
    }
} 