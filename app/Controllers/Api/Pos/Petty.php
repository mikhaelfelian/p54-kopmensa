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
use CodeIgniter\API\ResponseTrait;

class Petty extends BaseController
{
    use ResponseTrait;

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
     * Get petty cash overview (GET endpoint)
     */
    public function index()
    {
        $outlet_id = $this->request->getGet('outlet_id');
        
        if (!$outlet_id) {
            return $this->failValidationErrors('Outlet ID required');
        }

        // Check if there's an active shift
        $activeShift = $this->shiftModel->getActiveShift($outlet_id);
        if (!$activeShift) {
            return $this->respond([
                'active_shift' => null,
                'message' => 'No active shift found. Please open shift first.'
            ]);
        }

        // Get petty cash summary for current shift
        $summary = $this->pettyModel->getPettyCashSummaryByShift($activeShift['id']);
        
        // Get recent petty cash entries (last 10)
        $recentEntries = $this->pettyModel->getPettyCashWithDetails(null, [
            'outlet_id' => $outlet_id,
            'shift_id' => $activeShift['id'],
            'limit' => 10
        ]);

        return $this->respond([
            'active_shift' => [
                'id' => $activeShift['id'],
                'shift_code' => $activeShift['shift_code'],
                'start_at' => $activeShift['start_at']
            ],
            'summary' => $summary,
            'recent_entries' => $recentEntries
        ]);
    }

