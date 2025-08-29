<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ShiftController extends BaseController
{
    /**
     * Get shift transaction summary
     */
    public function getSummary()
    {
        // Load the helper
        helper('shift');
        
        $shiftId = $this->request->getGet('shift_id');
        $options = [];
        
        // Get filter options
        if ($this->request->getGet('status')) {
            $options['status'] = $this->request->getGet('status');
        }
        
        if ($this->request->getGet('date_from')) {
            $options['date_from'] = $this->request->getGet('date_from');
        }
        
        if ($this->request->getGet('date_to')) {
            $options['date_to'] = $this->request->getGet('date_to');
        }
        
        $summary = get_shift_transaction_summary($shiftId, $options);
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($summary);
        }
        
        return view('admin-lte-3/shift/summary', ['summary' => $summary]);
    }
    
    /**
     * Get simple transaction count for current shift
     */
    public function getCount()
    {
        helper('shift');
        
        $count = get_shift_transaction_count();
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['count' => $count]);
        }
        
        return $count;
    }
    
    /**
     * Get total amount for current shift
     */
    public function getTotalAmount()
    {
        helper('shift');
        
        $amount = get_shift_total_amount();
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['amount' => $amount]);
        }
        
        return $amount;
    }
    
    /**
     * Dashboard view with shift summary
     */
    public function dashboard()
    {
        helper('shift');
        
        $summary = get_shift_transaction_summary();
        
        return view('admin-lte-3/shift/dashboard', [
            'summary' => $summary,
            'title' => 'Shift Dashboard'
        ]);
    }
    
    /**
     * API endpoint for shift data
     */
    public function api()
    {
        helper('shift');
        
        $action = $this->request->getGet('action');
        
        switch ($action) {
            case 'count':
                $data = ['count' => get_shift_transaction_count()];
                break;
                
            case 'amount':
                $data = ['amount' => get_shift_total_amount()];
                break;
                
            case 'summary':
                $options = [];
                if ($this->request->getGet('status')) {
                    $options['status'] = $this->request->getGet('status');
                }
                $data = get_shift_transaction_summary(null, $options);
                break;
                
            default:
                $data = [
                    'success' => false,
                    'message' => 'Invalid action specified'
                ];
        }
        
        return $this->response->setJSON($data);
    }
}
