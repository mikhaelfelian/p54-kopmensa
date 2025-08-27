<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaturanThemeModel extends Model
{
    protected $table = 'tbl_pengaturan_theme';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_pengaturan', 'nama', 'path', 'status'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = '';
    protected $updatedField = '';
    protected $deletedField = '';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get active theme
     */
    public function getActiveTheme() {
        return $this->where('status', 1)->first();
    }

    /**
     * Set theme as active
     */
    public function setActiveTheme($id) {
        // First, set all themes to inactive
        $this->where('id !=', $id)->set(['status' => 0])->update();
        
        // Then set the selected theme as active
        return $this->update($id, ['status' => 1]);
    }

    /**
     * Get theme path
     */
    public function getThemePath() {
        $theme = $this->getActiveTheme();
        return $theme ? $theme->path : 'admin-lte-3';
    }
} 