<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * SupplierModel
 * 
 * This model handles database operations for supplier data
 */

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table            = 'tbl_m_supplier';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode', 'nama', 'npwp', 'alamat', 'rt', 'rw', 
        'kecamatan', 'kelurahan', 'kota', 'no_tlp', 'no_hp',
        'tipe', 'kategori', 'status', 'status_hps'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField   = 'deleted_at';

    /**
     * Generate unique supplier code
     */
    public function generateKode()
    {
        $prefix = 'SUP';
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
     * Get supplier type label
     */
    public function getTipeLabel($tipe)
    {
        $labels = [
            '0' => '-',
            '1' => 'Instansi',
            '2' => 'Personal'
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
     * Archive supplier (soft delete)
     */
    public function archive($id): bool
    {
        return $this->builder()
            ->where('id', $id)
            ->set([
                'status_hps' => '1',
                'deleted_at' => date('Y-m-d H:i:s'),
            ])
            ->update();
    }

    /**
     * Restore archived supplier
     */
    public function restore($id): bool
    {
        return $this->builder()
            ->where('id', $id)
            ->set([
                'status_hps' => '0',
                'deleted_at' => null,
            ])
            ->update();
    }

    /**
     * Permanently delete supplier
     */
    public function purge($id): bool
    {
        return $this->builder()->where('id', $id)->delete();
    }

    /**
     * Count archived suppliers
     */
    public function countArchived(): int
    {
        return (int) $this->builder()
            ->groupStart()
                ->where('status_hps', '1')
                ->orWhere('deleted_at IS NOT NULL', null, false)
            ->groupEnd()
            ->countAllResults();
    }

    /**
     * Get all suppliers for a specific item
     * Uses the item-supplier mapping table
     * 
     * @param int $itemId
     * @return array
     */
    public function getSuppliersByItem($itemId)
    {
        return $this->db->table('tbl_m_item_supplier')
                    ->select('tbl_m_item_supplier.*, tbl_m_supplier.id as supplier_id, tbl_m_supplier.kode as supplier_kode, tbl_m_supplier.nama as supplier_nama, tbl_m_supplier.no_tlp, tbl_m_supplier.alamat, tbl_m_supplier.status')
                    ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_m_item_supplier.id_supplier')
                    ->where('tbl_m_item_supplier.id_item', $itemId)
                    ->where('tbl_m_supplier.status_hps', '0')
                    ->where('tbl_m_item_supplier.deleted_at IS NULL', null, false)
                    ->orderBy('tbl_m_item_supplier.prioritas', 'ASC')
                    ->get()
                    ->getResult();
    }
} 