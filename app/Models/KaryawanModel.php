<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-17
 * 
 * KaryawanModel
 * 
 * This model handles database operations for employee (karyawan) data
 */

namespace App\Models;

use CodeIgniter\Model;

class KaryawanModel extends Model
{
    protected $table            = 'tbl_m_karyawan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_user',
        'id_user_group',
        'kode',
        'nik',
        'nama',
        'nama_pgl',
        'tmp_lahir',
        'tgl_lahir',
        'alamat',
        'alamat_domisili',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'kota',
        'jns_klm',
        'jabatan',
        'no_hp',
        'file_foto',
        'status',
        'status_hps',
        'password_hash'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField   = 'deleted_at';

    /**
     * Generate unique employee code
     */
    public function generateKode()
    {
        $prefix = 'KRY';
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

    /**
     * Get status label
     */
    public function getStatusLabel($status)
    {
        $labels = [
            1 => 'Perawat',
            2 => 'Dokter', 
            3 => 'Kasir',
            4 => 'Analis',
            5 => 'Radiografer',
            6 => 'Farmasi'
        ];

        return $labels[$status] ?? '-';
    }

    /**
     * Get karyawan by group ID
     * 
     * @param int $groupId The group ID to filter by
     * @return array Array of karyawan objects
     */
    public function getByGroup($groupId)
    {
        return $this->select('tbl_m_karyawan.*')
                    ->where('tbl_m_karyawan.id_user_group', $groupId)
                    ->where('tbl_m_karyawan.status', '1')
                    ->orderBy('tbl_m_karyawan.nama', 'ASC')
                    ->findAll();
    }

} 