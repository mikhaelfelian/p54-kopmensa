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
        'id_item', 'kode', 'nama', 'atribut1', 'nilai1', 'atribut2', 'nilai2', 
        'atribut3', 'nilai3', 'harga', 'barcode', 'foto', 'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'id_item' => 'required|integer',
        'kode'    => 'required|max_length[50]',
        'nama'    => 'required|max_length[255]',
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
     * Get variant attributes as string
     * 
     * @param object $varian Variant object
     * @return string
     */
    public function getAtributString($varian)
    {
        $atributs = [];
        
        if (!empty($varian->atribut1) && !empty($varian->nilai1)) {
            $atributs[] = $varian->atribut1 . ': ' . $varian->nilai1;
        }
        if (!empty($varian->atribut2) && !empty($varian->nilai2)) {
            $atributs[] = $varian->atribut2 . ': ' . $varian->nilai2;
        }
        if (!empty($varian->atribut3) && !empty($varian->nilai3)) {
            $atributs[] = $varian->atribut3 . ': ' . $varian->nilai3;
        }
        
        return implode(', ', $atributs);
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