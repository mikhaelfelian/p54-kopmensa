<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-11
 * Github : github.com/mikhaelfelian
 * description : Model for managing purchase transaction detail data
 * This file represents the Model for TransBeliDetModel.
 */
class TransBeliDetModel extends Model
{
    protected $table            = 'tbl_trans_beli_det';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
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
        'status_item'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'id_user' => 'permit_empty|integer',
        'id_pembelian' => 'required|integer',
        'id_item' => 'permit_empty|integer',
        'id_satuan' => 'permit_empty|integer',
        'tgl_masuk' => 'permit_empty|valid_date',
        'tgl_terima' => 'permit_empty|valid_date',
        'tgl_ed' => 'permit_empty|valid_date',
        'kode' => 'permit_empty|max_length[50]',
        'kode_batch' => 'permit_empty|max_length[50]',
        'item' => 'permit_empty|max_length[160]',
        'jml' => 'permit_empty|decimal',
        'jml_satuan' => 'permit_empty|integer',
        'jml_diterima' => 'permit_empty|integer',
        'jml_retur' => 'permit_empty|integer',
        'satuan' => 'permit_empty|max_length[160]',
        'harga' => 'permit_empty|decimal',
        'disk1' => 'permit_empty|decimal',
        'disk2' => 'permit_empty|decimal',
        'disk3' => 'permit_empty|decimal',
        'diskon' => 'permit_empty|decimal',
        'potongan' => 'permit_empty|decimal',
        'subtotal' => 'permit_empty|decimal',
        'satuan_retur' => 'permit_empty|max_length[160]',
        'keterangan' => 'permit_empty|max_length[160]',
        'status_item' => 'permit_empty|integer'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get purchase transaction details by purchase ID
     */
    public function getDetailsByPurchaseId($purchaseId)
    {
        return $this->where('id_pembelian', $purchaseId)->findAll();
    }

    /**
     * Get purchase transaction detail by ID
     */
    public function getDetailById($id)
    {
        return $this->find($id);
    }

    /**
     * Get details with item information
     */
    public function getDetailsWithItem($purchaseId)
    {
        $builder = $this->builder();
        $builder->select('tbl_trans_beli_det.*, tbl_m_item.nama as nama_item, tbl_m_satuan.nama as nama_satuan');
        $builder->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_beli_det.id_item', 'left');
        $builder->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_trans_beli_det.id_satuan', 'left');
        $builder->where('tbl_trans_beli_det.id_pembelian', $purchaseId);
        
        return $builder->get()->getResultArray();
    }

    /**
     * Calculate subtotal for a detail item
     */
    public function calculateSubtotal($harga, $jml, $diskon = 0, $potongan = 0)
    {
        $subtotal = $harga * $jml;
        $subtotal -= $diskon;
        $subtotal -= $potongan;
        
        return max(0, $subtotal);
    }

    /**
     * Calculate discount based on disk1, disk2, disk3
     */
    public function calculateDiscount($harga, $jml, $disk1 = 0, $disk2 = 0, $disk3 = 0)
    {
        $subtotal = $harga * $jml;
        $discount = 0;
        
        if ($disk1 > 0) {
            $discount += ($subtotal * $disk1 / 100);
        }
        
        if ($disk2 > 0) {
            $subtotalAfterDisk1 = $subtotal - $discount;
            $discount += ($subtotalAfterDisk1 * $disk2 / 100);
        }
        
        if ($disk3 > 0) {
            $subtotalAfterDisk2 = $subtotal - $discount;
            $discount += ($subtotalAfterDisk2 * $disk3 / 100);
        }
        
        return $discount;
    }

    /**
     * Format currency values
     */
    public function formatCurrency($amount)
    {
        return number_format($amount, 2, ',', '.');
    }

    /**
     * Get total quantity received for a purchase
     */
    public function getTotalReceived($purchaseId)
    {
        $result = $this->selectSum('jml_diterima')
                      ->where('id_pembelian', $purchaseId)
                      ->first();
        
        return $result['jml_diterima'] ?? 0;
    }

    /**
     * Get total quantity returned for a purchase
     */
    public function getTotalReturned($purchaseId)
    {
        $result = $this->selectSum('jml_retur')
                      ->where('id_pembelian', $purchaseId)
                      ->first();
        
        return $result['jml_retur'] ?? 0;
    }

    /**
     * Get total subtotal for a purchase
     */
    public function getTotalSubtotal($purchaseId)
    {
        $result = $this->selectSum('subtotal')
                      ->where('id_pembelian', $purchaseId)
                      ->first();
        
        return $result['subtotal'] ?? 0;
    }

    /**
     * Update received quantity for a detail item
     */
    public function updateReceivedQuantity($id, $quantity)
    {
        return $this->update($id, ['jml_diterima' => $quantity]);
    }

    /**
     * Update returned quantity for a detail item
     */
    public function updateReturnedQuantity($id, $quantity)
    {
        return $this->update($id, ['jml_retur' => $quantity]);
    }

    /**
     * Get items with batch tracking
     */
    public function getItemsWithBatch($purchaseId)
    {
        return $this->where('id_pembelian', $purchaseId)
                   ->where('kode_batch IS NOT NULL')
                   ->where('kode_batch !=', '')
                   ->findAll();
    }

    /**
     * Get items with expiry date tracking
     */
    public function getItemsWithExpiry($purchaseId)
    {
        return $this->where('id_pembelian', $purchaseId)
                   ->where('tgl_ed IS NOT NULL')
                   ->findAll();
    }

    /**
     * Get items that need to be received
     */
    public function getItemsToReceive($purchaseId)
    {
        return $this->where('id_pembelian', $purchaseId)
                   ->where('jml_diterima < jml')
                   ->findAll();
    }

    /**
     * Get items that can be returned
     */
    public function getItemsToReturn($purchaseId)
    {
        return $this->where('id_pembelian', $purchaseId)
                   ->where('jml_diterima > jml_retur')
                   ->findAll();
    }
} 