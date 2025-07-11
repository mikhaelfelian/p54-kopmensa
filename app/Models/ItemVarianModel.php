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
        'id_item', 'id_item_harga', 'kode', 'nama', 'harga_beli', 'harga_jual', 
        'barcode', 'foto', 'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'id_item' => 'required|integer',
        'id_item_harga' => 'required|integer',
        'kode'    => 'required|max_length[50]',
        'nama'    => 'required|max_length[255]',
        'harga_beli' => 'permit_empty|decimal',
        'harga_jual' => 'permit_empty|decimal',
        'barcode' => 'permit_empty|max_length[100]',
        'status' => 'permit_empty|in_list[0,1]'
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
    public function generateKode($itemId)
    {
        $prefix = 'VAR';
        $lastKode = $this->select('kode')
                        ->where('id_item', $itemId)
                        ->like('kode', $prefix, 'after')
                        ->orderBy('kode', 'DESC')
                        ->first();

        if (!$lastKode) {
            return $prefix . str_pad($itemId, 4, '0', STR_PAD_LEFT) . '001';
        }

        $lastNumber = (int)substr($lastKode->kode, -3);
        $newNumber = $lastNumber + 1;
        return $prefix . str_pad($itemId, 4, '0', STR_PAD_LEFT) . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
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
        $builder->select('tbl_m_item_varian.*, tbl_m_item_harga.nama as harga_nama, tbl_m_item_harga.harga as harga_jual_value');
        $builder->join('tbl_m_item_harga', 'tbl_m_item_harga.id = tbl_m_item_varian.id_item_harga', 'left');
        $builder->where('tbl_m_item_varian.id_item', $itemId);
        $builder->where('tbl_m_item_varian.status', '1');
        $builder->orderBy('tbl_m_item_varian.nama', 'ASC');
        
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
                   ->orderBy('nama', 'ASC')
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