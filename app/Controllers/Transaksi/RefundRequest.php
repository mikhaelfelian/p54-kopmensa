<?php

namespace App\Controllers\Transaksi;

use App\Controllers\BaseController;
use App\Models\RefundRequestModel;
use App\Models\TransJualModel;
use App\Models\PelangganModel;

/**
 * Refund Request Controller
 * 
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Description: Controller for managing refund requests
 */
class RefundRequest extends BaseController
{
    protected $refundRequestModel;
    protected $transJualModel;
    protected $pelangganModel;

    public function __construct()
    {
        $this->refundRequestModel = new RefundRequestModel();
        $this->transJualModel = new TransJualModel();
        $this->pelangganModel = new PelangganModel();
    }

    /**
     * Display list of refund requests (for cashiers to view their requests)
     */
    public function index()
    {
        $currentPage = (int) ($this->request->getVar('page_refund') ?? 1);
        $perPage = (int) $this->pengaturan->pagination_limit;
        $search = $this->request->getVar('search');
        $status = $this->request->getVar('status');

        // Get refund requests for current user (cashier) or all if superadmin
        $offset = ($currentPage - 1) * $perPage;
        $refundRequests = $this->refundRequestModel->getRefundRequestsWithRelations($perPage, $offset, $search, $status);

        // Get total count for pagination
        $totalRefunds = $this->refundRequestModel->countAllResults();

        // Create pagination
        $pager = \Config\Services::pager();

        $data = [
            'title' => 'Daftar Permintaan Refund',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'refundRequests' => $refundRequests,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'pager' => $pager,
            'totalRefunds' => $totalRefunds,
            'search' => $search,
            'status' => $status
        ];

        return $this->view($this->theme->getThemePath() . '/transaksi/refund/index', $data);
    }

    /**
     * Display refund request form for cashiers
     */
    public function create()
    {
        // Get sales transactions that haven't been refunded
        $salesTransactions = $this->transJualModel
            ->select('tbl_trans_jual.*, COALESCE(tbl_m_pelanggan.nama, "UMUM") as customer_nama')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
            ->where('tbl_trans_jual.status_bayar', '1') // Only paid transactions
            ->where('tbl_trans_jual.status_retur', '0') // Not returned
            ->orderBy('tbl_trans_jual.created_at', 'DESC')
            ->findAll();



        $data = [
            'title' => 'Buat Permintaan Refund',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'salesTransactions' => $salesTransactions
        ];

        return $this->view($this->theme->getThemePath() . '/transaksi/refund/create', $data);
    }

