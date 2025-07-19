<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table            = 'tbl_m_kategori';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['kode', 'kategori', 'keterangan', 'status', 'updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'kode'       => 'permit_empty|max_length[100]',
        'kategori'   => 'permit_empty|max_length[255]',
        'keterangan' => 'permit_empty',
        'status'     => 'permit_empty|in_list[0,1]',
    ];

    /**
     * Generate unique kode for kategori
     * Format: KTG-001, KTG-002, etc
     */
    /**
     * Generate unique kode for kategori using SAP style:
     * e.g. CAT01, CAT02, etc.
     * - Prefix: 3 chars from category name (no space, uppercase, pad with X if <3)
     * - Suffix: 2 digit running number (01, 02, ...)
     * - Max length: 5 chars
     * @param string $categoryName
     * @return string
     */
    public function generateKode($categoryName = null)
    {
        if ($categoryName === null) {
            // Optionally, you can throw an exception or return a default value
            throw new \InvalidArgumentException('Category name is required');
        }

        // Take the first 2 chars from brand name (no padding with X)
        $prefix = substr($categoryName, 0, 2);
        $prefix = strtoupper($prefix);

        // Find the last code for this prefix
        // Count how many existing codes with this prefix
        $numRows = $this->countAllResults();

        if ($numRows == 0) {
            // Start from 0001
            $suffix = '0001';
        } else {
            $suffix = str_pad($numRows + 1, 4, '0', STR_PAD_LEFT);
        }

        // Combine and ensure max 6 chars
        $kode = $prefix . $suffix;
        return substr($kode, 0, 6);
    }
} 