<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: <?= date('Y-m-d') ?>

 * Github: github.com/mikhaelfelian
 * description: Model for Petty Cash Category (tbl_m_petty_category)
 * This file represents the Model.
 */

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
        'nama', 'kode', 'deskripsi', 'status', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'nama'      => 'required|min_length[3]|max_length[100]|is_unique[tbl_m_petty_category.nama,id,{id}]',
        'kode'      => 'required|min_length[2]|max_length[100]|is_unique[tbl_m_petty_category.kode,id,{id}]',
        'deskripsi' => 'permit_empty|max_length[100]',
        'status'    => 'required|in_list[1,0]'
    ];

    protected $validationMessages = [
        'nama' => [
            'required'   => 'Nama kategori harus diisi',
            'min_length' => 'Nama kategori minimal 3 karakter',
            'max_length' => 'Nama kategori maksimal 100 karakter',
            'is_unique'  => 'Nama kategori sudah digunakan'
        ],
        'kode' => [
            'required'   => 'Kode kategori harus diisi',
            'min_length' => 'Kode kategori minimal 2 karakter',
            'max_length' => 'Kode kategori maksimal 100 karakter',
            'is_unique'  => 'Kode kategori sudah digunakan'
        ],
        'deskripsi' => [
            'max_length' => 'Deskripsi maksimal 100 karakter'
        ],
        'status' => [
            'required' => 'Status aktif harus diisi',
            'in_list'  => 'Status aktif tidak valid'
        ]
    ];

    /**
     * Get active categories
     */
    public function getActiveCategories()
    {
        return $this->where('status', '1')
                    ->orderBy('nama', 'ASC')
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
            $dropdown[$category->id] = $category->kode . ' - ' . $category->nama;
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
