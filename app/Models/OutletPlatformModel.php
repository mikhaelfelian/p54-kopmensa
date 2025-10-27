<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-27
 * 
 * OutletPlatformModel
 * 
 * This model handles database operations for Outlet Platform junction table
 */

namespace App\Models;

use CodeIgniter\Model;

class OutletPlatformModel extends Model
{
    protected $table            = 'tbl_outlet_platform';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_outlet', 'id_platform', 'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get platforms for a specific outlet
     */
    public function getPlatformsByOutlet($id_outlet)
    {
        return $this->select('tbl_outlet_platform.*, tbl_m_platform.platform, tbl_m_platform.kode as platform_kode, tbl_m_platform.persen, tbl_m_platform.keterangan')
                    ->join('tbl_m_platform', 'tbl_m_platform.id = tbl_outlet_platform.id_platform')
                    ->where('tbl_outlet_platform.id_outlet', $id_outlet)
                    ->where('tbl_outlet_platform.status', '1')
                    ->findAll();
    }

    /**
     * Check if platform is already assigned to outlet
     */
    public function isPlatformAssigned($id_outlet, $id_platform)
    {
        return $this->where('id_outlet', $id_outlet)
                    ->where('id_platform', $id_platform)
                    ->first() !== null;
    }

    /**
     * Remove platform from outlet
     */
    public function removePlatform($id_outlet, $id_platform)
    {
        return $this->where('id_outlet', $id_outlet)
                    ->where('id_platform', $id_platform)
                    ->delete();
    }

    /**
     * Get all platforms not assigned to outlet
     */
    public function getAvailablePlatforms($id_outlet)
    {
        $db = \Config\Database::connect();
        
        return $db->table('tbl_m_platform')
                  ->select('tbl_m_platform.*')
                  ->where('tbl_m_platform.status', '1')
                  ->whereNotIn('tbl_m_platform.id', function($builder) use ($id_outlet) {
                      return $builder->select('id_platform')
                                    ->from('tbl_outlet_platform')
                                    ->where('id_outlet', $id_outlet)
                                    ->where('status', '1');
                  })
                  ->get()
                  ->getResult();
    }
}

