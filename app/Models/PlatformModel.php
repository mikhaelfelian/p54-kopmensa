<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * PlatformModel
 * 
 * This model handles database operations for Platform (Payment Platform) data
 */

namespace App\Models;

use CodeIgniter\Model;

class PlatformModel extends Model
{
    protected $table            = 'tbl_m_platform';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode', 'platform', 'keterangan', 'persen', 'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate unique platform code
     */
    public function generateKode()
    {
        $prefix = 'PLT';
        $lastKode = $this->select('kode')
                        ->like('kode', $prefix, 'after')
                        ->orderBy('kode', 'DESC')
                        ->first();

        if (!$lastKode) {
            return $prefix . '0001';
        }

        $lastNumber = (int)substr($lastKode->kode, strlen($prefix));
        $newNumber = $lastNumber + 1;
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get status label
     */
    public function getStatusLabel($status)
    {
        return $status === '1' ? 'Aktif' : 'Tidak Aktif';
    }
} 