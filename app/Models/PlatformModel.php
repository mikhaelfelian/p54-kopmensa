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
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_outlet','kode', 'platform', 'keterangan', 'persen', 'status', 'status_sys', 'status_hps', 'is_default'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField   = 'deleted_at';

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

    /**
     * Get platforms by outlet
     *
     * @param int $outletId
     * @return array
     */
    public function getByOutlet($outletId)
    {
        return $this->where('id_outlet', $outletId)
                    ->where('status', '1')
                    ->findAll();
    }

    /**
     * Set default payment methods for an outlet
     *
     * @param int $outletId
     * @param array $metodeIds
     * @return bool
     */
    public function setDefault($outletId, array $metodeIds = [])
    {
        try {
            $this->db = \Config\Database::connect();
            
            // First, unset all defaults for this outlet
            $this->builder()
                ->where('id_outlet', $outletId)
                ->set(['is_default' => '0'])
                ->update();
            
            // Then set the new defaults
            if (!empty($metodeIds)) {
                return $this->builder()
                    ->where('id_outlet', $outletId)
                    ->whereIn('id', $metodeIds)
                    ->set(['is_default' => '1'])
                    ->update();
            }
            
            return true;
        } catch (\Exception $e) {
            log_message('error', '[PlatformModel::setDefault] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Archive multiple platforms (soft delete)
     *
     * @param array $ids Platform IDs to archive
     * @return bool
     */
    public function archiveMany(array $ids): bool
    {
        if (empty($ids)) {
            return false;
        }

        try {
            $now = date('Y-m-d H:i:s');
            return $this->builder()
                ->whereIn('id', $ids)
                ->set(['status_hps' => '1', 'deleted_at' => $now])
                ->update();
        } catch (\Exception $e) {
            log_message('error', '[PlatformModel::archiveMany] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Restore multiple archived platforms
     *
     * @param array $ids Platform IDs to restore
     * @return bool
     */
    public function restoreMany(array $ids): bool
    {
        if (empty($ids)) {
            return false;
        }

        try {
            return $this->builder()
                ->whereIn('id', $ids)
                ->set(['status_hps' => '0', 'deleted_at' => null])
                ->update();
        } catch (\Exception $e) {
            log_message('error', '[PlatformModel::restoreMany] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Count archived platforms
     *
     * @return int
     */
    public function countArchived(): int
    {
        $builder = $this->db->table($this->table);
        return (int) $builder
            ->groupStart()
                ->where('status_hps', '1')
                ->orWhere('deleted_at IS NOT NULL', null, false)
            ->groupEnd()
            ->countAllResults();
    }
} 