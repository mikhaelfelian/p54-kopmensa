<?php

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-08-01
 * Github : github.com/mikhaelfelian
 * Description : Model for handling voucher master data (tbl_m_voucher)
 * This file represents the Model.
 */

namespace App\Models;

use CodeIgniter\Model;

class VoucherModel extends Model
{
    protected $table            = 'tbl_m_voucher';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_user',
        'created_at',
        'updated_at',
        'kode',
        'jml',
        'jml_keluar',
        'jml_max',
        'tgl_masuk',
        'tgl_keluar',
        'status',
        'keterangan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'kode'       => 'required|max_length[50]|is_unique[tbl_m_voucher.kode,id,{id}]',
        'jml'        => 'required|integer|greater_than[0]',
        'jml_keluar' => 'integer|greater_than_equal_to[0]',
        'jml_max'    => 'required|integer|greater_than[0]',
        'tgl_masuk'  => 'required|valid_date',
        'tgl_keluar' => 'required|valid_date',
        'status'     => 'in_list[0,1]'
    ];

    protected $validationMessages   = [
        'kode' => [
            'required'  => 'Kode voucher harus diisi',
            'max_length' => 'Kode voucher maksimal 50 karakter',
            'is_unique' => 'Kode voucher sudah digunakan'
        ],
        'jml' => [
            'required'     => 'Jumlah voucher harus diisi',
            'integer'      => 'Jumlah voucher harus berupa angka',
            'greater_than' => 'Jumlah voucher harus lebih dari 0'
        ],
        'jml_keluar' => [
            'integer'                => 'Jumlah keluar harus berupa angka',
            'greater_than_equal_to'  => 'Jumlah keluar tidak boleh negatif'
        ],
        'jml_max' => [
            'required'     => 'Batas maksimal harus diisi',
            'integer'      => 'Batas maksimal harus berupa angka',
            'greater_than' => 'Batas maksimal harus lebih dari 0'
        ],
        'tgl_masuk' => [
            'required'   => 'Tanggal mulai harus diisi',
            'valid_date' => 'Format tanggal mulai tidak valid'
        ],
        'tgl_keluar' => [
            'required'   => 'Tanggal berakhir harus diisi',
            'valid_date' => 'Format tanggal berakhir tidak valid'
        ]
    ];

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
     * Generate voucher code
     */
    public function generateCode()
    {
        $prefix = 'VOC';
        $date = date('Ymd');
        
        // Get last number for today
        $lastVoucher = $this->where('kode LIKE', $prefix . $date . '%')
                           ->orderBy('id', 'DESC')
                           ->first();
        
        if ($lastVoucher) {
            $lastNumber = (int) substr($lastVoucher->kode, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get active vouchers
     */
    public function getActiveVouchers()
    {
        return $this->where('status', '1')
                    ->where('tgl_masuk <=', date('Y-m-d'))
                    ->where('tgl_keluar >=', date('Y-m-d'))
                    ->findAll();
    }

    /**
     * Get voucher by code
     */
    public function getVoucherByCode($code)
    {
        return $this->where('kode', $code)
                    ->where('status', '1')
                    ->first();
    }

    /**
     * Check if voucher is valid and available
     */
    public function isVoucherValid($code)
    {
        $voucher = $this->getVoucherByCode($code);
        
        if (!$voucher) {
            return false;
        }

        // Check if voucher is within date range
        $today = date('Y-m-d');
        if ($voucher->tgl_masuk > $today || $voucher->tgl_keluar < $today) {
            return false;
        }

        // Check if voucher usage hasn't exceeded maximum
        if ($voucher->jml_keluar >= $voucher->jml_max) {
            return false;
        }

        return true;
    }

    /**
     * Use voucher (increment jml_keluar)
     */
    public function useVoucher($id)
    {
        $voucher = $this->find($id);
        if (!$voucher) {
            return false;
        }

        // Check if voucher can still be used
        if ($voucher->jml_keluar >= $voucher->jml_max) {
            return false;
        }

        // Increment usage count
        return $this->update($id, [
            'jml_keluar' => $voucher->jml_keluar + 1
        ]);
    }

    /**
     * Get voucher statistics
     */
    public function getVoucherStats($id)
    {
        $voucher = $this->find($id);
        if (!$voucher) {
            return null;
        }

        return [
            'total'     => $voucher->jml_max,
            'used'      => $voucher->jml_keluar,
            'remaining' => $voucher->jml_max - $voucher->jml_keluar,
            'percentage_used' => ($voucher->jml_keluar / $voucher->jml_max) * 100
        ];
    }

    /**
     * Get vouchers with pagination and search
     */
    public function getVouchersWithPagination($keyword = null, $perPage = 10)
    {
        if ($keyword) {
            $this->groupStart()
                 ->like('kode', $keyword)
                 ->orLike('keterangan', $keyword)
                 ->groupEnd();
        }

        return $this->orderBy('id', 'DESC')->paginate($perPage);
    }
}