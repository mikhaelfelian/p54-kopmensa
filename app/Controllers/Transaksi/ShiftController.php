<?php

namespace App\Controllers\Transaksi;

use App\Controllers\BaseController;
use App\Models\ShiftModel;
use App\Models\GudangModel;
use App\Models\PettyModel;
use App\Models\TransJualModel;

class ShiftController extends BaseController
{
    protected $shiftModel;
    protected $gudangModel;
    protected $pettyModel;
    protected $transJualModel;
    protected $ionAuth;

    public function __construct()
    {
        $this->shiftModel = new ShiftModel();
        $this->gudangModel = new GudangModel();
        $this->pettyModel = new PettyModel();
        $this->transJualModel = new TransJualModel();
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
    }

    public function index()
    {
        $outlet_id = session()->get('outlet_id');
        
        if ($outlet_id) {
            // If user has outlet_id in session, show shifts for that outlet
            $shifts = $this->shiftModel->getShiftsByOutlet($outlet_id, 50, 0);
        } else {
            // If no outlet_id in session, show all shifts
            $shifts = $this->shiftModel->getAllShifts(50, 0);
        }
        
        // Process shifts to ensure proper user data display
        $processedShifts = [];
        foreach ($shifts as $shift) {
            // Ensure user names are properly displayed
            $shift['user_open_name'] = $shift['user_open_name'] ?? 'Unknown';
            $shift['user_open_lastname'] = $shift['user_open_lastname'] ?? '';
            $shift['user_close_name'] = $shift['user_close_name'] ?? '';
            $shift['user_close_lastname'] = $shift['user_close_lastname'] ?? '';
            $shift['user_approve_name'] = $shift['user_approve_name'] ?? '';
            $shift['user_approve_lastname'] = $shift['user_approve_lastname'] ?? '';
            
            // If user_open_name is still empty or null, try to get from IonAuth
            if (empty($shift['user_open_name']) || $shift['user_open_name'] === 'Unknown') {
                try {
                    // Try to get user data directly from database
                    $db = \Config\Database::connect();
                    $userQuery = $db->table('tbl_ion_users')
                        ->select('first_name, last_name, username, email')
                        ->where('id', $shift['user_open_id'])
                        ->get();
                    
                    if ($userQuery->getNumRows() > 0) {
                        $user = $userQuery->getRow();
                        $shift['user_open_name'] = $user->first_name ?? $user->username ?? 'User';
                        $shift['user_open_lastname'] = $user->last_name ?? '';
                    } else {
                        // Fallback to IonAuth method
                        $user = $this->ionAuth->user($shift['user_open_id'])->row();
                        if ($user) {
                            $shift['user_open_name'] = $user->first_name ?? 'User';
                            $shift['user_open_lastname'] = $user->last_name ?? '';
                        } else {
                            $shift['user_open_name'] = 'User ID: ' . $shift['user_open_id'];
                        }
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Error getting user data: ' . $e->getMessage());
                    $shift['user_open_name'] = 'User ID: ' . $shift['user_open_id'];
                }
            }
            
            $processedShifts[] = $shift;
        }
        
        $data = array_merge($this->data, [
            'title' => 'Shift Management',
            'shifts' => $processedShifts,
            'current_outlet_id' => $outlet_id
        ]);
        
        return view('admin-lte-3/shift/index', $data);
    }

    /**
     * Show the form to open a new shift (GET)
     */
    public function showOpenForm()
    {
        log_message('debug', 'Shift showOpenForm - GET request, showing form');
        
        $user_id = $this->ionAuth->user()->row()->id;
        $existingShifts = [];
        
        // Check if user has any shifts for today
        $today = date('Y-m-d');
        $todayShifts = $this->shiftModel
            ->where('user_open_id', $user_id)
            ->where('DATE(start_at)', $today)
            ->findAll();
            
        if (!empty($todayShifts)) {
            $existingShifts = $todayShifts;
        }
        
        $data = array_merge($this->data, [
            'title' => 'Buka Shift Baru',
            'outlets' => $this->gudangModel->getOutletsForDropdown(),
            'existingShifts' => $existingShifts
        ]);
        
        return view('admin-lte-3/shift/open', $data);
    }

    /**
     * Store the new shift (POST)
     */
    public function storeShift()
    {
        $outlet_id  = $this->request->getPost('outlet_id');
        $open_float = $this->request->getPost('open_float');

        // Clean the open_float value - remove any formatting and convert to decimal
        if (is_string($open_float)) {
            $open_float = str_replace('.', '', $open_float); // Remove thousands separator
            $open_float = str_replace(',', '.', $open_float); // Replace decimal comma with dot
            $open_float = floatval($open_float);
        }

        $rules = [
            'outlet_id' => 'required|integer',
            'open_float' => 'required|numeric'
        ];

        if ($this->validate($rules)) {
            $user = $this->ionAuth->user()->row();
            $user_id = $user ? $user->id : null;
            
            // Check if user already has a shift for today at this outlet
            $existingShift = $this->getUserShiftForToday($user_id, $outlet_id);
            
            if ($existingShift) {
                // Check if the existing shift is open
                $shift_status = is_array($existingShift) ? $existingShift['status'] : $existingShift->status;
                
                if ($shift_status === 'open') {
                    // User already has an open shift - recreate session instead of creating duplicate
                    $this->recreateSessionForShift($existingShift);
                    $shift_code = is_array($existingShift) ? $existingShift['shift_code'] : $existingShift->shift_code;
                    session()->setFlashdata('success', 'Session berhasil dipulihkan untuk shift yang sudah terbuka: ' . $shift_code);
                    return redirect()->to('/transaksi/jual/cashier');
                } else {
                    // User already has a closed shift for today - prevent creating new one
                    session()->setFlashdata('error', 'Anda sudah memiliki shift untuk outlet ini hari ini. Hanya satu shift per hari per outlet yang diizinkan.');
                    return redirect()->back()->withInput();
                }
            }
            
            // Generate shift code
            $shift_code = $this->generateShiftCode($outlet_id);

            $data = [
                'shift_code'        => $shift_code,
                'outlet_id'         => $outlet_id,
                'user_open_id'      => $user_id,
                'start_at'          => date('Y-m-d H:i:s'),
                'open_float'        => $open_float,
                'sales_cash_total'  => 0.00,
                'petty_in_total'    => 0.00,
                'petty_out_total'   => 0.00,
                'expected_cash'     => $open_float,
                'status'            => 'open'
            ];

            try {
                if ($this->shiftModel->insert($data)) {
                    // Set session kasir_shift with last insert id before redirect
                    $lastInsertId = $this->shiftModel->getInsertID();
                    session()->set('kasir_shift', $lastInsertId);
                    session()->set('kasir_outlet', $outlet_id);

                    if (session()->has('kasir_outlet')) {
                        session()->setFlashdata('success', 'Shift berhasil dibuka');
                        return redirect()->to('/transaksi/jual/cashier');
                    }
                    return redirect()->to('/transaksi/shift');
                } else {
                    // Debug: Log any database errors
                    $db_error = $this->shiftModel->db->error();
                    session()->setFlashdata('error', 'Gagal membuka shift: ' . ($db_error['message'] ?? 'Unknown error'));
                }
            } catch (\Exception $e) {
                // Catch the exception and show toastr message instead of exception page
                session()->setFlashdata('error', $e->getMessage());
                return redirect()->back()->withInput();
            }
        } else {
            // Debug: Log validation errors
            $validation_errors = $this->validator->getErrors();
            session()->setFlashdata('error', 'Validasi gagal: ' . implode(', ', $validation_errors));
        }

        // If we get here, there was an error, redirect back to form with data
        return redirect()->back()->withInput();
    }

    /**
     * Show the form to close a shift (GET)
     */
    public function closeShift($shift_id)
    {
        $shift = $this->shiftModel->getShiftWithDetails($shift_id);
        if (!$shift) {
            session()->setFlashdata('error', 'Shift tidak ditemukan');
            return redirect()->to('/transaksi/shift');
        }

        // Get petty cash summary (TEMPORARILY DISABLED TO FIX ERROR)
        $pettySummary = [
            'total_in' => 0,
            'total_out' => 0,
            'count_in' => 0,
            'count_out' => 0
        ];
        // TODO: Re-enable after PettyModel is fixed
        /*
        if ($this->isPettyCashAvailable()) {
            try {
                $pettySummary = $this->pettyModel->getPettyCashSummaryByShift($shift_id);
            } catch (\Exception $e) {
                log_message('error', 'Petty cash summary error: ' . $e->getMessage());
                $pettySummary = [
                    'total_in' => 0,
                    'total_out' => 0,
                    'count_in' => 0,
                    'count_out' => 0
                ];
            }
        } else {
            // Petty cash not available, use default values
            $pettySummary = [
                'total_in' => 0,
                'total_out' => 0,
                'count_in' => 0,
                'count_out' => 0
            ];
        }
        */
        
        // Get sales summary
        $salesSummary = [];
        try {
            $salesSummary = $this->transJualModel->getSalesSummaryByShift($shift_id);
        } catch (\Exception $e) {
            log_message('error', 'Sales summary error: ' . $e->getMessage());
            $salesSummary = [
                'total_transactions' => 0,
                'total_cash_sales' => 0,
                'total_non_cash_sales' => 0,
                'total_sales' => 0
            ];
        }

        $data = array_merge($this->data, [
            'title' => 'Tutup Shift',
            'shift' => $shift,
            'pettySummary' => $pettySummary,
            'salesSummary' => $salesSummary
        ]);
        
        return view('admin-lte-3/shift/close', $data);
    }

    /**
     * Process the shift closing (POST)
     */
    public function processClose()
    {
        $shift_id = $this->request->getPost('shift_id');
        $counted_cash = $this->request->getPost('counted_cash');
        $notes = $this->request->getPost('notes');

        // Clean the counted_cash value - remove any formatting and convert to decimal
        if (is_string($counted_cash)) {
            $counted_cash = format_angka_db($counted_cash);
        }

        $rules = [
            'shift_id' => 'required|integer',
            'counted_cash' => 'required|numeric',
            'notes' => 'permit_empty|max_length[500]'
        ];

        if ($this->validate($rules)) {
            $user_close_id = $this->ionAuth->user()->row()->id;

            if ($this->shiftModel->closeShift($shift_id, $user_close_id, $counted_cash, $notes)) {
                session()->setFlashdata('success', 'Shift berhasil ditutup');
                return redirect()->to('/transaksi/shift');
            } else {
                session()->setFlashdata('error', 'Gagal menutup shift');
            }
        } else {
            session()->setFlashdata('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
        }

        // If we get here, there was an error, redirect back to form with data
        return redirect()->back()->withInput();
    }

    public function approveShift($shift_id)
    {
        $user_approve_id = $this->ionAuth->user()->row()->id;
        
        if ($this->shiftModel->approveShift($shift_id, $user_approve_id)) {
            session()->setFlashdata('success', 'Shift berhasil disetujui');
        } else {
            session()->setFlashdata('error', 'Gagal menyetujui shift');
        }
        
        return redirect()->to('/transaksi/shift');
    }

    public function viewShift($shift_id)
    {
        // Check if required database tables exist
        $missingTables = $this->checkDatabaseTables();
        if (!empty($missingTables)) {
            session()->setFlashdata('error', 'Database tables missing: ' . implode(', ', $missingTables) . '. Please run database migrations.');
            return redirect()->to('/transaksi/shift');
        }
        
        $shift = $shift_id ?? null;
        $shift = $this->shiftModel->getShiftWithDetails($shift_id = null);
        if (!$shift) {
            session()->setFlashdata('error', 'Shift tidak ditemukan');
            return redirect()->to('/transaksi/shift');
        }

        // Get transaction statistics
        $transactionStats = [];
        try {
            $transactionStats = $this->shiftModel->getShiftTransactionStats($shift_id ?? null);
        } catch (\Exception $e) {
            $transactionStats = [
                'transaction_count' => 0,
                'total_sales' => 0,
                'total_payment' => 0,
                'cash' => 0,
                'card' => 0,
                'qris' => 0,
                'other' => 0
            ];
        }
        
        // Get recent transactions
        $recentTransactions = [];
        try {
            $recentTransactions = $this->shiftModel->getShiftRecentTransactions($shift_id, 10);
        } catch (\Exception $e) {
            log_message('error', 'Recent transactions error: ' . $e->getMessage());
            $recentTransactions = [];
        }
        
        // Get petty cash entries (TEMPORARILY DISABLED TO FIX ERROR)
        $pettyEntries = [];
        // TODO: Re-enable after PettyModel is fixed
        /*
        if ($this->isPettyCashAvailable() && method_exists($this->pettyModel, 'getPettyCashWithDetails')) {
            try {
                $pettyEntries = $this->pettyModel->getPettyCashWithDetails(['shift_id' => $shift_id]);
            } catch (\Exception $e) {
                log_message('error', 'Petty cash entries error: ' . $e->getMessage());
                $pettyEntries = [];
            }
        }
        */
        
        // Get sales entries (if method exists)
        $salesEntries = [];
        if (method_exists($this->transJualModel, 'getSalesByShift')) {
            try {
                $salesEntries = $this->transJualModel->getSalesByShift($shift_id);
            } catch (\Exception $e) {
                log_message('error', 'Sales entries error: ' . $e->getMessage());
                $salesEntries = [];
            }
        }

        $data = array_merge($this->data, [
            'title'              => 'Detail Shift',
            'shift'              => $shift,
            'transactionCount'   => $transactionStats['transaction_count'] ?? 0,
            'totalSales'         => $transactionStats['total_sales'] ?? 0,
            'totalPayment'       => $transactionStats['total_payment'] ?? 0,
            'cashTransactions'   => $transactionStats['cash'] ?? 0,
            'cardTransactions'   => $transactionStats['card'] ?? 0,
            'qrisTransactions'   => $transactionStats['qris'] ?? 0,
            'otherTransactions'  => $transactionStats['other'] ?? 0,
            'recentTransactions' => $recentTransactions,
            'pettyEntries'       => $pettyEntries,
            'salesEntries'       => $salesEntries,
        ]);
        
        return view('admin-lte-3/shift/view', $data);
    }

    public function checkShiftStatus()
    {
        $outlet_id = session()->get('outlet_id');
        $activeShift = $this->shiftModel->getActiveShift($outlet_id);
        
        return $this->response->setJSON([
            'has_active_shift' => !empty($activeShift),
            'shift' => $activeShift
        ]);
    }

    public function getShiftSummary()
    {
        $outlet_id = session()->get('outlet_id');
        $date = $this->request->getGet('date') ?? date('Y-m-d');
        
        $summary = $this->shiftModel->getShiftSummary($outlet_id, $date);
        
        return $this->response->setJSON($summary);
    }

    /**
     * Check if user has an existing open shift
     */
    private function getUserOpenShift($user_id, $outlet_id)
    {
        return $this->shiftModel
            ->where('user_open_id', $user_id)
            ->where('outlet_id', $outlet_id)
            ->where('status', 'open')
            ->first();
    }

    /**
     * Check if user has any shift for the same day and outlet (regardless of status)
     */
    private function getUserShiftForToday($user_id, $outlet_id)
    {
        $today = date('Y-m-d');
        return $this->shiftModel
            ->where('user_open_id', $user_id)
            ->where('outlet_id', $outlet_id)
            ->where('DATE(start_at)', $today)
            ->first();
    }

    /**
     * Check if required database tables exist
     */
    private function checkDatabaseTables()
    {
        $tables = ['tbl_m_shift', 'tbl_pos_petty_cash', 'tbl_trans_jual'];
        $missingTables = [];
        
        try {
            $db = \Config\Database::connect();
            foreach ($tables as $table) {
                if (!$db->tableExists($table)) {
                    $missingTables[] = $table;
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Database table check failed: ' . $e->getMessage());
            $missingTables = $tables; // Assume all tables are missing if we can't check
        }
        
        return $missingTables;
    }

    /**
     * Check if petty cash functionality is available
     */
    private function isPettyCashAvailable()
    {
        try {
            return $this->pettyModel->isTableReady();
        } catch (\Exception $e) {
            log_message('error', 'Petty cash availability check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Recreate session for existing shift
     */
    private function recreateSessionForShift($shift)
    {
        // Handle both array and object formats
        $shift_id = is_array($shift) ? $shift['id'] : $shift->id;
        $outlet_id = is_array($shift) ? $shift['outlet_id'] : $shift->outlet_id;
        
        session()->set('kasir_shift', $shift_id);
        session()->set('kasir_outlet', $outlet_id);
        session()->set('outlet_id', $outlet_id);
        
        return true;
    }

    /**
     * Recover session for existing shift (public method for AJAX calls)
     */
    public function recoverSession()
    {
        $shift_id = $this->request->getPost('shift_id');
        
        if (!$shift_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Shift ID harus diisi'
            ]);
        }
        
        $shift = $this->shiftModel->find($shift_id);
        
        if (!$shift || $shift->status !== 'open') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Shift tidak ditemukan atau sudah ditutup'
            ]);
        }
        
        // Check if shift belongs to current user
        $user_id = $this->ionAuth->user()->row()->id;
        $shift_user_id = is_array($shift) ? $shift['user_open_id'] : $shift->user_open_id;
        if ($shift_user_id !== $user_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke shift ini'
            ]);
        }
        
        $this->recreateSessionForShift($shift);
        
        $shift_code = is_array($shift) ? $shift['shift_code'] : $shift->shift_code;
        $shift_id = is_array($shift) ? $shift['id'] : $shift->id;
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Session berhasil dipulihkan untuk shift: ' . $shift_code,
            'shift_id' => $shift_id,
            'shift_code' => $shift_code
        ]);
    }

    private function generateShiftCode($outlet_id)
    {
        $date = date('Ymd');
        $outlet_code = str_pad($outlet_id, 3, '0', STR_PAD_LEFT);
        $counter = 1;
        
        do {
            $shift_code = "SH{$date}{$outlet_code}" . str_pad($counter, 2, '0', STR_PAD_LEFT);
            $exists = $this->shiftModel->where('shift_code', $shift_code)->first();
            $counter++;
        } while ($exists);
        
        return $shift_code;
    }

    public function apiOpenShift()
    {
        $outlet_id = $this->request->getPost('outlet_id');
        $open_float = $this->request->getPost('open_float');
        
        if (!$outlet_id || !$open_float) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Outlet ID dan Open Float harus diisi'
            ]);
        }

        $user_id = $this->ionAuth->user()->row()->id;
        
        // Check if user already has a shift for today at this outlet
        $existingShift = $this->getUserShiftForToday($user_id, $outlet_id);
        
        if ($existingShift) {
            // Check if the existing shift is open
            $shift_status = is_array($existingShift) ? $existingShift['status'] : $existingShift->status;
            
            if ($shift_status === 'open') {
                // User already has an open shift - recreate session instead of creating duplicate
                $this->recreateSessionForShift($existingShift);
                $shift_code = is_array($existingShift) ? $existingShift['shift_code'] : $existingShift->shift_code;
                $shift_id = is_array($existingShift) ? $existingShift['id'] : $existingShift->id;
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Session berhasil dipulihkan untuk shift yang sudah terbuka: ' . $shift_code,
                    'shift_id' => $shift_id,
                    'shift_code' => $shift_code,
                    'recreated' => true
                ]);
            } else {
                // User already has a closed shift for today - prevent creating new one
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Anda sudah memiliki shift untuk outlet ini hari ini. Hanya satu shift per hari per outlet yang diizinkan.'
                ]);
            }
        }

        $data = [
            'shift_code' => $this->generateShiftCode($outlet_id),
            'outlet_id' => $outlet_id,
            'user_open_id' => $user_id,
            'start_at' => date('Y-m-d H:i:s'),
            'open_float' => $open_float,
            'sales_cash_total' => 0.00,
            'petty_in_total' => 0.00,
            'petty_out_total' => 0.00,
            'expected_cash' => $open_float,
            'status' => 'open'
        ];

        try {
            if ($this->shiftModel->insert($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Shift berhasil dibuka',
                    'shift_id' => $this->shiftModel->insertID,
                    'shift_code' => $data['shift_code']
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal membuka shift'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function apiCloseShift()
    {
        $shift_id = $this->request->getPost('shift_id');
        $counted_cash = $this->request->getPost('counted_cash');
        $notes = $this->request->getPost('notes') ?? '';
        
        if (!$shift_id || !$counted_cash) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Shift ID dan Counted Cash harus diisi'
            ]);
        }

        $user_close_id = $this->ionAuth->user()->row()->id;
        
        if ($this->shiftModel->closeShift($shift_id, $user_close_id, $counted_cash, $notes)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Shift berhasil ditutup'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menutup shift'
            ]);
        }
    }
}
