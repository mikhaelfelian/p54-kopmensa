<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-04
 * 
 * TransBeli Model
 * Handles database operations for tbl_trans_beli table
 */

namespace App\Models;

use CodeIgniter\Model;

class TransBeliModel extends Model
{
    protected $table            = 'tbl_trans_beli';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_penerima',
        'id_supplier',
        'id_user',
        'id_po',
        'created_at',
        'updated_at',
        'deleted_at',
        'tgl_bayar',
        'tgl_masuk',
        'tgl_keluar',
        'no_nota',
        'no_po',
        'supplier',
        'jml_total',
        'disk1',
        'disk2',
        'disk3',
        'jml_potongan',
        'jml_retur',
        'jml_diskon',
        'jml_biaya',
        'jml_ongkir',
        'jml_subtotal',
        'jml_dpp',
        'ppn',
        'jml_ppn',
        'jml_gtotal',
        'jml_bayar',
        'jml_kembali',
        'jml_kurang',
        'status_bayar',
        'status_nota',
        'status_ppn',
        'status_retur',
        'status_penerimaan',
        'metode_bayar',
        'status_hps',
        'status_terima',
        'tgl_terima',
        'catatan_terima',
        'id_user_terima'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert  = [];
    protected $afterInsert   = [];
    protected $beforeUpdate  = [];
    protected $afterUpdate   = [];
    protected $beforeFind    = [];
    protected $afterFind     = [];
    protected $beforeDelete  = [];
    protected $afterDelete   = [];

    /**
     * Get purchase transaction with supplier data
     *
     * @param int|null $id
     * @return object|null
     */
    public function getWithSupplier($id = null)
    {
        $builder = $this->db->table($this->table)
            ->select($this->table . '.*, supplier.nama as supplier_name')
            ->join('tbl_m_supplier supplier', 'supplier.id = ' . $this->table . '.id_supplier', 'left');

        if ($id !== null) {
            return $builder->where($this->table . '.id', $id)
                         ->get()
                         ->getRow();
        }

        return $builder->get()->getResult();
    }

    /**
     * Generate unique transaction code
     * 
     * @return string
     */
    public function generateKode()
    {
        $prefix = 'FB-' . date('ym');
        $lastKode = $this->select('no_nota')
                        ->like('no_nota', $prefix, 'after')
                        ->orderBy('id', 'DESC')
                        ->first();

        if ($lastKode) {
            $lastNumber = (int) substr($lastKode->no_nota, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
} 