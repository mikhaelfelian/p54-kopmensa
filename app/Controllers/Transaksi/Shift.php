<?php

namespace App\Controllers\Transaksi;

use App\Controllers\BaseController;
use App\Models\ShiftModel;
use App\Models\GudangModel;
use App\Models\PettyModel;
use App\Models\TransJualModel;
use App\Models\TransJualDetModel;
use App\Models\TransJualPlatModel;

class Shift extends BaseController
{
    protected $shiftModel;
    protected $gudangModel;
    protected $pettyModel;
    protected $transJualModel;
    protected $transJualDetModel;
    protected $transJualPlatModel;
    protected $ionAuth;

    public function __construct()
    {
        $this->shiftModel = new ShiftModel();
        $this->gudangModel = new GudangModel();
        $this->pettyModel = new PettyModel();
        $this->transJualModel = new TransJualModel();
        $this->transJualDetModel = new TransJualDetModel();
        $this->transJualPlatModel = new TransJualPlatModel();
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
    }

    public function index()
    {
        $outlet_id = session()->get('outlet_id');
        $user_id = $this->ionAuth->user()->row()->id;
        
        // Check if user has an active shift
        $activeShift = $this->shiftModel->getUserActiveShift($user_id);
        if ($activeShift) {
            // Get outlet name for active shift
            $gudangModel = new \App\Models\GudangModel();
            $outlet = $gudangModel->find($activeShift['outlet_id']);
            $activeShift['outlet_name'] = $outlet ? $outlet->nama : 'N/A';
        }
        
        $startDate  = $this->request->getGet('start_date');
        $endDate    = $this->request->getGet('end_date');
        $statusFlt  = $this->request->getGet('status');
        $keyword    = $this->request->getGet('keyword');
        $outletFlt  = $this->request->getGet('outlet_id');

        $filters = [
            'start_date' => $startDate ?: null,
            'end_date'   => $endDate ?: null,
            'keyword'    => $keyword ? trim($keyword) : null,
        ];
        if ($statusFlt !== null && $statusFlt !== '') {
            $filters['status'] = $statusFlt;
        }

        // outlet_id absent on first visit → restrict to session outlet when set; explicit GET (including empty = all outlets)
        if ($this->request->getGet('outlet_id') !== null) {
            if ($outletFlt !== null && $outletFlt !== '') {
                $filters['outlet_id'] = (int) $outletFlt;
            }
        } elseif ($outlet_id) {
            $filters['outlet_id'] = (int) $outlet_id;
        }

        $perPage = (int) ($this->request->getGet('per_page') ?? 25);
        if (! in_array($perPage, [25, 50, 100], true)) {
            $perPage = 25;
        }
        $page = max(1, (int) ($this->request->getGet('page_shift') ?? 1));
        $totalShifts = $this->shiftModel->countShiftsFiltered($filters);
        $offset = ($page - 1) * $perPage;
        $shifts = $this->shiftModel->getShiftsFiltered($filters, $perPage, $offset);

        $pager = \Config\Services::pager();
        $pager->store('shift_hist', $page, $perPage, $totalShifts);
        $pager->only(['start_date', 'end_date', 'status', 'keyword', 'outlet_id', 'per_page']);

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
                        ->where('id', $shift['user_open_id'] ?? 0)
                        ->get();
                    
                    if ($userQuery->getNumRows() > 0) {
                        $user = $userQuery->getRow();
                        $shift['user_open_name'] = $user->first_name ?? $user->username ?? 'User';
                        $shift['user_open_lastname'] = $user->last_name ?? '';
                    } else {
                        // Fallback to IonAuth method
                        $uid = $shift['user_open_id'] ?? 0;
                        $user = $this->ionAuth->user($uid)->row();
                        if ($user) {
                            $shift['user_open_name'] = $user->first_name ?? 'User';
                            $shift['user_open_lastname'] = $user->last_name ?? '';
                        } else {
                            $shift['user_open_name'] = 'User ID: ' . $uid;
                        }
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Error getting user data: ' . $e->getMessage());
                    $shift['user_open_name'] = 'User ID: ' . ($shift['user_open_id'] ?? '');
                }
            }
            
