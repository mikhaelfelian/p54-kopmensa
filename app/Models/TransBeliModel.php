<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-11
 * Github : github.com/mikhaelfelian
 * description : Model for managing purchase transaction data
 * This file represents the Model for TransBeliModel.
 */
class TransBeliModel extends Model
{
    protected $table            = 'tbl_trans_beli';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_penerima',
        'id_supplier',
        'id_user',
        'id_po',
        'created_at',
        'updated_at',
        'deleted_at',
        'tgl_bayar',
        'tgl_masuk',
        'tgl_keluar',
        'no_nota',
        'no_po',
        'supplier',
        'jml_total',
        'disk1',
        'disk2',
        'disk3',
        'jml_potongan',
        'jml_retur',
        'jml_diskon',
        'jml_biaya',
        'jml_ongkir',
        'jml_subtotal',
        'jml_dpp',
        'ppn',
        'jml_ppn',
        'jml_gtotal',
        'jml_bayar',
        'jml_kembali',
        'jml_kurang',
        'status_bayar',
        'status_nota',
        'status_ppn',
        'status_retur',
        'status_penerimaan',
        'metode_bayar',
        'status_hps'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'id_penerima' => 'permit_empty|integer',
        'id_supplier' => 'permit_empty|integer',
        'id_user' => 'permit_empty|integer',
        'id_po' => 'permit_empty|integer',
        'tgl_bayar' => 'permit_empty|valid_date',
        'tgl_masuk' => 'permit_empty|valid_date',
        'tgl_keluar' => 'permit_empty|valid_date',
        'no_nota' => 'permit_empty|max_length[160]',
        'no_po' => 'permit_empty|max_length[160]',
        'supplier' => 'permit_empty|max_length[160]',
        'jml_total' => 'permit_empty|decimal',
        'disk1' => 'permit_empty|decimal',
        'disk2' => 'permit_empty|decimal',
        'disk3' => 'permit_empty|decimal',
        'jml_potongan' => 'permit_empty|decimal',
        'jml_retur' => 'permit_empty|decimal',
        'jml_diskon' => 'permit_empty|decimal',
        'jml_biaya' => 'permit_empty|decimal',
        'jml_ongkir' => 'permit_empty|decimal',
        'jml_subtotal' => 'permit_empty|decimal',
        'jml_dpp' => 'permit_empty|decimal',
        'ppn' => 'permit_empty|integer',
        'jml_ppn' => 'permit_empty|decimal',
        'jml_gtotal' => 'permit_empty|decimal',
        'jml_bayar' => 'permit_empty|decimal',
        'jml_kembali' => 'permit_empty|decimal',
        'jml_kurang' => 'permit_empty|decimal',
        'status_bayar' => 'permit_empty|integer',
        'status_nota' => 'permit_empty|integer',
        'status_ppn' => 'permit_empty|in_list[0,1,2]',
        'status_retur' => 'permit_empty|in_list[0,1]',
        'status_penerimaan' => 'permit_empty|in_list[0,1,2,3]',
        'metode_bayar' => 'permit_empty|max_length[50]',
        'status_hps' => 'permit_empty|in_list[0,1]'
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
     * Get status labels for various status fields
     */
    public function getStatusPpnLabels()
    {
        return [
            '0' => 'Tidak Ada PPN',
            '1' => 'PPN Masukan',
            '2' => 'PPN Keluaran'
        ];
    }

    public function getStatusReturLabels()
    {
        return [
            '0' => 'Tidak Ada Retur',
            '1' => 'Ada Retur'
        ];
    }

    public function getStatusPenerimaanLabels()
    {
        return [
            '0' => 'Belum Diterima',
            '1' => 'Sebagian Diterima',
            '2' => 'Lengkap Diterima',
            '3' => 'Ditolak'
        ];
    }

    public function getStatusHpsLabels()
    {
        return [
            '0' => 'Aktif',
            '1' => 'Dihapus'
        ];
    }

    /**
     * Get formatted status labels
     */
    public function getStatusPpnLabel($status)
    {
        $labels = $this->getStatusPpnLabels();
        return $labels[$status] ?? 'Unknown';
    }

    public function getStatusReturLabel($status)
    {
        $labels = $this->getStatusReturLabels();
        return $labels[$status] ?? 'Unknown';
    }

    public function getStatusPenerimaanLabel($status)
    {
        $labels = $this->getStatusPenerimaanLabels();
        return $labels[$status] ?? 'Unknown';
    }

    public function getStatusHpsLabel($status)
    {
        $labels = $this->getStatusHpsLabels();
        return $labels[$status] ?? 'Unknown';
    }

    /**
     * Format currency values
     */
    public function formatCurrency($amount)
    {
        return number_format($amount, 2, ',', '.');
    }

    /**
     * Get purchase transactions with filters
     */
    public function getPurchaseTransactions($filters = [])
    {
        $builder = $this->builder();
        
        // Apply filters
        if (!empty($filters['supplier'])) {
            $builder->like('supplier', $filters['supplier']);
        }
        
        if (!empty($filters['no_nota'])) {
            $builder->like('no_nota', $filters['no_nota']);
        }
        
        if (!empty($filters['tgl_masuk_start'])) {
            $builder->where('tgl_masuk >=', $filters['tgl_masuk_start']);
        }
        
        if (!empty($filters['tgl_masuk_end'])) {
            $builder->where('tgl_masuk <=', $filters['tgl_masuk_end']);
        }
        
        if (isset($filters['status_penerimaan'])) {
            $builder->where('status_penerimaan', $filters['status_penerimaan']);
        }
        
        if (isset($filters['status_bayar'])) {
            $builder->where('status_bayar', $filters['status_bayar']);
        }
        
        // Only show non-deleted records
        $builder->where('status_hps', '0');
        
        return $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
    }

    /**
     * Get purchase transaction by ID
     */
    public function getPurchaseTransaction($id)
    {
        return $this->where('id', $id)
                   ->where('status_hps', '0')
                   ->first();
    }

    /**
     * Soft delete purchase transaction
     */
    public function softDeleteTransaction($id)
    {
        return $this->update($id, ['status_hps' => '1']);
    }

    /**
     * Restore soft deleted purchase transaction
     */
    public function restoreTransaction($id)
    {
        return $this->update($id, ['status_hps' => '0']);
    }

    /**
     * Get total purchase amount for a specific period
     */
    public function getTotalPurchaseAmount($startDate = null, $endDate = null)
    {
        $builder = $this->builder();
        $builder->selectSum('jml_gtotal');
        $builder->where('status_hps', '0');
        
        if ($startDate) {
            $builder->where('tgl_masuk >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('tgl_masuk <=', $endDate);
        }
        
        $result = $builder->get()->getRow();
        return $result->jml_gtotal ?? 0;
    }
} 