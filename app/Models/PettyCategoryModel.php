<?php

namespace App\Models;

use CodeIgniter\Model;

class PettyCategoryModel extends Model
{
    protected $table            = 'tbl_m_petty_category';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'is_active',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'name' => 'required|max_length[100]|is_unique[tbl_m_petty_category.name,id,{id}]',
        'is_active' => 'required|in_list[0,1]'
    ];
    protected $validationMessages   = [
        'name' => [
            'required' => 'Nama kategori harus diisi',
            'max_length' => 'Nama kategori maksimal 100 karakter',
            'is_unique' => 'Nama kategori sudah ada'
        ],
        'is_active' => [
            'required' => 'Status aktif harus diisi',
            'in_list' => 'Status aktif tidak valid'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

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
     * Get all categories with usage count
     */
    public function getCategoriesWithUsage()
    {
        $builder = $this->db->table('tbl_m_petty_category pc_cat')
            ->select('
                pc_cat.*,
                COUNT(pc.id) as usage_count,
                SUM(CASE WHEN pc.direction = "IN" THEN pc.amount ELSE 0 END) as total_in,
                SUM(CASE WHEN pc.direction = "OUT" THEN pc.amount ELSE 0 END) as total_out
            ')
            ->join('tbl_pos_petty_cash pc', 'pc.category_id = pc_cat.id', 'left')
            ->where('pc.status !=', 'void')
            ->orWhere('pc.status IS NULL')
            ->groupBy('pc_cat.id, pc_cat.name, pc_cat.is_active, pc_cat.created_at, pc_cat.updated_at')
            ->orderBy('pc_cat.name', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get category by name
     */
    public function getCategoryByName($name)
    {
        return $this->where('name', $name)
                    ->where('is_active', '1')
                    ->first();
    }

    /**
     * Toggle category status
     */
    public function toggleStatus($id)
    {
        $category = $this->find($id);
        if (!$category) {
            return false;
        }

        $new_status = $category['is_active'] == '1' ? '0' : '1';
        return $this->update($id, ['is_active' => $new_status]);
    }

    /**
     * Get categories for dropdown
     */
    public function getCategoriesForDropdown()
    {
        $categories = $this->getActiveCategories();
        $dropdown = [];
        
        foreach ($categories as $category) {
            $dropdown[$category['id']] = $category['name'];
        }
        
        return $dropdown;
    }

    /**
     * Check if category can be deleted
     */
    public function canDelete($id)
    {
        $builder = $this->db->table('tbl_pos_petty_cash')
            ->where('category_id', $id)
            ->where('status !=', 'void');
        
        $count = $builder->countAllResults();
        return $count == 0;
    }

    /**
     * Get category statistics
     */
    public function getCategoryStatistics($date_from = null, $date_to = null)
    {
        $builder = $this->db->table('tbl_m_petty_category pc_cat')
            ->select('
                pc_cat.id,
                pc_cat.name,
                pc_cat.is_active,
                COUNT(pc.id) as total_entries,
                SUM(CASE WHEN pc.direction = "IN" THEN pc.amount ELSE 0 END) as total_in,
                SUM(CASE WHEN pc.direction = "OUT" THEN pc.amount ELSE 0 END) as total_out,
                AVG(CASE WHEN pc.direction = "OUT" THEN pc.amount ELSE NULL END) as avg_out_amount
            ')
            ->join('tbl_pos_petty_cash pc', 'pc.category_id = pc_cat.id', 'left')
            ->where('pc.status !=', 'void')
            ->orWhere('pc.status IS NULL');

        if ($date_from) {
            $builder->where('DATE(pc.created_at) >=', $date_from);
        }

        if ($date_to) {
            $builder->where('DATE(pc.created_at) <=', $date_to);
        }

        $builder->groupBy('pc_cat.id, pc_cat.name, pc_cat.is_active')
                ->orderBy('total_out', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Search categories
     */
    public function searchCategories($keyword)
    {
        return $this->like('name', $keyword)
                    ->where('is_active', '1')
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Get category usage by outlet
     */
    public function getCategoryUsageByOutlet($outlet_id, $date_from = null, $date_to = null)
    {
        $builder = $this->db->table('tbl_m_petty_category pc_cat')
            ->select('
                pc_cat.id,
                pc_cat.name,
                COUNT(pc.id) as usage_count,
                SUM(CASE WHEN pc.direction = "IN" THEN pc.amount ELSE 0 END) as total_in,
                SUM(CASE WHEN pc.direction = "OUT" THEN pc.amount ELSE 0 END) as total_out
            ')
            ->join('tbl_pos_petty_cash pc', 'pc.category_id = pc_cat.id', 'left')
            ->where('pc.outlet_id', $outlet_id)
            ->where('pc.status !=', 'void')
            ->orWhere('pc.status IS NULL');

        if ($date_from) {
            $builder->where('DATE(pc.created_at) >=', $date_from);
        }

        if ($date_to) {
            $builder->where('DATE(pc.created_at) <=', $date_to);
        }

        $builder->groupBy('pc_cat.id, pc_cat.name')
                ->orderBy('total_out', 'DESC');

        return $builder->get()->getResultArray();
    }
}
