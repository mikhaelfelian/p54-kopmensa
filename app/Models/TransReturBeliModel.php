<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-25
 * Github : github.com/mikhaelfelian
 * description : Model for managing purchase return data
 * This file represents the Model.
 */

namespace App\Models;

use CodeIgniter\Model;

class TransReturBeliModel extends Model
{
    protected $table            = 'tbl_trans_retur_beli';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_beli', 'id_supplier', 'id_user', 'id_user_terima', 'tgl_retur', 
        'no_nota_retur', 'no_nota_asal', 'alasan_retur', 'jml_retur', 
        'jml_potongan', 'jml_subtotal', 'jml_ppn', 'jml_total', 
        'status_ppn', 'status_retur', 'catatan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id_beli'        => 'required|integer',
        'id_supplier'    => 'required|integer',
        'tgl_retur'      => 'required|valid_date',
        'no_nota_retur'  => 'required|max_length[160]',
        'status_ppn'     => 'permit_empty|in_list[0,1,2]',
        'status_retur'   => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'id_beli' => [
            'required' => 'Transaksi pembelian harus dipilih.',
            'integer'  => 'ID pembelian harus berupa angka.'
        ],
        'id_supplier' => [
            'required' => 'Supplier harus dipilih.',
            'integer'  => 'ID supplier harus berupa angka.'
        ],
        'tgl_retur' => [
            'required'   => 'Tanggal retur harus diisi.',
            'valid_date' => 'Format tanggal retur tidak valid.'
        ],
        'no_nota_retur' => [
            'required'   => 'Nomor nota retur harus diisi.',
            'max_length' => 'Nomor nota retur maksimal 160 karakter.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Generate unique return note number
     */
    public function generateKode()
    {
        $prefix = 'RET';
        $date = date('Ymd');
        
        $lastRetur = $this->where('DATE(created_at)', date('Y-m-d'))
                         ->orderBy('id', 'DESC')
                         ->first();

        if ($lastRetur) {
            $lastNumber = (int) substr($lastRetur->no_nota_retur, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get returns with related data
     */
    public function getReturnsWithRelations($limit = null, $offset = null)
    {
        $builder = $this->builder();
        $builder->select('
            tbl_trans_retur_beli.*,
            tbl_m_supplier.nama as supplier_nama,
            tbl_trans_beli.no_nota as no_nota_pembelian,
            tbl_ion_users.first_name, tbl_ion_users.last_name,
            user_terima.first_name as terima_first_name,
            user_terima.last_name as terima_last_name
        ');
        $builder->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_retur_beli.id_supplier', 'left');
        $builder->join('tbl_trans_beli', 'tbl_trans_beli.id = tbl_trans_retur_beli.id_beli', 'left');
        $builder->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_retur_beli.id_user', 'left');
        $builder->join('tbl_ion_users as user_terima', 'user_terima.id = tbl_trans_retur_beli.id_user_terima', 'left');
        $builder->orderBy('tbl_trans_retur_beli.created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->get()->getResult();
    }

    /**
     * Get return details with items
     */
    public function getReturWithDetails($id)
    {
        $retur = $this->getReturnsWithRelations();
        $retur = array_filter($retur, function($item) use ($id) {
            return $item->id == $id;
        });
        
        if (empty($retur)) {
            return null;
        }
        
        $retur = array_values($retur)[0];
        
        // Get return items
        $detailModel = new \App\Models\TransReturBeliDetModel();
        $retur->items = $detailModel->getDetailsByReturId($id);
        
        return $retur;
    }

    /**
     * Get status label
     */
    public function getStatusLabel($status)
    {
        $statuses = [
            '0' => 'Draft',
            '1' => 'Selesai'
        ];
        
        return $statuses[$status] ?? 'Unknown';
    }

    /**
     * Get PPN status label
     */
    public function getPpnStatusLabel($status)
    {
        $statuses = [
            '0' => 'Non PPN',
            '1' => 'Dengan PPN',
            '2' => 'PPN Ditangguhkan'
        ];
        
        return $statuses[$status] ?? 'Unknown';
    }

    /**
     * Get last returns for dashboard
     */
    public function getLastReturns($limit = 5)
    {
        return $this->getReturnsWithRelations($limit);
    }
} 