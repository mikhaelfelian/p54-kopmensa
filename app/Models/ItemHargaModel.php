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
        'id_item', 'nama', 'jml_min', 'harga', 'keterangan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get prices by item ID
     */
    public function getPricesByItemId($itemId)
    {
        return $this->where('id_item', $itemId)
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
} 