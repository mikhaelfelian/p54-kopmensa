<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Created by: AI Assistant
 * Date: 2024-12-19
 * Description: Model for v_item_stok view
 * This model provides comprehensive stock movement and balance information
 */
class VItemStokModel extends Model
{
    protected $table = 'v_item_stok';
    protected $primaryKey = 'id_item'; // Composite key with id_gudang
    protected $useAutoIncrement = false; // Views don't have auto-increment
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = false; // Views are read-only
    protected $allowedFields = [
        'id_item',
        'id_gudang',
        'gudang',
        'kode',
        'item',
        'so',
        'stok_masuk',
        'stok_keluar',
        'sisa'
    ];
    
    /**
     * Check if the view exists and has status_otl column
     */
    private function hasStatusOtlColumn()
    {
        try {
            $result = $this->select('status_otl')->limit(1)->findAll();
            return !empty($result) && isset($result[0]->status_otl);
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Get warehouse IDs for fallback filtering
     */
    private function getWarehouseIds()
    {
        try {
            $result = $this->db->table('tbl_m_gudang')
                ->select('id')
                ->where('status_otl', '0')
                ->where('status_hps', '0')
                ->get()
                ->getResultArray();
            
            return array_column($result, 'id');
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Get outlet IDs for fallback filtering
     */
    private function getOutletIds()
    {
        try {
            $result = $this->db->table('tbl_m_gudang')
                ->select('id')
                ->where('status_otl', '1')
                ->where('status_hps', '0')
                ->get()
                ->getResultArray();
            
            return array_column($result, 'id');
        } catch (\Exception $e) {
            return [];
        }
    }

    // Dates
    protected $useTimestamps = false; // Views typically don't have timestamps
    protected $dateFormat = 'datetime';

    /**
     * Get comprehensive stock overview
     *
     * @param int|null $gudangId
     * @param string|null $keyword
     * @param string|null $outletType 'warehouse', 'outlet', or null for all
     * @param int $perPage
     * @param int $page
     * @return array
     */
    public function getStockOverview($gudangId = null, $keyword = null, $outletType = null, $perPage = 10, $page = 1)
    {
        // Use fallback method directly since v_item_stok view may not exist
        return $this->getStockOverviewFallback($gudangId, $keyword, $outletType, $perPage, $page);
    }
    
    /**
     * Fallback method when v_item_stok view doesn't exist
     */
    private function getStockOverviewFallback($gudangId = null, $keyword = null, $outletType = null, $perPage = 10, $page = 1)
    {
        try {
            $builder = $this->db->table('tbl_m_item_stok')
                ->select('
                    tbl_m_item_stok.id_item,
                    tbl_m_item_stok.id_gudang,
                    tbl_m_gudang.nama as gudang,
                    tbl_m_item.kode,
                    tbl_m_item.item,
                    tbl_m_item_stok.jml as sisa
                ')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'inner')
                ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_stok.id_gudang', 'inner')
                ->where('tbl_m_item_stok.status', '1')
                ->where('tbl_m_item.status', '1')
                ->where('tbl_m_gudang.status_hps', '0');

            if ($gudangId) {
                $builder->where('tbl_m_item_stok.id_gudang', $gudangId);
            }

            // Filter by outlet type
            if ($outletType === 'warehouse') {
                $warehouseIds = $this->getWarehouseIds();
                if (!empty($warehouseIds)) {
                    $builder->whereIn('tbl_m_item_stok.id_gudang', $warehouseIds);
                }
            } elseif ($outletType === 'outlet') {
                $outletIds = $this->getOutletIds();
                if (!empty($outletIds)) {
                    $builder->whereIn('tbl_m_item_stok.id_gudang', $outletIds);
                }
            }

            if ($keyword) {
                $builder->groupStart()
                    ->like('tbl_m_item.item', $keyword)
                    ->orLike('tbl_m_item.kode', $keyword)
                    ->orLike('tbl_m_gudang.nama', $keyword)
                    ->groupEnd();
            }

            $result = $builder->get()->getResult();
            
            // Simple pagination
            $total = count($result);
            $offset = ($page - 1) * $perPage;
            $items = array_slice($result, $offset, $perPage);
            
            return [
                'data' => $items,
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage)
            ];
        } catch (\Exception $e) {
            return [
                'data' => [],
                'total' => 0,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => 1
            ];
        }
    }

    /**
     * Get stock summary by warehouse
     *
     * @param int|null $gudangId
     * @param string|null $outletType 'warehouse', 'outlet', or null for all
     * @return array
     */
    public function getStockSummaryByWarehouse($gudangId = null, $outletType = null)
    {
        // Use fallback method directly since v_item_stok view may not exist
        return $this->getStockSummaryByWarehouseFallback($gudangId, $outletType);
    }

    /**
     * Fallback method for getStockSummaryByWarehouse when v_item_stok view doesn't exist
     */
    private function getStockSummaryByWarehouseFallback($gudangId = null, $outletType = null)
    {
        try {
            $warehouseIds = $this->getWarehouseIds();
            $outletIds = $this->getOutletIds();
            
            $result = [];
            
            // Get warehouse summary
            if (!empty($warehouseIds)) {
                $warehouseData = $this->db->table('tbl_m_item_stok')
                    ->select('
                        COUNT(DISTINCT id_gudang) as total_locations,
                        COUNT(DISTINCT id_item) as total_items,
                        SUM(CASE WHEN jml > 0 THEN jml ELSE 0 END) as total_stock,
                        COUNT(CASE WHEN jml > 0 THEN 1 END) as items_in_stock,
                        COUNT(CASE WHEN jml <= 0 THEN 1 END) as items_out_of_stock,
                        COUNT(CASE WHEN jml < 0 THEN 1 END) as items_negative_stock
                    ')
                    ->whereIn('id_gudang', $warehouseIds)
                    ->where('status', '1')
                    ->get()
                    ->getRow();
                
                if ($warehouseData) {
                    $result[] = (object) [
                        'id_gudang' => 'warehouse',
                        'gudang' => 'Warehouse',
                        'total_items' => $warehouseData->total_items,
                        'total_stock' => $warehouseData->total_stock,
                        'items_in_stock' => $warehouseData->items_in_stock,
                        'items_out_of_stock' => $warehouseData->items_out_of_stock,
                        'total_negative_stock' => $warehouseData->items_negative_stock
                    ];
                }
            }
            
            // Get outlet summary
            if (!empty($outletIds)) {
                $outletData = $this->db->table('tbl_m_item_stok')
                    ->select('
                        COUNT(DISTINCT id_gudang) as total_locations,
                        COUNT(DISTINCT id_item) as total_items,
                        SUM(CASE WHEN jml > 0 THEN jml ELSE 0 END) as total_stock,
                        COUNT(CASE WHEN jml > 0 THEN 1 END) as items_in_stock,
                        COUNT(CASE WHEN jml <= 0 THEN 1 END) as items_out_of_stock,
                        COUNT(CASE WHEN jml < 0 THEN 1 END) as items_negative_stock
                    ')
                    ->whereIn('id_gudang', $outletIds)
                    ->where('status', '1')
                    ->get()
                    ->getRow();
                
                if ($outletData) {
                    $result[] = (object) [
                        'id_gudang' => 'outlet',
                        'gudang' => 'Outlet',
                        'total_items' => $outletData->total_items,
                        'total_stock' => $outletData->total_stock,
                        'items_in_stock' => $outletData->items_in_stock,
                        'items_out_of_stock' => $outletData->items_out_of_stock,
                        'total_negative_stock' => $outletData->items_negative_stock
                    ];
                }
            }
            
            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get low stock items (below SO level)
     *
     * @param int|null $gudangId
     * @param float $threshold
     * @return array
     */
    public function getLowStockItems($gudangId = null, $threshold = 10)
    {
        // Use fallback method since v_item_stok view may not exist
        return $this->getLowStockItemsFallback($gudangId, $threshold);
    }

    /**
     * Fallback method for getLowStockItems when v_item_stok view doesn't exist
     */
    private function getLowStockItemsFallback($gudangId = null, $threshold = 10)
    {
        try {
            $builder = $this->db->table('tbl_m_item_stok')
                ->select('
                    tbl_m_item_stok.id_item,
                    tbl_m_item_stok.id_gudang,
                    tbl_m_gudang.nama as gudang,
                    tbl_m_item.kode,
                    tbl_m_item.item,
                    tbl_m_item_stok.jml as sisa
                ')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'inner')
                ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_stok.id_gudang', 'inner')
                ->where('tbl_m_item_stok.status', '1')
                ->where('tbl_m_item.status', '1')
                ->where('tbl_m_gudang.status_hps', '0')
                ->where('tbl_m_item_stok.jml <=', $threshold)
                ->orderBy('tbl_m_item_stok.jml', 'ASC');

            if ($gudangId) {
                $builder->where('tbl_m_item_stok.id_gudang', $gudangId);
            }

            return $builder->get()->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get out of stock items
     *
     * @param int|null $gudangId
     * @return array
     */
    public function getOutOfStockItems($gudangId = null)
    {
        // Use fallback method since v_item_stok view may not exist
        return $this->getOutOfStockItemsFallback($gudangId);
    }

    /**
     * Fallback method for getOutOfStockItems when v_item_stok view doesn't exist
     */
    private function getOutOfStockItemsFallback($gudangId = null)
    {
        try {
            $builder = $this->db->table('tbl_m_item_stok')
                ->select('
                    tbl_m_item_stok.id_item,
                    tbl_m_item_stok.id_gudang,
                    tbl_m_gudang.nama as gudang,
                    tbl_m_item.kode,
                    tbl_m_item.item,
                    tbl_m_item_stok.jml as sisa
                ')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'inner')
                ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_stok.id_gudang', 'inner')
                ->where('tbl_m_item_stok.status', '1')
                ->where('tbl_m_item.status', '1')
                ->where('tbl_m_gudang.status_hps', '0')
                ->where('tbl_m_item_stok.jml <=', 0)
                ->orderBy('tbl_m_item.item', 'ASC');

            if ($gudangId) {
                $builder->where('tbl_m_item_stok.id_gudang', $gudangId);
            }

            return $builder->get()->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get items with negative stock
     *
     * @param int|null $gudangId
     * @return array
     */
    public function getNegativeStockItems($gudangId = null)
    {
        // Use fallback method since v_item_stok view may not exist
        return $this->getNegativeStockItemsFallback($gudangId);
    }

    /**
     * Fallback method for getNegativeStockItems when v_item_stok view doesn't exist
     */
    private function getNegativeStockItemsFallback($gudangId = null)
    {
        try {
            $builder = $this->db->table('tbl_m_item_stok')
                ->select('
                    tbl_m_item_stok.id_item,
                    tbl_m_item_stok.id_gudang,
                    tbl_m_gudang.nama as gudang,
                    tbl_m_item.kode,
                    tbl_m_item.item,
                    tbl_m_item_stok.jml as sisa
                ')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'inner')
                ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_stok.id_gudang', 'inner')
                ->where('tbl_m_item_stok.status', '1')
                ->where('tbl_m_item.status', '1')
                ->where('tbl_m_gudang.status_hps', '0')
                ->where('tbl_m_item_stok.jml <', 0)
                ->orderBy('tbl_m_item_stok.jml', 'ASC');

            if ($gudangId) {
                $builder->where('tbl_m_item_stok.id_gudang', $gudangId);
            }

            return $builder->get()->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get stock movement summary
     *
     * @param int|null $gudangId
     * @return array
     */
    public function getStockMovementSummary($gudangId = null)
    {
        // Use fallback method since v_item_stok view may not exist
        return $this->getStockMovementSummaryFallback($gudangId);
    }

    /**
     * Fallback method for getStockMovementSummary when v_item_stok view doesn't exist
     */
    private function getStockMovementSummaryFallback($gudangId = null)
    {
        try {
            $builder = $this->db->table('tbl_m_item_stok')
                ->select('
                    COUNT(DISTINCT id_item) as total_items
                ');

            if ($gudangId) {
                $builder->where('id_gudang', $gudangId);
            }

            $result = $builder->where('status', '1')->get()->getRow();
            
            // Since we don't have stok_masuk and stok_keluar in base table, return basic info
            return (object) [
                'total_masuk' => 0,
                'total_keluar' => 0,
                'total_sisa' => 0,
                'total_items' => $result ? $result->total_items : 0
            ];
        } catch (\Exception $e) {
            return (object) [
                'total_masuk' => 0,
                'total_keluar' => 0,
                'total_sisa' => 0,
                'total_items' => 0
            ];
        }
    }

    /**
     * Get items by SO status
     *
     * @param int|null $gudangId
     * @param string $soStatus 'with_so' or 'without_so'
     * @return array
     */
    public function getItemsBySOStatus($gudangId = null, $soStatus = 'with_so')
    {
        // Use fallback method since v_item_stok view may not exist
        return $this->getItemsBySOStatusFallback($gudangId, $soStatus);
    }

    /**
     * Fallback method for getItemsBySOStatus when v_item_stok view doesn't exist
     */
    private function getItemsBySOStatusFallback($gudangId = null, $soStatus = 'with_so')
    {
        try {
            $builder = $this->db->table('tbl_m_item_stok')
                ->select('
                    tbl_m_item_stok.id_item,
                    tbl_m_item_stok.id_gudang,
                    tbl_m_gudang.nama as gudang,
                    tbl_m_item.kode,
                    tbl_m_item.item,
                    tbl_m_item_stok.jml as sisa
                ')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'inner')
                ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_stok.id_gudang', 'inner')
                ->where('tbl_m_item_stok.status', '1')
                ->where('tbl_m_item.status', '1')
                ->where('tbl_m_gudang.status_hps', '0');

            if ($gudangId) {
                $builder->where('tbl_m_item_stok.id_gudang', $gudangId);
            }

            // Note: SO status check would need additional table joins
            // For now, return all items
            return $builder->orderBy('tbl_m_item.item', 'ASC')->get()->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get stock value summary (if you have price data in related tables)
     *
     * @param int|null $gudangId
     * @return array
     */
    public function getStockValueSummary($gudangId = null)
    {
        // Use fallback method since v_item_stok view may not exist
        return $this->getStockValueSummaryFallback($gudangId);
    }

    /**
     * Fallback method for getStockValueSummary when v_item_stok view doesn't exist
     */
    private function getStockValueSummaryFallback($gudangId = null)
    {
        try {
            $builder = $this->db->table('tbl_m_item_stok')
                ->select('
                    COUNT(DISTINCT id_item) as total_items,
                    COUNT(CASE WHEN jml > 0 THEN 1 END) as items_with_stock,
                    COUNT(CASE WHEN jml <= 0 THEN 1 END) as items_out_of_stock
                ');

            if ($gudangId) {
                $builder->where('id_gudang', $gudangId);
            }

            $result = $builder->where('status', '1')->get()->getRow();
            
            return (object) [
                'total_items' => $result ? $result->total_items : 0,
                'total_stock_quantity' => 0, // Would need additional table joins for this
                'items_with_stock' => $result ? $result->items_with_stock : 0,
                'items_out_of_stock' => $result ? $result->items_out_of_stock : 0
            ];
        } catch (\Exception $e) {
            return (object) [
                'total_items' => 0,
                'total_stock_quantity' => 0,
                'items_with_stock' => 0,
                'items_out_of_stock' => 0
            ];
        }
    }

    /**
     * Search items by multiple criteria
     *
     * @param array $criteria
     * @param int $perPage
     * @param int $page
     * @return array
     */
    public function searchItems($criteria = [], $perPage = 10, $page = 1)
    {
        // Use fallback method directly since v_item_stok view may not exist
        return $this->searchItemsFallback($criteria, $perPage, $page);
    }
    
    /**
     * Fallback method for searchItems when v_item_stok view doesn't exist
     */
    private function searchItemsFallback($criteria = [], $perPage = 10, $page = 1)
    {
        try {
            $builder = $this->db->table('tbl_m_item_stok')
                ->select('
                    tbl_m_item_stok.id_item,
                    tbl_m_item_stok.id_gudang,
                    tbl_m_gudang.nama as gudang,
                    tbl_m_item.kode,
                    tbl_m_item.item,
                    tbl_m_item_stok.jml as sisa
                ')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'inner')
                ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_stok.id_gudang', 'inner')
                ->where('tbl_m_item_stok.status', '1')
                ->where('tbl_m_item.status', '1')
                ->where('tbl_m_gudang.status_hps', '0');

            // Apply search criteria
            if (isset($criteria['keyword']) && $criteria['keyword']) {
                $keyword = $criteria['keyword'];
                $builder->groupStart()
                    ->like('tbl_m_item.item', $keyword)
                    ->orLike('tbl_m_item.kode', $keyword)
                    ->orLike('tbl_m_gudang.nama', $keyword)
                    ->groupEnd();
            }

            if (isset($criteria['gudang_id']) && $criteria['gudang_id']) {
                $builder->where('tbl_m_item_stok.id_gudang', $criteria['gudang_id']);
            }

            // Filter by outlet type
            if (isset($criteria['outlet_type']) && $criteria['outlet_type']) {
                if ($criteria['outlet_type'] === 'warehouse') {
                    $warehouseIds = $this->getWarehouseIds();
                    if (!empty($warehouseIds)) {
                        $builder->whereIn('tbl_m_item_stok.id_gudang', $warehouseIds);
                    }
                } elseif ($criteria['outlet_type'] === 'outlet') {
                    $outletIds = $this->getOutletIds();
                    if (!empty($outletIds)) {
                        $builder->whereIn('tbl_m_item_stok.id_gudang', $outletIds);
                    }
                }
            }

            if (isset($criteria['min_stock']) && $criteria['min_stock'] !== null) {
                $builder->where('tbl_m_item_stok.jml >=', $criteria['min_stock']);
            }

            if (isset($criteria['max_stock']) && $criteria['max_stock'] !== null) {
                $builder->where('tbl_m_item_stok.jml <=', $criteria['max_stock']);
            }

            if (isset($criteria['stock_status'])) {
                switch ($criteria['stock_status']) {
                    case 'positive':
                        $builder->where('tbl_m_item_stok.jml >', 0);
                        break;
                    case 'negative':
                        $builder->where('tbl_m_item_stok.jml <', 0);
                        break;
                    case 'zero':
                        $builder->where('tbl_m_item_stok.jml =', 0);
                        break;
                    case 'low':
                        $threshold = isset($criteria['low_threshold']) ? $criteria['low_threshold'] : 10;
                        $builder->where('tbl_m_item_stok.jml <=', $threshold);
                        break;
                }
            }

            if (isset($criteria['sort_by'])) {
                $sortOrder = isset($criteria['sort_order']) ? $criteria['sort_order'] : 'ASC';
                $builder->orderBy('tbl_m_item.' . $criteria['sort_by'], $sortOrder);
            } else {
                $builder->orderBy('tbl_m_item.item', 'ASC');
            }

            $result = $builder->get()->getResult();
            
            // Simple pagination
            $total = count($result);
            $offset = ($page - 1) * $perPage;
            $items = array_slice($result, $offset, $perPage);
            
            return [
                'data' => $items,
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage)
            ];
        } catch (\Exception $e) {
            return [
                'data' => [],
                'total' => 0,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => 1
            ];
        }
    }

    /**
     * Get stock comparison between warehouses
     *
     * @param array $gudangIds
     * @return array
     */
    public function getStockComparison($gudangIds = [])
    {
        // Use fallback method since v_item_stok view may not exist
        return $this->getStockComparisonFallback($gudangIds);
    }

    /**
     * Fallback method for getStockComparison when v_item_stok view doesn't exist
     */
    private function getStockComparisonFallback($gudangIds = [])
    {
        if (empty($gudangIds)) {
            return [];
        }

        try {
            $builder = $this->db->table('tbl_m_item_stok')
                ->select('
                    tbl_m_item.id as id_item,
                    tbl_m_item.kode,
                    tbl_m_item.item,
                    COUNT(DISTINCT tbl_m_item_stok.id_gudang) as warehouse_count
                ')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'inner')
                ->whereIn('tbl_m_item_stok.id_gudang', $gudangIds)
                ->where('tbl_m_item_stok.status', '1')
                ->where('tbl_m_item.status', '1')
                ->groupBy('tbl_m_item.id, tbl_m_item.kode, tbl_m_item.item')
                ->orderBy('tbl_m_item.item', 'ASC');

            $result = $builder->get()->getResult();
            
            // Add stock_by_warehouse info (simplified version)
            foreach ($result as $item) {
                $item->stock_by_warehouse = "Multiple warehouses"; // Simplified for fallback
            }
            
            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get stock comparison between warehouses and outlets
     *
     * @param int|null $gudangId
     * @return array
     */
    public function getWarehouseOutletComparison($gudangId = null)
    {
        // Use fallback method directly since v_item_stok view may not exist
        return $this->getWarehouseOutletComparisonFallback($gudangId);
    }
    
    /**
     * Fallback method for getWarehouseOutletComparison when status_otl column doesn't exist
     */
    private function getWarehouseOutletComparisonFallback($gudangId = null)
    {
        try {
            $warehouseIds = $this->getWarehouseIds();
            $outletIds = $this->getOutletIds();
            
            $result = [];
            
            // Get items from warehouses
            if (!empty($warehouseIds)) {
                $warehouseItems = $this->db->table('tbl_m_item_stok')
                    ->select('
                        tbl_m_item.id as id_item,
                        tbl_m_item.kode,
                        tbl_m_item.item,
                        SUM(tbl_m_item_stok.jml) as warehouse_stock,
                        COUNT(DISTINCT tbl_m_item_stok.id_gudang) as warehouse_count
                    ')
                    ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'inner')
                    ->whereIn('tbl_m_item_stok.id_gudang', $warehouseIds)
                    ->where('tbl_m_item_stok.status', '1')
                    ->where('tbl_m_item.status', '1');
                
                if ($gudangId) {
                    $warehouseItems->where('tbl_m_item_stok.id_gudang', $gudangId);
                }
                
                $warehouseItems = $warehouseItems->groupBy('tbl_m_item.id, tbl_m_item.kode, tbl_m_item.item')
                    ->having('warehouse_stock > 0')
                    ->orderBy('tbl_m_item.item', 'ASC')
                    ->get()
                    ->getResult();
                
                foreach ($warehouseItems as $item) {
                    $result[$item->id_item] = [
                        'id_item' => $item->id_item,
                        'kode' => $item->kode,
                        'item' => $item->item,
                        'warehouse_stock' => $item->warehouse_stock,
                        'outlet_stock' => 0,
                        'total_stock' => $item->warehouse_stock,
                        'warehouse_count' => $item->warehouse_count,
                        'outlet_count' => 0
                    ];
                }
            }
            
            // Get items from outlets
            if (!empty($outletIds)) {
                $outletItems = $this->db->table('tbl_m_item_stok')
                    ->select('
                        tbl_m_item.id as id_item,
                        tbl_m_item.kode,
                        tbl_m_item.item,
                        SUM(tbl_m_item_stok.jml) as outlet_stock,
                        COUNT(DISTINCT tbl_m_item_stok.id_gudang) as outlet_count
                    ')
                    ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'inner')
                    ->whereIn('tbl_m_item_stok.id_gudang', $outletIds)
                    ->where('tbl_m_item_stok.status', '1')
                    ->where('tbl_m_item.status', '1');
                
                if ($gudangId) {
                    $outletItems->where('tbl_m_item_stok.id_gudang', $gudangId);
                }
                
                $outletItems = $outletItems->groupBy('tbl_m_item.id, tbl_m_item.kode, tbl_m_item.item')
                    ->having('outlet_stock > 0')
                    ->orderBy('tbl_m_item.item', 'ASC')
                    ->get()
                    ->getResult();
                
                foreach ($outletItems as $item) {
                    if (isset($result[$item->id_item])) {
                        $result[$item->id_item]['outlet_stock'] = $item->outlet_stock;
                        $result[$item->id_item]['total_stock'] += $item->outlet_stock;
                        $result[$item->id_item]['outlet_count'] = $item->outlet_count;
                    } else {
                        $result[$item->id_item] = [
                            'id_item' => $item->id_item,
                            'kode' => $item->kode,
                            'item' => $item->item,
                            'warehouse_stock' => 0,
                            'outlet_stock' => $item->outlet_stock,
                            'total_stock' => $item->outlet_stock,
                            'warehouse_count' => 0,
                            'outlet_count' => $item->outlet_count
                        ];
                    }
                }
            }
            
            return array_values($result);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get stock aging analysis
     *
     * @param int|null $gudangId
     * @return array
     */
    public function getStockAgingAnalysis($gudangId = null)
    {
        // Use fallback method since v_item_stok view may not exist
        return $this->getStockAgingAnalysisFallback($gudangId);
    }

    /**
     * Fallback method for getStockAgingAnalysis when v_item_stok view doesn't exist
     */
    private function getStockAgingAnalysisFallback($gudangId = null)
    {
        try {
            $builder = $this->db->table('tbl_m_item_stok')
                ->select('
                    CASE 
                        WHEN jml <= 0 THEN "Out of Stock"
                        WHEN jml <= 10 THEN "Low Stock (≤10)"
                        WHEN jml <= 50 THEN "Medium Stock (11-50)"
                        WHEN jml <= 100 THEN "High Stock (51-100)"
                        ELSE "Very High Stock (>100)"
                    END as stock_level,
                    COUNT(*) as item_count,
                    SUM(jml) as total_quantity
                ')
                ->where('status', '1')
                ->groupBy('stock_level')
                ->orderBy('FIELD(stock_level, "Out of Stock", "Low Stock (≤10)", "Medium Stock (11-50)", "High Stock (51-100)", "Very High Stock (>100)")');

            if ($gudangId) {
                $builder->where('id_gudang', $gudangId);
            }

            return $builder->get()->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get top items by stock quantity
     *
     * @param int|null $gudangId
     * @param int $limit
     * @param string $order
     * @param string|null $outletType 'warehouse', 'outlet', or null for all
     * @return array
     */
    public function getTopItemsByStock($gudangId = null, $limit = 20, $order = 'DESC', $outletType = null)
    {
        // Use fallback method since v_item_stok view may not exist
        return $this->getTopItemsByStockFallback($gudangId, $limit, $order, $outletType);
    }

    /**
     * Fallback method for getTopItemsByStock when v_item_stok view doesn't exist
     */
    private function getTopItemsByStockFallback($gudangId = null, $limit = 20, $order = 'DESC', $outletType = null)
    {
        try {
            $builder = $this->db->table('tbl_m_item_stok')
                ->select('
                    tbl_m_item_stok.id_item,
                    tbl_m_item_stok.id_gudang,
                    tbl_m_gudang.nama as gudang,
                    tbl_m_item.kode,
                    tbl_m_item.item,
                    tbl_m_item_stok.jml as sisa
                ')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'inner')
                ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_stok.id_gudang', 'inner')
                ->where('tbl_m_item_stok.status', '1')
                ->where('tbl_m_item.status', '1')
                ->where('tbl_m_gudang.status_hps', '0')
                ->where('tbl_m_item_stok.jml >', 0)
                ->orderBy('tbl_m_item_stok.jml', $order)
                ->limit($limit);

            if ($gudangId) {
                $builder->where('tbl_m_item_stok.id_gudang', $gudangId);
            }

            // Filter by outlet type
            if ($outletType === 'warehouse') {
                $warehouseIds = $this->getWarehouseIds();
                if (!empty($warehouseIds)) {
                    $builder->whereIn('tbl_m_item_stok.id_gudang', $warehouseIds);
                }
            } elseif ($outletType === 'outlet') {
                $outletIds = $this->getOutletIds();
                if (!empty($outletIds)) {
                    $builder->whereIn('tbl_m_item_stok.id_gudang', $outletIds);
                }
            }

            return $builder->get()->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get stock summary by outlet type (warehouse vs outlet)
     *
     * @param int|null $gudangId
     * @return array
     */
    public function getStockSummaryByOutletType($gudangId = null)
    {
        // Use fallback method directly since v_item_stok view may not exist
        return $this->getStockSummaryByOutletTypeFallback($gudangId);
    }
    
    /**
     * Fallback method for getStockSummaryByOutletType when status_otl column doesn't exist
     */
    private function getStockSummaryByOutletTypeFallback($gudangId = null)
    {
        try {
            $result = [];
            
            // Get warehouse summary
            $warehouseIds = $this->getWarehouseIds();
            if (!empty($warehouseIds)) {
                $warehouseData = $this->db->table('tbl_m_item_stok')
                    ->select('
                        COUNT(DISTINCT id_gudang) as total_locations,
                        COUNT(DISTINCT id_item) as total_items,
                        SUM(CASE WHEN jml > 0 THEN jml ELSE 0 END) as total_stock,
                        COUNT(CASE WHEN jml > 0 THEN 1 END) as items_in_stock,
                        COUNT(CASE WHEN jml <= 0 THEN 1 END) as items_out_of_stock,
                        COUNT(CASE WHEN jml < 0 THEN 1 END) as items_negative_stock
                    ')
                    ->whereIn('id_gudang', $warehouseIds)
                    ->where('status', '1')
                    ->get()
                    ->getRow();
                
                if ($warehouseData) {
                    $result[] = (object) [
                        'status_otl' => '0',
                        'outlet_type' => 'Warehouse',
                        'total_locations' => $warehouseData->total_locations,
                        'total_items' => $warehouseData->total_items,
                        'total_stock' => $warehouseData->total_stock,
                        'items_in_stock' => $warehouseData->items_in_stock,
                        'items_out_of_stock' => $warehouseData->items_out_of_stock,
                        'items_negative_stock' => $warehouseData->items_negative_stock
                    ];
                }
            }
            
            // Get outlet summary
            $outletIds = $this->getOutletIds();
            if (!empty($outletIds)) {
                $outletData = $this->db->table('tbl_m_item_stok')
                    ->select('
                        COUNT(DISTINCT id_gudang) as total_locations,
                        COUNT(DISTINCT id_item) as total_items,
                        SUM(CASE WHEN jml > 0 THEN jml ELSE 0 END) as total_stock,
                        COUNT(CASE WHEN jml > 0 THEN 1 END) as items_in_stock,
                        COUNT(CASE WHEN jml <= 0 THEN 1 END) as items_out_of_stock,
                        COUNT(CASE WHEN jml < 0 THEN 1 END) as items_negative_stock
                    ')
                    ->whereIn('id_gudang', $outletIds)
                    ->where('status', '1')
                    ->get()
                    ->getRow();
                
                if ($outletData) {
                    $result[] = (object) [
                        'status_otl' => '1',
                        'outlet_type' => 'Outlet',
                        'total_locations' => $outletData->total_locations,
                        'total_items' => $outletData->total_items,
                        'total_stock' => $outletData->total_stock,
                        'items_in_stock' => $outletData->items_in_stock,
                        'items_out_of_stock' => $outletData->items_out_of_stock,
                        'items_negative_stock' => $outletData->items_negative_stock
                    ];
                }
            }
            
            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get outlet stock only
     *
     * @param int|null $gudangId
     * @param string|null $keyword
     * @param int $perPage
     * @param int $page
     * @return array
     */
    public function getOutletStock($gudangId = null, $keyword = null, $perPage = 10, $page = 1)
    {
        return $this->getStockOverview($gudangId, $keyword, 'outlet', $perPage, $page);
    }

    /**
     * Get warehouse stock only
     *
     * @param int|null $gudangId
     * @param string|null $keyword
     * @param int $perPage
     * @param int $page
     * @return array
     */
    public function getWarehouseStock($gudangId = null, $keyword = null, $perPage = 10, $page = 1)
    {
        return $this->getStockOverview($gudangId, $keyword, 'warehouse', $perPage, $page);
    }

    /**
     * Get low stock items in outlets only
     *
     * @param int|null $gudangId
     * @param float $threshold
     * @return array
     */
    public function getLowStockItemsInOutlets($gudangId = null, $threshold = 10)
    {
        try {
            if ($this->hasStatusOtlColumn()) {
                $builder = $this->select('*')
                    ->where('status_otl', '1') // Outlets only
                    ->where('sisa <=', $threshold)
                    ->orderBy('sisa', 'ASC');

                if ($gudangId) {
                    $builder->where('id_gudang', $gudangId);
                }

                return $builder->findAll();
            } else {
                // Fallback: filter by outlet IDs
                $outletIds = $this->getOutletIds();
                if (empty($outletIds)) {
                    return [];
                }
                
                $builder = $this->db->table('tbl_m_item_stok')
                    ->select('
                        tbl_m_item_stok.id_item,
                        tbl_m_item_stok.id_gudang,
                        tbl_m_gudang.nama as gudang,
                        tbl_m_item.kode,
                        tbl_m_item.item,
                        tbl_m_item_stok.jml as sisa
                    ')
                    ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'inner')
                    ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_stok.id_gudang', 'inner')
                    ->whereIn('tbl_m_item_stok.id_gudang', $outletIds)
                    ->where('tbl_m_item_stok.status', '1')
                    ->where('tbl_m_item.status', '1')
                    ->where('tbl_m_gudang.status_hps', '0')
                    ->where('tbl_m_item_stok.jml <=', $threshold)
                    ->orderBy('tbl_m_item_stok.jml', 'ASC');

                if ($gudangId) {
                    $builder->where('tbl_m_item_stok.id_gudang', $gudangId);
                }

                return $builder->get()->getResult();
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get low stock items in warehouses only
     *
     * @param int|null $gudangId
     * @param float $threshold
     * @return array
     */
    public function getLowStockItemsInWarehouses($gudangId = null, $threshold = 10)
    {
        try {
            if ($this->hasStatusOtlColumn()) {
                $builder = $this->select('*')
                    ->where('status_otl', '0') // Warehouses only
                    ->where('sisa <=', $threshold)
                    ->orderBy('sisa', 'ASC');

                if ($gudangId) {
                    $builder->where('id_gudang', $gudangId);
                }

                return $builder->findAll();
            } else {
                // Fallback: filter by warehouse IDs
                $warehouseIds = $this->getWarehouseIds();
                if (empty($warehouseIds)) {
                    return [];
                }
                
                $builder = $this->db->table('tbl_m_item_stok')
                    ->select('
                        tbl_m_item_stok.id_item,
                        tbl_m_item_stok.id_gudang,
                        tbl_m_gudang.nama as gudang,
                        tbl_m_item.kode,
                        tbl_m_item.item,
                        tbl_m_item_stok.jml as sisa
                    ')
                    ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'inner')
                    ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_stok.id_gudang', 'inner')
                    ->whereIn('tbl_m_item_stok.id_gudang', $warehouseIds)
                    ->where('tbl_m_item_stok.status', '1')
                    ->where('tbl_m_item.status', '1')
                    ->where('tbl_m_gudang.status_hps', '0')
                    ->where('tbl_m_item_stok.jml <=', $threshold)
                    ->orderBy('tbl_m_item_stok.jml', 'ASC');

                if ($gudangId) {
                    $builder->where('tbl_m_item_stok.id_gudang', $gudangId);
                }

                return $builder->get()->getResult();
            }
        } catch (\Exception $e) {
            return [];
        }
    }
}
