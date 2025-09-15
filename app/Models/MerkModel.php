<?php

namespace App\Models;

use CodeIgniter\Model;

class MerkModel extends Model
{
    protected $table            = 'tbl_m_merk';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['kode', 'merk', 'keterangan', 'status', 'updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'kode'       => 'permit_empty|max_length[160]',
        'merk'       => 'required|max_length[160]',
        'keterangan' => 'permit_empty',
        'status'     => 'permit_empty|in_list[0,1]',
    ];

    /**
     * Generate unique kode for merk using SAP style:
     * e.g. BRND01, BRND02, etc.
     * - Prefix: 4 chars from brand name (no space, uppercase, pad with X if <4)
     * - Suffix: 2 digit running number (01, 02, ...)
     * - Max length: 6 chars
     * @param string $brandName
     * @return string
     */
    public function generateKode($brandName)
    {
        // Take the first 2 chars from brand name (no padding with X)
        $prefix = substr($brandName, 0, 2);
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