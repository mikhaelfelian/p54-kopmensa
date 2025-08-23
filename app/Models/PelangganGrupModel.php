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

    /**
     * Get groups with customer information for listing
     */
    public function getGroupsWithCustomerInfo($perPage = 10, $keyword = '', $page = 1)
    {
        $this->select('tbl_m_pelanggan_grup.*, tbl_m_pelanggan.nama as nama_pelanggan, tbl_m_pelanggan.no_telp as telepon_pelanggan')
             ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_m_pelanggan_grup.id_pelanggan', 'left')
             ->orderBy('tbl_m_pelanggan_grup.id', 'DESC');

        if ($keyword) {
            $this->groupStart()
                 ->like('tbl_m_pelanggan_grup.grup', $keyword)
                 ->orLike('tbl_m_pelanggan_grup.deskripsi', $keyword)
                 ->orLike('tbl_m_pelanggan.nama', $keyword)
                 ->groupEnd();
        }

        return $this->paginate($perPage, 'adminlte_pagination', $page);
    }

    /**
     * Get single group with customer information
     */
    public function getGroupWithCustomerInfo($id)
    {
        return $this->select('tbl_m_pelanggan_grup.*, tbl_m_pelanggan.nama as nama_pelanggan, tbl_m_pelanggan.no_telp as telepon_pelanggan, tbl_m_pelanggan.alamat as alamat_pelanggan')
                    ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_m_pelanggan_grup.id_pelanggan', 'left')
                    ->where('tbl_m_pelanggan_grup.id', $id)
                    ->first();
    }
}
