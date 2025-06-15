<?php

namespace App\Models;

use CodeIgniter\Model;

class MerkModel extends Model
{
    protected $table            = 'tbl_m_merk';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['kode', 'merk', 'keterangan', 'status', 'updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'kode'       => 'permit_empty|max_length[160]',
        'merk'       => 'permit_empty|max_length[160]',
        'keterangan' => 'permit_empty',
        'status'     => 'permit_empty|in_list[0,1]',
    ];

    /**
     * Generate unique kode for merk
     * Format: MRK-001, MRK-002, etc
     */
    public function generateKode()
    {
        $prefix = 'MRK-';
        $lastKode = $this->select('kode')
                        ->like('kode', $prefix, 'after')
                        ->orderBy('kode', 'DESC')
                        ->first();

        if (!$lastKode) {
            return $prefix . '001';
        }

        $lastNumber = (int) substr($lastKode->kode, strlen($prefix));
        $newNumber = $lastNumber + 1;
        
        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
} 