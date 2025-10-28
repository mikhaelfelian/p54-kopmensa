<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-17
 * Github : github.com/mikhaelfelian
 * description : Model for managing item data
 * This file represents the Model for Item data management.
 */
class ItemModel extends Model
{
    protected $table            = 'tbl_m_item';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_user',
        'id_kategori',
        'id_satuan',
        'id_merk',
        'id_supplier',
        'kode',
        'barcode',
        'item',
        'deskripsi',
        'jml_min',
        'harga_beli',
        'harga_jual',
        'foto',
        'tipe',
        'status',
        'status_stok',
        'status_hps',
        'status_ppn',
        'sp'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Generate unique item code based on category, type, and date
     * Format: <category_id><type><auto_increment><mmyy>
     * Max 7 characters, all numeric
     * 
     * @param int $categoryId Category ID
     * @param string $type Item type
     * @return string
     */
    /**
     * Generate unique item code using SAP style numeric only.
     * Format: <category_id(2)><type(2)><running_number(4)>
     * All numeric, max 8 digits.
     * Example: 01010001 (category=1, type=1, running=0001)
     *
     * @param int|string $categoryId
     * @param int|string $type
     * @return string
     */
    /**
     * Generate unique 18-digit numeric item code (SAP style)
     * Format: <category_id(4)><type(2)><date(yyyymmdd)><running_number(4)>
     * All numeric, max 18 digits.
     * Example: 000101202406210001 (category=1, type=1, date=2024-06-21, running=0001)
     *
     * @param int|string $categoryId
     * @param int|string $type
     * @return string
     */
    /**
     * Generate unique 6-digit numeric item code (SAP style)
     * Format: <category_id(2)><type(2)><running_number(2)>
     * All numeric, max 6 digits.
     * Example: 010101 (category=1, type=1, running=01)
     *
     * @param int|string $categoryId
     * @param int|string $type
     * @return string
     */
    public function generateKode($categoryId = null, $type = null)
    {
        // Category code: 2 digits, left padded with 0
        $categoryCode = $categoryId ? str_pad((int)$categoryId, 2, '0', STR_PAD_LEFT) : '00';

        // Type code: 2 digits, left padded with 0
        $typeCode = $type ? str_pad((int)$type, 2, '0', STR_PAD_LEFT) : '00';

        // Prefix for searching: 4 digits
        $prefix = $categoryCode . $typeCode;

        // Use a query to get the max running number for this category and type
        $builder = $this->db->table($this->table);

        // The running number is the last 4 digits (for 8 digit code)
        $startPos = strlen($prefix) + 1;
        $builder->select("MAX(CAST(SUBSTRING(kode, {$startPos}, 4) AS UNSIGNED)) AS max_run", false);
        $builder->where('status_hps', '0');
        $builder->where('id_kategori', $categoryId);
        $builder->where('tipe', $type);
        $builder->like('kode', $prefix, 'after');
        $query = $builder->get();
        $row = $query->getRow();

        $lastNumber = isset($row->max_run) && $row->max_run !== null ? (int)$row->max_run : 0;
        $newNumber = $lastNumber + 1;

        // Running number: 4 digits, left padded with 0
        $runningNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        // Concatenate all parts: 2+2+4 = 8 digits
        return $categoryCode . $typeCode . $runningNumber;
    }

