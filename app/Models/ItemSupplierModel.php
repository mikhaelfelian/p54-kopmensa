<?php
/**
 * Created by: Kopmensa Dev Team
 * Date: 2025-01-29
 * 
 * ItemSupplierModel
 * 
 * This model handles database operations for item-supplier mapping
 */

namespace App\Models;

use CodeIgniter\Model;

class ItemSupplierModel extends Model
{
    protected $table            = 'tbl_m_item_supplier';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_item',
        'id_supplier',
        'harga_beli',
        'prioritas'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id_item'      => 'required|integer',
        'id_supplier'  => 'required|integer',
        'harga_beli'   => 'permit_empty|decimal',
        'prioritas'    => 'permit_empty|integer'
    ];

    protected $validationMessages = [
        'id_item' => [
            'required' => 'Item harus dipilih',
            'integer'  => 'ID item tidak valid'
        ],
        'id_supplier' => [
            'required' => 'Supplier harus dipilih',
            'integer'  => 'ID supplier tidak valid'
        ]
    ];

    /**
     * Get all suppliers for a specific item
     * 
     * @param int $itemId
     * @return array
     */
    public function getSuppliersByItem($itemId)
    {
        return $this->select('tbl_m_item_supplier.*, tbl_m_supplier.kode as supplier_kode, tbl_m_supplier.nama as supplier_nama, tbl_m_supplier.no_tlp, tbl_m_supplier.alamat')
                    ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_m_item_supplier.id_supplier')
                    ->where('tbl_m_item_supplier.id_item', $itemId)
                    ->where('tbl_m_supplier.status_hps', '0')
                    ->orderBy('tbl_m_item_supplier.prioritas', 'ASC')
                    ->findAll();
    }

    /**
     * Get all items for a specific supplier
     * 
     * @param int $supplierId
     * @return array
     */
    public function getItemsBySupplier($supplierId)
    {
        return $this->select('tbl_m_item_supplier.*, tbl_m_item.kode as item_kode, tbl_m_item.item as item_nama, tbl_m_item.harga_jual')
                    ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_supplier.id_item')
                    ->where('tbl_m_item_supplier.id_supplier', $supplierId)
                    ->where('tbl_m_item.status_hps', '0')
                    ->orderBy('tbl_m_item.item', 'ASC')
                    ->findAll();
    }

    /**
     * Check if item-supplier mapping already exists
     * 
     * @param int $itemId
     * @param int $supplierId
     * @return bool
     */
    public function mappingExists($itemId, $supplierId)
    {
        return $this->where('id_item', $itemId)
                    ->where('id_supplier', $supplierId)
                    ->first() !== null;
    }

    /**
     * Add or update item-supplier mapping
     * 
     * @param int $itemId
     * @param int $supplierId
     * @param float $hargaBeli
     * @param int $prioritas
     * @return bool|int
     */
    public function addOrUpdateMapping($itemId, $supplierId, $hargaBeli = 0, $prioritas = 0)
    {
        $existing = $this->where('id_item', $itemId)
                         ->where('id_supplier', $supplierId)
                         ->first();

        if ($existing) {
            // Update existing mapping
            return $this->update($existing->id, [
                'harga_beli' => $hargaBeli,
                'prioritas'  => $prioritas
            ]);
        } else {
            // Insert new mapping
            return $this->insert([
                'id_item'      => $itemId,
                'id_supplier'  => $supplierId,
                'harga_beli'   => $hargaBeli,
                'prioritas'    => $prioritas
            ]);
        }
    }

    /**
     * Remove item-supplier mapping
     * 
     * @param int $itemId
     * @param int $supplierId
     * @return bool
     */
    public function removeMapping($itemId, $supplierId)
    {
        return $this->where('id_item', $itemId)
                    ->where('id_supplier', $supplierId)
                    ->delete();
    }

    /**
     * Get default (highest priority) supplier for an item
     * 
     * @param int $itemId
     * @return object|null
     */
    public function getDefaultSupplier($itemId)
    {
        return $this->select('tbl_m_item_supplier.*, tbl_m_supplier.kode as supplier_kode, tbl_m_supplier.nama as supplier_nama')
                    ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_m_item_supplier.id_supplier')
                    ->where('tbl_m_item_supplier.id_item', $itemId)
                    ->where('tbl_m_supplier.status_hps', '0')
                    ->orderBy('tbl_m_item_supplier.prioritas', 'ASC')
                    ->first();
    }
}

