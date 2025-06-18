<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-17
 * Github : github.com/mikhaelfelian
 * description : Model for managing item data
 * This file represents the Model for Item data management.
 */
class ItemModel extends Model
{
    protected $table            = 'tbl_m_item';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_user',
        'id_kategori',
        'id_satuan',
        'id_merk',
        'kode',
        'barcode',
        'item',
        'deskripsi',
        'jml_min',
        'harga_beli',
        'harga_jual',
        'foto',
        'tipe',
        'status',
        'status_hps'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Generate unique item code
     * 
     * @return string
     */
    public function generateKode()
    {
        $lastCode = $this->select('kode')
            ->where('status_hps', '0')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode['kode'], 4); // Remove 'ITM-' prefix
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'ITM-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
} 