    /**
     * Get all stockable items with pagination
     *
     * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
     * Date: 2024-07-15
     * Github : github.com/mikhaelfelian
     * description : This function retrieves all items that are marked as stockable.
     * This file represents the ItemModel.
     */
    public function itemStockable($perPage = 10, $keyword = null, $page = 1)
    {
        $builder = $this->where('status_stok', '1');

        if ($keyword) {
            $builder->groupStart()
                ->like('item', $keyword)
                ->orLike('kode', $keyword)
                ->orLike('barcode', $keyword)
                ->groupEnd();
        }

        return $builder->paginate($perPage, 'items', $page);
    }

/**
 * Get all items with category and brand information
 *
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-21
 * Github : github.com/mikhaelfelian
 * description : This function retrieves all items with their category and brand information using joins.
 * This file represents the ItemModel.
 */
public function getItemsWithRelations($perPage = 10, $keyword = null, $page = 1, $kategori = null, $stok = null, $supplier = null)
{
    // Check if id_supplier column exists before joining
    $hasSupplierColumn = $this->db->fieldExists('id_supplier', 'tbl_m_item');
    
    if ($hasSupplierColumn) {
        $builder = $this->select('tbl_m_item.*, tbl_m_kategori.kategori, tbl_m_merk.merk, tbl_m_supplier.nama as supplier_nama')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_m_item.id_supplier', 'left');
    } else {
        $builder = $this->select('tbl_m_item.*, tbl_m_kategori.kategori, tbl_m_merk.merk, NULL as supplier_nama')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left');
    }
    
    $builder->where('tbl_m_item.status_hps', '0')
        ->orderBy('tbl_m_item.id', 'DESC');

    if ($keyword) {
        $builder->groupStart()
            ->like('tbl_m_item.item', $keyword)
            ->orLike('tbl_m_item.kode', $keyword)
            ->orLike('tbl_m_item.barcode', $keyword)
            ->orLike('tbl_m_kategori.kategori', $keyword)
            ->orLike('tbl_m_merk.merk', $keyword)
            ->orLike('tbl_m_supplier.nama', $keyword)
            ->groupEnd();
    }
    if ($kategori) {
        $builder->where('tbl_m_item.id_kategori', $kategori);
    }
    if ($stok !== null && $stok !== '') {
        $builder->where('tbl_m_item.status_stok', $stok);
    }
    if ($supplier) {
        $builder->where('tbl_m_item.id_supplier', $supplier);
    }

    return $builder->paginate($perPage, 'items', $page);
}

public function getItemsWithRelationsActive($perPage = 10, $keyword = null, $page = 1, $kategori = null, $merk = null, $gudang = null)
{
    $builder = $this->select('tbl_m_item.*, SUM(tbl_m_item_stok.jml) as stok, tbl_m_kategori.kategori, tbl_m_merk.merk, tbl_m_supplier.nama as supplier')
        ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
        ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
        ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_m_item.id_supplier', 'left')
        ->join('tbl_m_item_stok', 'tbl_m_item_stok.id_item = tbl_m_item.id')
        ->where('tbl_m_item.status_hps', '0')
        ->where('tbl_m_item.status', '1')
        ->groupBy('tbl_m_item_stok.id_item')
        ->orderBy('tbl_m_item.item', 'DESC');

    if ($keyword) {
        $builder->groupStart()
            ->like('tbl_m_item.item', $keyword)
            ->orLike('tbl_m_item.kode', $keyword)
            ->orLike('tbl_m_item.barcode', $keyword)
            ->orLike('tbl_m_kategori.kategori', $keyword)
            ->orLike('tbl_m_merk.merk', $keyword)
            ->orLike('tbl_m_supplier.nama', $keyword)
            ->groupEnd();
    }

    if ($kategori) {
        $builder->where('tbl_m_item.id_kategori', $kategori);
    }

    if ($merk) {
        $builder->where('tbl_m_item.id_merk', $merk);
    }

    if ($gudang) {
        $builder->where('tbl_m_item_stok.id_gudang', $gudang);
    }

    return $builder->paginate($perPage, 'items', $page);
}

/**
 * Get all stockable items with category and brand information
 *
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-21
 * Github : github.com/mikhaelfelian
 * description : This function retrieves all stockable items with their category and brand information using joins.
 * This file represents the ItemModel.
 */
public function getItemStocksWithRelations($perPage = 10, $keyword = null, $page = 1)
{
    $builder = $this->select('tbl_m_item.*, tbl_m_kategori.kategori, tbl_m_merk.merk')
        ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
        ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
        ->where('tbl_m_item.status_hps', '0')
        ->where('tbl_m_item.status_stok', '1')
        ->orderBy('tbl_m_item.id', 'DESC');

    if ($keyword) {
        $builder->groupStart()
            ->like('tbl_m_item.item', $keyword)
            ->orLike('tbl_m_item.kode', $keyword)
            ->orLike('tbl_m_item.barcode', $keyword)
            ->orLike('tbl_m_kategori.kategori', $keyword)
            ->orLike('tbl_m_merk.merk', $keyword)
            ->groupEnd();
    }

    return $builder->paginate($perPage, 'items', $page);
}

/**
 * Get single item with category and brand information
 *
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-21
 * Github : github.com/mikhaelfelian
 * description : This function retrieves a single item with its category and brand information using joins.
 * This file represents the ItemModel.
 */
public function getItemWithRelations($id)
{
    return $this->select('tbl_m_item.*, tbl_m_kategori.kategori, tbl_m_merk.merk')
        ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
        ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
        ->where('tbl_m_item.id', $id)
        ->where('tbl_m_item.status_hps', '0')
        ->first();
}

