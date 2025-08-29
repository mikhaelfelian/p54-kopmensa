<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-28
 * Github: github.com/mikhaelfelian
 * Description: API Controller for Shift management via mobile app
 * This file represents the Controller.
 */

namespace App\Controllers\Api\Pos;

use App\Controllers\BaseController;
use App\Models\ShiftModel;
use App\Models\GudangModel;
use App\Models\PettyModel;
use App\Models\TransJualModel;
use CodeIgniter\API\ResponseTrait;

class Shift extends BaseController
{
    use ResponseTrait;

    protected $shiftModel;
    protected $gudangModel;
    protected $pettyModel;
    protected $transJualModel;

    public function __construct()
    {
        $this->shiftModel = new ShiftModel();
        $this->gudangModel = new GudangModel();
        $this->pettyModel = new PettyModel();
        $this->transJualModel = new TransJualModel();
    }

    /**
     * Get shift list
     */
    public function index()
    {
        $outlet_id = $this->request->getGet('outlet_id');
        $per_page = (int) ($this->request->getGet('per_page') ?? 10);
        $page = (int) ($this->request->getGet('page') ?? 1);
        
        if (!$outlet_id) {
            return $this->failValidationError('Outlet ID required');
        }

        $shifts = $this->shiftModel->getShiftsByOutlet($outlet_id, $per_page, ($page - 1) * $per_page);
        
        // Format items as array of objects with only the required fields
        $formattedItems = [];
        foreach ($shifts as $shift) {
            $formattedItems[] = [
                'id' => (int) $shift['id'],
                'shift_code' => $shift['shift_code'],
                'outlet_id' => (int) $shift['outlet_id'],
                'outlet_name' => $shift['outlet_name'] ?? 'Unknown Outlet',
                'user_open_id' => (int) $shift['user_open_id'],
                'user_open_name' => $shift['user_open_name'] ?? 'Unknown User',
                'start_at' => $shift['start_at'],
                'end_at' => $shift['end_at'],
                'open_float' => (float) $shift['open_float'],
                'status' => $shift['status'],
                'created_at' => $shift['created_at'],
                'updated_at' => $shift['updated_at'],
            ];
        }

        $data = [
            'total' => count($shifts),
            'current_page' => (int) $page,
            'per_page' => $per_page,
            'total_page' => ceil(count($shifts) / $per_page),
            'items' => $formattedItems,
        ];

        return $this->respond($data);
    }

    /**
     * Get shift detail by ID
     */
    public function detail($shift_id = null)
    {
        if (!$shift_id) {
            return $this->failValidationError('Shift ID required');
        }

        $shift = $this->shiftModel->getShiftWithDetails($shift_id);
        if (!$shift) {
            return $this->failNotFound('Shift not found');
        }

        // Get petty cash entries for this shift
        $pettyEntries = $this->pettyModel->getPettyCashWithDetails(['shift_id' => $shift_id]);
        
        // Get sales entries for this shift
        $salesEntries = $this->transJualModel->getSalesByShift($shift_id);

        return $this->respond([
            'shift' => $shift,
            'petty_entries' => $pettyEntries,
            'sales_entries' => $salesEntries
        ]);
    }

