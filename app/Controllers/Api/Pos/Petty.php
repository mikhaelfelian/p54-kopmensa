<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-28
 * Github: github.com/mikhaelfelian
 * Description: API Controller for Petty Cash management via mobile app
 * This file represents the Controller.
 */

namespace App\Controllers\Api\Pos;

use App\Controllers\BaseController;
use App\Models\PettyModel;
use App\Models\ShiftModel;
use App\Models\PettyCategoryModel;
use App\Models\GudangModel;

class Petty extends BaseController
{
    protected $pettyModel;
    protected $shiftModel;
    protected $categoryModel;
    protected $gudangModel;

    public function __construct()
    {
        $this->pettyModel = new PettyModel();
        $this->shiftModel = new ShiftModel();
        $this->categoryModel = new PettyCategoryModel();
        $this->gudangModel = new GudangModel();
    }

    /**
     * Get petty cash entries for current shift
     */
    public function getPettyCash()
    {
        $outlet_id = $this->request->getPost('outlet_id');
        
        if (!$outlet_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Outlet ID required'
            ]);
        }

        // Check if there's an active shift
        $activeShift = $this->shiftModel->getActiveShift($outlet_id);
        if (!$activeShift) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No active shift found. Please open shift first.',
                'code' => 'NO_ACTIVE_SHIFT'
            ]);
        }

        $filters = [
            'outlet_id' => $outlet_id,
            'shift_id' => $activeShift['id'],
            'date_from' => $this->request->getPost('date_from') ?? date('Y-m-d'),
            'date_to' => $this->request->getPost('date_to') ?? date('Y-m-d'),
            'direction' => $this->request->getPost('direction') ?? '',
            'status' => $this->request->getPost('status') ?? ''
        ];

        $pettyEntries = $this->pettyModel->getPettyCashWithDetails(null, $filters);
        $summary = $this->pettyModel->getPettyCashSummaryByShift($activeShift['id']);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'petty_entries' => $pettyEntries,
                'summary' => $summary,
                'active_shift' => [
                    'id' => $activeShift['id'],
                    'shift_code' => $activeShift['shift_code'],
                    'start_at' => $activeShift['start_at']
                ]
            ]
        ]);
    }

    /**
     * Create new petty cash entry
     */
    public function create()
    {
        $outlet_id = $this->request->getPost('outlet_id');
        $direction = $this->request->getPost('direction');
        $amount = $this->request->getPost('amount');
        $reason = $this->request->getPost('reason');
        $category_id = $this->request->getPost('category_id');
        $ref_no = $this->request->getPost('ref_no');

        // Validate required fields
        if (!$outlet_id || !$direction || !$amount || !$reason) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Outlet ID, direction, amount, and reason are required'
            ]);
        }

        // Check if there's an active shift
        $activeShift = $this->shiftModel->getActiveShift($outlet_id);
        if (!$activeShift) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No active shift found. Please open shift first.',
                'code' => 'NO_ACTIVE_SHIFT'
            ]);
        }

        // Validate direction
        if (!in_array($direction, ['IN', 'OUT'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Direction must be IN or OUT'
            ]);
        }

        // Validate amount
        if (!is_numeric($amount) || $amount <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Amount must be a positive number'
            ]);
        }

        // Prepare data
        $data = [
            'shift_id' => $activeShift['id'],
            'outlet_id' => $outlet_id,
            'kasir_user_id' => $this->request->getPost('user_id'), // From mobile app
            'category_id' => $category_id ?: null,
            'direction' => $direction,
            'amount' => $amount,
            'reason' => $reason,
            'ref_no' => $ref_no ?: null,
            'status' => 'posted'
        ];

        // Insert petty cash entry
        if ($this->pettyModel->insert($data)) {
            // Update shift petty totals
            $this->updateShiftPettyTotals($activeShift['id']);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Petty cash entry created successfully',
                'data' => [
                    'id' => $this->pettyModel->insertID,
                    'shift_id' => $activeShift['id'],
                    'amount' => $amount,
                    'direction' => $direction
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create petty cash entry'
            ]);
        }
    }

    /**
     * Get petty cash categories
     */
    public function getCategories()
    {
        $categories = $this->categoryModel->getActiveCategories();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get petty cash summary for current shift
     */
    public function getSummary()
    {
        $outlet_id = $this->request->getPost('outlet_id');
        
        if (!$outlet_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Outlet ID required'
            ]);
        }

        // Check if there's an active shift
        $activeShift = $this->shiftModel->getActiveShift($outlet_id);
        if (!$activeShift) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No active shift found. Please open shift first.',
                'code' => 'NO_ACTIVE_SHIFT'
            ]);
        }

        $summary = $this->pettyModel->getPettyCashSummaryByShift($activeShift['id']);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'summary' => $summary,
                'shift_info' => [
                    'id' => $activeShift['id'],
                    'shift_code' => $activeShift['shift_code'],
                    'start_at' => $activeShift['start_at']
                ]
            ]
        ]);
    }

    /**
     * Update petty cash entry
     */
    public function update($id = null)
    {
        if (!$id) {
            $id = $this->request->getPost('id');
        }

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petty cash entry ID required'
            ]);
        }

        $petty = $this->pettyModel->find($id);
        if (!$petty) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petty cash entry not found'
            ]);
        }

        // Check if can edit (only draft or posted status)
        if ($petty['status'] === 'void') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot edit voided entry'
            ]);
        }

        $direction = $this->request->getPost('direction');
        $amount = $this->request->getPost('amount');
        $reason = $this->request->getPost('reason');
        $category_id = $this->request->getPost('category_id');

        if (!$direction || !$amount || !$reason) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Direction, amount, and reason are required'
            ]);
        }

        $data = [
            'category_id' => $category_id ?: null,
            'direction' => $direction,
            'amount' => $amount,
            'reason' => $reason
        ];

        if ($this->pettyModel->update($id, $data)) {
            // Update shift petty totals
            $this->updateShiftPettyTotals($petty['shift_id']);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Petty cash entry updated successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update petty cash entry'
            ]);
        }
    }

    /**
     * Delete petty cash entry
     */
    public function delete($id = null)
    {
        if (!$id) {
            $id = $this->request->getPost('id');
        }

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petty cash entry ID required'
            ]);
        }

        $petty = $this->pettyModel->find($id);
        if (!$petty) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Petty cash entry not found'
            ]);
        }

        if ($petty['status'] !== 'draft') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only draft entries can be deleted'
            ]);
        }

        if ($this->pettyModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Petty cash entry deleted successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete petty cash entry'
            ]);
        }
    }

    /**
     * Update shift petty totals
     */
    private function updateShiftPettyTotals($shift_id)
    {
        $summary = $this->pettyModel->getPettyCashSummaryByShift($shift_id);
        
        $this->shiftModel->updatePettyTotals(
            $shift_id, 
            $summary['total_in'] ?? 0, 
            $summary['total_out'] ?? 0
        );
    }
}
