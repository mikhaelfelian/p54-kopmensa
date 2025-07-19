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
    public function generateKode($categoryId = null, $type = null)
    {
        // Get current month and year (mmyy format)
        $currentDate = date('my'); // e.g., 0225 for February 2025
        
        // Get category ID (1 digit)
        $categoryCode = $categoryId ? substr($categoryId, -1) : '0';
        
        // Get type code (1 digit)
        $typeCode = $type ? substr($type, 0, 1) : '0';
        
        // Get last code for this category and type combination
        $lastCode = $this->select('kode')
            ->where('status_hps', '0')
            ->where('id_kategori', $categoryId)
            ->where('tipe', $type)
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastCode) {
            // Extract auto increment number from last code
            $lastNumber = (int) substr($lastCode->kode, -3); // Last 3 digits
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format: <category><type><auto_increment><mmyy>
        // Example: 1025001 (category=1, type=0, auto=25, date=001 for Jan 2025)
        $autoIncrement = str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        
        return $categoryCode . $typeCode . $autoIncrement . $currentDate;
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

public function getItemsWithRelationsActive($perPage = 10, $keyword = null, $page = 1, $kategori = null, $merk = null)
{
    $builder = $this->select('tbl_m_item.*, tbl_m_kategori.kategori, tbl_m_merk.merk, tbl_m_supplier.nama as supplier')
        ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
        ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
        ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_m_item.id_supplier', 'left')
        ->where('tbl_m_item.status_hps', '0')
        ->where('tbl_m_item.status', '1')
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

    if ($merk) {
        $builder->where('tbl_m_item.id_merk', $merk);
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
} 