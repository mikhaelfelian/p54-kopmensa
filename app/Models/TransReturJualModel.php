<?php

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-26
 * Github : github.com/mikhaelfelian
 * description : Model for sales return transactions
 * This file represents the Model.
 */

namespace App\Models;

use CodeIgniter\Model;

class TransReturJualModel extends Model
{
    protected $table = 'tbl_trans_retur_jual';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $protectFields = true;

    protected $allowedFields = [
        'id_penjualan',
        'id_user',
        'id_pelanggan',
        'id_sales',
        'id_gudang',
        'no_nota',
        'no_retur',
        'tgl_masuk',
        'keterangan',
        'status',
        'status_retur',
        'status_terima'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id_penjualan'   => 'required|integer',
        'no_nota'        => 'permit_empty|max_length[50]',
        'no_retur'       => 'required|max_length[50]|is_unique[tbl_trans_retur_jual.no_retur,id,{id}]',
        'tgl_masuk'      => 'permit_empty|valid_date',
        'keterangan'     => 'permit_empty|string',
        'status'         => 'permit_empty|in_list[0,1,2]',
        'status_retur'   => 'permit_empty|in_list[1,2]',
        'status_terima'  => 'permit_empty|in_list[0,1,2]'
    ];

    protected $validationMessages = [
        'id_penjualan' => [
            'required' => 'ID Penjualan harus diisi',
            'integer'  => 'ID Penjualan harus berupa angka'
        ],
        'no_nota' => [
            'required'   => 'Nomor nota harus diisi',
            'max_length' => 'Nomor nota maksimal 50 karakter'
        ],
        'no_retur' => [
            'required'   => 'Nomor retur harus diisi',
            'max_length' => 'Nomor retur maksimal 50 karakter',
            'is_unique'  => 'Nomor retur sudah digunakan'
        ],
        'tgl_masuk' => [
            'required'   => 'Tanggal masuk harus diisi',
            'valid_date' => 'Format tanggal masuk tidak valid'
        ],
        'status' => [
            'in_list' => 'Status harus berupa 0, 1, atau 2'
        ],
        'status_retur' => [
            'in_list' => 'Status retur harus berupa 1 (refund) atau 2 (retur barang)'
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
     * Generate unique return number
     */
    public function generateReturNumber()
    {
        $date = date('Y-m-d');
        $prefix = 'RTR-' . date('Ymd') . '-';

        // Get last number for today
        $lastRetur = $this->select('no_retur')
            ->where('DATE(tgl_masuk)', $date)
            ->like('no_retur', $prefix, 'after')
            ->orderBy('no_retur', 'DESC')
            ->first();

        if ($lastRetur) {
            $lastNumber = (int) substr($lastRetur->no_retur, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get returns with relationships
     */
    public function getReturnsWithRelations($limit = null, $offset = null, $search = null)
    {
        $builder = $this->builder();
        $builder->select('
            tbl_trans_retur_jual.*,
            tbl_trans_jual.no_nota,
            tbl_m_pelanggan.nama as customer_nama,
            tbl_ion_users.first_name, tbl_ion_users.last_name,
            CONCAT(tbl_ion_users.first_name, " ", tbl_ion_users.last_name) as username
        ');
        $builder->join('tbl_trans_jual', 'tbl_trans_jual.id = tbl_trans_retur_jual.id_penjualan', 'left');
        $builder->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_retur_jual.id_pelanggan', 'left');
        $builder->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_retur_jual.id_user', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('tbl_trans_retur_jual.no_retur', $search)
                ->orLike('tbl_m_pelanggan.nama', $search)
                ->orLike('tbl_trans_jual.no_nota', $search)
                ->groupEnd();
        }

                $builder->orderBy('tbl_trans_retur_jual.created_at', 'DESC');
        
        if ($limit !== null) {
            $builder->limit($limit, $offset ?? 0);
        }
        
        $results = $builder->get()->getResult();
        
        // Add computed fields for each result
        foreach ($results as $result) {
            $result->retur_type = $this->getReturType($result->status_retur ?? '1');
            $result->total_amount = $this->calculateTotalAmount($result->id);
        }
        
        return $results;
    }

    /**
     * Get return with details
     */
    public function getReturWithDetails($id)
    {
        $retur = $this->select('tbl_trans_retur_jual.*, 
                              tbl_trans_jual.no_nota,
                              tbl_m_pelanggan.nama as customer_nama,
                              tbl_ion_users.first_name, tbl_ion_users.last_name,
                              CONCAT(tbl_ion_users.first_name, " ", tbl_ion_users.last_name) as username')
            ->join('tbl_trans_jual', 'tbl_trans_jual.id = tbl_trans_retur_jual.id_penjualan', 'left')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_retur_jual.id_pelanggan', 'left')
            ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_retur_jual.id_user', 'left')
            ->find($id);

        if ($retur) {
            // Get return details from detail table
            $detailModel = new \App\Models\TransReturJualDetModel();
            $retur->items = $detailModel->getDetailsByReturId($id);
            
            // Add computed retur_type based on status_retur
            $retur->retur_type = $this->getReturType($retur->status_retur ?? '1');
        }

        return $retur;
    }

    /**
     * Get status label
     */
    public function getStatusLabel($status)
    {
        $labels = [
            '0' => 'Draft',
            '1' => 'Diproses',
            '2' => 'Selesai'
        ];

        return $labels[$status] ?? 'Unknown';
    }

    /**
     * Get status_retur label
     */
    public function getStatusReturLabel($status_retur)
    {
        $labels = [
            '1' => 'Refund',
            '2' => 'Retur Barang'
        ];

        return $labels[$status_retur] ?? 'Unknown';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass($status)
    {
        $classes = [
            '0' => 'badge-secondary',
            '1' => 'badge-warning',
            '2' => 'badge-success'
        ];

        return $classes[$status] ?? 'badge-dark';
    }
    
    /**
     * Get return type based on status_retur
     */
    public function getReturType($status_retur)
    {
        return $status_retur === '1' ? 'refund' : 'exchange';
    }
    
    /**
     * Calculate total amount from detail items
     */
    public function calculateTotalAmount($returId)
    {
        $detailModel = new \App\Models\TransReturJualDetModel();
        $total = $detailModel->selectSum('subtotal')
                            ->where('id_retur_jual', $returId)
                            ->where('jml >', 0) // Only positive quantities (return items, not exchange items)
                            ->first();
        
        return $total->subtotal ?? 0;
    }
}