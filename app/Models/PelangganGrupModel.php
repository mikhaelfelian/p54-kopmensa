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
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'grup', 'deskripsi', 'status', 'status_hps'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField   = 'deleted_at';

    /**
     * Get all active customer groups with member count
     */
    public function getActiveGroups()
    {
        return $this->select('tbl_m_pelanggan_grup.*, COUNT(tbl_m_pelanggan_grup_member.id_pelanggan) as member_count')
                    ->join('tbl_m_pelanggan_grup_member', 'tbl_m_pelanggan_grup_member.id_grup = tbl_m_pelanggan_grup.id', 'left')
                    ->where('tbl_m_pelanggan_grup.status', '1')
                    ->groupBy('tbl_m_pelanggan_grup.id')
                    ->orderBy('tbl_m_pelanggan_grup.grup', 'ASC')
                    ->findAll();
    }

    /**
     * Get customer groups by customer ID
     */
    public function getGroupsByCustomerId($customerId)
    {
        return $this->select('tbl_m_pelanggan_grup.*')
                    ->join('tbl_m_pelanggan_grup_member', 'tbl_m_pelanggan_grup_member.id_grup = tbl_m_pelanggan_grup.id')
                    ->where('tbl_m_pelanggan_grup_member.id_pelanggan', $customerId)
                    ->where('tbl_m_pelanggan_grup.status', '1')
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
    public function isCustomerInGroup($customerId, $groupId)
    {
        $db = \Config\Database::connect();
        return $db->table('tbl_m_pelanggan_grup_member')
                  ->where('id_pelanggan', $customerId)
                  ->where('id_grup', $groupId)
                  ->countAllResults() > 0;
    }

    /**
     * Get groups with member count for listing
     */
    public function getGroupsWithMemberCount($perPage = 10, $keyword = '', $page = 1)
    {
        $this->select('tbl_m_pelanggan_grup.*, COUNT(tbl_m_pelanggan_grup_member.id_pelanggan) as member_count')
             ->join('tbl_m_pelanggan_grup_member', 'tbl_m_pelanggan_grup_member.id_grup = tbl_m_pelanggan_grup.id', 'left')
             ->groupBy('tbl_m_pelanggan_grup.id')
             ->orderBy('tbl_m_pelanggan_grup.id', 'DESC');

        if ($keyword) {
            $this->groupStart()
                 ->like('tbl_m_pelanggan_grup.grup', $keyword)
                 ->orLike('tbl_m_pelanggan_grup.deskripsi', $keyword)
                 ->groupEnd();
        }

        return $this->paginate($perPage, 'adminlte_pagination', $page);
    }

    /**
     * Get single group with member count
     */
    public function getGroupWithMemberCount($id)
    {
        return $this->select('tbl_m_pelanggan_grup.*, COUNT(tbl_m_pelanggan_grup_member.id_pelanggan) as member_count')
                    ->join('tbl_m_pelanggan_grup_member', 'tbl_m_pelanggan_grup_member.id_grup = tbl_m_pelanggan_grup.id', 'left')
                    ->where('tbl_m_pelanggan_grup.id', $id)
                    ->groupBy('tbl_m_pelanggan_grup.id')
                    ->first();
    }

    /**
     * Get group members
     */
    public function getGroupMembers($groupId)
    {
        $db = \Config\Database::connect();
        return $db->table('tbl_m_pelanggan_grup_member')
                  ->select('tbl_m_pelanggan_grup_member.*, tbl_m_pelanggan.nama, tbl_m_pelanggan.no_telp, tbl_m_pelanggan.alamat')
                  ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_m_pelanggan_grup_member.id_pelanggan')
                  ->where('tbl_m_pelanggan_grup_member.id_grup', $groupId)
                  ->get()
                  ->getResult();
    }

    /**
     * Add member to group
     */
    public function addMemberToGroup($groupId, $customerId)
    {
        $db = \Config\Database::connect();
        
        // Check if already exists
        $exists = $db->table('tbl_m_pelanggan_grup_member')
                     ->where('id_grup', $groupId)
                     ->where('id_pelanggan', $customerId)
                     ->countAllResults();
        
        if ($exists == 0) {
            return $db->table('tbl_m_pelanggan_grup_member')->insert([
                'id_grup' => $groupId,
                'id_pelanggan' => $customerId,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        return false; // Already exists
    }

    /**
     * Remove member from group
     */
    public function removeMemberFromGroup($groupId, $customerId)
    {
        $db = \Config\Database::connect();
        return $db->table('tbl_m_pelanggan_grup_member')
                  ->where('id_grup', $groupId)
                  ->where('id_pelanggan', $customerId)
                  ->delete();
    }

    /**
     * Get available customers (not in this group)
     */
    public function getAvailableCustomers($groupId)
    {
        $db = \Config\Database::connect();
        return $db->table('tbl_m_pelanggan')
                  ->select('tbl_m_pelanggan.*')
                  ->whereNotIn('tbl_m_pelanggan.id', function($subQuery) use ($groupId) {
                      $subQuery->select('id_pelanggan')
                               ->from('tbl_m_pelanggan_grup_member')
                               ->where('id_grup', $groupId);
                  })
                  ->where('tbl_m_pelanggan.status', '1')
                  ->get()
                  ->getResult();
    }

    /**
     * Get available customers for a group (paginated)
     */
    public function getAvailableCustomersPaginated($groupId, $perPage = 20, $page = 1, $search = '', $status = '')
    {
        $offset = ($page - 1) * $perPage;
        
        $query = $this->db->table('tbl_m_pelanggan p')
            ->select('p.id, p.nama, p.no_telp, p.status')
            ->where('p.status', '1') // Only active customers
            ->whereNotIn('p.id', function($subQuery) use ($groupId) {
                $subQuery->select('pgm.id_pelanggan')
                    ->from('tbl_m_pelanggan_grup_member pgm')
                    ->where('pgm.id_grup', $groupId);
            });
        
        // Apply search filter
        if (!empty($search)) {
            $query->groupStart()
                ->like('p.nama', $search)
                ->orLike('p.no_telp', $search)
                ->groupEnd();
        }
        
        // Apply status filter
        if (!empty($status)) {
            $query->where('p.status', $status);
        }
        
        return $query->orderBy('p.nama', 'ASC')
            ->limit($perPage, $offset)
            ->get()
            ->getResult();
    }

    /**
     * Get total count of available customers for a group
     */
    public function getTotalAvailableCustomers($groupId, $search = '', $status = '')
    {
        $query = $this->db->table('tbl_m_pelanggan p')
            ->where('p.status', '1') // Only active customers
            ->whereNotIn('p.id', function($subQuery) use ($groupId) {
                $subQuery->select('pgm.id_pelanggan')
                    ->from('tbl_m_pelanggan_grup_member pgm')
                    ->where('pgm.id_grup', $groupId);
            });
        
        // Apply search filter
        if (!empty($search)) {
            $query->groupStart()
                ->like('p.nama', $search)
                ->orLike('p.no_telp', $search)
                ->groupEnd();
        }
        
        // Apply status filter
        if (!empty($status)) {
            $query->where('p.status', $status);
        }
        
        return $query->countAllResults();
    }

    /**
     * Archive multiple groups (soft delete)
     *
     * @param array $ids Group IDs to archive
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
            log_message('error', '[PelangganGrupModel::archiveMany] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Restore multiple archived groups
     *
     * @param array $ids Group IDs to restore
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
            log_message('error', '[PelangganGrupModel::restoreMany] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Count archived groups
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
