<?php

namespace App\Controllers\Transaksi;

use App\Controllers\BaseController;
use App\Models\PettyModel;
use App\Models\ShiftModel;
use App\Models\PettyCategoryModel;
use App\Models\GudangModel;

class PettyController extends BaseController
{
    protected $pettyModel;
    protected $shiftModel;
    protected $categoryModel;
    protected $gudangModel;
    protected $ionAuth;

    public function __construct()
    {
        $this->pettyModel = new PettyModel();
        $this->shiftModel = new ShiftModel();
        $this->categoryModel = new PettyCategoryModel();
        $this->gudangModel = new GudangModel();
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
    }

    public function index()
    {
        $filters = [
            'outlet_id' => session()->get('outlet_id'),
            'date_from' => $this->request->getGet('date_from') ?? date('Y-m-d', strtotime('-30 days')),
            'date_to' => $this->request->getGet('date_to') ?? date('Y-m-d'),
            'direction' => $this->request->getGet('direction') ?? '',
            'status' => $this->request->getGet('status') ?? ''
        ];

        // Pagination
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        // Get total records for pagination
        $totalRecords = $this->pettyModel->getTotalRecords($filters);
        
        // Get paginated data
        $pettyEntries = $this->pettyModel->getPettyCashWithDetails($filters, $perPage, $offset);

        $data = array_merge($this->data, [
            'title' => 'Petty Cash Management',
            'pettyEntries' => $pettyEntries,
            'summary' => $this->pettyModel->getPettyCashSummaryByOutlet($filters['outlet_id'], $filters['date_from'], $filters['date_to']),
            'filters' => $filters,
            'outlets' => $this->gudangModel->getActiveOutlets(),
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalRecords' => $totalRecords
        ]);
        
        return view('admin-lte-3/petty/index', $data);
    }

    public function create()
    {
        // Check if there's an active shift
        $outlet_id = session()->get('outlet_id');
        $activeShift = $this->shiftModel->getActiveShift($outlet_id);

        // Prepare variables for form repopulation
        $direction   = $this->request->getPost('direction')   ?? '';
        $amount      = $this->request->getPost('amount')      ?? '';
        $reason      = $this->request->getPost('reason')      ?? '';
        $category_id = $this->request->getPost('category_id') ?? '';
        $ref_no      = $this->request->getPost('ref_no')      ?? '';

        if (!$activeShift) {
            session()->setFlashdata('error', 'Tidak ada shift aktif. Silakan buka shift terlebih dahulu.');
            return redirect()->to('/transaksi/petty');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'direction'    => 'required|in_list[IN,OUT]',
                'amount'       => 'required',
                'reason'       => 'required|max_length[255]',
                'category_id'  => 'permit_empty|integer',
            ];

            // Use format_angka_db for amount before validation and insert
            $amount_db = format_angka_db($amount);

            // Overwrite amount in $_POST for validation and insert
            $_POST['amount'] = $amount_db;

            if ($this->validate($rules)) {
                $data = [
                    'shift_id'      => $activeShift['id'],
                    'outlet_id'     => $outlet_id,
                    'kasir_user_id' => $this->ionAuth->user()->row()->id,
                    'category_id'   => $category_id ?: null,
                    'direction'     => $direction,
                    'amount'        => $amount_db,
                    'reason'        => $reason,
                    'ref_no'        => $ref_no ?: null,
                    'status'        => 'posted', // Auto approve for now
                ];

                if ($this->pettyModel->insert($data)) {
                    // Update shift petty totals
                    $this->updateShiftPettyTotals($activeShift['id']);

                    session()->setFlashdata('success', 'Petty cash berhasil ditambahkan');
                    return redirect()->to('/transaksi/petty');
                } else {
                    session()->setFlashdata('error', 'Gagal menambahkan petty cash');
                }
            } else {
                session()->setFlashdata('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
            }
        }

        $data = array_merge($this->data, [
            'title' => 'Tambah Petty Cash',
            'activeShift' => $activeShift,
            'categories' => $this->categoryModel->getActiveCategories(),
            'direction' => $direction,
            'amount' => $amount,
            'reason' => $reason,
            'category_id' => $category_id,
            'ref_no' => $ref_no
        ]);

        return view('admin-lte-3/petty/create', $data);
    }

