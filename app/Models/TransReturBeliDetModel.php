<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-25
 * Github : github.com/mikhaelfelian
 * description : Model for managing purchase return detail data
 * This file represents the Model.
 */

namespace App\Models;

use CodeIgniter\Model;

class TransReturBeliDetModel extends Model
{
    protected $table            = 'tbl_trans_retur_beli_det';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_retur', 'id_beli_det', 'id_user', 'id_item', 'id_satuan', 
        'id_gudang', 'tgl_masuk', 'tgl_keluar', 'kode', 'kode_batch', 
        'item', 'keterangan', 'satuan', 'jml', 'harga', 
        'disk1', 'disk2', 'disk3', 'diskon', 'potongan', 'subtotal'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id_retur'   => 'required|integer',
        'id_item'    => 'required|integer',
        'jml'  => 'required|decimal',
        'harga'      => 'required|decimal'
    ];

    protected $validationMessages = [
        'id_retur' => [
            'required' => 'ID retur harus diisi.',
            'integer'  => 'ID retur harus berupa angka.'
        ],
        'id_item' => [
            'required' => 'Item harus dipilih.',
            'integer'  => 'ID item harus berupa angka.'
        ],
        'jml' => [
            'required' => 'Jumlah harus diisi.',
            'decimal'  => 'Jumlah harus berupa angka.'
        ],
        'harga' => [
            'required' => 'Harga harus diisi.',
            'decimal'  => 'Harga harus berupa angka.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get details by return ID with item information
     */
    public function getDetailsByReturId($returId)
    {
        $builder = $this->builder();
        $builder->select('
            tbl_trans_retur_beli_det.*,
            tbl_m_item.item as item_nama,
            tbl_m_item.kode as item_kode,
            tbl_m_satuan.satuanBesar as satuan_nama,
            tbl_m_kategori.kategori as kategori_nama,
            tbl_m_merk.merk as merk_nama
        ');
        $builder->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_retur_beli_det.id_item', 'left');
        $builder->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_trans_retur_beli_det.id_satuan', 'left');
        $builder->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left');
        $builder->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left');
        $builder->where('tbl_trans_retur_beli_det.id_retur', $returId);
        $builder->orderBy('tbl_trans_retur_beli_det.id', 'ASC');
        
        return $builder->get()->getResult();
    }

    /**
     * Calculate total for return
     */
    public function calculateTotal($returId)
    {
        $builder = $this->builder();
        $builder->selectSum('subtotal', 'total');
        $builder->where('id_retur', $returId);
        
        $result = $builder->get()->getRow();
        return $result->total ?? 0;
    }

    /**
     * Get items summary for return
     */
    public function getItemsSummary($returId)
    {
        $builder = $this->builder();
        $builder->selectSum('jml', 'total_qty');
        $builder->selectSum('subtotal', 'total_amount');
        $builder->selectCount('id', 'total_items');
        $builder->where('id_retur', $returId);
        
        return $builder->get()->getRow();
    }
} 