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
     * Generate unique PO number with format PO25010001
     * PO + YY + MM + 4-digit sequence
     * 
     * @return string
     */
    public function generateNoNota()
    {
        $prefix = 'PO' . date('ym');
        
        $lastPO = $this->select('no_nota')
                       ->like('no_nota', $prefix, 'after')
                       ->orderBy('no_nota', 'DESC')
                       ->first();

        if (!$lastPO) {
            return $prefix . '0001';
        }

        // Extract the numeric part and increment
        $lastNumber = (int)substr($lastPO->no_nota, -4);
        $newNumber = $lastNumber + 1;
        
        // Format with leading zeros
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
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
            ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_beli_po.id_user', 'left');

        // If single record is requested
        if (isset($conditions['tbl_trans_beli_po.id'])) {
            return $builder->where($conditions)->get()->getRow();
        }

        // Apply filters for list view
        if (!empty($conditions['supplier'])) {
            $builder->where('tbl_trans_beli_po.id_supplier', $conditions['supplier']);
        }

        if (isset($conditions['status']) && $conditions['status'] !== '') {
            $builder->where('tbl_trans_beli_po.status', $conditions['status']);
        }

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