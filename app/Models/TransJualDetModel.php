<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github: github.com/mikhaelfelian
 * Description: Model for handling sales transaction details (tbl_trans_jual_det)
 * This file represents the Model.
 */

namespace App\Models;

use CodeIgniter\Model;

class TransJualDetModel extends Model
{
    protected $table            = 'tbl_trans_jual_det';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_penjualan',
        'id_item',
        'id_satuan',
        'id_kategori',
        'id_merk',
        'created_at',
        'updated_at',
        'no_nota',
        'kode',
        'produk',
        'satuan',
        'keterangan',
        'harga',
        'harga_beli',
        'jml',
        'jml_satuan',
        'disk1',
        'disk2',
        'disk3',
        'diskon',
        'potongan',
        'subtotal',
        'ppn_amount',
        'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'id_penjualan' => 'permit_empty|integer',
        'id_item' => 'permit_empty|integer',
        'id_satuan' => 'permit_empty|integer',
        'id_kategori' => 'permit_empty|integer',
        'id_merk' => 'permit_empty|integer',
        'no_nota' => 'permit_empty|max_length[50]',
        'kode' => 'permit_empty|max_length[50]',
        'produk' => 'permit_empty|max_length[256]',
        'satuan' => 'permit_empty|max_length[50]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get transaction details by sales transaction ID
     */
    public function getDetailsByPenjualanId($penjualanId)
    {
        return $this->where('id_penjualan', $penjualanId)->findAll();
    }

    /**
     * Get transaction details by nota number
     */
    public function getDetailsByNota($noNota)
    {
        return $this->where('no_nota', $noNota)->findAll();
    }

    /**
     * Get transaction details by item ID
     */
    public function getDetailsByItemId($itemId)
    {
        return $this->where('id_item', $itemId)->findAll();
    }

    /**
     * Get transaction details with item information
     */
    public function getDetailsWithItem($penjualanId = null)
    {
        $builder = $this->db->table('tbl_trans_jual_det tjd');
        $builder->select('tjd.*, 
            mi.item as nama_item, 
            mk.kategori as nama_kategori, 
            mm.merk as nama_merk, 
            ms.satuanBesar as nama_satuan');
        $builder->join('tbl_m_item mi', 'mi.id = tjd.id_item', 'left');
        $builder->join('tbl_m_kategori mk', 'mk.id = tjd.id_kategori', 'left');
        $builder->join('tbl_m_merk mm', 'mm.id = tjd.id_merk', 'left');
        $builder->join('tbl_m_satuan ms', 'ms.id = tjd.id_satuan', 'left');
        
        if ($penjualanId) {
            $builder->where('tjd.id_penjualan', $penjualanId);
        }
        
        $results = $builder->get()->getResult();
        
        // Fetch variants for items that might have variants
        if (!empty($results)) {
            $itemIds = array_unique(array_filter(array_column($results, 'id_item')));
            if (!empty($itemIds)) {
                $itemVarianModel = new \App\Models\ItemVarianModel();
                $variants = $itemVarianModel->whereIn('id_item', $itemIds)
                    ->where('status', '1')
                    ->findAll();
                
                // Create a map of item_id => variants array
                $variantMap = [];
                foreach ($variants as $variant) {
                    if (!isset($variantMap[$variant->id_item])) {
                        $variantMap[$variant->id_item] = [];
                    }
                    $variantMap[$variant->id_item][] = $variant;
                }
                
                // Attach variants to each result
                foreach ($results as $result) {
                    if (isset($variantMap[$result->id_item])) {
                        // Try to match variant from produk field
                        $produk = $result->produk ?? '';
                        $itemName = $result->nama_item ?? '';
                        
                        // Check if produk contains variant name (format: "Item Name - Variant Name")
                        foreach ($variantMap[$result->id_item] as $variant) {
                            if (strpos($produk, $variant->varian) !== false || 
                                strpos($produk, ' - ' . $variant->varian) !== false) {
                                $result->id_item_varian = $variant->id;
                                $result->nama_varian = $variant->varian;
                                $result->kode_varian = $variant->kode;
                                break;
                            }
                        }
                    }
                }
            }
        }
        
        return $results;
    }

    /**
     * Get sales items for return purposes
     */
    public function getSalesItems($salesId)
    {
        return $this->select('tbl_trans_jual_det.*, 
                            tbl_m_item.item as produk,
                            tbl_m_item.kode,
                            tbl_m_satuan.satuanBesar')
                    ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_jual_det.id_item', 'left')
                    ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_trans_jual_det.id_satuan', 'left')
                    ->where('tbl_trans_jual_det.id_penjualan', $salesId)
                    ->orderBy('tbl_trans_jual_det.created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Calculate total subtotal for a sales transaction
     */
    public function getTotalSubtotalByPenjualanId($penjualanId)
    {
        return $this->selectSum('subtotal')
                    ->where('id_penjualan', $penjualanId)
                    ->first();
    }

    /**
     * Get sales summary by date range
     */
    public function getSalesSummaryByDateRange($startDate, $endDate)
    {
        return $this->select('SUM(subtotal) as total_sales, COUNT(*) as total_items')
                    ->join('tbl_trans_jual tj', 'tj.id = tbl_trans_jual_det.id_penjualan')
                    ->where('tj.created_at >=', $startDate)
                    ->where('tj.created_at <=', $endDate)
                    ->where('tj.status', '1')
                    ->first();
    }
} 