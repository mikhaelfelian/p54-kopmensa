<?php

namespace App\Models;

use CodeIgniter\Model;

class PettyModel extends Model
{
    protected $table            = 'tbl_pos_petty_cash';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'shift_id',
        'outlet_id',
        'kasir_user_id',
        'category_id',
        'direction',
        'amount',
        'reason',
        'ref_no',
        'attachment_path',
        'status',
        'approved_by',
        'approved_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'shift_id' => 'required|integer',
        'outlet_id' => 'required|integer',
        'kasir_user_id' => 'required|integer',
        'direction' => 'required|in_list[IN,OUT]',
        'amount' => 'required|decimal',
        'status' => 'required|in_list[draft,posted,void]'
    ];
    protected $validationMessages   = [];
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
     * Get petty cash entries with related data
     */
    public function getPettyCashWithDetails($id = null, $filters = [])
    {
        $builder = $this->db->table('tbl_pos_petty_cash pc')
            ->select('
                pc.*,
                s.shift_code,
                g.nama as outlet_name,
                g.kode as outlet_code,
                u_kasir.first_name as kasir_first_name,
                u_kasir.last_name as kasir_last_name,
                pc_cat.name as category_name,
                u_approve.first_name as approver_first_name,
                u_approve.last_name as approver_last_name
            ')
            ->join('tbl_m_shift s', 's.id = pc.shift_id', 'left')
            ->join('tbl_m_gudang g', 'g.id = pc.outlet_id', 'left')
            ->join('tbl_ion_users u_kasir', 'u_kasir.id = pc.kasir_user_id', 'left')
            ->join('tbl_m_petty_category pc_cat', 'pc_cat.id = pc.category_id', 'left')
            ->join('tbl_ion_users u_approve', 'u_approve.id = pc.approved_by', 'left');

        if ($id) {
            $builder->where('pc.id', $id);
            return $builder->get()->getRowArray();
        }

        // Apply filters
        if (!empty($filters['outlet_id'])) {
            $builder->where('pc.outlet_id', $filters['outlet_id']);
        }

        if (!empty($filters['shift_id'])) {
            $builder->where('pc.shift_id', $filters['shift_id']);
        }

        if (!empty($filters['direction'])) {
            $builder->where('pc.direction', $filters['direction']);
        }

        if (!empty($filters['status'])) {
            $builder->where('pc.status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('DATE(pc.created_at) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('DATE(pc.created_at) <=', $filters['date_to']);
        }

        $builder->orderBy('pc.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get petty cash entries by shift
     */
    public function getPettyCashByShift($shift_id)
    {
        return $this->getPettyCashWithDetails(null, ['shift_id' => $shift_id]);
    }

    /**
     * Get petty cash entries by outlet
     */
    public function getPettyCashByOutlet($outlet_id, $date_from = null, $date_to = null)
    {
        $filters = ['outlet_id' => $outlet_id];
        
        if ($date_from) {
            $filters['date_from'] = $date_from;
        }
        
        if ($date_to) {
            $filters['date_to'] = $date_to;
        }

        return $this->getPettyCashWithDetails(null, $filters);
    }

    /**
     * Get petty cash summary by shift
     */
    public function getPettyCashSummaryByShift($shift_id)
    {
        $builder = $this->db->table('tbl_pos_petty_cash pc')
            ->select('
                SUM(CASE WHEN pc.direction = "IN" THEN pc.amount ELSE 0 END) as total_in,
                SUM(CASE WHEN pc.direction = "OUT" THEN pc.amount ELSE 0 END) as total_out,
                COUNT(CASE WHEN pc.direction = "IN" THEN 1 END) as count_in,
                COUNT(CASE WHEN pc.direction = "OUT" THEN 1 END) as count_out,
                COUNT(*) as total_entries
            ')
            ->where('pc.shift_id', $shift_id)
            ->where('pc.status !=', 'void');

        return $builder->get()->getRowArray();
    }

    /**
     * Get petty cash summary by outlet and date range
     */
    public function getPettyCashSummaryByOutlet($outlet_id, $date_from, $date_to)
    {
        $builder = $this->db->table('tbl_pos_petty_cash pc')
            ->select('
                SUM(CASE WHEN pc.direction = "IN" THEN pc.amount ELSE 0 END) as total_in,
                SUM(CASE WHEN pc.direction = "OUT" THEN pc.amount ELSE 0 END) as total_out,
                COUNT(CASE WHEN pc.direction = "IN" THEN 1 END) as count_in,
                COUNT(CASE WHEN pc.direction = "OUT" THEN 1 END) as count_out,
                COUNT(*) as total_entries
            ')
            ->where('pc.outlet_id', $outlet_id)
            ->where('pc.status !=', 'void')
            ->where('DATE(pc.created_at) >=', $date_from)
            ->where('DATE(pc.created_at) <=', $date_to);

        return $builder->get()->getRowArray();
    }

    /**
     * Approve petty cash entry
     */
    public function approvePettyCash($id, $approved_by)
    {
        return $this->update($id, [
            'approved_by' => $approved_by,
            'approved_at' => date('Y-m-d H:i:s'),
            'status' => 'posted'
        ]);
    }

    /**
     * Void petty cash entry
     */
    public function voidPettyCash($id, $reason = '')
    {
        return $this->update($id, [
            'status' => 'void',
            'reason' => $reason
        ]);
    }

    /**
     * Get pending approvals
     */
    public function getPendingApprovals($outlet_id = null)
    {
        $builder = $this->where('status', 'draft');
        
        if ($outlet_id) {
            $builder->where('outlet_id', $outlet_id);
        }

        return $builder->findAll();
    }

    /**
     * Get petty cash by category
     */
    public function getPettyCashByCategory($outlet_id, $date_from, $date_to)
    {
        $builder = $this->db->table('tbl_pos_petty_cash pc')
            ->select('
                pc.category_id,
                pc_cat.name as category_name,
                SUM(CASE WHEN pc.direction = "IN" THEN pc.amount ELSE 0 END) as total_in,
                SUM(CASE WHEN pc.direction = "OUT" THEN pc.amount ELSE 0 END) as total_out,
                COUNT(*) as total_entries
            ')
            ->join('tbl_m_petty_category pc_cat', 'pc_cat.id = pc.category_id', 'left')
            ->where('pc.outlet_id', $outlet_id)
            ->where('pc.status !=', 'void')
            ->where('DATE(pc.created_at) >=', $date_from)
            ->where('DATE(pc.created_at) <=', $date_to)
            ->groupBy('pc.category_id, pc_cat.name')
            ->orderBy('total_out', 'DESC');

        return $builder->get()->getResultArray();
    }
}
