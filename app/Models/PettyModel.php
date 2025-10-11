<?php

namespace App\Models;

use CodeIgniter\Model;

class PettyModel extends Model
{
    protected $table            = 'tbl_pos_petty_cash';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'shift_id', 'outlet_id', 'kasir_user_id', 'category_id', 'direction', 
        'amount', 'reason', 'ref_no', 'attachment_path', 'status', 'approved_by', 'approved_at',
        'created_at', 'updated_at', 'deleted_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'shift_id'       => 'required|integer',
        'outlet_id'      => 'required|integer',
        'kasir_user_id'  => 'required|integer',
        'category_id'    => 'permit_empty|integer',
        'direction'      => 'required|in_list[IN,OUT]',
        'amount'         => 'required|numeric|greater_than[0]',
        'reason'         => 'required|min_length[10]',
        'status'         => 'required|in_list[draft,posted,void]'
    ];

    protected $validationMessages = [
        'shift_id' => [
            'required' => 'Shift ID harus diisi',
            'integer' => 'Shift ID harus berupa angka'
        ],
        'outlet_id' => [
            'required' => 'Outlet harus dipilih',
            'integer' => 'Outlet ID harus berupa angka'
        ],
        'kasir_user_id' => [
            'required' => 'Kasir harus dipilih',
            'integer' => 'Kasir ID harus berupa angka'
        ],
        'category_id' => [
            'integer' => 'Kategori ID harus berupa angka'
        ],
        'direction' => [
            'required' => 'Arah transaksi harus dipilih',
            'in_list' => 'Arah transaksi harus IN atau OUT'
        ],
        'amount' => [
            'required' => 'Jumlah harus diisi',
            'numeric' => 'Jumlah harus berupa angka',
            'greater_than' => 'Jumlah harus lebih dari 0'
        ],
        'reason' => [
            'required' => 'Alasan harus diisi',
            'min_length' => 'Alasan minimal 10 karakter'
        ],
        'status' => [
            'required' => 'Status harus diisi',
            'in_list' => 'Status tidak valid'
        ]
    ];

    /**
     * Get petty cash with related data
     */
    public function getPettyCashWithDetails($filters = [], $limit = null, $offset = 0)
    {

        
        $builder = $this->select('
            tbl_pos_petty_cash.*,
            tbl_ion_users.first_name as user_name,
            tbl_ion_users.last_name as user_lastname,
            tbl_m_gudang.nama as outlet_name,
            tbl_m_petty_category.nama as kategori_nama,
            approver.first_name as approver_name,
            approver.last_name as approver_lastname
        ')
        ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_pos_petty_cash.kasir_user_id', 'left')
        ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_pos_petty_cash.outlet_id', 'left')
        ->join('tbl_m_petty_category', 'tbl_m_petty_category.id = tbl_pos_petty_cash.category_id', 'left')
        ->join('tbl_ion_users approver', 'approver.id = tbl_pos_petty_cash.approved_by', 'left');

        // Apply filters
        if (!empty($filters['outlet_id'])) {
            $builder->where('tbl_pos_petty_cash.outlet_id', $filters['outlet_id']);
        }

        if (!empty($filters['shift_id'])) {
            // Check if table and shift_id column exist before using it
            if ($this->isTableReady()) {
                try {
                    $builder->where('tbl_pos_petty_cash.shift_id', $filters['shift_id']);
                } catch (\Exception $e) {
                    log_message('error', 'PettyModel: Database error in getPettyCashWithDetails shift_id filter: ' . $e->getMessage());
                }
            }
        }

        if (!empty($filters['user_id'])) {
            $builder->where('tbl_pos_petty_cash.kasir_user_id', $filters['user_id']);
        }

        if (!empty($filters['status'])) {
            $builder->where('tbl_pos_petty_cash.status', $filters['status']);
        }

        if (!empty($filters['direction'])) {
            $builder->where('tbl_pos_petty_cash.direction', $filters['direction']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('tbl_pos_petty_cash.created_at >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('tbl_pos_petty_cash.created_at <=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('tbl_pos_petty_cash.reason', $filters['search'])
                ->orLike('tbl_m_petty_category.nama', $filters['search'])
                ->orLike('tbl_ion_users.first_name', $filters['search'])
                ->orLike('tbl_ion_users.last_name', $filters['search'])
                ->groupEnd();
        }

        $builder->orderBy('tbl_pos_petty_cash.created_at', 'DESC');

        if ($limit) {
            $builder->limit($limit, $offset);
        }



        return $builder->get()->getResult();
    }

    /**
     * Get petty cash summary by outlet
     */
    public function getSummaryByOutlet($outletId = null, $dateFrom = null, $dateTo = null)
    {
        $builder = $this->select('
            outlet_id,
            direction,
            COUNT(*) as total_transactions,
            SUM(amount) as total_amount
        ');

        if ($outletId) {
            $builder->where('outlet_id', $outletId);
        }

        if ($dateFrom) {
            $builder->where('created_at >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('created_at <=', $dateTo);
        }

        $builder->where('status', 'posted')
                ->groupBy(['outlet_id', 'direction']);

        return $builder->get()->getResult();
    }

    /**
     * Get petty cash summary by category
     */
    public function getSummaryByCategory($outletId = null, $dateFrom = null, $dateTo = null)
    {
        $builder = $this->select('
            tbl_m_petty_category.nama as kategori_nama,
            COUNT(*) as total_transactions,
            SUM(tbl_pos_petty_cash.amount) as total_amount
        ')
        ->join('tbl_m_petty_category', 'tbl_m_petty_category.id = tbl_pos_petty_cash.category_id', 'left');

        if ($outletId) {
            $builder->where('tbl_pos_petty_cash.outlet_id', $outletId);
        }

        if ($dateFrom) {
            $builder->where('tbl_pos_petty_cash.created_at >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('tbl_pos_petty_cash.created_at <=', $dateTo);
        }

        $builder->where('tbl_pos_petty_cash.status', 'posted')
                ->groupBy(['tbl_m_petty_category.id', 'tbl_m_petty_category.nama'])
                ->orderBy('total_amount', 'DESC');

        return $builder->get()->getResult();
    }

    /**
     * Get pending approvals
     */
    public function getPendingApprovals($outletId = null)
    {
        $builder = $this->select('
            tbl_pos_petty_cash.*,
            tbl_ion_users.first_name as user_name,
            tbl_ion_users.last_name as user_lastname,
            tbl_m_gudang.nama as outlet_name,
            tbl_m_petty_category.nama as kategori_nama
        ')
        ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_pos_petty_cash.kasir_user_id', 'left')
        ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_pos_petty_cash.outlet_id', 'left')
        ->join('tbl_m_petty_category', 'tbl_m_petty_category.id = tbl_pos_petty_cash.category_id', 'left')
        ->where('tbl_pos_petty_cash.status', 'draft');

        if ($outletId) {
            $builder->where('tbl_pos_petty_cash.outlet_id', $outletId);
        }

        return $builder->orderBy('tbl_pos_petty_cash.created_at', 'ASC')
                      ->get()
                      ->getResult();
    }

    /**
     * Approve petty cash
     */
    public function approvePettyCash($id, $approverId, $notes = null)
    {
        return $this->update($id, [
            'status' => 'posted',
            'approved_by' => $approverId,
            'approved_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Reject petty cash
     */
    public function rejectPettyCash($id, $rejectorId, $reason)
    {
        return $this->update($id, [
            'status' => 'void',
            'approved_by' => $rejectorId,
            'approved_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Void petty cash
     */
    public function voidPettyCash($id, $userId, $reason)
    {
        return $this->update($id, [
            'status' => 'void',
            'approved_by' => $userId,
            'approved_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get total petty cash by outlet and date range
     */
    public function getTotalByOutletAndDate($outletId, $dateFrom, $dateTo)
    {
        $result = $this->select('
            SUM(CASE WHEN direction = "IN" THEN amount ELSE 0 END) as total_masuk,
            SUM(CASE WHEN direction = "OUT" THEN amount ELSE 0 END) as total_keluar,
            COUNT(CASE WHEN direction = "IN" THEN 1 END) as count_masuk,
            COUNT(CASE WHEN direction = "OUT" THEN 1 END) as count_keluar
        ')
        ->where('outlet_id', $outletId)
        ->where('created_at >=', $dateFrom)
        ->where('created_at <=', $dateTo)
        ->where('status', 'posted')
        ->get()
        ->getRow();

        return [
            'total_masuk' => $result->total_masuk ?? 0,
            'total_keluar' => $result->total_keluar ?? 0,
            'count_masuk' => $result->count_masuk ?? 0,
            'count_keluar' => $result->count_keluar ?? 0,
            'net_amount' => ($result->total_masuk ?? 0) - ($result->total_keluar ?? 0)
        ];
    }

    /**
     * Get total records for pagination
     */
    public function getTotalRecords($filters = [])
    {

        
        $builder = $this->select('COUNT(*) as total')
                        ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_pos_petty_cash.kasir_user_id', 'left')
                        ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_pos_petty_cash.outlet_id', 'left')
                        ->join('tbl_m_petty_category', 'tbl_m_petty_category.id = tbl_pos_petty_cash.category_id', 'left');

        // Apply filters
        if (!empty($filters['outlet_id'])) {
            $builder->where('tbl_pos_petty_cash.outlet_id', $filters['outlet_id']);
        }

        if (!empty($filters['user_id'])) {
            $builder->where('tbl_pos_petty_cash.kasir_user_id', $filters['user_id']);
        }

        if (!empty($filters['status'])) {
            $builder->where('tbl_pos_petty_cash.status', $filters['status']);
        }

        if (!empty($filters['direction'])) {
            $builder->where('tbl_pos_petty_cash.direction', $filters['direction']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('tbl_pos_petty_cash.created_at >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('tbl_pos_petty_cash.created_at <=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('tbl_pos_petty_cash.reason', $filters['search'])
                ->orLike('tbl_m_petty_category.nama', $filters['search'])
                ->orLike('tbl_ion_users.first_name', $filters['search'])
                ->orLike('tbl_ion_users.last_name', $filters['search'])
                ->groupEnd();
        }


        
        $result = $builder->get()->getRow();
        
        return $result ? $result->total : 0;
    }

    /**
     * Check if petty cash table is properly set up
     */
    public function isTableReady()
    {
        try {
            $tableExists = $this->db->tableExists($this->table);
            $fieldExists = $this->db->fieldExists('shift_id', $this->table);
            
            log_message('debug', 'PettyModel: Table exists: ' . ($tableExists ? 'yes' : 'no'));
            log_message('debug', 'PettyModel: shift_id field exists: ' . ($fieldExists ? 'yes' : 'no'));
            
            return $tableExists && $fieldExists;
        } catch (\Exception $e) {
            log_message('error', 'PettyModel: Error checking table readiness: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get petty cash summary by shift
     */
    public function getPettyCashSummaryByShift($shiftId)
    {
        // Check if table and shift_id column exist
        if (!$this->isTableReady()) {
            log_message('debug', 'PettyModel: Table not ready, returning empty summary');
            // Return empty summary if table/column doesn't exist
            return [
                'total_in' => 0,
                'total_out' => 0,
                'count_in' => 0,
                'count_out' => 0
            ];
        }
        
        log_message('debug', 'PettyModel: Getting summary for shift_id: ' . $shiftId);
        
        try {
            $result = $this->select('
                SUM(CASE WHEN direction = "IN" THEN amount ELSE 0 END) as total_in,
                SUM(CASE WHEN direction = "OUT" THEN amount ELSE 0 END) as total_out,
                COUNT(CASE WHEN direction = "IN" THEN 1 END) as count_in,
                COUNT(CASE WHEN direction = "OUT" THEN 1 END) as count_out
            ')
            ->where('shift_id', $shiftId)
            ->where('status', 'posted')
            ->get()
            ->getRow();
        } catch (\Exception $e) {
            log_message('error', 'PettyModel: Database error in getPettyCashSummaryByShift: ' . $e->getMessage());
            return [
                'total_in' => 0,
                'total_out' => 0,
                'count_in' => 0,
                'count_out' => 0
            ];
        }

        return [
            'total_in' => $result->total_in ?? 0,
            'total_out' => $result->total_out ?? 0,
            'count_in' => $result->count_in ?? 0,
            'count_out' => $result->count_out ?? 0,
            'net_amount' => ($result->total_in ?? 0) - ($result->total_out ?? 0)
        ];
    }

    /**
     * Get petty cash summary by outlet (for summary page)
     */
    public function getPettyCashSummaryByOutlet($outletId, $dateFrom, $dateTo)
    {
        $result = $this->select('
            SUM(CASE WHEN direction = "IN" THEN amount ELSE 0 END) as total_in,
            SUM(CASE WHEN direction = "OUT" THEN amount ELSE 0 END) as total_out,
            COUNT(CASE WHEN direction = "IN" THEN 1 END) as count_in,
            COUNT(CASE WHEN direction = "OUT" THEN 1 END) as count_out
        ')
        ->where('outlet_id', $outletId)
        ->where('created_at >=', $dateFrom)
        ->where('created_at <=', $dateTo)
        ->where('status', 'posted')
        ->get()
        ->getRow();

        return [
            'total_in' => $result->total_in ?? 0,
            'total_out' => $result->total_out ?? 0,
            'count_in' => $result->count_in ?? 0,
            'count_out' => $result->count_out ?? 0,
            'net_amount' => ($result->total_in ?? 0) - ($result->total_out ?? 0)
        ];
    }

    /**
     * Get petty cash by category (for category report)
     */
    public function getPettyCashByCategory($outletId, $dateFrom, $dateTo)
    {
        $builder = $this->select('
            tbl_m_petty_category.nama as category_name,
            COUNT(*) as total_transactions,
            SUM(CASE WHEN direction = "IN" THEN amount ELSE 0 END) as total_in,
            SUM(CASE WHEN direction = "OUT" THEN amount ELSE 0 END) as total_out
        ')
        ->join('tbl_m_petty_category', 'tbl_m_petty_category.id = tbl_pos_petty_cash.category_id', 'left')
        ->where('tbl_pos_petty_cash.outlet_id', $outletId)
        ->where('tbl_pos_petty_cash.created_at >=', $dateFrom)
        ->where('tbl_pos_petty_cash.created_at <=', $dateTo)
        ->where('tbl_pos_petty_cash.status', 'posted')
        ->groupBy(['tbl_m_petty_category.id', 'tbl_m_petty_category.nama'])
        ->orderBy('total_transactions', 'DESC');

        return $builder->get()->getResult();
    }
}
