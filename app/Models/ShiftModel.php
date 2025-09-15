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
     * Get active shift for a specific outlet and user
     */
    public function getActiveShift($outlet_id, $user_open_id = null)
    {
        $builder = $this->where('outlet_id', $outlet_id)
                        ->where('status', 'open');
        
        if ($user_open_id !== null) {
            $builder->where('user_open_id', $user_open_id)
                   ->where('user_close_id', null);
        }
        
        return $builder->first();
    }

    /**
     * Check if user already has a shift today (to prevent duplicates)
     */
    public function hasShiftToday($user_id, $outlet_id = null)
    {
        $builder = $this->where('user_open_id', $user_id)
                        ->where('user_close_id', null)
                        ->where('DATE(start_at)', date('Y-m-d'));
        
        if ($outlet_id !== null) {
            $builder->where('outlet_id', $outlet_id);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Get user's shift for today
     */
    public function getTodayShift($user_id, $outlet_id = null)
    {
        $builder = $this->where('user_open_id', $user_id)
                        ->where('DATE(start_at)', date('Y-m-d'));
        
        if ($outlet_id !== null) {
            $builder->where('outlet_id', $outlet_id);
        }
        
        return $builder->first();
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

    /**
     * Validate if user can open a new shift
     */
    public function canOpenShift($user_id, $outlet_id)
    {
        // Check if user already has a shift today
        if ($this->hasShiftToday($user_id, $outlet_id)) {
            return [
                'can_open' => false,
                'message' => 'Anda sudah memiliki shift hari ini. Satu user hanya dapat membuka satu shift per hari.'
            ];
        }

        // Check if there's already an open shift for this outlet by any user
        $existingShift = $this->getActiveShift($outlet_id);
        if ($existingShift) {
            return [
                'can_open' => false,
                'message' => 'Sudah ada shift aktif di outlet ini. Tutup shift yang ada terlebih dahulu.'
            ];
        }

        return [
            'can_open' => true,
            'message' => 'Dapat membuka shift baru.'
        ];
    }

    /**
     * Open a new shift with validation
     */
    public function openShift($data)
    {
        // Validate if user can open shift
        $validation = $this->canOpenShift($data['user_open_id'], $data['outlet_id']);
        if (!$validation['can_open']) {
            return [
                'success' => false,
                'message' => $validation['message']
            ];
        }

        // Generate shift code if not provided
        if (empty($data['shift_code'])) {
            $data['shift_code'] = $this->generateShiftCode($data['outlet_id']);
        }

        // Set default values
        $data['status'] = 'open';
        $data['start_at'] = date('Y-m-d H:i:s');
        $data['user_close_id'] = null;
        $data['end_at'] = null;

        try {
            $this->insert($data);
            return [
                'success' => true,
                'message' => 'Shift berhasil dibuka.',
                'shift_id' => $this->getInsertID()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal membuka shift: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Override insert method to prevent duplicate shifts per user per day
     */
    public function insert($data = null, bool $returnID = true)
    {
        // If user_open_id is provided, check for duplicate shifts
        if (isset($data['user_open_id']) && !empty($data['user_open_id'])) {
            $user_id = $data['user_open_id'];
            $outlet_id = $data['outlet_id'] ?? null;
            
            // Check if user already has a shift today
            if ($this->hasShiftToday($user_id, $outlet_id)) {
                throw new \Exception('User sudah memiliki shift hari ini. Satu user hanya dapat membuka satu shift per hari.');
            }
            
            // Check if there's already an open shift for this outlet
            if ($outlet_id) {
                $existingShift = $this->getActiveShift($outlet_id);
                if ($existingShift) {
                    throw new \Exception('Sudah ada shift aktif di outlet ini. Tutup shift yang ada terlebih dahulu.');
                }
            }
        }
        
        return parent::insert($data, $returnID);
    }

    /**
     * Generate unique shift code
     */
    public function generateShiftCode($outlet_id)
    {
        $date = date('Ymd');
        $prefix = 'SH' . $outlet_id . $date;
        
        // Find the last shift code for today
        $lastShift = $this->like('shift_code', $prefix, 'after')
                         ->where('DATE(start_at)', date('Y-m-d'))
                         ->orderBy('shift_code', 'DESC')
                         ->first();

        if ($lastShift) {
            $lastNumber = (int)substr($lastShift['shift_code'], -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
