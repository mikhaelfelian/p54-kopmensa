<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-05
 * Github : github.com/mikhaelfelian
 * description : Model for item price management
 * This file represents the Model for ItemHargaModel.
 */

namespace App\Models;

use CodeIgniter\Model;

class ItemHargaModel extends Model
{
    protected $table            = 'tbl_m_item_harga';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_item', 'nama', 'jml_min', 'harga', 'keterangan', 'id_grup_pelanggan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get prices by item ID
     */
    public function getPricesByItemId($itemId, $selectFields = '*')
    {
        return $this->select($selectFields)
                    ->where('id_item', $itemId)
                    ->orderBy('jml_min', 'ASC')
                    ->findAll();
    }

    /**
     * Get price by item ID and quantity
     */
    public function getPriceByQuantity($itemId, $quantity)
    {
        return $this->where('id_item', $itemId)
                    ->where('jml_min <=', $quantity)
                    ->orderBy('jml_min', 'DESC')
                    ->first();
    }

    /**
     * Get all prices with item information
     */
    public function getPricesWithItem()
    {
        return $this->select('tbl_m_item_harga.*, tbl_m_item.item as nama_item, tbl_m_item.kode as kode_item')
                    ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_harga.id_item')
                    ->findAll();
    }

    /**
     * Get price by item ID, customer group and quantity
     */
    public function getPriceByGroupAndQuantity($itemId, $customerGroup, $quantity)
    {
        return $this->where('id_item', $itemId)
                    ->where('nama', $customerGroup)
                    ->where('jml_min <=', $quantity)
                    ->orderBy('jml_min', 'DESC')
                    ->first();
    }

    /**
     * Get prices by item ID and customer group
     */
    public function getPricesByItemAndGroup($itemId, $customerGroup)
    {
        return $this->where('id_item', $itemId)
                    ->where('nama', $customerGroup)
                    ->orderBy('jml_min', 'ASC')
                    ->findAll();
    }

    /**
     * Get all customer groups for an item
     */
    public function getCustomerGroupsByItem($itemId)
    {
        return $this->select('nama as grup, deskripsi')
                    ->where('id_item', $itemId)
                    ->groupBy('nama')
                    ->orderBy('nama', 'ASC')
                    ->findAll();
    }

    /**
     * Get pricing rules for checkout based on item, customer group and quantity
     */
    public function getPricingRulesForCheckout($itemId, $customerGroup = null, $quantity = 1)
    {
        $query = $this->where('id_item', $itemId);
        
        if ($customerGroup) {
            $query->where('nama', $customerGroup);
        }
        
        return $query->where('jml_min <=', $quantity)
                    ->orderBy('jml_min', 'DESC')
                    ->orderBy('nama', 'ASC')
                    ->findAll();
    }

    /**
     * Get best price for a given item, customer group and quantity
     */
    public function getBestPrice($itemId, $customerGroup, $quantity)
    {
        return $this->where('id_item', $itemId)
                    ->where('nama', $customerGroup)
                    ->where('jml_min', '<=', $quantity)
                    ->orderBy('jml_min', 'DESC')
                    ->orderBy('harga', 'ASC')
                    ->first();
    }
}