            $processedShifts[] = $shift;
        }
        
        $data = array_merge($this->data, [
            'title' => 'Shift Management',
            'shifts' => $processedShifts,
            'current_outlet_id' => $outlet_id,
            'activeShift' => $activeShift,
            'filter_start_date' => $startDate,
            'filter_end_date'   => $endDate,
            'filter_status'     => $statusFlt,
            'filter_keyword'    => $keyword,
            'filter_outlet_id'  => $this->request->getGet('outlet_id') !== null ? $outletFlt : $outlet_id,
            'outlets'           => $this->gudangModel->getOutlets(),
            'pager'             => $pager,
            'per_page'          => $perPage,
            'current_page'      => $page,
            'total_shifts'      => $totalShifts,
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
        
        // Check if user has an active shift
        $activeShift = $this->shiftModel->getUserActiveShift($user_id);
        
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
            'existingShifts' => $existingShifts,
            'activeShift' => $activeShift
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
        // Check if user is logged in
        if (!$this->ionAuth->loggedIn()) {
            session()->setFlashdata('error', 'Session telah berakhir. Silakan login kembali.');
            return redirect()->to('/auth/login');
        }

        $shift = $this->shiftModel->getShiftWithDetails($shift_id);
        if (!$shift) {
            session()->setFlashdata('error', 'Shift tidak ditemukan');
            return redirect()->to('/transaksi/shift');
        }

        // Check if shift is open
        if ($shift['status'] !== 'open') {
            session()->setFlashdata('error', 'Hanya shift yang sedang berjalan yang dapat ditutup');
            return redirect()->to('/transaksi/shift');
        }

        // Check if current user is the same as the user who opened the shift
        $current_user_id = $this->ionAuth->user()->row()->id;
        if ($shift['user_open_id'] != $current_user_id) {
            session()->setFlashdata('error', 'Hanya user yang membuka shift yang dapat menutup shift ini');
            return redirect()->to('/transaksi/shift');
        }

        $draftCount = $this->transJualModel->where('id_shift', $shift_id)->where('status', '0')->countAllResults();
        if ($draftCount > 0) {
            session()->setFlashdata('error', 'Tidak dapat menutup shift: masih ada ' . $draftCount . ' transaksi draft. Hapus atau selesaikan draft terlebih dahulu.');
            return redirect()->to('/transaksi/shift/view/' . $shift_id);
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

        // Get complete payment breakdown
        $paymentBreakdown = [];
        try {
            $paymentBreakdown = $this->shiftModel->getShiftPaymentBreakdown($shift_id);
        } catch (\Exception $e) {
            log_message('error', 'Payment breakdown error: ' . $e->getMessage());
            $paymentBreakdown = [
                'payment_methods' => [],
                'total_refund' => 0
            ];
        }

        $transactionStats = $this->shiftModel->getShiftSalesAggregates((int) $shift_id);

        // Live preview: expected cash = open + computed cash sales + petty (same as closeShiftWithTotals)
        try {
            $pettyAtClose = $this->pettyModel->getPettyCashSummaryByShift((int) $shift_id);
            $cashSalesLive = $this->shiftModel->computeCashSalesTotalForShift((int) $shift_id);
            $pIn = (float) ($pettyAtClose['total_in'] ?? 0);
            $pOut = (float) ($pettyAtClose['total_out'] ?? 0);
            $shift['expected_cash'] = (float) ($shift['open_float'] ?? 0) + $cashSalesLive + $pIn - $pOut;
        } catch (\Exception $e) {
            log_message('error', 'closeShift preview: ' . $e->getMessage());
        }

        $data = array_merge($this->data, [
            'title' => 'Tutup Shift',
            'shift' => $shift,
            'pettySummary' => $pettySummary,
            'salesSummary' => $salesSummary,
            'paymentBreakdown' => $paymentBreakdown,
            'transactionStats' => $transactionStats
        ]);
        
        return view('admin-lte-3/shift/close', $data);
    }

    /**
     * Process the shift closing (POST)
     */
    public function processClose()
    {
        // Check if user is logged in
        if (!$this->ionAuth->loggedIn()) {
            session()->setFlashdata('error', 'Session telah berakhir. Silakan login kembali.');
            return redirect()->to('/auth/login');
        }

        $shift_id = $this->request->getPost('shift_id');
        $counted_cash = $this->request->getPost('counted_cash');
        $notes = $this->request->getPost('notes');
        $catatan_shift = $this->request->getPost('catatan_shift');

        // Clean the counted_cash value - remove any formatting and convert to decimal
        if (is_string($counted_cash)) {
            $counted_cash = format_angka_db($counted_cash);
        }

        // Check if shift exists and get shift details
        $shift = $this->shiftModel->find($shift_id);
        if (!$shift) {
            session()->setFlashdata('error', 'Shift tidak ditemukan');
            return redirect()->to('/transaksi/shift');
        }

        // Handle both array and object formats
        if (is_array($shift)) {
            $shift_status = $shift['status'];
            $shift_user_open_id = $shift['user_open_id'];
        } else {
            $shift_status = $shift->status;
            $shift_user_open_id = $shift->user_open_id;
        }

        // Check if shift is open
        if ($shift_status !== 'open') {
            session()->setFlashdata('error', 'Hanya shift yang sedang berjalan yang dapat ditutup');
            return redirect()->to('/transaksi/shift');
        }

        // Check if current user is the same as the user who opened the shift
        $current_user_id = $this->ionAuth->user()->row()->id;
        if ($shift_user_open_id != $current_user_id) {
            session()->setFlashdata('error', 'Hanya user yang membuka shift yang dapat menutup shift ini');
            return redirect()->to('/transaksi/shift');
        }

        $draftCount = $this->transJualModel->where('id_shift', $shift_id)->where('status', '0')->countAllResults();
        if ($draftCount > 0) {
            session()->setFlashdata('error', 'Tidak dapat menutup shift: masih ada ' . $draftCount . ' transaksi draft. Hapus atau selesaikan draft terlebih dahulu.');

            return redirect()->to('/transaksi/shift/view/' . $shift_id);
        }

        // Format counted_cash before validation
        $counted_cash_formatted = format_angka_db($counted_cash);
        
        $rules = [
            'shift_id' => 'required|integer',
            'notes' => 'permit_empty|max_length[500]'
        ];

        // Override the counted_cash value for validation
        $this->request->setGlobal('post', array_merge($this->request->getPost(), ['counted_cash' => $counted_cash_formatted]));

        if ($this->validate($rules)) {
            $user_close_id = $this->ionAuth->user()->row()->id;

            $shiftData = $this->shiftModel->find($shift_id);
            if (! $shiftData) {
                session()->setFlashdata('error', 'Shift tidak ditemukan');
                return redirect()->to('/transaksi/shift');
            }

            $pettySummary = ['total_in' => 0, 'total_out' => 0];
            try {
                $pettySummary = $this->pettyModel->getPettyCashSummaryByShift((int) $shift_id);
            } catch (\Exception $e) {
                log_message('error', 'Petty cash at close: ' . $e->getMessage());
            }

            $ok = $this->shiftModel->closeShiftWithTotals(
                (int) $shift_id,
                (int) $user_close_id,
                $counted_cash_formatted,
                (string) ($notes ?? ''),
                (string) ($catatan_shift ?? ''),
                (float) ($pettySummary['total_in'] ?? 0),
                (float) ($pettySummary['total_out'] ?? 0)
            );

            if ($ok) {
                session()->setFlashdata('success', 'Shift berhasil ditutup');

                return redirect()->to('/transaksi/shift/print/' . $shift_id);
            }
            session()->setFlashdata('error', 'Gagal menutup shift');
        } else {
            session()->setFlashdata('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
        }

        // If we get here, there was an error, redirect back to form with data
        return redirect()->back()->withInput();
    }

    public function approveShift($shift_id)
    {
        // Check if user is logged in
        if (!$this->ionAuth->loggedIn()) {
            session()->setFlashdata('error', 'Session telah berakhir. Silakan login kembali.');
            return redirect()->to('/auth/login');
        }

        // Check if shift exists and is closed
        $shift = $this->shiftModel->find($shift_id);
        if (!$shift) {
            session()->setFlashdata('error', 'Shift tidak ditemukan');
            return redirect()->to('/transaksi/shift');
        }

        // Handle both array and object formats
        $shift_status = is_array($shift) ? $shift['status'] : $shift->status;

        if ($shift_status !== 'closed') {
            session()->setFlashdata('error', 'Hanya shift yang sudah ditutup yang dapat disetujui');
            return redirect()->to('/transaksi/shift');
        }

        $user_approve_id = $this->ionAuth->user()->row()->id;
        
        if ($this->shiftModel->approveShift($shift_id, $user_approve_id)) {
            session()->setFlashdata('success', 'Shift berhasil disetujui');
        } else {
            session()->setFlashdata('error', 'Gagal menyetujui shift');
        }
        
        return redirect()->to('/transaksi/shift');
    }

    public function reopen($shift_id)
    {
        // Check if user is logged in
        if (!$this->ionAuth->loggedIn()) {
            session()->setFlashdata('error', 'Session telah berakhir. Silakan login kembali.');
            return redirect()->to('/auth/login');
        }

        // Check if shift exists
        $shift = $this->shiftModel->find($shift_id);
        if (!$shift) {
            session()->setFlashdata('error', 'Shift tidak ditemukan');
            return redirect()->to('/transaksi/shift');
        }

        $user_id = $this->ionAuth->user()->row()->id;
        
        // Handle both array and object formats
        $shift_status = is_array($shift) ? $shift['status'] : $shift->status;
        $shift_user_id = is_array($shift) ? $shift['user_open_id'] : $shift->user_open_id;

        // Check if current user is the owner of this shift
        if ($shift_user_id != $user_id) {
            session()->setFlashdata('error', 'Hanya user yang membuka shift yang dapat membuka kembali shift ini');
            return redirect()->to('/transaksi/shift');
        }

        // If shift is already open, just restore the session context
        if ($shift_status === 'open') {
            // Set shift context in session
            session()->set([
                'shift_id' => $shift_id,
                'outlet_id' => is_array($shift) ? $shift['outlet_id'] : $shift->outlet_id,
                'shift_code' => is_array($shift) ? $shift['shift_code'] : $shift->shift_code
            ]);
            
            session()->setFlashdata('success', 'Sesi shift berhasil dipulihkan');
            return redirect()->to('/transaksi/jual/cashier');
        }

        // Only allow reopening closed shifts, not approved ones
        if ($shift_status === 'approved') {
            session()->setFlashdata('error', 'Shift yang sudah disetujui tidak dapat dibuka kembali');
            return redirect()->to('/transaksi/shift');
        }

        if ($shift_status !== 'closed') {
            session()->setFlashdata('error', 'Hanya shift yang sudah ditutup atau terbuka yang dapat dibuka kembali');
            return redirect()->to('/transaksi/shift');
        }

        // Reopen closed shift and restore session
        if ($this->shiftModel->reopenShift($shift_id, $user_id)) {
            // Set shift context in session
            session()->set([
                'shift_id' => $shift_id,
                'outlet_id' => is_array($shift) ? $shift['outlet_id'] : $shift->outlet_id,
                'shift_code' => is_array($shift) ? $shift['shift_code'] : $shift->shift_code
            ]);
            
            session()->setFlashdata('success', 'Shift berhasil dibuka kembali dan sesi dipulihkan');
            return redirect()->to('/transaksi/jual/cashier');
        } else {
            session()->setFlashdata('error', 'Gagal membuka kembali shift');
        }
        
        return redirect()->to('/transaksi/shift');
    }

    public function approve($shift_id)
    {
        return $this->approveShift($shift_id);
    }


    public function viewShift($shift_id)
    {
        // Check if required database tables exist
        $missingTables = $this->checkDatabaseTables();
        if (!empty($missingTables)) {
            session()->setFlashdata('error', 'Database tables missing: ' . implode(', ', $missingTables) . '. Please run database migrations.');
            return redirect()->to('/transaksi/shift');
        }
        
        // Validate shift_id
        if (empty($shift_id)) {
            session()->setFlashdata('error', 'Shift ID tidak valid');
            return redirect()->to('/transaksi/shift');
        }
        
        $shift = $this->shiftModel->getShiftWithDetails($shift_id);
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

        $draftSales = [];
        try {
            $draftSales = $this->transJualModel->getDraftSalesByShift($shift_id);
        } catch (\Exception $e) {
            log_message('error', 'Draft sales error: ' . $e->getMessage());
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
            'draftSales'         => $draftSales,
        ]);
        
        return view('admin-lte-3/shift/view', $data);
    }

    /**
     * Delete a draft sale that belongs to this shift (POST: penjualan_id).
     */
    public function deleteDraftSale($shift_id)
    {
        if (!$this->ionAuth->loggedIn()) {
            session()->setFlashdata('error', 'Session telah berakhir. Silakan login kembali.');
            return redirect()->to('/auth/login');
        }

        $penjualan_id = $this->request->getPost('penjualan_id');
        if (empty($penjualan_id)) {
            session()->setFlashdata('error', 'ID transaksi tidak valid.');
            return redirect()->to('/transaksi/shift/view/' . $shift_id);
        }

        $shift = $this->shiftModel->find($shift_id);
        if (!$shift) {
            session()->setFlashdata('error', 'Shift tidak ditemukan.');
            return redirect()->to('/transaksi/shift');
        }

        $draft = $this->transJualModel->find($penjualan_id);
        if (!$draft || (string) $draft->status !== '0') {
            session()->setFlashdata('error', 'Hanya transaksi draft yang dapat dihapus.');
            return redirect()->to('/transaksi/shift/view/' . $shift_id);
        }

        if ((int) $draft->id_shift !== (int) $shift_id) {
            session()->setFlashdata('error', 'Transaksi tidak termasuk shift ini.');
            return redirect()->to('/transaksi/shift/view/' . $shift_id);
        }

        $userId = $this->ionAuth->user()->row()->id;
        $isOwner = (int) $draft->id_user === (int) $userId;
        $isAdmin = $this->ionAuth->isAdmin() || $this->ionAuth->inGroup('admin');
        if (! $isOwner && ! $isAdmin) {
            session()->setFlashdata('error', 'Anda tidak memiliki akses untuk menghapus draft ini.');
            return redirect()->to('/transaksi/shift/view/' . $shift_id);
        }

        $db = \Config\Database::connect();
        $db->transStart();
        $this->transJualDetModel->where('id_penjualan', $penjualan_id)->delete();
        $this->transJualPlatModel->where('id_penjualan', $penjualan_id)->delete();
        $this->transJualModel->delete($penjualan_id);
        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal menghapus draft.');
        } else {
            session()->setFlashdata('success', 'Draft transaksi berhasil dihapus.');
        }

        return redirect()->to('/transaksi/shift/view/' . $shift_id);
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
        // Check if user is logged in
        if (!$this->ionAuth->loggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Session telah berakhir. Silakan login kembali.'
            ]);
        }

        $shift_id = $this->request->getPost('shift_id');
        $counted_cash = $this->request->getPost('counted_cash');
        $notes = $this->request->getPost('notes') ?? '';
        $catatan_shift = $this->request->getPost('catatan_shift') ?? '';
        
        // Format counted_cash to database format
        if (is_string($counted_cash)) {
            $counted_cash = format_angka_db($counted_cash);
        }
        
        if (!$shift_id || !$counted_cash) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Shift ID dan Counted Cash harus diisi'
            ]);
        }

        // Check if shift exists and get shift details
        $shift = $this->shiftModel->find($shift_id);
        if (!$shift) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Shift tidak ditemukan'
            ]);
        }

        // Handle both array and object formats
        if (is_array($shift)) {
            $shift_status = $shift['status'];
            $shift_user_open_id = $shift['user_open_id'];
        } else {
            $shift_status = $shift->status;
            $shift_user_open_id = $shift->user_open_id;
        }

        // Check if shift is open
        if ($shift_status !== 'open') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Hanya shift yang sedang berjalan yang dapat ditutup'
            ]);
        }

        // Check if current user is the same as the user who opened the shift
        $current_user_id = $this->ionAuth->user()->row()->id;
        if ($shift_user_open_id != $current_user_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Hanya user yang membuka shift yang dapat menutup shift ini'
            ]);
        }

        $draftCount = $this->transJualModel->where('id_shift', $shift_id)->where('status', '0')->countAllResults();
        if ($draftCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Masih ada transaksi draft. Hapus atau selesaikan draft sebelum menutup shift.',
            ]);
        }

        $user_close_id = $this->ionAuth->user()->row()->id;

        $pettySummary = ['total_in' => 0, 'total_out' => 0];
        try {
            $pettySummary = $this->pettyModel->getPettyCashSummaryByShift((int) $shift_id);
        } catch (\Exception $e) {
            log_message('error', 'Petty cash at API close: ' . $e->getMessage());
        }

        $ok = $this->shiftModel->closeShiftWithTotals(
            (int) $shift_id,
            (int) $user_close_id,
            (float) $counted_cash,
            (string) $notes,
            (string) $catatan_shift,
            (float) ($pettySummary['total_in'] ?? 0),
            (float) ($pettySummary['total_out'] ?? 0)
        );

        if ($ok) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Shift berhasil ditutup'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menutup shift'
        ]);
    }

    /**
     * Continue existing shift (reconnect to open shift)
     */
    public function continue_shift($shift_id = null)
    {
        if (!$this->ionAuth->loggedIn()) {
            session()->setFlashdata('error', 'Session telah berakhir. Silakan login kembali.');
            return redirect()->to('/auth/login');
        }

        // Get shift_id from parameter, request, or user's active shift
        if (empty($shift_id)) {
            $shift_id = $this->request->getGet('shift_id') ?? $this->request->getPost('shift_id');
        }
        
        // If still no shift_id, try to get from user's active shift
        if (empty($shift_id)) {
            $user_id = $this->ionAuth->user()->row()->id;
            $activeShift = $this->shiftModel->getUserActiveShift($user_id);
            if ($activeShift) {
                $shift_id = $activeShift['id'];
            }
        }

        if (empty($shift_id)) {
            session()->setFlashdata('error', 'Shift ID tidak ditemukan');
            return redirect()->to('/transaksi/shift');
        }

        $user_id = $this->ionAuth->user()->row()->id;
        $shift = $this->shiftModel->find($shift_id);

        if (!$shift) {
            session()->setFlashdata('error', 'Shift tidak ditemukan');
            return redirect()->to('/transaksi/shift');
        }

        // Handle both array and object formats
        $shift_user_open_id = is_array($shift) ? $shift['user_open_id'] : $shift->user_open_id;
        $shift_status = is_array($shift) ? $shift['status'] : $shift->status;
        $shift_code = is_array($shift) ? $shift['shift_code'] : $shift->shift_code;

        // Verify shift belongs to user and is open
        if ($shift_user_open_id != $user_id) {
            session()->setFlashdata('error', 'Anda tidak memiliki akses ke shift ini');
            return redirect()->to('/transaksi/shift');
        }

        if ($shift_status !== 'open') {
            session()->setFlashdata('error', 'Shift ini sudah ditutup');
            return redirect()->to('/transaksi/shift');
        }

        // Recreate session for shift
        $this->recreateSessionForShift($shift);
        
        session()->setFlashdata('success', 'Shift berhasil dilanjutkan: ' . $shift_code);
        return redirect()->to('/transaksi/jual/cashier');
    }

    /**
     * Print shift report
     */
    public function printShiftReport($shift_id)
    {
        if (!$this->ionAuth->loggedIn()) {
            session()->setFlashdata('error', 'Session telah berakhir. Silakan login kembali.');
            return redirect()->to('/auth/login');
        }

        $shift = $this->shiftModel->getShiftWithDetails($shift_id);
        if (!$shift) {
            session()->setFlashdata('error', 'Shift tidak ditemukan');
            return redirect()->to('/transaksi/shift');
        }

        // Get payment breakdown
        $paymentBreakdown = $this->shiftModel->getShiftPaymentBreakdown($shift_id);

        $transactionStats = $this->shiftModel->getShiftSalesAggregates((int) $shift_id);

        // Get Pengaturan for footer
        $pengaturanModel = new \App\Models\PengaturanModel();
        $pengaturan = $pengaturanModel->asObject()->where('id', 1)->first();

        $data = array_merge($this->data, [
            'title' => 'Laporan Shift',
            'shift' => $shift,
            'paymentBreakdown' => $paymentBreakdown,
            'transactionStats' => $transactionStats,
            'Pengaturan' => $pengaturan
        ]);

        // Check if PDF export requested
        $format = $this->request->getGet('format');
        if ($format === 'pdf') {
            // Generate PDF using TCPDF
            $this->generateShiftReportPDF($shift, $paymentBreakdown, $transactionStats, $pengaturan);
            return; // PDF is output directly
        }

        // Default: POS thermal format
        return view('admin-lte-3/shift/print_shift_report', $data);
    }

    /**
     * Generate PDF report for shift
     */
    private function generateShiftReportPDF($shift, $paymentBreakdown, $transactionStats, $pengaturan)
    {
        // Load TCPDF
        require_once(APPPATH . '../vendor/tecnickcom/tcpdf/tcpdf.php');
        
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator($pengaturan->judul_app ?? 'KOPMENSA POS');
        $pdf->SetAuthor($pengaturan->nama_perusahaan ?? 'KOPMENSA');
        $pdf->SetTitle('Laporan Shift - ' . $shift['shift_code']);
        $pdf->SetSubject('Shift Report');
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Set margins
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        
        // Add page
        $pdf->AddPage();
        
        // Header
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, strtoupper($pengaturan->nama_perusahaan ?? 'KOPMENSA'), 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 5, $pengaturan->alamat ?? '', 0, 1, 'C');
        if (!empty($pengaturan->no_telp)) {
            $pdf->Cell(0, 5, 'Telp: ' . $pengaturan->no_telp, 0, 1, 'C');
        }
        $pdf->Ln(5);
        $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
        $pdf->Ln(5);
        
        // Report title
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 8, 'LAPORAN SHIFT', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 5, $shift['shift_code'], 0, 1, 'C');
        $pdf->Ln(3);
        
        // Shift Information
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 6, 'INFORMASI SHIFT', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        
        $info = [
            'Kasir' => ($shift['user_open_name'] ?? '') . ' ' . ($shift['user_open_lastname'] ?? ''),
            'Outlet' => $shift['outlet_name'] ?? 'N/A',
            'Waktu Buka' => date('d/m/Y H:i', strtotime($shift['start_at'])),
            'Waktu Tutup' => !empty($shift['end_at']) ? date('d/m/Y H:i', strtotime($shift['end_at'])) : '-'
        ];
        
        foreach ($info as $label => $value) {
            $pdf->Cell(50, 6, $label . ':', 0, 0, 'L');
            $pdf->Cell(0, 6, $value, 0, 1, 'L');
        }
        $pdf->Ln(3);
        
        // Financial Summary
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 6, 'RINGKASAN KEUANGAN', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        
        $financial = [
            'Uang Modal (Opening Float)' => 'Rp ' . number_format($shift['open_float'], 0, ',', '.'),
            'Total Transaksi' => ($transactionStats['total_transactions'] ?? 0) . ' transaksi',
            'Total Pendapatan' => 'Rp ' . number_format($transactionStats['total_revenue'] ?? 0, 0, ',', '.')
        ];
        
        foreach ($financial as $label => $value) {
            $pdf->Cell(80, 6, $label . ':', 0, 0, 'L');
            $pdf->Cell(0, 6, $value, 0, 1, 'R');
        }
        $pdf->Ln(3);
        
        // Payment Methods
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 6, 'METODE PEMBAYARAN', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        
        if (!empty($paymentBreakdown['payment_methods'])) {
            $pdf->SetFillColor(240, 240, 240);
            $pdf->Cell(100, 6, 'Metode', 1, 0, 'L', true);
            $pdf->Cell(40, 6, 'Jumlah', 1, 0, 'R', true);
            $pdf->Cell(40, 6, 'Transaksi', 1, 1, 'C', true);
            
            foreach ($paymentBreakdown['payment_methods'] as $payment) {
                $pdf->Cell(100, 6, $payment['payment_method_name'] ?? $payment['payment_method_type'] ?? 'Unknown', 1, 0, 'L');
                $pdf->Cell(40, 6, 'Rp ' . number_format($payment['total_amount'], 0, ',', '.'), 1, 0, 'R');
                $pdf->Cell(40, 6, $payment['transaction_count'] . 'x', 1, 1, 'C');
            }
        } else {
            $pdf->Cell(0, 6, '- Tidak ada transaksi -', 0, 1, 'C');
        }
        
        if (($paymentBreakdown['total_refund'] ?? 0) > 0) {
            $pdf->SetTextColor(255, 0, 0);
            $pdf->Cell(100, 6, 'Total Refund:', 1, 0, 'L');
            $pdf->Cell(80, 6, '-Rp ' . number_format($paymentBreakdown['total_refund'], 0, ',', '.'), 1, 1, 'R');
            $pdf->SetTextColor(0, 0, 0);
        }
        $pdf->Ln(3);
        
        // Petty Cash
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 6, 'KAS KECIL', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        
        $petty = [
            'Kas Kecil Masuk' => '+Rp ' . number_format($shift['petty_in_total'], 0, ',', '.'),
            'Kas Kecil Keluar' => '-Rp ' . number_format($shift['petty_out_total'], 0, ',', '.')
        ];
        
        foreach ($petty as $label => $value) {
            $pdf->Cell(80, 6, $label . ':', 0, 0, 'L');
            $pdf->Cell(0, 6, $value, 0, 1, 'R');
        }
        $pdf->Ln(3);
        
        // Closing Summary
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 6, 'PENUTUPAN', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        
        $closing = [
            'Uang Diharapkan' => 'Rp ' . number_format($shift['expected_cash'], 0, ',', '.'),
            'Uang Dihitung' => 'Rp ' . number_format($shift['counted_cash'] ?? 0, 0, ',', '.'),
            'Selisih' => 'Rp ' . number_format($shift['diff_cash'] ?? 0, 0, ',', '.')
        ];
        
        foreach ($closing as $label => $value) {
            $pdf->Cell(80, 6, $label . ':', 0, 0, 'L');
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 6, $value, 0, 1, 'R');
            $pdf->SetFont('helvetica', '', 10);
        }
        $pdf->Ln(3);
        
        // Notes
        if (!empty($shift['catatan_shift'])) {
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->Cell(0, 6, 'CATATAN', 0, 1, 'L');
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(0, 6, $shift['catatan_shift'], 0, 'L');
            $pdf->Ln(3);
        }
        
        // Footer
        $pdf->SetY(-20);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(0, 5, 'Dicetak: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
        $pdf->Cell(0, 5, $pengaturan->footer_nota ?? 'Terima Kasih', 0, 1, 'C');
        
        // Output PDF
        $pdf->Output('Shift_Report_' . $shift['shift_code'] . '.pdf', 'I');
    }
}

