<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-20
 * 
 * TransBeliPODetModel
 * 
 * This model handles database operations for purchase order details
 */

namespace App\Models;

use CodeIgniter\Model;

class TransBeliPODetModel extends Model
{
    protected $table            = 'tbl_trans_beli_po_det';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_user', 'id_pembelian', 'id_item', 'id_satuan', 'tgl_masuk',
        'kode', 'item', 'jml', 'jml_satuan', 'satuan', 'keterangan',
        'keterangan_itm', 'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get PO details with relations
     */
    public function getWithRelations($id_pembelian = null)
    {
        $builder = $this->select('
                tbl_trans_beli_po_det.*,
                tbl_m_item.item as item_name,
                tbl_m_satuan.satuanBesar as satuan_name,
                tbl_ion_users.username as username
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_beli_po_det.id_item', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_trans_beli_po_det.id_satuan', 'left')
            ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_beli_po_det.id_user', 'left');

        if ($id_pembelian !== null) {
            return $builder->where('tbl_trans_beli_po_det.id_pembelian', $id_pembelian)
                         ->get()
                         ->getResult();
        }

        return $builder->get()->getResult();
    }

    /**
     * Get status label
     */
    public function getStatusLabel($status)
    {
        return $status === '1' ? 'Selesai' : 'Pending';
    }

    /**
     * Get total items for a PO
     */
    public function getTotalItems($id_pembelian)
    {
        return $this->where('id_pembelian', $id_pembelian)
                   ->countAllResults();
    }

    /**
     * Get total quantity for a PO
     */
    public function getTotalQuantity($id_pembelian)
    {
        return $this->selectSum('jml')
                   ->where('id_pembelian', $id_pembelian)
                   ->get()
                   ->getRow()
                   ->jml ?? 0;
    }

    /**
     * Get items by PO ID
     * 
     * @param int $id PO ID
     * @return array
     */
    public function getItemByPO($id)
    {
        return $this->where('id_pembelian', $id)->findAll();
    }
} 