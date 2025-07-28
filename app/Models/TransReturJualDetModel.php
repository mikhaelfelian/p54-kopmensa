<?php

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-26
 * Github : github.com/mikhaelfelian
 * description : Model for sales return detail transactions
 * This file represents the Model.
 */

namespace App\Models;

use CodeIgniter\Model;

class TransReturJualDetModel extends Model
{
    protected $table = 'tbl_trans_retur_jual_det';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'id_retur_jual',
        'id_item',
        'id_satuan',
        'id_gudang',
        'kode',
        'item',
        'satuan',
        'harga',
        'jml',
        'subtotal',
        'keterangan',
        'status_item',
        'status_terima'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = '';

    // Validation
    protected $validationRules = [
        'id_retur_jual' => 'required|integer',
        'id_item' => 'permit_empty|integer',
        'id_satuan' => 'permit_empty|integer',
        'id_gudang' => 'permit_empty|integer',
        'kode' => 'permit_empty|string|max_length[100]',
        'item' => 'permit_empty|string|max_length[255]',
        'satuan' => 'permit_empty|string|max_length[100]',
        'harga' => 'permit_empty|decimal',
        'jml' => 'permit_empty|integer',
        'subtotal' => 'permit_empty|decimal',
        'keterangan' => 'permit_empty|string',
        'status_item' => 'permit_empty|in_list[1,2]',
        'status_terima' => 'permit_empty|in_list[0,1,2]'
    ];

    protected $validationMessages = [
        'id_retur_jual' => [
            'required' => 'ID Retur Jual harus diisi',
            'integer' => 'ID Retur Jual harus berupa angka'
        ],
        'id_item' => [
            'integer' => 'ID Item harus berupa angka'
        ],
        'id_satuan' => [
            'integer' => 'ID Satuan harus berupa angka'
        ],
        'id_gudang' => [
            'integer' => 'ID Gudang harus berupa angka'
        ],
        'kode' => [
            'max_length' => 'Kode maksimal 100 karakter'
        ],
        'item' => [
            'max_length' => 'Nama item maksimal 255 karakter'
        ],
        'satuan' => [
            'max_length' => 'Satuan maksimal 100 karakter'
        ],
        'harga' => [
            'decimal' => 'Harga harus berupa angka desimal'
        ],
        'jml' => [
            'integer' => 'Jumlah harus berupa angka'
        ],
        'subtotal' => [
            'decimal' => 'Subtotal harus berupa angka desimal'
        ],
        'status_item' => [
            'in_list' => 'Status item harus berupa 1 atau 2'
        ],
        'status_terima' => [
            'in_list' => 'Status terima harus berupa 0, 1, atau 2'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get details by return ID
     */
    public function getDetailsByReturId($returId)
    {
        return $this->select('tbl_trans_retur_jual_det.*, 
                            tbl_trans_retur_jual_det.item as produk,
                            tbl_trans_retur_jual_det.jml as jml_satuan,
                            tbl_m_item.item as item_nama,
                            tbl_m_item.kode as item_kode,
                            tbl_m_satuan.satuanBesar as satuan')
                    ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_retur_jual_det.id_item', 'left')
                    ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_trans_retur_jual_det.id_satuan', 'left')
                    ->where('id_retur_jual', $returId)
                    ->orderBy('tbl_trans_retur_jual_det.created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Calculate total by return ID
     */
    public function calculateTotalByReturId($returId)
    {
        $result = $this->selectSum('subtotal', 'total')
                       ->where('id_retur_jual', $returId)
                       ->where('status_item', '1') // Only valid items
                       ->first();

        return $result->total ?? 0;
    }

    /**
     * Get item summary by return ID
     */
    public function getItemSummaryByReturId($returId)
    {
        return $this->select('COUNT(*) as total_items, 
                            SUM(CASE WHEN status_item = "1" THEN 1 ELSE 0 END) as valid_items,
                            SUM(CASE WHEN status_item = "2" THEN 1 ELSE 0 END) as rejected_items,
                            SUM(CASE WHEN status_item = "1" THEN subtotal ELSE 0 END) as total_amount')
                    ->where('id_retur_jual', $returId)
                    ->first();
    }

    /**
     * Get status label
     */
    public function getStatusItemLabel($status)
    {
        $labels = [
            '1' => 'Valid',
            '2' => 'Ditolak'
        ];

        return $labels[$status] ?? 'Unknown';
    }

    /**
     * Get status badge class
     */
    public function getStatusItemBadgeClass($status)
    {
        $classes = [
            '1' => 'badge-success',
            '2' => 'badge-danger'
        ];

        return $classes[$status] ?? 'badge-dark';
    }

    /**
     * Update item status
     */
    public function updateItemStatus($id, $status, $alasan = null)
    {
        $data = [
            'status_item' => $status
        ];

        if ($alasan) {
            $data['alasan_item'] = $alasan;
        }

        return $this->update($id, $data);
    }

    /**
     * Bulk update status by return ID
     */
    public function bulkUpdateStatusByReturId($returId, $status, $alasan = null)
    {
        $data = [
            'status_item' => $status
        ];

        if ($alasan) {
            $data['alasan_item'] = $alasan;
        }

        return $this->where('id_retur_jual', $returId)
                    ->set($data)
                    ->update();
    }
} 