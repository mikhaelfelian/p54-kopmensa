<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-04
 * 
 * TransBeliDet Model
 * Handles database operations for tbl_trans_beli_det table
 */

namespace App\Models;

use CodeIgniter\Model;

class TransBeliDetModel extends Model
{
    protected $table            = 'tbl_trans_beli_det';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_user',
        'id_pembelian',
        'id_item',
        'id_satuan',
        'created_at',
        'updated_at',
        'tgl_masuk',
        'tgl_terima',
        'tgl_ed',
        'kode',
        'kode_batch',
        'item',
        'jml',
        'jml_satuan',
        'jml_diterima',
        'jml_retur',
        'satuan',
        'harga',
        'disk1',
        'disk2',
        'disk3',
        'diskon',
        'potongan',
        'subtotal',
        'satuan_retur',
        'keterangan',
        'status_item',
        'id_gudang',
        'status_terima',
        'keterangan_terima'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    /**
     * Get purchase transaction details with item data
     *
     * @param int $id_pembelian
     * @return array
     */
    public function getWithItem($id_pembelian)
    {
        return $this->select('tbl_trans_beli_det.*, item.nama as item_name, satuan.nama as satuan_name')
                   ->join('tbl_m_item item', 'item.id = tbl_trans_beli_det.id_item', 'left')
                   ->join('tbl_m_satuan satuan', 'satuan.id = tbl_trans_beli_det.id_satuan', 'left')
                   ->where('id_pembelian', $id_pembelian)
                   ->findAll();
    }
} 