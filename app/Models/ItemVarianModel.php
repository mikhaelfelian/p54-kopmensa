<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-11
 * Github : github.com/mikhaelfelian
 * description : Model for managing item variant data
 * This file represents the Model for ItemVarian data management.
 */
class ItemVarianModel extends Model
{
    protected $table            = 'tbl_m_item_varian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_item',
        'id_item_harga',
        'kode',
        'barcode',
        'varian',
        'harga_beli',
        'harga_dasar',
        'harga_jual',
        'foto',
        'status',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'id_item' => 'required|integer',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Generate unique variant code based on item ID
     * 
     * @param int $itemId Item ID
     * @return string
     */
    /**
     * Generate SAP-style variant code: [YYYYMMDD][6-digit running number]
     * Only numeric, no prefix, running number is sorted in DB per date.
     * Example: 20240612000001
     *
     * @return string
     */
    public function generateKode()
    {
        // Format: yymmddxxxN (e.g., 2507290014)
        $datePart = date('ymd');
        // Find the last code for today
        $lastKode = $this->select('kode')
            ->like('kode', $datePart, 'after')
            ->orderBy('kode', 'DESC')
            ->first();

        if (!$lastKode || !preg_match('/^' . $datePart . '(\d{3})$/', $lastKode->kode, $matches)) {
            $newNumber = 1;
        } else {
            $newNumber = intval(substr($lastKode->kode, 6, 3)) + 1;
        }

        // Ensure the running number does not exceed 3 digits (max 999)
        if ($newNumber > 999) {
            $newNumber = 1; // Reset or handle as needed
        }

        // Add a random digit at the end for extra uniqueness (0-9)
        $randomDigit = random_int(0, 9);

        return $datePart . str_pad($newNumber, 3, '0', STR_PAD_LEFT) . $randomDigit;
    }

    /**
     * Get status label
     * 
     * @param string $status Status code
     * @return string
     */
    public function getStatusLabel($status)
    {
        return $status == '1' ? 'Aktif' : 'Non Aktif';
    }

    /**
     * Get formatted price
     * 
     * @param float $harga Price value
     * @return string
     */
    public function getHargaFormatted($harga)
    {
        return number_format($harga, 0, ',', '.');
    }

    /**
     * Get variants with price information
     * 
     * @param int $itemId Item ID
     * @return array
     */
    public function getVariantsWithPrice($itemId)
    {
        $builder = $this->builder();
        $builder->select('tbl_m_item_varian.id, tbl_m_item_varian.id_item, tbl_m_item_varian.kode, tbl_m_item_varian.barcode, tbl_m_item_varian.varian as nama, tbl_m_item_varian.harga_beli, tbl_m_item_varian.harga_dasar, tbl_m_item_varian.harga_jual, tbl_m_item_varian.foto, tbl_m_item_varian.status, tbl_m_item_varian.id_item_harga, tbl_m_item_harga.nama as harga_nama, tbl_m_item_harga.harga as harga_jual_value');
        $builder->join('tbl_m_item_harga', 'tbl_m_item_harga.id = tbl_m_item_varian.id_item_harga', 'left');
        $builder->where('tbl_m_item_varian.id_item', $itemId);
        $builder->where('tbl_m_item_varian.status', '1');
        $builder->orderBy('tbl_m_item_varian.varian', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get variants by item ID
     * 
     * @param int $itemId Item ID
     * @return array
     */
    public function getVariantsByItemId($itemId)
    {
        return $this->where('id_item', $itemId)
                   ->where('status', '1')
                   ->orderBy('varian', 'ASC')
                   ->findAll();
    }

    /**
     * Get active variants count by item ID
     * 
     * @param int $itemId Item ID
     * @return int
     */
    public function getActiveVariantsCount($itemId)
    {
        return $this->where('id_item', $itemId)
                   ->where('status', '1')
                   ->countAllResults();
    }
} 