    /**
     * Store new refund request
     */
    public function store()
    {
        // Validation rules
        $rules = [
            'id_transaction' => 'required|integer',
            'amount' => 'required|numeric',
            'reason' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('transaksi/refund/create'))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            // Get sales transaction data
            $transaction = $this->transJualModel->find($this->request->getPost('id_transaction'));
            if (!$transaction) {
                throw new \Exception('Transaksi tidak ditemukan');
            }

            // Check if refund request already exists for this transaction
            $existingRefund = $this->refundRequestModel
                ->where('id_transaction', $this->request->getPost('id_transaction'))
                ->first();

            if ($existingRefund) {
                throw new \Exception('Permintaan refund untuk transaksi ini sudah ada');
            }

            // Validate amount
            $amount = (float) $this->request->getPost('amount');
            if ($amount > $transaction->jml_gtotal) {
                throw new \Exception('Jumlah refund tidak boleh melebihi total transaksi');
            }

            // Handle customer ID - if null or invalid, get or create default UMUM customer
            $customerId = $transaction->id_pelanggan;
            if (empty($customerId) || $customerId <= 0) {
                // Get or create default UMUM customer
                $umumCustomer = $this->pelangganModel
                    ->where('nama', 'UMUM')
                    ->where('tipe', '2') // tipe 2 = umum
                    ->first();
                
                if (!$umumCustomer) {
                    // Create default UMUM customer
                    $umumData = [
                        'id_user' => 0, // No user account for general customer
                        'kode' => 'UMUM001',
                        'nama' => 'UMUM',
                        'no_telp' => '',
                        'alamat' => '',
                        'kota' => '',
                        'provinsi' => '',
                        'tipe' => '2', // 2 = umum
                        'status' => '1',
                        'status_hps' => '0',
                        'status_blokir' => '0',
                        'limit' => 0.00
                    ];
                    $customerId = $this->pelangganModel->insert($umumData);
                } else {
                    $customerId = $umumCustomer->id;
                }
            }

            // Create refund request
            $refundData = [
                'id_transaction' => $this->request->getPost('id_transaction'),
                'id_user' => $this->ionAuth->user()->row()->id,
                'id_pelanggan' => $customerId,
                'no_nota' => $transaction->no_nota,
                'amount' => $amount,
                'reason' => $this->request->getPost('reason'),
                'status' => 'pending'
            ];

            $refundId = $this->refundRequestModel->insert($refundData);

            if (!$refundId) {
                throw new \Exception('Gagal membuat permintaan refund');
            }

            return redirect()->to('transaksi/refund')
                ->with('success', 'Permintaan refund berhasil dibuat dan menunggu persetujuan');

        } catch (\Exception $e) {
            return redirect()->to(current_url())
                ->withInput()
                ->with('error', 'Gagal membuat permintaan refund: ' . $e->getMessage());
        }
    }

    /**
     * Display refund request details
     */
    public function show($id)
    {
        $refundRequest = $this->refundRequestModel->getRefundRequestWithRelations($id);

        if (!$refundRequest) {
            return redirect()->to('transaksi/refund')
                ->with('error', 'Data permintaan refund tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Permintaan Refund',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'refundRequest' => $refundRequest
        ];

        return $this->view($this->theme->getThemePath() . '/transaksi/refund/show', $data);
    }

    /**
     * Display refund approval dashboard (for superadmin)
     */
    public function approval()
    {
        $currentPage = (int) ($this->request->getVar('page_refund') ?? 1);
        $perPage = (int) $this->pengaturan->pagination_limit;
        $search = $this->request->getVar('search');
        $status = $this->request->getVar('status');

        // Get pending refund requests
        $offset = ($currentPage - 1) * $perPage;
        $refundRequests = $this->refundRequestModel->getRefundRequestsWithRelations($perPage, $offset, $search, $status);

        // Get total count for pagination
        $totalRefunds = $this->refundRequestModel->countAllResults();

        // Get pending count
        $pendingCount = $this->refundRequestModel->getPendingCount();

        // Create pagination
        $pager = \Config\Services::pager();

        $data = [
            'title' => 'Dashboard Persetujuan Refund',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'refundRequests' => $refundRequests,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'pager' => $pager,
            'totalRefunds' => $totalRefunds,
            'pendingCount' => $pendingCount,
            'search' => $search,
            'status' => $status
        ];

        return $this->view($this->theme->getThemePath() . '/transaksi/refund/approval', $data);
    }

    /**
     * Approve refund request
     */
    public function approve($id)
    {
        try {
            $refundRequest = $this->refundRequestModel->find($id);
            if (!$refundRequest) {
                throw new \Exception('Permintaan refund tidak ditemukan');
            }

            if ($refundRequest->status !== 'pending') {
                throw new \Exception('Permintaan refund sudah diproses');
            }

            // Approve the refund
            $this->refundRequestModel->approveRefund($id, $this->ionAuth->user()->row()->id);

            return redirect()->to('transaksi/refund/approval')
                ->with('success', 'Permintaan refund berhasil disetujui');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyetujui refund: ' . $e->getMessage());
        }
    }

    /**
     * Reject refund request
     */
    public function reject($id)
    {
        try {
            $refundRequest = $this->refundRequestModel->find($id);
            if (!$refundRequest) {
                throw new \Exception('Permintaan refund tidak ditemukan');
            }

            if ($refundRequest->status !== 'pending') {
                throw new \Exception('Permintaan refund sudah diproses');
            }

            $rejectionReason = $this->request->getPost('rejection_reason');
            if (empty($rejectionReason)) {
                throw new \Exception('Alasan penolakan wajib diisi');
            }

            // Reject the refund
            $this->refundRequestModel->rejectRefund($id, $this->ionAuth->user()->row()->id, $rejectionReason);

            return redirect()->to('transaksi/refund/approval')
                ->with('success', 'Permintaan refund berhasil ditolak');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menolak refund: ' . $e->getMessage());
        }
    }

    /**
     * Get transaction details for AJAX
     */
    public function getTransactionDetails($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        try {
            $transaction = $this->transJualModel
                ->select('tbl_trans_jual.*, COALESCE(tbl_m_pelanggan.nama, "UMUM") as customer_name')
                ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
                ->find($id);

            if (!$transaction) {
                return $this->response->setJSON(['success' => false, 'message' => 'Transaksi tidak ditemukan']);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'id' => $transaction->id,
                    'no_nota' => $transaction->no_nota,
                    'customer_name' => $transaction->customer_name,
                    'amount' => $transaction->jml_gtotal,
                    'date' => $transaction->tgl_masuk
                ]
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengambil data transaksi: ' . $e->getMessage()]);
        }
    }
}
