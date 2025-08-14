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
    protected $table = 'tbl_m_item_stok';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_item',
        'id_gudang',
        'id_outlet',
        'id_user',
        'jml',
        'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
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
     * Get all stock data for a specific outlet
     *
     * @param int $outletId
     * @return array
     */
    public function getStockByOutlet($outletId)
    {
        return $this->select('tbl_m_item_stok.*, tbl_m_satuan.satuanBesar as satuan_nama, tbl_m_item.item as item_nama')
                    ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'left')
                    ->join('tbl_m_satuan', 'tbl_m_item.id_satuan', 'left')
                    ->where('tbl_m_item_stok.id_outlet', $outletId)
                    ->findAll();
    }

    /**
     * Get all stock data for a specific warehouse
     *
     * @param int $gudangId
     * @return array
     */
    public function getStockByWarehouse($gudangId)
    {
        // Use a simpler approach - start from tbl_m_item and join to stock
        $builder = $this->select('tbl_m_item.*, COALESCE(tbl_m_item_stok.jml, 0) as stok, tbl_m_kategori.kategori, tbl_m_merk.merk, tbl_m_satuan.satuanBesar as satuan')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'right')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->where('tbl_m_item_stok.id_gudang', $gudangId)
            ->where('tbl_m_item.status_hps', '0')
            ->where('tbl_m_item.status', '1')
            ->orderBy('tbl_m_item.item', 'DESC');

        return $builder->findAll();
    }

    /**
     * Get all stock data for a specific outlet (same as getStockByWarehouse but using id_outlet)
     *
     * @param int $outletId
     * @param int $perPage
     * @param string|null $keyword
     * @param int $page
     * @return array
     */
    public function getStockByOutletPaginate($gudangId, $perPage = 10, $keyword = null, $page = 1)
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

        if ($gudangId) {
            $builder->where('tbl_m_item_stok.id_gudang', $gudangId);
        }

        return $builder->paginate($perPage, 'items', $page);
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