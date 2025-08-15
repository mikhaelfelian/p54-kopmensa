<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-07-12
 * 
 * ItemHist Model
 * Handles database operations for tbl_m_item_hist table
 */

class ItemHistModel extends Model
{
    protected $table            = 'tbl_m_item_hist';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_item',
        'id_satuan',
        'id_gudang',
        'id_outlet',
        'id_user',
        'id_pelanggan',
        'id_supplier',
        'id_penjualan',
        'id_pembelian',
        'id_pembelian_det',
        'id_so',
        'id_mutasi',
        'created_at',
        'updated_at',
        'tgl_masuk',
        'no_nota',
        'kode',
        'item',
        'keterangan',
        'nominal',
        'jml',
        'jml_satuan',
        'satuan',
        'status',
        'sp'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    /**
     * Get status label
     *
     * @param string $status
     * @return string
     */
    public function getStatusLabel($status)
    {
        $labels = [
            '1' => 'Stok Masuk Pembelian',
            '2' => 'Stok Masuk',
            '3' => 'Stok Masuk Retur Jual',
            '4' => 'Stok Keluar Penjualan',
            '5' => 'Stok Keluar Retur Beli',
            '6' => 'SO',
            '7' => 'Stok Keluar',
            '8' => 'Mutasi Antar Gd'
        ];

        return $labels[$status] ?? 'Tidak Diketahui';
    }

    /**
     * Get item history with relations and pagination
     *
     * @param int|null $id_item
     * @param int|null $id_gudang
     * @param string|null $status
     * @param int $perPage
     * @param int $page
     * @param float|null $jml
     * @param string|null $keterangan
     * @param string|null $date_from
     * @param string|null $date_to
     * @return array
     */
    public function getWithRelationsPaginated($id_item = null, $id_gudang = null, $status = null, $perPage = 10, $page = 1, $jml = null, $keterangan = null, $date_from = null, $date_to = null)
    {
        // Reset the model to avoid query builder conflicts
        $this->resetQuery();
        
        // Build the query
        $builder = $this->select('
                tbl_m_item_hist.*,
                tbl_m_item.item as item_name,
                tbl_m_gudang.nama as gudang_name,
                tbl_m_satuan.satuanBesar as satuan_name
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_hist.id_item', 'left')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_hist.id_gudang', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item_hist.id_satuan', 'left');

        if ($id_item !== null && $id_item !== '') {
            $builder->where('tbl_m_item_hist.id_item', $id_item);
        }

        if ($id_gudang !== null && $id_gudang !== '') {
            $builder->where('tbl_m_item_hist.id_gudang', $id_gudang);
        }

        if ($status !== null && $status !== '') {
            $builder->where('tbl_m_item_hist.status', $status);
        }

        if ($jml !== null && $jml !== '') {
            $builder->where('tbl_m_item_hist.jml', $jml);
        }

        if ($keterangan !== null && $keterangan !== '') {
            $builder->like('tbl_m_item_hist.keterangan', $keterangan);
        }

        if ($date_from !== null && $date_from !== '') {
            $builder->where('DATE(tbl_m_item_hist.created_at) >=', $date_from);
        }

        if ($date_to !== null && $date_to !== '') {
            $builder->where('DATE(tbl_m_item_hist.created_at) <=', $date_to);
        }

        $builder->orderBy('tbl_m_item_hist.created_at', 'DESC');
        
        // Get total count first
        $total = $builder->countAllResults(false);
        
        // Reset and rebuild for pagination
        $this->resetQuery();
        $builder = $this->select('
                tbl_m_item_hist.*,
                tbl_m_item.item as item_name,
                tbl_m_gudang.nama as gudang_name,
                tbl_m_satuan.satuanBesar as satuan_name
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_hist.id_item', 'left')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_hist.id_gudang', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item_hist.id_satuan', 'left');

        if ($id_item !== null && $id_item !== '') {
            $builder->where('tbl_m_item_hist.id_item', $id_item);
        }

        if ($id_gudang !== null && $id_gudang !== '') {
            $builder->where('tbl_m_item_hist.id_gudang', $id_gudang);
        }

        if ($status !== null && $status !== '') {
            $builder->where('tbl_m_item_hist.status', $status);
        }

        if ($jml !== null && $jml !== '') {
            $builder->where('tbl_m_item_hist.jml', $jml);
        }

        if ($keterangan !== null && $keterangan !== '') {
            $builder->like('tbl_m_item_hist.keterangan', $keterangan);
        }

        if ($date_from !== null && $date_from !== '') {
            $builder->where('DATE(tbl_m_item_hist.created_at) >=', $date_from);
        }

        if ($date_to !== null && $date_to !== '') {
            $builder->where('DATE(tbl_m_item_hist.created_at) <=', $date_to);
        }

        $builder->orderBy('tbl_m_item_hist.created_at', 'DESC');
        
        // Get paginated results
        $data = $builder->paginate($perPage, 'item_hist', $page);
        
        return [
            'data'         => $data,
            'pager'        => $this->pager,
            'current_page' => $page,
            'per_page'     => $perPage,
            'total'        => $total,
        ];
    }

    /**
     * Get count of filtered results
     *
     * @param int|null $id_item
     * @param int|null $id_gudang
     * @param string|null $status
     * @return int
     */
    public function getFilteredCount($id_item = null, $id_gudang = null, $status = null)
    {
        $builder = $this->builder();

        if ($id_item !== null) {
            $builder->where('id_item', $id_item);
        }

        if ($id_gudang !== null) {
            $builder->where('id_gudang', $id_gudang);
        }

        if ($status !== null) {
            $builder->where('status', $status);
        }

        return $builder->countAllResults();
    }

    /**
     * Add stock history record
     *
     * @param array $data
     * @return bool
     */
    public function addHistory($data)
    {
        return $this->insert($data);
    }

    /**
     * Get stock movement summary for an item
     *
     * @param int $id_item
     * @param int|null $id_gudang
     * @return object
     */
    public function getStockMovement($id_item, $id_gudang = null)
    {
        $builder = $this->select('
                SUM(CASE WHEN status IN (1,2,3) THEN jml ELSE 0 END) as total_masuk,
                SUM(CASE WHEN status IN (4,5,7) THEN jml ELSE 0 END) as total_keluar,
                COUNT(*) as total_transaksi
            ')
            ->where('id_item', $id_item);

        if ($id_gudang !== null) {
            $builder->where('id_gudang', $id_gudang);
        }

        return $builder->get()->getRow();
    }
} 