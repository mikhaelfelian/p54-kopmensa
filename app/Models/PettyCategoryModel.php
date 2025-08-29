<?php

namespace App\Models;

use CodeIgniter\Model;

class PettyCategoryModel extends Model
{
    protected $table            = 'tbl_m_petty_category';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name', 'is_active', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name'      => 'required|min_length[3]|max_length[100]|is_unique[tbl_m_petty_category.name,id,{id}]',
        'is_active' => 'required|in_list[1,0]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama kategori harus diisi',
            'min_length' => 'Nama kategori minimal 3 karakter',
            'max_length' => 'Nama kategori maksimal 100 karakter',
            'is_unique' => 'Nama kategori sudah digunakan'
        ],
        'is_active' => [
            'required' => 'Status aktif harus diisi',
            'in_list' => 'Status aktif tidak valid'
        ]
    ];

    /**
     * Get active categories
     */
    public function getActiveCategories()
    {
        return $this->where('is_active', '1')
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Get categories for dropdown
     */
    public function getCategoriesForDropdown()
    {
        $categories = $this->getActiveCategories();
        $dropdown = [];
        
        foreach ($categories as $category) {
            $dropdown[$category->id] = $category->name;
        }
        
        return $dropdown;
    }

    /**
     * Check if category is used in transactions
     */
    public function isCategoryUsed($categoryId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_pos_petty_cash');
        
        return $builder->where('category_id', $categoryId)->countAllResults() > 0;
    }
}