    /**
     * Search items for exchange (with stock information)
     */
    public function searchItems($search = null)
    {
        $builder = $this->select('tbl_m_item.*, 
                                tbl_m_kategori.kategori, 
                                tbl_m_merk.merk,
                                tbl_m_item.item as nama,
                                tbl_m_item_stok.jml as stok,
                                tbl_m_satuan.satuanBesar as satuan')
                        ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
                        ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
                        ->join('tbl_m_item_stok', 'tbl_m_item_stok.id_item = tbl_m_item.id', 'left')
                        ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
                        ->where('tbl_m_item.status_hps', '0')
                        ->where('tbl_m_item.status', '1')
                        ->groupBy('tbl_m_item.id')
                        ->orderBy('tbl_m_item.item', 'ASC');

        if ($search) {
            $builder->groupStart()
                    ->like('tbl_m_item.item', $search)
                    ->orLike('tbl_m_item.kode', $search)
                    ->orLike('tbl_m_item.barcode', $search)
                    ->groupEnd();
        }

        return $builder->findAll();
    }

    /**
     * Get items with stock information
     */
    public function getItemsWithStock()
    {
        return $this->select('tbl_m_item.*, 
                            tbl_m_kategori.kategori, 
                            tbl_m_merk.merk,
                            tbl_m_item.item as nama,
                            SUM(tbl_m_item_stok.jml) as stok,
                            tbl_m_satuan.satuanBesar as satuan')
                    ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
                    ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
                    ->join('tbl_m_item_stok', 'tbl_m_item_stok.id_item = tbl_m_item.id', 'left')
                    ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
                    ->where('tbl_m_item.status_hps', '0')
                    ->where('tbl_m_item.status', '1')
                    ->groupBy('tbl_m_item.id')
                    ->orderBy('tbl_m_item.item', 'ASC')
                    ->findAll();
    }

    /**
     * Get items with stock information for a specific warehouse
     *
     * @param int $warehouseId
     * @param string|null $search
     * @param int|null $categoryId
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getItemsByWarehouse($warehouseId, $search = null, $categoryId = null, $limit = null, $offset = null)
    {
        // Ensure parameters are properly typed
        $warehouseId = (int)$warehouseId;
        $categoryId = $categoryId !== null && $categoryId !== '' ? (int)$categoryId : null;
        
        $builder = $this->select('
                    tbl_m_item.*,
                    COALESCE(tbl_m_item_stok.jml, 0) as stok,
                    tbl_m_kategori.kategori,
                    tbl_m_merk.merk,
                    tbl_m_satuan.satuanBesar as satuan
                ')
                ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
                ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
                ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
                ->join('tbl_m_item_stok', 'tbl_m_item_stok.id_item = tbl_m_item.id AND tbl_m_item_stok.id_gudang = ' . $warehouseId, 'left')
                ->where('tbl_m_item.status_hps', '0')
                ->where('tbl_m_item.status', '1');

        // Add category filter if provided
        if (!empty($categoryId)) {
            $builder->where('tbl_m_item.id_kategori', $categoryId);
        }

        // Add search conditions if search term is provided
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('tbl_m_item.item', $search)
                    ->orLike('tbl_m_item.kode', $search)
                    ->orLike('tbl_m_item.barcode', $search)
                    ->orLike('tbl_m_kategori.kategori', $search)
                    ->orLike('tbl_m_merk.merk', $search)
                    ->groupEnd();
        }

        // Apply limit and offset for pagination
        if ($limit !== null) {
            $builder->limit((int)$limit);
            if ($offset !== null) {
                $builder->offset((int)$offset);
            }
        }

        // Debug: Log the parameters
        log_message('info', 'getItemsByWarehouse Parameters - warehouseId: ' . $warehouseId . ', search: ' . $search . ', categoryId: ' . $categoryId . ', limit: ' . $limit . ', offset: ' . $offset);

        $result = $builder->orderBy('tbl_m_item.item', 'DESC')->findAll();
        log_message('info', 'getItemsByWarehouse result count: ' . count($result));
        
        return $result;
    }

    /**
     * Get items by category with optional search, limit, and offset
     *
     * @param int $categoryId
     * @param string|null $search
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getItemsByCategory($categoryId, $search = null, $limit = null, $offset = null)
    {
        // Ensure parameters are properly typed
        $categoryId = $categoryId !== null && $categoryId !== '' ? (int)$categoryId : null;

        $builder = $this->select('
                    tbl_m_item.*,
                    COALESCE(SUM(tbl_m_item_stok.jml), 0) as stok,
                    tbl_m_kategori.kategori,
                    tbl_m_merk.merk,
                    tbl_m_satuan.satuanBesar as satuan
                ')
                ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
                ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
                ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
                ->join('tbl_m_item_stok', 'tbl_m_item_stok.id_item = tbl_m_item.id', 'left')
                ->where('tbl_m_item.status_hps', '0');

        // Add category filter if provided
        if (!empty($categoryId)) {
            $builder->where('tbl_m_item.id_kategori', $categoryId);
        }

        // Add search conditions if search term is provided
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('tbl_m_item.item', $search)
                    ->orLike('tbl_m_item.kode', $search)
                    ->orLike('tbl_m_item.barcode', $search)
                    ->orLike('tbl_m_kategori.kategori', $search)
                    ->orLike('tbl_m_merk.merk', $search)
                    ->groupEnd();
        }

        // Apply limit and offset for pagination
        if ($limit !== null) {
            $builder->limit((int)$limit);
            if ($offset !== null) {
                $builder->offset((int)$offset);
            }
        }

        $builder->groupBy('tbl_m_item.id');
        
        // Debug: Log the query and parameters
        log_message('info', 'getItemsByCategory - categoryId: ' . $categoryId . ', search: ' . $search . ', limit: ' . $limit . ', offset: ' . $offset);
        
        $result = $builder->orderBy('tbl_m_item.item', 'DESC')->findAll();
        
        log_message('info', 'getItemsByCategory - result count: ' . count($result));
        
        return $result;
    }

    /**
     * Get items with relations active using direct limit/offset for AJAX pagination
     * This method is optimized for cashier interface performance
     *
     * @param int|null $limit
     * @param string|null $keyword
     * @param int|null $kategori
     * @param int|null $offset
     * @return array
     */
    public function getItemsWithRelationsActiveLimited($limit = null, $keyword = null, $kategori = null, $offset = null)
    {
        // Ensure parameters are properly typed
        $kategori = $kategori !== null && $kategori !== '' ? (int)$kategori : null;
        
        $builder = $this->select('tbl_m_item.*, COALESCE(SUM(tbl_m_item_stok.jml), 0) as stok, tbl_m_kategori.kategori, tbl_m_merk.merk, tbl_m_supplier.nama as supplier')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_m_item.id_supplier', 'left')
            ->join('tbl_m_item_stok', 'tbl_m_item_stok.id_item = tbl_m_item.id', 'left')
            ->where('tbl_m_item.status_hps', '0')
            ->where('tbl_m_item.status', '1')
            ->groupBy('tbl_m_item.id')
            ->orderBy('tbl_m_item.item', 'DESC');

        if ($keyword) {
            $builder->groupStart()
                ->like('tbl_m_item.item', $keyword)
                ->orLike('tbl_m_item.kode', $keyword)
                ->orLike('tbl_m_item.barcode', $keyword)
                ->orLike('tbl_m_kategori.kategori', $keyword)
                ->orLike('tbl_m_merk.merk', $keyword)
                ->orLike('tbl_m_supplier.nama', $keyword)
                ->groupEnd();
        }

        if ($kategori) {
            $builder->where('tbl_m_item.id_kategori', $kategori);
        }

        // Apply limit and offset for pagination
        if ($limit !== null) {
            $builder->limit((int)$limit);
            if ($offset !== null) {
                $builder->offset((int)$offset);
            }
        }

        // Debug: Log the parameters
        log_message('info', 'getItemsWithRelationsActiveLimited Parameters - limit: ' . $limit . ', keyword: ' . $keyword . ', kategori: ' . $kategori . ', offset: ' . $offset);

        $result = $builder->findAll();
        log_message('info', 'getItemsWithRelationsActiveLimited result count: ' . count($result));
        
        return $result;
    }

    /**
     * Archive multiple items by setting status_hps to '1' and deleted_at
     *
     * @param array $ids Item IDs to archive
     * @return bool
     */
    public function archiveMany(array $ids): bool
    {
        if (empty($ids)) {
            return false;
        }

        try {
            $now = date('Y-m-d H:i:s');
            return $this->builder()
                ->whereIn('id', $ids)
                ->set(['status_hps' => '1', 'deleted_at' => $now])
                ->update();
        } catch (\Exception $e) {
            log_message('error', '[ItemModel::archiveMany] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Restore multiple archived items by setting status_hps to '0' and deleted_at to null
     *
     * @param array $ids Item IDs to restore
     * @return bool
     */
    public function restoreMany(array $ids): bool
    {
        if (empty($ids)) {
            return false;
        }

        try {
            return $this->builder()
                ->whereIn('id', $ids)
                ->set(['status_hps' => '0', 'deleted_at' => null])
                ->update();
        } catch (\Exception $e) {
            log_message('error', '[ItemModel::restoreMany] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Permanently delete multiple items (admin/maintenance only)
     * WARNING: This permanently removes records from database
     *
     * @param array $ids Item IDs to purge
     * @return bool
     */
    public function purgeMany(array $ids): bool
    {
        if (empty($ids)) {
            return false;
        }

        try {
            // Only purge if status_hps is '1' (archived)
            return $this->builder()
                ->whereIn('id', $ids)
                ->where('status_hps', '1')
                ->delete();
        } catch (\Exception $e) {
            log_message('error', '[ItemModel::purgeMany] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all suppliers for this item
     * Uses the item-supplier mapping table
     * 
     * @param int $itemId
     * @return array
     */
    public function getSuppliers($itemId = null)
    {
        if ($itemId === null) {
            return [];
        }

        return $this->db->table('tbl_m_item_supplier')
                    ->select('tbl_m_item_supplier.*, tbl_m_supplier.kode as supplier_kode, tbl_m_supplier.nama as supplier_nama, tbl_m_supplier.no_tlp, tbl_m_supplier.alamat, tbl_m_supplier.status')
                    ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_m_item_supplier.id_supplier')
                    ->where('tbl_m_item_supplier.id_item', $itemId)
                    ->where('tbl_m_supplier.status_hps', '0')
                    ->where('tbl_m_item_supplier.deleted_at IS NULL', null, false)
                    ->orderBy('tbl_m_item_supplier.prioritas', 'ASC')
                    ->get()
                    ->getResult();
    }

    /**
     * Get all available items that are not assigned to any supplier
     * 
     * @return array
     */
    public function getAvailableItems()
    {
        return $this->select('id as id_item, item as nama_item, kode as item_kode')
                    ->where('status', '1')
                    ->where('status_hps', '0')
                    ->where('id_supplier IS NULL', null, false)
                    ->orderBy('item', 'ASC')
                    ->findAll();
    }

    /**
     * Get items for POS with stock status information
     * 
     * @param string|null $keyword Search keyword
     * @return array
     */
    public function getPosItems($keyword = null)
    {
        $builder = $this->db->table('tbl_m_item')
            ->select('id, item, harga_jual, id_supplier, status_stok, kode, barcode')
            ->where('status', '1')
            ->where('status_hps', '0');

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('item', $keyword)
                ->orLike('kode', $keyword)
                ->orLike('barcode', $keyword)
                ->groupEnd();
        }

        $builder->orderBy('item', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * Get stock status label
     * 
     * @param string|int $statusStok
     * @return string
     */
    public function getStockStatusLabel($statusStok)
    {
        return ((int)$statusStok === 1) ? 'Stockable' : 'Non-Stockable';
    }

    /**
     * Get stock status badge class for UI
     * 
     * @param string|int $statusStok
     * @return string
     */
    public function getStockStatusBadgeClass($statusStok)
    {
        return ((int)$statusStok === 1) ? 'badge-success' : 'badge-warning';
    }
} 