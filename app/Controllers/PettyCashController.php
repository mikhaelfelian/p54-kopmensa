<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PettyCashModel;
use App\Models\PettyCategoryModel;
use App\Models\OutletModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class PettyCashController extends BaseController
{
    protected $pettyCashModel;
    protected $categoryModel;
    protected $outletModel;
    protected $userModel;

    public function __construct()
    {
        $this->pettyCashModel = new PettyCashModel();
        $this->categoryModel = new PettyCategoryModel();
        $this->outletModel = new OutletModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display petty cash index page
     */
    public function index()
    {
        $filters = [
            'outlet_id' => $this->request->getGet('outlet_id'),
            'user_id' => $this->request->getGet('user_id'),
            'status' => $this->request->getGet('status'),
            'jenis' => $this->request->getGet('jenis'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'search' => $this->request->getGet('search')
        ];

        // Get data with pagination
        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 20;
        $offset = ($page - 1) * $perPage;

        $data = [
            'title' => 'Petty Cash',
            'pettyCashList' => $this->pettyCashModel->getPettyCashWithDetails($filters, $perPage, $offset),
            'totalRecords' => $this->pettyCashModel->getPettyCashWithDetails($filters)->countAllResults(),
            'currentPage' => $page,
            'perPage' => $perPage,
            'filters' => $filters,
            'outlets' => $this->outletModel->getActiveOutlets(),
            'users' => $this->userModel->getActiveUsers(),
            'categories' => $this->categoryModel->getActiveCategories()
        ];

        return view('admin-lte-3/petty/index', $data);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Petty Cash',
            'outlets' => $this->outletModel->getActiveOutlets(),
            'categories' => $this->categoryModel->getActiveCategories(),
            'users' => $this->userModel->getActiveUsers()
        ];

        return view('admin-lte-3/petty/create', $data);
    }

    /**
     * Store new petty cash
     */
    public function store()
    {
        $rules = [
            'id_outlet' => 'required|integer',
            'id_kategori' => 'required|integer',
            'tgl_transaksi' => 'required|valid_date',
            'jenis' => 'required|in_list[masuk,keluar]',
            'nominal' => 'required|numeric|greater_than[0]',
            'keterangan' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'id_user' => session()->get('user_id'),
            'id_outlet' => $this->request->getPost('id_outlet'),
            'id_kategori' => $this->request->getPost('id_kategori'),
            'tgl_transaksi' => $this->request->getPost('tgl_transaksi'),
            'jenis' => $this->request->getPost('jenis'),
            'nominal' => str_replace(',', '', $this->request->getPost('nominal')),
            'keterangan' => $this->request->getPost('keterangan'),
            'status' => 'pending'
        ];

        if ($this->pettyCashModel->insert($data)) {
            return redirect()->to('petty')->with('success', 'Petty cash berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan petty cash');
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $pettyCash = $this->pettyCashModel->find($id);
        
        if (!$pettyCash) {
            return redirect()->to('petty')->with('error', 'Data tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Petty Cash',
            'pettyCash' => $pettyCash,
            'outlets' => $this->outletModel->getActiveOutlets(),
            'categories' => $this->categoryModel->getActiveCategories(),
            'users' => $this->userModel->getActiveUsers()
        ];

        return view('admin-lte-3/petty/edit', $data);
    }

    /**
     * Update petty cash
     */
    public function update($id)
    {
        $pettyCash = $this->pettyCashModel->find($id);
        
        if (!$pettyCash) {
            return redirect()->to('petty')->with('error', 'Data tidak ditemukan');
        }

        $rules = [
            'id_outlet' => 'required|integer',
            'id_kategori' => 'required|integer',
            'tgl_transaksi' => 'required|valid_date',
            'jenis' => 'required|in_list[masuk,keluar]',
            'nominal' => 'required|numeric|greater_than[0]',
            'keterangan' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'id_outlet' => $this->request->getPost('id_outlet'),
            'id_kategori' => $this->request->getPost('id_kategori'),
            'tgl_transaksi' => $this->request->getPost('tgl_transaksi'),
            'jenis' => $this->request->getPost('jenis'),
            'nominal' => str_replace(',', '', $this->request->getPost('nominal')),
            'keterangan' => $this->request->getPost('keterangan')
        ];

        if ($this->pettyCashModel->update($id, $data)) {
            return redirect()->to('petty')->with('success', 'Petty cash berhasil diupdate');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate petty cash');
        }
    }

    /**
     * Delete petty cash
     */
    public function delete($id)
    {
        $pettyCash = $this->pettyCashModel->find($id);
        
        if (!$pettyCash) {
            return redirect()->to('petty')->with('error', 'Data tidak ditemukan');
        }

        if ($pettyCash->status !== 'pending') {
            return redirect()->to('petty')->with('error', 'Hanya data pending yang dapat dihapus');
        }

        if ($this->pettyCashModel->delete($id)) {
            return redirect()->to('petty')->with('success', 'Petty cash berhasil dihapus');
        } else {
            return redirect()->to('petty')->with('error', 'Gagal menghapus petty cash');
        }
    }

    /**
     * Show petty cash detail
     */
    public function show($id)
    {
        $pettyCash = $this->pettyCashModel->getPettyCashWithDetails(['id' => $id]);
        
        if (empty($pettyCash)) {
            return redirect()->to('petty')->with('error', 'Data tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Petty Cash',
            'pettyCash' => $pettyCash[0]
        ];

        return view('admin-lte-3/petty/show', $data);
    }

    /**
     * Approve petty cash
     */
    public function approve($id)
    {
        $pettyCash = $this->pettyCashModel->find($id);
        
        if (!$pettyCash) {
            return redirect()->to('petty')->with('error', 'Data tidak ditemukan');
        }

        if ($pettyCash->status !== 'pending') {
            return redirect()->to('petty')->with('error', 'Hanya data pending yang dapat diapprove');
        }

        $data = [
            'status' => 'approved',
            'approved_by' => session()->get('user_id'),
            'approved_at' => date('Y-m-d H:i:s')
        ];

        if ($this->pettyCashModel->update($id, $data)) {
            return redirect()->to('petty')->with('success', 'Petty cash berhasil diapprove');
        } else {
            return redirect()->to('petty')->with('error', 'Gagal approve petty cash');
        }
    }

    /**
     * Reject petty cash
     */
    public function reject($id)
    {
        $pettyCash = $this->pettyCashModel->find($id);
        
        if (!$pettyCash) {
            return redirect()->to('petty')->with('error', 'Data tidak ditemukan');
        }

        if ($pettyCash->status !== 'pending') {
            return redirect()->to('petty')->with('error', 'Hanya data pending yang dapat direject');
        }

        $reason = $this->request->getPost('reason');
        if (empty($reason)) {
            return redirect()->back()->with('error', 'Alasan rejection harus diisi');
        }

        $data = [
            'status' => 'rejected',
            'rejected_by' => session()->get('user_id'),
            'rejected_at' => date('Y-m-d H:i:s'),
            'rejection_reason' => $reason
        ];

        if ($this->pettyCashModel->update($id, $data)) {
            return redirect()->to('petty')->with('success', 'Petty cash berhasil direject');
        } else {
            return redirect()->to('petty')->with('error', 'Gagal reject petty cash');
        }
    }

    /**
     * Void petty cash
     */
    public function void($id)
    {
        $pettyCash = $this->pettyCashModel->find($id);
        
        if (!$pettyCash) {
            return redirect()->to('petty')->with('error', 'Data tidak ditemukan');
        }

        if ($pettyCash->status === 'void') {
            return redirect()->to('petty')->with('error', 'Data sudah di-void');
        }

        $reason = $this->request->getPost('reason');
        if (empty($reason)) {
            return redirect()->back()->with('error', 'Alasan void harus diisi');
        }

        $data = [
            'status' => 'void',
            'rejected_by' => session()->get('user_id'),
            'rejected_at' => date('Y-m-d H:i:s'),
            'rejection_reason' => $reason
        ];

        if ($this->pettyCashModel->update($id, $data)) {
            return redirect()->to('petty')->with('success', 'Petty cash berhasil di-void');
        } else {
            return redirect()->to('petty')->with('error', 'Gagal void petty cash');
        }
    }

    /**
     * Get petty cash summary
     */
    public function summary()
    {
        $outletId = $this->request->getGet('outlet_id');
        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-01');
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-t');

        $data = [
            'title' => 'Ringkasan Petty Cash',
            'outlets' => $this->outletModel->getActiveOutlets(),
            'selectedOutlet' => $outletId,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'summaryByOutlet' => $this->pettyCashModel->getSummaryByOutlet($outletId, $dateFrom, $dateTo),
            'summaryByCategory' => $this->pettyCashModel->getSummaryByCategory($outletId, $dateFrom, $dateTo),
            'totalSummary' => $outletId ? $this->pettyCashModel->getTotalByOutletAndDate($outletId, $dateFrom, $dateTo) : null
        ];

        return view('admin-lte-3/petty/summary', $data);
    }

    /**
     * Export petty cash data
     */
    public function export()
    {
        $filters = [
            'outlet_id' => $this->request->getGet('outlet_id'),
            'user_id' => $this->request->getGet('user_id'),
            'status' => $this->request->getGet('status'),
            'jenis' => $this->request->getGet('jenis'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'search' => $this->request->getGet('search')
        ];

        $data = $this->pettyCashModel->getPettyCashWithDetails($filters);

        // Create CSV export
        $filename = 'petty_cash_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'No', 'Tanggal', 'Outlet', 'Kategori', 'Jenis', 'Nominal', 
            'Keterangan', 'Status', 'User', 'Approved By', 'Approved At'
        ]);
        
        $no = 1;
        foreach ($data as $row) {
            fputcsv($output, [
                $no++,
                $row->tgl_transaksi,
                $row->outlet_name ?? 'N/A',
                $row->kategori_nama ?? 'N/A',
                ucfirst($row->jenis),
                number_format($row->nominal, 0, ',', '.'),
                $row->keterangan,
                ucfirst($row->status),
                ($row->user_name ?? '') . ' ' . ($row->user_lastname ?? ''),
                ($row->approver_name ?? '') . ' ' . ($row->approver_lastname ?? ''),
                $row->approved_at ?? 'N/A'
            ]);
        }
        
        fclose($output);
        exit;
    }
}
