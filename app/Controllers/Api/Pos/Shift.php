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

class Shift extends BaseController
{
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
     * Get active shift for outlet
     */
    public function getActiveShift()
    {
        $outlet_id = $this->request->getPost('outlet_id');
        
        if (!$outlet_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Outlet ID required'
            ]);
        }

        $activeShift = $this->shiftModel->getActiveShift($outlet_id);
        
        if (!$activeShift) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No active shift found',
                'code' => 'NO_ACTIVE_SHIFT',
                'data' => null
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Active shift found',
            'data' => $activeShift
        ]);
    }

    /**
     * Open new shift
     */
    public function openShift()
    {
        $outlet_id = $this->request->getPost('outlet_id');
        $open_float = $this->request->getPost('open_float');
        $user_id = $this->request->getPost('user_id');

        if (!$outlet_id || !$open_float || !$user_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Outlet ID, open float, and user ID are required'
            ]);
        }

        // Check if there's already an active shift
        $existingShift = $this->shiftModel->getActiveShift($outlet_id);
        if ($existingShift) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'There is already an active shift for this outlet',
                'code' => 'SHIFT_ALREADY_OPEN',
                'data' => $existingShift
            ]);
        }

        // Validate open float
        if (!is_numeric($open_float) || $open_float < 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Open float must be a positive number'
            ]);
        }

        // Generate shift code
        $shift_code = $this->generateShiftCode($outlet_id);

        $data = [
            'shift_code' => $shift_code,
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

        if ($this->shiftModel->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Shift opened successfully',
                'data' => [
                    'shift_id' => $this->shiftModel->insertID,
                    'shift_code' => $shift_code,
                    'outlet_id' => $outlet_id,
                    'open_float' => $open_float,
                    'start_at' => $data['start_at']
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to open shift'
            ]);
        }
    }

    /**
     * Close active shift
     */
    public function closeShift()
    {
        $shift_id = $this->request->getPost('shift_id');
        $counted_cash = $this->request->getPost('counted_cash');
        $notes = $this->request->getPost('notes');
        $user_id = $this->request->getPost('user_id');

        if (!$shift_id || !$counted_cash || !$user_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Shift ID, counted cash, and user ID are required'
            ]);
        }

        // Get shift details
        $shift = $this->shiftModel->getShiftWithDetails($shift_id);
        if (!$shift) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Shift not found'
            ]);
        }

        // Check if shift is already closed
        if ($shift['status'] !== 'open') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Shift is not open or already closed',
                'code' => 'SHIFT_NOT_OPEN'
            ]);
        }

        // Validate counted cash
        if (!is_numeric($counted_cash) || $counted_cash < 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Counted cash must be a positive number'
            ]);
        }

        // Close the shift
        if ($this->shiftModel->closeShift($shift_id, $user_id, $counted_cash, $notes)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Shift closed successfully',
                'data' => [
                    'shift_id' => $shift_id,
                    'shift_code' => $shift['shift_code'],
                    'counted_cash' => $counted_cash,
                    'close_time' => date('Y-m-d H:i:s')
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to close shift'
            ]);
        }
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
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Shift ID required'
            ]);
        }

        $shift = $this->shiftModel->getShiftWithDetails($shift_id);
        if (!$shift) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Shift not found'
            ]);
        }

        // Get petty cash entries for this shift
        $pettyEntries = $this->pettyModel->getPettyCashByShift($shift_id);
        
        // Get sales entries for this shift
        $salesEntries = $this->transJualModel->getSalesByShift($shift_id);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'shift' => $shift,
                'petty_entries' => $pettyEntries,
                'sales_entries' => $salesEntries
            ]
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
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Outlet ID required'
            ]);
        }

        $summary = $this->shiftModel->getShiftSummary($outlet_id, $date);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Get shifts by outlet
     */
    public function getShiftsByOutlet()
    {
        $outlet_id = $this->request->getPost('outlet_id');
        $limit = $this->request->getPost('limit') ?? 50;
        $offset = $this->request->getPost('offset') ?? 0;
        
        if (!$outlet_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Outlet ID required'
            ]);
        }

        $shifts = $this->shiftModel->getShiftsByOutlet($outlet_id, $limit, $offset);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $shifts
        ]);
    }

    /**
     * Check shift status
     */
    public function checkShiftStatus()
    {
        $outlet_id = $this->request->getPost('outlet_id');
        
        if (!$outlet_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Outlet ID required'
            ]);
        }

        $activeShift = $this->shiftModel->getActiveShift($outlet_id);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'has_active_shift' => !empty($activeShift),
                'shift' => $activeShift
            ]
        ]);
    }

    /**
     * Get outlets for dropdown
     */
    public function getOutlets()
    {
        $outlets = $this->gudangModel->getOutletsForDropdown();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $outlets
        ]);
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