    /**
     * Get shift summary by ID (GET request)
     */
    public function summary($shift_id = null)
    {
        if (!$shift_id) {
            return $this->failValidationError('Shift ID required');
        }

        // Get shift details
        $shift = $this->shiftModel->getShiftWithDetails($shift_id);
        if (!$shift) {
            return $this->failNotFound('Shift not found');
        }

        // Get petty cash summary for this shift
        $pettySummary = $this->pettyModel->getPettyCashSummaryByShift($shift_id);
        
        // Get sales summary for this shift
        $salesSummary = $this->transJualModel->getSalesSummaryByShift($shift_id);

        // Calculate totals
        $totalCashIn = $shift['open_float'] + ($pettySummary['total_in'] ?? 0) + ($salesSummary['total_cash'] ?? 0);
        $totalCashOut = ($pettySummary['total_out'] ?? 0);
        $expectedCash = $totalCashIn - $totalCashOut;

        $summary = [
            'shift_id' => $shift_id,
            'shift_code' => $shift['shift_code'],
            'outlet_id' => $shift['outlet_id'],
            'outlet_name' => $shift['outlet_name'] ?? 'Unknown Outlet',
            'user_open' => $shift['user_open_name'] ?? 'Unknown User',
            'start_at' => $shift['start_at'],
            'end_at' => $shift['status'] === 'closed' ? $shift['end_at'] : null,
            'status' => $shift['status'],
            'open_float' => (float) $shift['open_float'],
            'sales_summary' => [
                'total_transactions' => $salesSummary['total_transactions'] ?? 0,
                'total_amount' => (float) ($salesSummary['total_amount'] ?? 0),
                'total_cash' => (float) ($salesSummary['total_cash'] ?? 0),
                'total_non_cash' => (float) ($salesSummary['total_non_cash'] ?? 0)
            ],
            'petty_cash_summary' => [
                'total_in' => (float) ($pettySummary['total_in'] ?? 0),
                'total_out' => (float) ($pettySummary['total_out'] ?? 0),
                'total_transactions' => ($pettySummary['total_transactions'] ?? 0)
            ],
            'cash_summary' => [
                'total_cash_in' => $totalCashIn,
                'total_cash_out' => $totalCashOut,
                'expected_cash' => $expectedCash,
                'counted_cash' => $shift['status'] === 'closed' ? (float) ($shift['counted_cash'] ?? 0) : null,
                'cash_difference' => $shift['status'] === 'closed' ? ($expectedCash - (float) ($shift['counted_cash'] ?? 0)) : null
            ]
        ];

        return $this->respond($summary);
    }

    /**
     * Open new shift
     */
    public function open()
    {
        $outlet_id = $this->request->getPost('outlet_id');
        $saldo_awal = $this->request->getPost('saldo_awal');
        $user_id = $this->request->getPost('user_id');

        if (!$outlet_id || !$saldo_awal || !$user_id) {
            return $this->failValidationError('Outlet ID, saldo awal, and user ID are required');
        }

        // Check if there's already an active shift
        $existingShift = $this->shiftModel->getActiveShift($outlet_id);
        if ($existingShift) {
            return $this->failValidationError('There is already an active shift for this outlet');
        }

        // Validate saldo awal
        if (!is_numeric($saldo_awal) || $saldo_awal < 0) {
            return $this->failValidationError('Saldo awal must be a positive number');
        }

        // Generate shift code
        $shift_code = $this->generateShiftCode($outlet_id);

        $data = [
            'shift_code' => $shift_code,
            'outlet_id' => $outlet_id,
            'user_open_id' => $user_id,
            'start_at' => date('Y-m-d H:i:s'),
            'open_float' => $saldo_awal,
            'sales_cash_total' => 0.00,
            'petty_in_total' => 0.00,
            'petty_out_total' => 0.00,
            'expected_cash' => $saldo_awal,
            'status' => 'open'
        ];

        if ($this->shiftModel->insert($data)) {
            return $this->respond([
                'shift_id' => $this->shiftModel->insertID,
                'shift_code' => $shift_code,
                'outlet_id' => $outlet_id,
                'saldo_awal' => $saldo_awal,
                'start_at' => $data['start_at']
            ]);
        } else {
            return $this->failServerError('Failed to open shift');
        }
    }

