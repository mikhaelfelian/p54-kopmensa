<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2024-07-15
 * Github : github.com/mikhaelfelian
 * description : Model for managing item stock data
 * This file represents the ItemStokModel.
 */
class ItemStokModel extends Model
{
    protected $table            = 'tbl_m_item_stok';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_item',
        'id_gudang',
        'id_outlet',
        'id_user',
        'jml',
        'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get stock data for a specific item
     *
     * @param int $itemId
     * @return array
     */
    public function getStockByItem($itemId)
    {
        return $this->where('id_item', $itemId)
                    ->findAll();
    }

    /**
     * Get stock data for a specific item and warehouse
     *
     * @param int $itemId
     * @param int $gudangId
     * @return object|null
     */
    public function getStockByItemAndGudang($itemId, $gudangId)
    {
        return $this->where('id_item', $itemId)
                    ->where('id_gudang', $gudangId)
                    ->first();
    }

    /**
     * Get stock data for a specific item and outlet
     *
     * @param int $itemId
     * @param int $outletId
     * @return object|null
     */
    public function getStockByItemAndOutlet($itemId, $outletId)
    {
        return $this->select('tbl_m_item_stok.*, tbl_m_satuan.satuanBesar as satuan_nama, tbl_m_item.item as item_nama')
                    ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'left')
                    ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
                    ->where('tbl_m_item_stok.id_item', $itemId)
                    ->where('tbl_m_item_stok.id_outlet', $outletId)
                    ->first();
    }

    /**
     * Update stock quantity for an item in warehouse
     *
     * @param int $itemId
     * @param int $gudangId
     * @param float $quantity
     * @param int $userId
     * @return bool
     */
    public function updateStock($itemId, $gudangId, $quantity, $userId = 1)
    {
        $existingStock = $this->getStockByItemAndGudang($itemId, $gudangId);
        
        if ($existingStock) {
            // Update existing stock
            return $this->update($existingStock->id, [
                'jml' => $quantity,
                'id_user' => $userId
            ]);
        } else {
            // Create new stock record
            return $this->insert([
                'id_item' => $itemId,
                'id_gudang' => $gudangId,
                'jml' => $quantity,
                'id_user' => $userId,
                'status' => '1'
            ]);
        }
    }

    /**
     * Update stock quantity for an item in outlet
     *
     * @param int $itemId
     * @param int $outletId
     * @param float $quantity
     * @param int $userId
     * @return bool
     */
    public function updateStockOutlet($itemId, $outletId, $quantity, $userId = 1)
    {
        $existingStock = $this->getStockByItemAndOutlet($itemId, $outletId);
        
        if ($existingStock) {
            // Update existing stock
            return $this->update($existingStock->id, [
                'jml' => $quantity,
                'id_user' => $userId
            ]);
        } else {
            // Create new stock record
            return $this->insert([
                'id_item' => $itemId,
                'id_outlet' => $outletId,
                'jml' => $quantity,
                'id_user' => $userId,
                'status' => '1'
            ]);
        }
    }

    /**
     * Get total stock for an item across all warehouses
     *
     * @param int $itemId
     * @return float
     */
    public function getTotalStock($itemId)
    {
        $result = $this->selectSum('jml')
                       ->where('id_item', $itemId)
                       ->where('status', '1')
                       ->first();
        
        return $result ? (float) $result->jml : 0;
    }
}