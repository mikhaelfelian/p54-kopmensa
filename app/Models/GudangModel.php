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
    protected $allowedFields    = ['kode', 'gudang', 'keterangan', 'status', 'status_gd', 'updated_at'];

    // Pengaturan tanggal
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validasi
    protected $validationRules = [
        'gudang'     => 'required|max_length[160]',
        'kode'       => 'permit_empty|max_length[160]',
        'status'     => 'permit_empty|in_list[0,1]',
        'status_gd'  => 'permit_empty|in_list[0,1]',
    ];

    /**
     * Menghasilkan kode unik untuk gudang
     * Format: GDG-001, GDG-002, dll
     */
    public function generateKode()
    {
        $prefix = 'GDG-';
        $lastKode = $this->select('kode')
                        ->like('kode', $prefix, 'after')
                        ->orderBy('kode', 'DESC')
                        ->first();

        if (!$lastKode) {
            return $prefix . '001';
        }

        $lastNumber = (int) substr($lastKode->kode, strlen($prefix));
        $newNumber = $lastNumber + 1;
        
        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
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