    public function store()
    {
        // Check if there's an active shift
        $outlet_id = session()->get('outlet_id');
        $activeShift = $this->shiftModel->getActiveShift($outlet_id);

        if (!$activeShift) {
            session()->setFlashdata('error', 'Tidak ada shift aktif. Silakan buka shift terlebih dahulu.');
            return redirect()->to('/transaksi/petty');
        }

        $rules = [
            'direction'    => 'required|in_list[IN,OUT]',
            'amount'       => 'required',
            'reason'       => 'required|max_length[255]',
            'category_id'  => 'permit_empty|integer',
        ];

        if ($this->validate($rules)) {
            // Use format_angka_db for amount before validation and insert
            $amount = $this->request->getPost('amount');
            $amount_db = format_angka_db($amount);

            $data = [
                'shift_id'      => $activeShift['id'],
                'outlet_id'     => $outlet_id,
                'kasir_user_id' => $this->ionAuth->user()->row()->id,
                'category_id'   => $this->request->getPost('category_id') ?: null,
                'direction'     => $this->request->getPost('direction'),
                'amount'        => $amount_db,
                'reason'        => $this->request->getPost('reason'),
                'ref_no'        => $this->request->getPost('ref_no') ?: null,
                'status'        => 'posted', // Auto approve for now
            ];

            if ($this->pettyModel->insert($data)) {
                // Update shift petty totals
                $this->updateShiftPettyTotals($activeShift['id']);

                session()->setFlashdata('success', 'Petty cash berhasil ditambahkan');
                return redirect()->to('/transaksi/petty');
            } else {
                session()->setFlashdata('error', 'Gagal menambahkan petty cash');
            }
        } else {
            session()->setFlashdata('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
        }

        return redirect()->back()->withInput();
    }

    public function edit($id)
    {
        $petty = $this->pettyModel->getPettyCashWithDetails(['id' => $id]);
        if (empty($petty)) {
            session()->setFlashdata('error', 'Petty cash tidak ditemukan');
            return redirect()->to('/transaksi/petty');
        }
        $petty = $petty[0]; // Get first result

        // Check if can edit (only draft or posted status)
        if ($petty['status'] === 'void') {
            session()->setFlashdata('error', 'Petty cash yang sudah di-void tidak dapat diedit');
            return redirect()->to('/transaksi/petty');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'direction' => 'required|in_list[IN,OUT]',
                'amount' => 'required|decimal',
                'reason' => 'required|max_length[255]',
                'category_id' => 'permit_empty|integer'
            ];

            if ($this->validate($rules)) {
                $data = [
                    'category_id' => $this->request->getPost('category_id') ?: null,
                    'direction' => $this->request->getPost('direction'),
                    'amount' => $this->request->getPost('amount'),
                    'reason' => $this->request->getPost('reason'),
                    'ref_no' => $this->request->getPost('ref_no') ?: null
                ];

                if ($this->pettyModel->update($id, $data)) {
                    // Update shift petty totals
                    $this->updateShiftPettyTotals($petty['shift_id']);
                    
                    session()->setFlashdata('success', 'Petty cash berhasil diupdate');
                    return redirect()->to('/transaksi/petty');
                } else {
                    session()->setFlashdata('error', 'Gagal mengupdate petty cash');
                }
            } else {
                session()->setFlashdata('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
            }
        }

        $data = array_merge($this->data, [
            'title' => 'Edit Petty Cash',
            'petty' => $petty,
            'categories' => $this->categoryModel->getActiveCategories()
        ]);
        
        return view('admin-lte-3/petty/edit', $data);
    }

    public function viewDetail($id)
    {
        $petty = $this->pettyModel->getPettyCashWithDetails(['id' => $id]);
        if (empty($petty)) {
            session()->setFlashdata('error', 'Petty cash tidak ditemukan');
            return redirect()->to('/transaksi/petty');
        }
        $petty = $petty[0]; // Get first result

        $data = array_merge($this->data, [
            'title' => 'Detail Petty Cash',
            'petty' => $petty
        ]);
        
        return view('admin-lte-3/petty/view', $data);
    }

    public function approve($id)
    {
        $petty = $this->pettyModel->find($id);
        if (!$petty) {
            session()->setFlashdata('error', 'Petty cash tidak ditemukan');
            return redirect()->to('/transaksi/petty');
        }

        if ($petty['status'] !== 'draft') {
            session()->setFlashdata('error', 'Hanya petty cash dengan status draft yang dapat disetujui');
            return redirect()->to('/transaksi/petty');
        }

        $approved_by = $this->ionAuth->user()->row()->id;
        
        if ($this->pettyModel->approvePettyCash($id, $approved_by)) {
            session()->setFlashdata('success', 'Petty cash berhasil disetujui');
        } else {
            session()->setFlashdata('error', 'Gagal menyetujui petty cash');
        }
        
        return redirect()->to('/transaksi/petty');
    }

    public function void($id)
    {
        $petty = $this->pettyModel->find($id);
        if (!$petty) {
            session()->setFlashdata('error', 'Petty cash tidak ditemukan');
            return redirect()->to('/transaksi/petty');
        }

        if ($petty['status'] === 'void') {
            session()->setFlashdata('error', 'Petty cash sudah di-void');
            return redirect()->to('/transaksi/petty');
        }

        $reason = $this->request->getPost('reason') ?? 'Divoid oleh user';
        
        if ($this->pettyModel->voidPettyCash($id, $reason)) {
            // Update shift petty totals
            $this->updateShiftPettyTotals($petty['shift_id']);
            
            session()->setFlashdata('success', 'Petty cash berhasil di-void');
        } else {
            session()->setFlashdata('error', 'Gagal void petty cash');
        }
        
        return redirect()->to('/transaksi/petty');
    }

    public function delete($id)
    {
        $petty = $this->pettyModel->find($id);
        if (!$petty) {
            session()->setFlashdata('error', 'Petty cash tidak ditemukan');
            return redirect()->to('/transaksi/petty');
        }

        if ($petty['status'] !== 'draft') {
            session()->setFlashdata('error', 'Hanya petty cash dengan status draft yang dapat dihapus');
            return redirect()->to('/transaksi/petty');
        }

        if ($this->pettyModel->delete($id)) {
            session()->setFlashdata('success', 'Petty cash berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus petty cash');
        }
        
        return redirect()->to('/transaksi/petty');
    }

    public function getPendingApprovals()
    {
        $outlet_id = session()->get('outlet_id');
        $pendingApprovals = $this->pettyModel->getPendingApprovals($outlet_id);
        
        $data = array_merge($this->data, [
            'title' => 'Pending Approvals',
            'pendingApprovals' => $pendingApprovals
        ]);
        
        return view('admin-lte-3/petty/pending_approvals', $data);
    }

    public function getCategoryReport()
    {
        $outlet_id = session()->get('outlet_id');
        $date_from = $this->request->getGet('date_from') ?? date('Y-m-d', strtotime('-30 days'));
        $date_to = $this->request->getGet('date_to') ?? date('Y-m-d');
        
        $data = array_merge($this->data, [
            'title' => 'Laporan Petty Cash per Kategori',
            'categoryReport' => $this->pettyModel->getPettyCashByCategory($outlet_id, $date_from, $date_to),
            'filters' => [
                'date_from' => $date_from,
                'date_to' => $date_to
            ]
        ]);
        
        return view('admin-lte-3/petty/category_report', $data);
    }

    public function getSummary()
    {
        $outlet_id = session()->get('outlet_id');
        $date_from = $this->request->getGet('date_from') ?? date('Y-m-d', strtotime('-30 days'));
        $date_to = $this->request->getGet('date_to') ?? date('Y-m-d');
        
        $data = array_merge($this->data, [
            'title' => 'Ringkasan Petty Cash',
            'summary' => $this->pettyModel->getSummaryByOutlet($outlet_id, $date_from, $date_to),
            'categorySummary' => $this->pettyModel->getSummaryByCategory($outlet_id, $date_from, $date_to),
            'filters' => [
                'date_from' => $date_from,
                'date_to' => $date_to
            ]
        ]);
        
        return view('admin-lte-3/petty/summary', $data);
    }

    private function updateShiftPettyTotals($shift_id)
    {
        $summary = $this->pettyModel->getPettyCashSummaryByShift($shift_id);
        
        $this->shiftModel->updatePettyTotals(
            $shift_id, 
            $summary['total_in'] ?? 0, 
            $summary['total_out'] ?? 0
        );
    }

    // API Methods for AJAX calls
    public function apiCreate()
    {
        $outlet_id = session()->get('outlet_id');
        $activeShift = $this->shiftModel->getActiveShift($outlet_id);
        
        if (!$activeShift) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada shift aktif'
            ]);
        }

        $direction = $this->request->getPost('direction');
        $amount = $this->request->getPost('amount');
        $reason = $this->request->getPost('reason');
        $category_id = $this->request->getPost('category_id') ?: null;

        if (!$direction || !$amount || !$reason) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Semua field harus diisi'
            ]);
        }

        $data = [
            'shift_id' => $activeShift['id'],
            'outlet_id' => $outlet_id,
            'kasir_user_id' => $this->ionAuth->user()->row()->id,
            'category_id' => $category_id,
            'direction' => $direction,
            'amount' => $amount,
            'reason' => $reason,
            'status' => 'posted'
        ];

        if ($this->pettyModel->insert($data)) {
            // Update shift petty totals
            $this->updateShiftPettyTotals($activeShift['id']);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Petty cash berhasil ditambahkan'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menambahkan petty cash'
            ]);
        }
    }
}
