<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-20
 * Github : github.com/mikhaelfelian
 * description : Model for managing customer (pelanggan) data
 * This file represents the Model for Customer data management.
 */

namespace App\Models;

use CodeIgniter\Model;

class PelangganModel extends Model
{
    protected $table            = 'tbl_m_pelanggan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_user', 'kode', 'nama', 'no_telp', 'alamat', 'kota', 
        'provinsi', 'tipe', 'status', 'status_hps', 'status_blokir', 'limit'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField   = 'deleted_at';

    /**
     * Generate unique customer code
     */
    public function generateKode()
    {
        $prefix = 'CST';
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
     * Get customer type label
     */
    public function getTipeLabel($tipe)
    {
        $labels = [
            '0' => '-',
            '1' => 'Anggota',
            '2' => 'Pelanggan'
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

    /**
     * Get status blokir label
     */
    public function getStatusBlokirLabel($status_blokir)
    {
        return $status_blokir == '1' ? 'Diblokir' : 'Tidak Diblokir';
    }

    /**
     * Get formatted limit
     */
    public function getLimitFormatted($limit)
    {
        return number_format($limit, 2, ',', '.');
    }

    /**
     * Get paginated records with proper method signature
     * 
     * @param int|null $perPage Number of items per page
     * @param string $group Name of pager group
     * @param int|null $page Page number
     * @param int $segment URI segment for page number
     * @return array
     */
    public function paginate(?int $perPage = null, string $group = 'default', ?int $page = null, int $segment = 0)
    {
        $this->orderBy('id', 'DESC');
        return parent::paginate($perPage, $group, $page, $segment);
    }
} 