    /**
     * Get petty cash entries for current shift
     */
    public function getPettyCash()
    {
        $outlet_id = $this->request->getPost('outlet_id');
        
        if (!$outlet_id) {
            return $this->failValidationErrors('Outlet ID required');
        }

        // Check if there's an active shift
        $activeShift = $this->shiftModel->getActiveShift($outlet_id);
        if (!$activeShift) {
            return $this->failValidationErrors('No active shift found. Please open shift first.');
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

        return $this->respond([
            'petty_entries' => $pettyEntries,
            'summary' => $summary,
            'active_shift' => [
                'id' => $activeShift['id'],
                'shift_code' => $activeShift['shift_code'],
                'start_at' => $activeShift['start_at']
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
            return $this->failValidationErrors('Outlet ID, direction, amount, and reason are required');
        }

        // Check if there's an active shift
        $activeShift = $this->shiftModel->getActiveShift($outlet_id);
        if (!$activeShift) {
            return $this->failValidationErrors('No active shift found. Please open shift first.');
        }

        // Validate direction
        if (!in_array($direction, ['IN', 'OUT'])) {
            return $this->failValidationErrors('Direction must be IN or OUT');
        }

        // Validate amount
        if (!is_numeric($amount) || $amount <= 0) {
            return $this->failValidationErrors('Amount must be a positive number');
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
            
            return $this->respond([
                'id' => $this->pettyModel->insertID,
                'shift_id' => $activeShift['id'],
                'amount' => $amount,
                'direction' => $direction
            ]);
        } else {
            return $this->failServerError('Failed to create petty cash entry');
        }
    }

    /**
     * Get petty cash categories
     */
    public function getCategories()
    {
        $categories = $this->categoryModel->getActiveCategories();
        
        return $this->respond($categories);
    }

    /**
     * Get petty cash summary for current shift
     */
    public function getSummary()
    {
        $outlet_id = $this->request->getPost('outlet_id');
        
        if (!$outlet_id) {
            return $this->failValidationErrors('Outlet ID required');
        }

        // Check if there's an active shift
        $activeShift = $this->shiftModel->getActiveShift($outlet_id);
        if (!$activeShift) {
            return $this->failValidationErrors('No active shift found. Please open shift first.');
        }

        $summary = $this->pettyModel->getPettyCashSummaryByShift($activeShift['id']);
        
        return $this->respond([
            'summary' => $summary,
            'shift_info' => [
                'id' => $activeShift['id'],
                'shift_code' => $activeShift['shift_code'],
                'start_at' => $activeShift['start_at']
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
            return $this->failValidationErrors('Petty cash entry ID required');
        }

        $petty = $this->pettyModel->find($id);
        if (!$petty) {
            return $this->failNotFound('Petty cash entry not found');
        }

        // Convert to array if it's an object
        if (is_object($petty)) {
            $petty = (array) $petty;
        }

        // Check if can edit (only draft or posted status)
        if ($petty['status'] === 'void') {
            return $this->failValidationErrors('Cannot edit voided entry');
        }

        $direction = $this->request->getPost('direction');
        $amount = $this->request->getPost('amount');
        $reason = $this->request->getPost('reason');
        $category_id = $this->request->getPost('category_id');

        if (!$direction || !$amount || !$reason) {
            return $this->failValidationErrors('Direction, amount, and reason are required');
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
            
            return $this->respond(['message' => 'Petty cash entry updated successfully']);
        } else {
            return $this->failServerError('Failed to update petty cash entry');
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
            return $this->failValidationErrors('Petty cash entry ID required');
        }

        $petty = $this->pettyModel->find($id);
        if (!$petty) {
            return $this->failNotFound('Petty cash entry not found');
        }

        // Convert to array if it's an object
        if (is_object($petty)) {
            $petty = (array) $petty;
        }

        if ($petty['status'] !== 'draft') {
            return $this->failValidationErrors('Only draft entries can be deleted');
        }

        if ($this->pettyModel->delete($id)) {
            return $this->respond(['message' => 'Petty cash entry deleted successfully']);
        } else {
            return $this->failServerError('Failed to delete petty cash entry');
        }
    }

    /**
     * Get petty cash detail by ID
     */
    public function detail($id = null)
    {
        if (!$id) {
            return $this->failValidationErrors('Petty cash entry ID required');
        }

        $petty = $this->pettyModel->getPettyCashWithDetails(['id' => $id]);
        if (empty($petty)) {
            return $this->failNotFound('Petty cash entry not found');
        }

        return $this->respond($petty[0]);
    }

    /**
     * Approve petty cash entry
     */
    public function approve($id = null)
    {
        if (!$id) {
            return $this->failValidationErrors('Petty cash entry ID required');
        }

        $petty = $this->pettyModel->find($id);
        if (!$petty) {
            return $this->failNotFound('Petty cash entry not found');
        }

        // Convert to array if it's an object
        if (is_object($petty)) {
            $petty = (array) $petty;
        }

        if ($petty['status'] !== 'draft') {
            return $this->failValidationErrors('Only draft entries can be approved');
        }

        $approved_by = $this->request->getPost('user_id') ?? 1; // From mobile app
        
        if ($this->pettyModel->approvePettyCash($id, $approved_by)) {
            return $this->respond(['message' => 'Petty cash entry approved successfully']);
        } else {
            return $this->failServerError('Failed to approve petty cash entry');
        }
    }

    /**
     * Reject petty cash entry
     */
    public function reject($id = null)
    {
        if (!$id) {
            return $this->failValidationErrors('Petty cash entry ID required');
        }

        $petty = $this->pettyModel->find($id);
        if (!$petty) {
            return $this->failNotFound('Petty cash entry not found');
        }

        // Convert to array if it's an object
        if (is_object($petty)) {
            $petty = (array) $petty;
        }

        if ($petty['status'] !== 'draft') {
            return $this->failValidationErrors('Only draft entries can be rejected');
        }

        $rejection_reason = $this->request->getPost('rejection_reason') ?? 'Rejected by supervisor';
        $rejected_by = $this->request->getPost('user_id') ?? 1; // From mobile app

        $data = [
            'status' => 'rejected',
            'rejection_reason' => $rejection_reason,
            'rejected_by' => $rejected_by,
            'rejected_at' => date('Y-m-d H:i:s')
        ];

        if ($this->pettyModel->update($id, $data)) {
            return $this->respond(['message' => 'Petty cash entry rejected successfully']);
        } else {
            return $this->failServerError('Failed to reject petty cash entry');
        }
    }

    /**
     * Void petty cash entry
     */
    public function void($id = null)
    {
        if (!$id) {
            return $this->failValidationErrors('Petty cash entry ID required');
        }

        $petty = $this->pettyModel->find($id);
        if (!$petty) {
            return $this->failNotFound('Petty cash entry not found');
        }

        // Convert to array if it's an object
        if (is_object($petty)) {
            $petty = (array) $petty;
        }

        if ($petty['status'] === 'void') {
            return $this->failValidationErrors('Entry is already voided');
        }

        $void_reason = $this->request->getPost('void_reason') ?? 'Voided by user';
        $voided_by = $this->request->getPost('user_id') ?? 1; // From mobile app
        
        if ($this->pettyModel->voidPettyCash($id, $voided_by, $void_reason)) {
            // Update shift petty totals
            $this->updateShiftPettyTotals($petty['shift_id']);
            
            return $this->respond(['message' => 'Petty cash entry voided successfully']);
        } else {
            return $this->failServerError('Failed to void petty cash entry');
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
