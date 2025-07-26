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
        'tgl_retur',
        'alasan',
        'jml_retur',
        'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id_penjualan' => 'required|integer',
        'no_nota' => 'required|max_length[50]',
        'no_retur' => 'required|max_length[50]|is_unique[tbl_trans_retur_jual.no_retur,id,{id}]',
        'tgl_retur' => 'required|valid_date',
        'alasan' => 'permit_empty|string',
        'jml_retur' => 'permit_empty|decimal',
        'status' => 'permit_empty|in_list[0,1,2]'
    ];

    protected $validationMessages = [
        'id_penjualan' => [
            'required' => 'ID Penjualan harus diisi',
            'integer' => 'ID Penjualan harus berupa angka'
        ],
        'no_nota' => [
            'required' => 'Nomor nota harus diisi',
            'max_length' => 'Nomor nota maksimal 50 karakter'
        ],
        'no_retur' => [
            'required' => 'Nomor retur harus diisi',
            'max_length' => 'Nomor retur maksimal 50 karakter',
            'is_unique' => 'Nomor retur sudah digunakan'
        ],
        'tgl_retur' => [
            'required' => 'Tanggal retur harus diisi',
            'valid_date' => 'Format tanggal retur tidak valid'
        ],
        'status' => [
            'in_list' => 'Status harus berupa 0, 1, atau 2'
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
                         ->where('DATE(tgl_retur)', $date)
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
    public function getReturnsWithRelations()
    {
        return $this->select('tbl_trans_retur_jual.*, 
                            tbl_trans_jual.no_nota as sales_no_nota,
                            tbl_m_pelanggan.nama as pelanggan_nama,
                            tbl_ion_users.first_name, tbl_ion_users.last_name')
                    ->join('tbl_trans_jual', 'tbl_trans_jual.id = tbl_trans_retur_jual.id_penjualan', 'left')
                    ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_retur_jual.id_pelanggan', 'left')
                    ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_retur_jual.id_user', 'left')
                    ->orderBy('tbl_trans_retur_jual.created_at', 'DESC');
    }

    /**
     * Get return with details
     */
    public function getReturWithDetails($id)
    {
        $retur = $this->select('tbl_trans_retur_jual.*, 
                              tbl_trans_jual.no_nota as sales_no_nota,
                              tbl_m_pelanggan.nama as pelanggan_nama,
                              tbl_ion_users.first_name, tbl_ion_users.last_name')
                      ->join('tbl_trans_jual', 'tbl_trans_jual.id = tbl_trans_retur_jual.id_penjualan', 'left')
                      ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_retur_jual.id_pelanggan', 'left')
                      ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_retur_jual.id_user', 'left')
                      ->find($id);

        if ($retur) {
            // Get return details if detail table exists
            // $retur->items = $this->db->table('tbl_trans_retur_jual_det')
            //                         ->where('id_retur', $id)
            //                         ->get()
            //                         ->getResult();
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
} 