<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-17
 * Github : github.com/mikhaelfelian
 * description : Model for managing outlet data
 * This file represents the Model for Outlet data management.
 */
class OutletModel extends Model
{
    protected $table            = 'tbl_m_outlet';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_user',
        'kode',
        'nama',
        'deskripsi',
        'status',
        'status_hps'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Generate unique kode for outlet
     * Format: OTL-001, OTL-002, etc
     */
    public function generateKode()
    {
        $prefix = 'OTL-';
        $lastKode = $this->select('kode')
                        ->like('kode', $prefix, 'after')
                        ->orderBy('kode', 'DESC')
                        ->first();

        if (!$lastKode) {
            return $prefix . '001';
        }

        $lastNumber = (int) substr($lastKode['kode'], strlen($prefix));
        $newNumber = $lastNumber + 1;
        
        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
} 