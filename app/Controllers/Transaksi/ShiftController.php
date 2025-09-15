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
        
        $data = array_merge($this->data, [
            'title' => 'Shift Management',
            'shifts' => $shifts,
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
        
        $data = array_merge($this->data, [
            'title' => 'Buka Shift Baru',
            'outlets' => $this->gudangModel->getOutletsForDropdown()
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

        // Get petty cash summary
        $pettySummary = $this->pettyModel->getPettyCashSummaryByShift($shift_id);
        
        // Get sales summary
        $salesSummary = $this->transJualModel->getSalesSummaryByShift($shift_id);

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
        $shift = $this->shiftModel->getShiftWithDetails($shift_id);
        if (!$shift) {
            session()->setFlashdata('error', 'Shift tidak ditemukan');
            return redirect()->to('/transaksi/shift');
        }

        // Get petty cash entries
        $pettyEntries = $this->pettyModel->getPettyCashWithDetails(['shift_id' => $shift_id]);
        
        // Get sales entries
        $salesEntries = $this->transJualModel->getSalesByShift($shift_id);

        $data = array_merge($this->data, [
            'title'         => 'Detail Shift',
            'shift'         => $shift,
            'pettyEntries'  => $pettyEntries,
            'salesEntries'  => $salesEntries,
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

        $data = [
            'shift_code' => $this->generateShiftCode($outlet_id),
            'outlet_id' => $outlet_id,
                                'user_open_id' => $this->ionAuth->user()->row()->id,
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
