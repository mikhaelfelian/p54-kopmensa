<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-08-23
 * Github : github.com/mikhaelfelian
 * description : Model for managing customer group data
 * This file represents the Model for Customer Group data management.
 */

class PelangganGrupModel extends Model
{
    protected $table            = 'tbl_m_pelanggan_grup';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pelanggan', 'grup', 'deskripsi', 'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all active customer groups
     */
    public function getActiveGroups()
    {
        return $this->where('status', '1')
                    ->groupBy('grup')
                    ->orderBy('grup', 'ASC')
                    ->findAll();
    }

    /**
     * Get customer groups by customer ID
     */
    public function getGroupsByCustomerId($customerId)
    {
        return $this->where('id_pelanggan', $customerId)
                    ->where('status', '1')
                    ->findAll();
    }

    /**
     * Get unique group names
     */
    public function getUniqueGroupNames()
    {
        return $this->select('grup, deskripsi')
                    ->where('status', '1')
                    ->groupBy('grup')
                    ->orderBy('grup', 'ASC')
                    ->findAll();
    }

    /**
     * Check if customer belongs to a specific group
     */
    public function isCustomerInGroup($customerId, $groupName)
    {
        return $this->where('id_pelanggan', $customerId)
                    ->where('grup', $groupName)
                    ->where('status', '1')
                    ->countAllResults() > 0;
    }
}
