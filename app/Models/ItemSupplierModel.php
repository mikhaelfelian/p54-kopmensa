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

    /**
     * Get items not yet assigned to a supplier
     * 
     * @param int $supplierId Optional supplier ID to exclude
     * @return array
     */
    public function getUnassignedItems($supplierId = null)
    {
        $builder = $this->db->table('tbl_m_item')
                            ->select('tbl_m_item.id, tbl_m_item.kode, tbl_m_item.item, tbl_m_item.harga_jual')
                            ->where('tbl_m_item.status_hps', '0');

        if ($supplierId) {
            // Exclude items already assigned to this supplier
            $builder->whereNotIn('tbl_m_item.id', function($query) use ($supplierId) {
                return $query->select('id_item')
                            ->from('tbl_m_item_supplier')
                            ->where('id_supplier', $supplierId)
                            ->where('deleted_at IS NULL', null, false);
            });
        } else {
            // Get items with no supplier assignments
            $builder->whereNotIn('tbl_m_item.id', function($query) {
                return $query->select('id_item')
                            ->from('tbl_m_item_supplier')
                            ->where('deleted_at IS NULL', null, false);
            });
        }

        return $builder->orderBy('tbl_m_item.item', 'ASC')->get()->getResult();
    }

    /**
     * Bulk assign items to supplier
     * 
     * @param int $supplierId
     * @param array $itemIds
     * @param float $defaultHargaBeli
     * @param int $defaultPrioritas
     * @return array Results with success/failure counts
     */
    public function bulkAssignItems($supplierId, $itemIds, $defaultHargaBeli = 0, $defaultPrioritas = 0)
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        foreach ($itemIds as $itemId) {
            try {
                // Check if mapping already exists
                if ($this->mappingExists($itemId, $supplierId)) {
                    $results['skipped']++;
                    continue;
                }

                // Insert new mapping
                $inserted = $this->insert([
                    'id_item'      => $itemId,
                    'id_supplier'  => $supplierId,
                    'harga_beli'   => $defaultHargaBeli,
                    'prioritas'    => $defaultPrioritas
                ]);

                if ($inserted) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Failed to assign item ID: $itemId";
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Error assigning item ID $itemId: " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Bulk remove items from supplier
     * 
     * @param int $supplierId
     * @param array $itemIds
     * @return array Results with success/failure counts
     */
    public function bulkRemoveItems($supplierId, $itemIds)
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($itemIds as $itemId) {
            try {
                $deleted = $this->removeMapping($itemId, $supplierId);
                if ($deleted) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Failed to remove item ID: $itemId";
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Error removing item ID $itemId: " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Get supplier statistics
     * 
     * @param int $supplierId
     * @return object
     */
    public function getSupplierStats($supplierId)
    {
        $totalItems = $this->where('id_supplier', $supplierId)->countAllResults(false);
        
        $avgPrice = $this->selectAvg('harga_beli')
                         ->where('id_supplier', $supplierId)
                         ->where('harga_beli >', 0)
                         ->first();

        $priceRange = $this->select('MIN(harga_beli) as min_price, MAX(harga_beli) as max_price')
                           ->where('id_supplier', $supplierId)
                           ->where('harga_beli >', 0)
                           ->first();

        return (object) [
            'total_items' => $totalItems,
            'avg_price' => $avgPrice ? $avgPrice->harga_beli : 0,
            'min_price' => $priceRange ? $priceRange->min_price : 0,
            'max_price' => $priceRange ? $priceRange->max_price : 0
        ];
    }

    /**
     * Search items for supplier assignment
     * 
     * @param int $supplierId
     * @param string $searchTerm
     * @return array
     */
    public function searchItemsForAssignment($supplierId, $searchTerm = '')
    {
        $builder = $this->db->table('tbl_m_item')
                            ->select('tbl_m_item.id, tbl_m_item.kode, tbl_m_item.item, tbl_m_item.harga_jual')
                            ->where('tbl_m_item.status_hps', '0');

        if ($searchTerm) {
            $builder->groupStart()
                    ->like('tbl_m_item.item', $searchTerm)
                    ->orLike('tbl_m_item.kode', $searchTerm)
                    ->groupEnd();
        }

        // Exclude items already assigned to this supplier
        $builder->whereNotIn('tbl_m_item.id', function($query) use ($supplierId) {
            return $query->select('id_item')
                        ->from('tbl_m_item_supplier')
                        ->where('id_supplier', $supplierId)
                        ->where('deleted_at IS NULL', null, false);
        });

        return $builder->orderBy('tbl_m_item.item', 'ASC')
                       ->limit(50)
                       ->get()
                       ->getResult();
    }
}