    /**
     * Close active shift
     */
    public function close($shift_id = null)
    {
        if (!$shift_id) {
            $shift_id = $this->request->getPost('shift_id');
        }

        $saldo_akhir = $this->request->getPost('saldo_akhir');
        $notes = $this->request->getPost('notes');
        $user_id = $this->request->getPost('user_id');

        if (!$shift_id || !$saldo_akhir || !$user_id) {
            return $this->failValidationError('Shift ID, saldo akhir, and user ID are required');
        }

        // Get shift details
        $shift = $this->shiftModel->getShiftWithDetails($shift_id);
        if (!$shift) {
            return $this->failNotFound('Shift not found');
        }

        // Check if shift is already closed
        if ($shift['status'] !== 'open') {
            return $this->failValidationError('Shift is not open or already closed');
        }

        // Validate saldo akhir
        if (!is_numeric($saldo_akhir) || $saldo_akhir < 0) {
            return $this->failValidationError('Saldo akhir must be a positive number');
        }

        // Close the shift
        if ($this->shiftModel->closeShift($shift_id, $user_id, $saldo_akhir, $notes)) {
            return $this->respond([
                'shift_id' => $shift_id,
                'shift_code' => $shift['shift_code'],
                'saldo_akhir' => $saldo_akhir,
                'close_time' => date('Y-m-d H:i:s')
            ]);
        } else {
            return $this->failServerError('Failed to close shift');
        }
    }

    /**
     * Get active shift for outlet
     */
    public function getActiveShift()
    {
        $outlet_id = $this->request->getPost('outlet_id');
        
        if (!$outlet_id) {
            return $this->failValidationError('Outlet ID required');
        }

        $activeShift = $this->shiftModel->getActiveShift($outlet_id);
        
        if (!$activeShift) {
            return $this->respond([
                'code' => 'NO_ACTIVE_SHIFT',
                'data' => null
            ]);
        }

        return $this->respond($activeShift);
    }

    /**
     * Get shift details
     */
    public function getShiftDetails($shift_id = null)
    {
        if (!$shift_id) {
            $shift_id = $this->request->getPost('shift_id');
        }

        if (!$shift_id) {
            return $this->failValidationError('Shift ID required');
        }

        $shift = $this->shiftModel->getShiftWithDetails($shift_id);
        if (!$shift) {
            return $this->failNotFound('Shift not found');
        }

        // Get petty cash entries for this shift
        $pettyEntries = $this->pettyModel->getPettyCashWithDetails(['shift_id' => $shift_id]);
        
        // Get sales entries for this shift
        $salesEntries = $this->transJualModel->getSalesByShift($shift_id);

        return $this->respond([
            'shift' => $shift,
            'petty_entries' => $pettyEntries,
            'sales_entries' => $salesEntries
        ]);
    }

    /**
     * Get shift summary
     */
    public function getShiftSummary()
    {
        $outlet_id = $this->request->getPost('outlet_id');
        $date = $this->request->getPost('date') ?? date('Y-m-d');
        
        if (!$outlet_id) {
            return $this->failValidationError('Outlet ID required');
        }

        $summary = $this->shiftModel->getShiftSummary($outlet_id, $date);
        
        return $this->respond($summary);
    }

    /**
     * Get shifts by outlet
     */
    public function getShiftsByOutlet()
    {
        $outlet_id = $this->request->getPost('outlet_id');
        $limit = (int) ($this->request->getPost('limit') ?? 50);
        $offset = (int) ($this->request->getPost('offset') ?? 0);
        
        if (!$outlet_id) {
            return $this->failValidationError('Outlet ID required');
        }

        $shifts = $this->shiftModel->getShiftsByOutlet($outlet_id, $limit, $offset);
        
        return $this->respond($shifts);
    }

    /**
     * Check shift status
     */
    public function checkShiftStatus()
    {
        $outlet_id = $this->request->getPost('outlet_id');
        
        if (!$outlet_id) {
            return $this->failValidationError('Outlet ID required');
        }

        $activeShift = $this->shiftModel->getActiveShift($outlet_id);
        
        return $this->respond([
            'has_active_shift' => !empty($activeShift),
            'shift' => $activeShift
        ]);
    }

    /**
     * Get outlets for dropdown
     */
    public function getOutlets()
    {
        $outlets = $this->gudangModel->getOutletsForDropdown();
        
        return $this->respond($outlets);
    }

    /**
     * Generate unique shift code
     */
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
}
