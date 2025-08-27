<?php

namespace App\Models;
use CodeIgniter\Model;

class PengaturanModel extends Model
{
    protected $table = 'tbl_pengaturan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'judul', 'judul_app', 'alamat', 'deskripsi', 'kota', 
        'url', 'theme', 'pagination_limit', 'favicon', 'logo', 
        'logo_header', 'apt_apa', 'apt_sipa', 'ppn'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = '';
    protected $updatedField = 'updated_at';
    protected $deletedField = '';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get settings as single row
     */
    public function getSettings() {
        return $this->first();
    }

    /**
     * Update settings
     */
    public function updateSettings($data) {
        $settings = $this->first();
        if ($settings) {
            return $this->update($settings->id, $data);
        }
        return false;
    }
}