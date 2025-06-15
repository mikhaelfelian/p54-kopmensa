<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * SupplierModel
 * 
 * This model handles database operations for supplier data
 */

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table            = 'tbl_m_supplier';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode', 'nama', 'npwp', 'alamat', 'rt', 'rw', 
        'kecamatan', 'kelurahan', 'kota', 'no_tlp', 'no_hp',
        'tipe', 'status', 'status_hps'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField   = 'deleted_at';

    /**
     * Generate unique supplier code
     */
    public function generateKode()
    {
        $prefix = 'SUP';
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
     * Get supplier type label
     */
    public function getTipeLabel($tipe)
    {
        $labels = [
            '0' => '-',
            '1' => 'Instansi',
            '2' => 'Personal'
        ];

        return $labels[$tipe] ?? '-';
    }

    /**
     * Get status label
     */
    public function getStatusLabel($status)
    {
        return $status == '1' ? 'Aktif' : 'Non-Aktif';
    }
} 