<?php

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github : github.com/mikhaelfelian
 * Description : Model for managing product variant data
 * This file represents the Model.
 */

namespace App\Models;

use CodeIgniter\Model;

class VarianModel extends Model
{
    protected $table = 'tbl_m_varian';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'kode',
        'nama', 
        'keterangan',
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
        'kode' => 'required|max_length[50]|is_unique[tbl_m_varian.kode,id,{id}]',
        'nama' => 'required|max_length[100]',
        'status' => 'in_list[0,1]'
    ];

    protected $validationMessages = [
        'kode' => [
            'required' => 'Kode varian harus diisi',
            'max_length' => 'Kode varian maksimal 50 karakter',
            'is_unique' => 'Kode varian sudah digunakan'
        ],
        'nama' => [
            'required' => 'Nama varian harus diisi',
            'max_length' => 'Nama varian maksimal 100 karakter'
        ],
        'status' => [
            'in_list' => 'Status harus berupa 0 atau 1'
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
     * Get active variants
     */
    public function getActiveVariants()
    {
        return $this->where('status', '1')->findAll();
    }

    /**
     * Get variant by code
     */
    public function getByCode($kode)
    {
        return $this->where('kode', $kode)->first();
    }

    /**
     * Generate new variant code
     */
    public function generateCode($prefix = 'VAR')
    {
        $lastCode = $this->selectMax('kode')
                         ->where('kode LIKE', $prefix . '%')
                         ->first();
        
        if ($lastCode && $lastCode->kode) {
            $lastNumber = (int) substr($lastCode->kode, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
} 