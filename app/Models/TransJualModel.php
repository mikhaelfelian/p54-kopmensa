<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github: github.com/mikhaelfelian
 * Description: Model for handling sales transactions (tbl_trans_jual)
 * This file represents the Model.
 */

namespace App\Models;

use CodeIgniter\Model;

class TransJualModel extends Model
{
    protected $table            = 'tbl_trans_jual';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_user',
        'id_sales',
        'id_pelanggan',
        'id_gudang',
        'no_nota',
        'created_at',
        'updated_at',
        'deleted_at',
        'tgl_bayar',
        'tgl_masuk',
        'tgl_keluar',
        'jml_total',
        'jml_biaya',
        'jml_ongkir',
        'jml_retur',
        'diskon',
        'jml_diskon',
        'jml_subtotal',
        'ppn',
        'jml_ppn',
        'jml_gtotal',
        'jml_bayar',
        'jml_kembali',
        'jml_kurang',
        'disk1',
        'jml_disk1',
        'disk2',
        'jml_disk2',
        'disk3',
        'jml_disk3',
        'metode_bayar',
        'status',
        'status_nota',
        'status_ppn',
        'status_bayar',
        'status_retur'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'no_nota' => 'required|max_length[50]',
        'id_user' => 'permit_empty|integer',
        'id_sales' => 'permit_empty|integer',
        'id_pelanggan' => 'permit_empty|integer',
        'id_gudang' => 'permit_empty|integer'
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
     * Get sales transaction by ID
     */
    public function getTransactionById($id)
    {
        return $this->find($id);
    }

    /**
     * Get sales transactions by customer ID
     */
    public function getTransactionsByCustomer($customerId)
    {
        return $this->where('id_pelanggan', $customerId)->findAll();
    }

    /**
     * Get sales transactions by date range
     */
    public function getTransactionsByDateRange($startDate, $endDate)
    {
        return $this->where('created_at >=', $startDate)
                    ->where('created_at <=', $endDate)
                    ->findAll();
    }

    /**
     * Get transactions by nota number
     */
    public function getTransactionByNota($noNota)
    {
        return $this->where('no_nota', $noNota)->first();
    }

    /**
     * Get total sales by date
     */
    public function getTotalSalesByDate($date)
    {
        return $this->selectSum('jml_gtotal')
                    ->where('DATE(created_at)', $date)
                    ->where('status', '1')
                    ->first();
    }
} 