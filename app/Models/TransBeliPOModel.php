<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-20
 * 
 * TransBeliPOModel
 * 
 * This model handles database operations for purchase orders
 */

namespace App\Models;

use CodeIgniter\Model;

class TransBeliPOModel extends Model
{
    protected $table            = 'tbl_trans_beli_po';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_penerima', 'id_supplier', 'id_user', 'tgl_masuk', 'tgl_keluar',
        'no_nota', 'supplier', 'keterangan', 'pengiriman', 'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime'; 
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate unique PO number in SAP style: 10-digit numeric, starting with 45 + YYMM + 6-digit sequence
     * Example: 452407000001 (45 + 24(year) + 07(month) + 000001)
     * 
     * @return string
     */
    public function generateNoNota()
    {
        $prefix = '45' . date('ym'); // SAP style: 45 + YYMM

        // Find the last PO with this prefix
        $lastPO = $this->select('no_nota')
                       ->like('no_nota', $prefix, 'after')
                       ->orderBy('no_nota', 'DESC')
                       ->first();

        if (!$lastPO) {
            // First PO for this period
            return $prefix . '000001';
        }

        // Extract the last 6 digits and increment
        $lastNumber = (int)substr($lastPO->no_nota, -6);
        $newNumber = $lastNumber + 1;

        // Format with leading zeros to 6 digits
        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get PO with relations
     */
    public function getWithRelations($conditions = [])
    {
        $builder = $this->select('
                tbl_trans_beli_po.*, 
                tbl_m_supplier.nama as supplier_name,
                tbl_m_supplier.alamat as supplier_address,
                tbl_m_supplier.no_tlp as supplier_phone,
                tbl_ion_users.username as created_by,
                (SELECT COUNT(*) FROM tbl_trans_beli_po_det WHERE id_pembelian = tbl_trans_beli_po.id) as total_items
            ')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli_po.id_supplier', 'left')
            ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_beli_po.id_user', 'left')
            ->where('tbl_trans_beli_po.deleted_at IS NULL');

        // If single record is requested
        if (isset($conditions['tbl_trans_beli_po.id'])) {
            return $builder->where($conditions)->get()->getRow();
        }

        // Apply filters for list view
        
        // Filter by PO Number (no_po)
        if (!empty($conditions['no_nota'])) {
            $builder->like('tbl_trans_beli_po.no_nota', $conditions['no_nota']);
        }

        // Filter by supplier
        if (!empty($conditions['supplier'])) {
            $builder->where('tbl_trans_beli_po.id_supplier', $conditions['supplier']);
        }

        // Filter by status
        if (isset($conditions['status']) && $conditions['status'] !== '') {
            $builder->where('tbl_trans_beli_po.status', $conditions['status']);
        }

        // Date range filter
        if (!empty($conditions['date_start'])) {
            $builder->where('DATE(tbl_trans_beli_po.created_at) >=', $conditions['date_start']);
        }

        if (!empty($conditions['date_end'])) {
            $builder->where('DATE(tbl_trans_beli_po.created_at) <=', $conditions['date_end']);
        }

        // Item count range filter
        if (!empty($conditions['min_items'])) {
            $builder->having('total_items >=', (int)$conditions['min_items']);
        }

        if (!empty($conditions['max_items'])) {
            $builder->having('total_items <=', (int)$conditions['max_items']);
        }

        // General search query (searches in PO number and supplier name)
        if (!empty($conditions['q'])) {
            $builder->groupStart()
                   ->like('tbl_trans_beli_po.no_nota', $conditions['q'])
                   ->orLike('tbl_m_supplier.nama', $conditions['q'])
                   ->groupEnd();
        }

        return $builder->orderBy('tbl_trans_beli_po.created_at', 'DESC');
    }

    /**
     * Get status label
     */
    public function getStatusLabel($status)
    {
        $labels = [
            0 => 'Draft',
            1 => 'Menunggu Persetujuan',
            2 => 'Disetujui',
            3 => 'Ditolak',
            4 => 'Diterima',
            5 => 'Selesai'
        ];

        return $labels[$status] ?? 'Unknown';
    }

    /**
     * Get count of trashed POs
     * 
     * @return int
     */
    public function getTrashCount()
    {
        return $this->where('status_hps', '1')
                    ->countAllResults();
    }

    /**
     * Get PO with supplier details
     * 
     * @param int $id PO ID
     * @return object|null
     */
    public function getPOWithDetails($id)
    {
        return $this->select('
                tbl_trans_beli_po.*,
                tbl_m_supplier.nama as supplier_name,
                tbl_m_supplier.alamat as supplier_address,
                tbl_m_supplier.no_tlp as supplier_phone
            ')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli_po.id_supplier', 'left')
            ->where('tbl_trans_beli_po.id', $id)
            ->first();
    }
} 