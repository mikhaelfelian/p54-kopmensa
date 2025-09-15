<?php

namespace App\Models;

use CodeIgniter\Model;

class ShiftModel extends Model
{
    protected $table            = 'tbl_m_shift';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'shift_code',
        'outlet_id',
        'user_open_id',
        'user_close_id',
        'user_approve_id',
        'start_at',
        'end_at',
        'open_float',
        'sales_cash_total',
        'petty_in_total',
        'petty_out_total',
        'expected_cash',
        'counted_cash',
        'diff_cash',
        'status',
        'notes',
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
        'shift_code' => 'required|max_length[30]|is_unique[tbl_m_shift.shift_code,id,{id}]',
        'outlet_id' => 'required|integer',
        'user_open_id' => 'required|integer',
        'start_at' => 'required|valid_date',
        'open_float' => 'required|decimal',
        'status' => 'required|in_list[open,closed,approved,void]'
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
     * Get active shift for a specific outlet
     */
    public function getActiveShift($outlet_id, $user_open_id)
    {
        return $this->where('outlet_id', $outlet_id)
                    ->where('user_open_id', $user_open_id)
                    ->where('user_close_id', 0)
                    ->where('status', 'open')
                    ->first();
    }

    /**
     * Get shift with related data
     */
    public function getShiftWithDetails($shift_id)
    {
        $builder = $this->db->table('tbl_m_shift s')
            ->select('
                s.*,
                g.nama as outlet_name,
                g.kode as outlet_code,
                u_open.first_name as user_open_name,
                u_open.last_name as user_open_lastname,
                u_close.first_name as user_close_name,
                u_close.last_name as user_close_lastname,
                u_approve.first_name as user_approve_name,
                u_approve.last_name as user_approve_lastname
            ')
            ->join('tbl_m_gudang g', 'g.id = s.outlet_id', 'left')
            ->join('tbl_ion_users u_open', 'u_open.id = s.user_open_id', 'left')
            ->join('tbl_ion_users u_close', 'u_close.id = s.user_close_id', 'left')
            ->join('tbl_ion_users u_approve', 'u_approve.id = s.user_approve_id', 'left')
            ->where('s.id', $shift_id);

        return $builder->get()->getRowArray();
    }

    /**
     * Get shifts by outlet with pagination
     */
    public function getShiftsByOutlet($outlet_id, $limit = 10, $offset = 0)
    {
        $builder = $this->db->table('tbl_m_shift s')
            ->select('
                s.*,
                g.nama as outlet_name,
                u_open.first_name as user_open_name,
                u_open.last_name as user_open_lastname
            ')
            ->join('tbl_m_gudang g', 'g.id = s.outlet_id', 'left')
            ->join('tbl_ion_users u_open', 'u_open.id = s.user_open_id', 'left')
            ->where('s.outlet_id', $outlet_id)
            ->orderBy('s.start_at', 'DESC');

        return $builder->limit($limit, $offset)->get()->getResultArray();
    }

    /**
     * Get all shifts with outlet and user information
     */
    public function getAllShifts($limit = 50, $offset = 0)
    {
        $builder = $this->db->table('tbl_m_shift s')
            ->select('
                s.*,
                g.nama as outlet_name,
                g.kode as outlet_code,
                u_open.first_name as user_open_name,
                u_open.last_name as user_open_lastname,
                u_close.first_name as user_close_name,
                u_close.last_name as user_close_lastname,
                u_approve.first_name as user_approve_name,
                u_approve.last_name as user_approve_lastname
            ')
            ->join('tbl_m_gudang g', 'g.id = s.outlet_id', 'left')
            ->join('tbl_ion_users u_open', 'u_open.id = s.user_open_id', 'left')
            ->join('tbl_ion_users u_close', 'u_close.id = s.user_close_id', 'left')
            ->join('tbl_ion_users u_approve', 'u_approve.id = s.user_approve_id', 'left')
            ->orderBy('s.start_at', 'DESC');

        return $builder->limit($limit, $offset)->get()->getResultArray();
    }

    /**
     * Close shift
     */
    public function closeShift($shift_id, $user_close_id, $counted_cash, $notes = '')
    {
        $shift = $this->find($shift_id);
        if (!$shift) {
            return false;
        }

        $expected_cash = $shift['open_float'] + $shift['sales_cash_total'] + $shift['petty_in_total'] - $shift['petty_out_total'];
        $diff_cash = $counted_cash - $expected_cash;

        return $this->update($shift_id, [
            'user_close_id' => $user_close_id,
            'end_at' => date('Y-m-d H:i:s'),
            'counted_cash' => $counted_cash,
            'expected_cash' => $expected_cash,
            'diff_cash' => $diff_cash,
            'status' => 'closed',
            'notes' => $notes
        ]);
    }

    /**
     * Approve shift
     */
    public function approveShift($shift_id, $user_approve_id)
    {
        return $this->update($shift_id, [
            'user_approve_id' => $user_approve_id,
            'status' => 'approved'
        ]);
    }

    /**
     * Update petty cash totals
     */
    public function updatePettyTotals($shift_id, $petty_in_total, $petty_out_total)
    {
        return $this->update($shift_id, [
            'petty_in_total' => $petty_in_total,
            'petty_out_total' => $petty_out_total
        ]);
    }

    /**
     * Get shift summary for dashboard
     */
    public function getShiftSummary($outlet_id = null, $date = null)
    {
        $builder = $this->db->table('tbl_m_shift s')
            ->select('
                COUNT(*) as total_shifts,
                SUM(CASE WHEN s.status = "open" THEN 1 ELSE 0 END) as open_shifts,
                SUM(CASE WHEN s.status = "closed" THEN 1 ELSE 0 END) as closed_shifts,
                SUM(CASE WHEN s.status = "approved" THEN 1 ELSE 0 END) as approved_shifts,
                SUM(s.sales_cash_total) as total_sales_cash,
                SUM(s.petty_in_total) as total_petty_in,
                SUM(s.petty_out_total) as total_petty_out
            ');

        if ($outlet_id) {
            $builder->where('s.outlet_id', $outlet_id);
        }

        if ($date) {
            $builder->where('DATE(s.start_at)', $date);
        }

        return $builder->get()->getRowArray();
    }

    /**
     * Alias for getShiftSummary method
     */
    public function getSummary($outlet_id = null, $date = null)
    {
        return $this->getShiftSummary($outlet_id, $date);
    }
}
