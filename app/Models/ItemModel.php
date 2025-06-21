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
        'status_hps'
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
     * Generate unique item code
     * 
     * @return string
     */
    public function generateKode()
    {
        $lastCode = $this->select('kode')
            ->where('status_hps', '0')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode->kode, 4); // Remove 'ITM-' prefix
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'ITM-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
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
    public function itemStockable($perPage = 10, $keyword = null)
    {
        $builder = $this->where('status_stok', '1');

        if ($keyword) {
            $builder->groupStart()
                ->like('item', $keyword)
                ->orLike('kode', $keyword)
                ->orLike('barcode', $keyword)
                ->groupEnd();
        }

        return $builder->paginate($perPage, 'items');
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
public function getItemsWithRelations($perPage = 10, $keyword = null)
{
    $builder = $this->select('tbl_m_item.*, tbl_m_kategori.kategori, tbl_m_merk.merk')
        ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
        ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
        ->where('tbl_m_item.status_hps', '0');

    if ($keyword) {
        $builder->groupStart()
            ->like('tbl_m_item.item', $keyword)
            ->orLike('tbl_m_item.kode', $keyword)
            ->orLike('tbl_m_item.barcode', $keyword)
            ->orLike('tbl_m_kategori.kategori', $keyword)
            ->orLike('tbl_m_merk.merk', $keyword)
            ->groupEnd();
    }

    return $builder->paginate($perPage, 'items');
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
public function getItemStocksWithRelations($perPage = 10, $keyword = null)
{
    $builder = $this->select('tbl_m_item.*, tbl_m_kategori.kategori, tbl_m_merk.merk')
        ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
        ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
        ->where('tbl_m_item.status_hps', '0')
        ->where('tbl_m_item.status_stok', '1');

    if ($keyword) {
        $builder->groupStart()
            ->like('tbl_m_item.item', $keyword)
            ->orLike('tbl_m_item.kode', $keyword)
            ->orLike('tbl_m_item.barcode', $keyword)
            ->orLike('tbl_m_kategori.kategori', $keyword)
            ->orLike('tbl_m_merk.merk', $keyword)
            ->groupEnd();
    }

    return $builder->paginate($perPage, 'items');
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
        ->from('tbl_m_item')
        ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
        ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
        ->where('tbl_m_item.id', $id)
        ->where('tbl_m_item.status_hps', '0')
        ->first();
}
} 