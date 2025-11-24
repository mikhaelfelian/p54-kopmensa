<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github: github.com/mikhaelfelian
 * Description: Controller for handling sales transactions and cashier operations
 * This file represents the Controller.
 */

namespace App\Controllers\Transaksi;

use App\Controllers\BaseController;
use App\Models\TransJualModel;
use App\Models\TransJualDetModel;
use App\Models\TransJualPlatModel;
use App\Models\PelangganModel;
use App\Models\ItemModel;
use App\Models\ItemStokModel;
use App\Models\KaryawanModel;
use App\Models\GudangModel;
use App\Models\PlatformModel;
use App\Models\OutletPlatformModel;
use App\Models\ItemHistModel;
use App\Models\VoucherModel;
use App\Models\PengaturanModel;
use App\Models\KategoriModel;
use App\Models\ShiftModel;


class TransJual extends BaseController
{
    protected $transJualModel;
    protected $transJualDetModel;
    protected $transJualPlatModel;
    protected $paymentGuard;
    protected $pelangganModel;
    protected $itemModel;
    protected $itemStokModel;
    protected $karyawanModel;
    protected $gudangModel;
    protected $platformModel;
    protected $outletPlatformModel;
    protected $itemHistModel;
    protected $voucherModel;
    protected $pengaturanModel;
    protected $kategoriModel;
    protected $shiftModel;
    protected $ionAuth;
    protected $db;


    public function __construct()
    {
        $this->transJualModel      = new TransJualModel();
        $this->transJualDetModel   = new TransJualDetModel();
        $this->transJualPlatModel  = new TransJualPlatModel();
        // Payment guard to enforce blocked-member rule for Piutang
        $this->paymentGuard        = new \App\Libraries\PaymentGuard();
        $this->pelangganModel      = new PelangganModel();
        $this->itemModel           = new ItemModel();
        $this->itemStokModel       = new ItemStokModel();
        $this->karyawanModel       = new KaryawanModel();
        $this->gudangModel         = new GudangModel();
        $this->platformModel       = new PlatformModel();
        $this->outletPlatformModel = new OutletPlatformModel();
        $this->itemHistModel       = new ItemHistModel();
        $this->voucherModel        = new VoucherModel();
        $this->pengaturanModel     = new PengaturanModel();
        $this->kategoriModel       = new KategoriModel();
        $this->shiftModel          = new ShiftModel();
        $this->ionAuth             = new \IonAuth\Libraries\IonAuth();
        $this->db                  = \Config\Database::connect();
    }

    /**
     * Initialize controller - this is called automatically by CodeIgniter
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        // Call parent initController to set up basic functionality
        parent::initController($request, $response, $logger);
        
        // Additional initialization specific to TransJual if needed
    }

    /**
     * Check if there's an active shift for the current outlet
     */
    /**
     * Check if there's an active shift by querying the database table
     */
    private function checkActiveShift()
    {
        // Skip shift check for superadmin
        if (session()->get('group_id') == 1) {
            return true;
        }

        // Get outlet_id from session
        $outlet_id  = session()->get('kasir_outlet');
        $user_id    = $this->ionAuth->user()->row()->id;

        // Use ShiftModel's getActiveShift method (see file_context_0)
        $activeShift = $this->shiftModel->getActiveShift($outlet_id, $user_id);

        if (empty($activeShift)) {
            // No active shift found, redirect to shift open page
            session()->setFlashdata('error', 'Tidak ada shift aktif. Silakan buka shift terlebih dahulu.');
            return false;
        }

        // Set shift data in session for later use
        session()->set([
            'active_shift_id'   => $activeShift['id'],
            'active_shift_code' => $activeShift['shift_code'],
            'kasir_shift'       => $activeShift['shift_code']
        ]);

        return true;
    }

    /**
     * Display cashier interface for sales transactions
     */
    public function index()
    {
        // Check shift status first
        if (!$this->checkActiveShift()) {
            return redirect()->to('transaksi/shift/open');
        }

        // Get current page for pagination
        $currentPage = $this->request->getVar('page_transjual') ?? 1;
        $perPage     = $this->pengaturan->pagination_limit ?? 10;

        // Get filter parameters
        $search   = $this->request->getVar('search');
        $status   = $this->request->getVar('status');
        $dateFrom = $this->request->getVar('date_from');
        $dateTo   = $this->request->getVar('date_to');

        // Build query
        $builder = $this->transJualModel;
        
        if ($search) {
            $builder = $builder->like('no_nota', $search)
                              ->orLike('id_pelanggan', $search);
        }
        
        if ($status !== null && $status !== '') {
            $builder = $builder->where('status', $status);
        }
        
        if ($dateFrom) {
            $builder = $builder->where('DATE(created_at) >=', $dateFrom);
        }
        
        if ($dateTo) {
            $builder = $builder->where('DATE(created_at) <=', $dateTo);
        }

        // Get paginated results
        $transactions = $builder->orderBy('created_at', 'DESC')
                               ->paginate($perPage, 'transjual');

        // Get summary data
        $totalSales = $this->transJualModel->selectSum('jml_gtotal')
                                          ->where('status', '1')
                                          ->where('DATE(created_at)', date('Y-m-d'))
                                          ->first();

        $totalTransactions = $this->transJualModel->where('status', '1')
                                                 ->where('DATE(created_at)', date('Y-m-d'))
                                                 ->countAllResults();

        // Get related data for dropdowns
        $customers  = $this->pelangganModel
                          ->where('status_blokir', '0')
                          ->findAll();

        $sales      = $this->karyawanModel
                          ->where('status', '1')
                          ->findAll();

        $warehouses = $this->gudangModel
                          ->where('status', '1')
                          ->findAll();

        $platforms  = $this->platformModel
                          ->where('status', '1')
                          ->findAll();

        // Get cashiers (users from IonAuth)
        $cashiers = [];
        try {
            $cashiers = $this->db->table('tbl_ion_users')
                          ->select('id, first_name, last_name, username, email')
                          ->where('active', '1')
                          ->get()
                          ->getResult();
        } catch (\Exception $e) {
            log_message('error', 'Error loading cashiers: ' . $e->getMessage());
            $cashiers = [];
        }

        $data = [
            'title'             => 'Kasir - Transaksi Penjualan',
            'Pengaturan'        => $this->pengaturan,
            'user'              => $this->ionAuth->user()->row(),
            'transactions'      => $transactions,
            'pager'             => $this->transJualModel->pager,
            'currentPage'       => $currentPage,
            'perPage'           => $perPage,
            'search'            => $search,
            'status'            => $status,
            'dateFrom'          => $dateFrom,
            'dateTo'            => $dateTo,
            'totalSales'        => $totalSales->jml_gtotal ?? 0,
            'totalTransactions' => $totalTransactions,
            'customers'         => $customers,
            'sales'             => $sales,
            'warehouses'        => $warehouses,
            'platforms'         => $platforms,
            'cashiers'          => $cashiers,
            'statusOptions'     => [
                '0' => 'Draft',
                '1' => 'Selesai',
                '2' => 'Batal',
                '3' => 'Retur',
                '4' => 'Pending'
            ]
        ];

        return $this->view($this->theme->getThemePath() . '/transaksi/jual/index', $data);
    }

    /**
     * Display Data Penjualan (Sales Data) page without sidebar
     */
    public function data_penjualan_kasir()
    {
        // Get current page for pagination
        $currentPage = $this->request->getVar('page_transjual') ?? 1;
        $perPage     = $this->pengaturan->pagination_limit ?? 10;

        // Get filter parameters
        $search   = $this->request->getVar('search');
        $status   = $this->request->getVar('status');
        $dateFrom = $this->request->getVar('date_from');
        $dateTo   = $this->request->getVar('date_to');
        $cashierFilter = $this->request->getVar('cashier_filter') ?? '';

        // Build query with joins to get store name, customer info, and cashier name
        $builder = $this->db->table('tbl_trans_jual')
            ->select('tbl_trans_jual.*, 
                tbl_m_gudang.nama as nama_toko,
                tbl_m_pelanggan.kode as no_anggota,
                tbl_m_pelanggan.nama as nama_pelanggan,
                CONCAT(tbl_ion_users.first_name, " ", tbl_ion_users.last_name) as nama_kasir,
                tbl_ion_users.username as username_kasir')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_trans_jual.id_gudang', 'left')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
            ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_jual.id_user', 'left');
        
        if ($search) {
            $builder->groupStart()
                ->like('tbl_trans_jual.no_nota', $search)
                ->orLike('tbl_trans_jual.id_pelanggan', $search)
                ->orLike('tbl_m_pelanggan.nama', $search)
                ->orLike('tbl_m_pelanggan.kode', $search)
                ->groupEnd();
        }
        
        if ($status !== null && $status !== '') {
            $builder->where('tbl_trans_jual.status', $status);
        }
        
        if ($dateFrom) {
            $builder->where('DATE(tbl_trans_jual.created_at) >=', $dateFrom);
        }
        
        if ($dateTo) {
            $builder->where('DATE(tbl_trans_jual.created_at) <=', $dateTo);
        }

        if ($cashierFilter) {
            $builder->where('tbl_trans_jual.id_user', $cashierFilter);
        }

        // Get total count for pagination
        $totalRows = $builder->countAllResults(false);
        
        // Get paginated results
        $transactions = $builder->orderBy('tbl_trans_jual.created_at', 'DESC')
                               ->limit($perPage, ($currentPage - 1) * $perPage)
                               ->get()
                               ->getResult();

        // Get payment methods for each transaction
        $transactionIds = array_column($transactions, 'id');
        $paymentMethods = [];
        if (!empty($transactionIds)) {
            $paymentData = $this->db->table('tbl_trans_jual_plat')
                ->select('id_penjualan, GROUP_CONCAT(CONCAT(platform, " (", FORMAT(nominal, 0), ")") SEPARATOR ", ") as metode_pembayaran')
                ->whereIn('id_penjualan', $transactionIds)
                ->groupBy('id_penjualan')
                ->get()
                ->getResult();
            
            foreach ($paymentData as $pm) {
                $paymentMethods[$pm->id_penjualan] = $pm->metode_pembayaran;
            }
        }

        // Attach payment methods to transactions
        foreach ($transactions as $transaction) {
            $transaction->metode_pembayaran = $paymentMethods[$transaction->id] ?? '-';
        }

        // Create pagination manually since we're using custom query
        $pager = \Config\Services::pager();
        $pager->store('transjual', $currentPage, $perPage, $totalRows);

        // Get summary data
        $totalSales = $this->transJualModel->selectSum('jml_gtotal')
                                          ->where('status', '1')
                                          ->where('DATE(created_at)', date('Y-m-d'))
                                          ->first();

        $totalTransactions = $this->transJualModel->where('status', '1')
                                                 ->where('DATE(created_at)', date('Y-m-d'))
                                                 ->countAllResults();

        // Get related data for dropdowns
        $customers  = $this->pelangganModel
                          ->where('status_blokir', '0')
                          ->findAll();

        $sales      = $this->karyawanModel
                          ->where('status', '1')
                          ->findAll();

        $warehouses = $this->gudangModel
                          ->where('status', '1')
                          ->findAll();

        $platforms  = $this->platformModel
                          ->where('status', '1')
                          ->findAll();

        // Get cashiers (users from IonAuth)
        $cashiers = [];
        try {
            $cashiers = $this->db->table('tbl_ion_users')
                          ->select('id, first_name, last_name, username, email')
                          ->where('active', '1')
                          ->get()
                          ->getResult();
        } catch (\Exception $e) {
            log_message('error', 'Error loading cashiers: ' . $e->getMessage());
            $cashiers = [];
        }

        $data = [
            'title'             => 'Data Penjualan',
            'Pengaturan'        => $this->pengaturan,
            'user'              => $this->ionAuth->user()->row(),
            'transactions'      => $transactions,
            'pager'             => $pager,
            'currentPage'       => $currentPage,
            'perPage'           => $perPage,
            'search'            => $search,
            'status'            => $status,
            'dateFrom'          => $dateFrom,
            'dateTo'            => $dateTo,
            'cashierFilter'     => $cashierFilter,
            'totalSales'        => $totalSales->jml_gtotal ?? 0,
            'totalTransactions' => $totalTransactions,
            'customers'         => $customers,
            'sales'             => $sales,
            'warehouses'        => $warehouses,
            'platforms'         => $platforms,
            'cashiers'          => $cashiers,
            'statusOptions'     => [
                '0' => 'Draft',
                '1' => 'Selesai',
                '2' => 'Batal',
                '3' => 'Retur',
                '4' => 'Pending'
            ]
        ];

        // Use a layout without sidebar
        return $this->view($this->theme->getThemePath() . '/transaksi/jual/index_no_sidebar', $data);
    }

    /**
     * Get transaction details by ID (AJAX)
     */
    public function getTransactionDetails($id)
    {
        // Get transaction with joins to get store name, customer info, and cashier name
        $transaction = $this->db->table('tbl_trans_jual')
            ->select('tbl_trans_jual.*, 
                tbl_m_gudang.nama as nama_toko,
                tbl_m_pelanggan.kode as no_anggota,
                tbl_m_pelanggan.nama as nama_pelanggan,
                CONCAT(tbl_ion_users.first_name, " ", tbl_ion_users.last_name) as nama_kasir,
                tbl_ion_users.username as username_kasir')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_trans_jual.id_gudang', 'left')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
            ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_jual.id_user', 'left')
            ->where('tbl_trans_jual.id', $id)
            ->get()
            ->getRow();
            
        if (!$transaction) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['error' => 'Transaction not found']);
            } else {
                return redirect()->to(base_url('transaksi/jual'))->with('error', 'Transaction not found');
            }
        }

        $details = $this->transJualDetModel->getDetailsWithItem($id);
        $platforms = $this->transJualPlatModel->getPlatformsWithInfo($id);

        // Get payment methods as formatted string
        $paymentMethods = [];
        if (!empty($platforms)) {
            foreach ($platforms as $platform) {
                $paymentMethods[] = $platform->platform . ' (' . format_angka($platform->nominal) . ')';
            }
        }
        $transaction->metode_pembayaran = !empty($paymentMethods) ? implode(', ', $paymentMethods) : '-';

        // If AJAX request, return JSON
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'     => true,
                'transaction' => $transaction,
                'details'     => $details,
                'platforms'   => $platforms,
            ]);
        }

        // pre($transaction);

        // If direct browser access, redirect to main transaction list with search
        return redirect()->to(base_url('transaksi/jual?search=' . $transaction->no_nota));
    }

    /**
     * Search items for cashier (AJAX)
     */
    public function searchItems()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        try {
            // Handle both GET and POST requests
            $search = $this->request->getVar('search');
            $warehouseId = $this->request->getVar('warehouse_id');
            $categoryId = $this->request->getVar('category_id');
            
            // Get pagination limit from database settings
            $paginationLimit = $this->pengaturan->pagination_limit ?? 20;

            // Ensure limit and offset are integers (fixes CI4 limit() type error)
            $limit = $this->request->getVar('limit');
            $offset = $this->request->getVar('offset');

            // Fallback to default if not set or not numeric
            $limit = (is_numeric($limit) && $limit > 0) ? (int)$limit : (int)$paginationLimit;
            $offset = (is_numeric($offset) && $offset >= 0) ? (int)$offset : 0;
            
            // Defensive: fallback to array if error
            $items = [];

            // If warehouse filter is applied, use ItemModel to get items by warehouse
            if ($warehouseId) {
                // Use Query Builder directly to avoid getCompiledSelect() error
                $builder = $this->db->table('tbl_m_item')
                    ->select('tbl_m_item.*, tbl_m_kategori.kategori, tbl_m_merk.merk, tbl_m_supplier.nama as supplier_nama, IFNULL(SUM(tbl_m_item_stok.jml),0) as stok')
                    ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
                    ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
                    ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_m_item.id_supplier', 'left')
                    ->join('tbl_m_item_stok', 'tbl_m_item_stok.id_item = tbl_m_item.id AND tbl_m_item_stok.id_gudang = ' . (int)$warehouseId, 'left')
                    ->where('tbl_m_item.status_hps', '0')
                    ->where('tbl_m_item.status', '1')
                    ->groupBy('tbl_m_item.id, tbl_m_item.status_stok')
                    ->orderBy('tbl_m_item.item', 'ASC');

                if ($search) {
                    $builder->groupStart()
                        ->like('tbl_m_item.item', $search)
                        ->orLike('tbl_m_item.kode', $search)
                        ->orLike('tbl_m_item.barcode', $search)
                        ->orLike('tbl_m_kategori.kategori', $search)
                        ->orLike('tbl_m_merk.merk', $search)
                        ->orLike('tbl_m_supplier.nama', $search)
                        ->groupEnd();
                }
                if ($categoryId) {
                    $builder->where('tbl_m_item.id_kategori', $categoryId);
                }

                $query = $builder->limit($limit, $offset)->get();
                $items = $query->getResultArray();
            } else {
                // Use Query Builder directly to avoid getCompiledSelect() error
                $builder = $this->db->table('tbl_m_item')
                    ->select('tbl_m_item.*, tbl_m_kategori.kategori, tbl_m_merk.merk, tbl_m_supplier.nama as supplier_nama, IFNULL(SUM(tbl_m_item_stok.jml),0) as stok')
                    ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
                    ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
                    ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_m_item.id_supplier', 'left')
                    ->join('tbl_m_item_stok', 'tbl_m_item_stok.id_item = tbl_m_item.id', 'left')
                    ->where('tbl_m_item.status_hps', '0')
                    ->where('tbl_m_item.status', '1')
                    ->groupBy('tbl_m_item.id, tbl_m_item.status_stok')
                    ->orderBy('tbl_m_item.item', 'ASC');

                if ($search) {
                    $builder->groupStart()
                        ->like('tbl_m_item.item', $search)
                        ->orLike('tbl_m_item.kode', $search)
                        ->orLike('tbl_m_item.barcode', $search)
                        ->orLike('tbl_m_kategori.kategori', $search)
                        ->orLike('tbl_m_merk.merk', $search)
                        ->orLike('tbl_m_supplier.nama', $search)
                        ->groupEnd();
                }
                if ($categoryId) {
                    $builder->where('tbl_m_item.id_kategori', $categoryId);
                }

                $query = $builder->limit($limit, $offset)->get();
                $items = $query->getResultArray();
            }

            // Defensive: Ensure $items is array
            if (!is_array($items)) {
                $items = [];
            }

            return $this->response->setJSON(['success' => true, 'items' => $items]);
        } catch (\Throwable $e) {
            // Log the error for debugging
            log_message('error', 'searchItems error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return $this->response->setJSON([
                'error' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Terjadi kesalahan pada server. Silakan coba lagi.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get outlet platforms (AJAX)
     */
    public function getOutletPlatforms()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        try {
            $warehouseId = $this->request->getPost('warehouse_id');
            
            if (empty($warehouseId)) {
                // Return all platforms if no outlet selected
                $platforms = $this->platformModel->where('status', '1')->findAll();
                return $this->response->setJSON([
                    'success' => true,
                    'platforms' => $platforms
                ]);
            }

            // Get platforms assigned to this outlet
            $outletPlatforms = $this->outletPlatformModel->getPlatformsByOutlet($warehouseId);
            
            // Return the platforms
            return $this->response->setJSON([
                'success' => true,
                'platforms' => $outletPlatforms
            ]);

        } catch (\Exception $e) {
            log_message('error', 'getOutletPlatforms error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get customer information (AJAX)
     */
    public function getCustomerInfo($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $customer = $this->pelangganModel->where('kode', $id)->first();
        if (!$customer) {
            return $this->response->setJSON(['error' => 'Customer not found']);
        }

        return $this->response->setJSON([
            'success' => true,
            'customer' => $customer
        ]);
    }

    /**
     * Generate new transaction number (AJAX)
     */
    public function generateNotaNumber()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $prefix = 'INV';
        $date = date('Ymd');
        $lastTransaction = $this->transJualModel->where('DATE(created_at)', date('Y-m-d'))
                                               ->orderBy('id', 'DESC')
                                               ->first();

        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->no_nota, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $notaNumber = $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        return $this->response->setJSON([
            'success' => true,
            'nota_number' => $notaNumber
        ]);
    }

    /**
     * Validate voucher code (AJAX)
     */
    public function validateVoucher()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $voucherCode = $this->request->getPost('voucher_code');
        
        if (empty($voucherCode)) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Kode voucher tidak boleh kosong'
            ]);
        }

        try {
            // Validate voucher using database
            $voucher = $this->voucherModel->getVoucherByCode($voucherCode);
            
            if (!$voucher) {
                return $this->response->setJSON([
                    'valid' => false,
                    'message' => 'Kode voucher tidak ditemukan'
                ]);
            }

            // Check if voucher is valid and available
            if (!$this->voucherModel->isVoucherValid($voucherCode)) {
                // Provide more specific error message
                $today = date('Y-m-d');
                $errorMessage = 'Voucher tidak valid';
                
                if ($voucher->tgl_masuk > $today) {
                    $errorMessage = 'Voucher belum aktif. Tanggal mulai: ' . date('d/m/Y', strtotime($voucher->tgl_masuk));
                } elseif ($voucher->tgl_keluar < $today) {
                    $errorMessage = 'Voucher sudah kadaluarsa. Tanggal berakhir: ' . date('d/m/Y', strtotime($voucher->tgl_keluar));
                } elseif ($voucher->jml_keluar >= $voucher->jml_max) {
                    $errorMessage = 'Voucher sudah habis digunakan';
                }
                
                return $this->response->setJSON([
                    'valid' => false,
                    'message' => $errorMessage
                ]);
            }

            // Return voucher details
            $discountValue = $voucher->jenis_voucher === 'persen' ? (float)$voucher->nominal : 0;
            $discountAmount = $voucher->jenis_voucher === 'nominal' ? (float)$voucher->nominal : 0;
            
            return $this->response->setJSON([
                'valid' => true,
                'discount' => $discountValue, // Percentage for percentage vouchers
                'discount_amount' => $discountAmount, // Fixed amount for nominal vouchers
                'jenis_voucher' => $voucher->jenis_voucher,
                'voucher_id' => $voucher->id,
                'message' => 'Voucher valid'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Voucher validation error: ' . $e->getMessage());
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Terjadi kesalahan saat memvalidasi voucher: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display cashier interface
     */
    public function cashier()
    {
        // Check shift status first
        if (!$this->checkActiveShift()) {
            return redirect()->to('/transaksi/shift/open')->with('error', 'Shift Belum dibuka');
        }

        // Get related data for dropdowns (formatted)
        $customers  = $this->pelangganModel->where('status_blokir', '0')->findAll();
        $sales      = $this->karyawanModel->where('status', '1')->findAll();
        $warehouses = $this->gudangModel->where('status', '1')->where('status_otl', '0')->where('status_hps', '0')->findAll();
        $outlets    = $this->gudangModel->getOutlets(); // Uses: status=1, status_otl=1, status_hps=0
        
        $platforms  = $this->platformModel->where('status', '1')->findAll();
        $items      = $this->itemModel->getPosItems(); // Get items with stock status info
        
        // Get active categories
        $categories = $this->kategoriModel->getActiveCategories();
        
        // Get last 5 transactions
        $lastTransactions = $this->transJualModel->getLastTransactions(5);

        $data = [
            'title'         => 'Kasir - Transaksi Penjualan',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'customers'     => $customers,
            'sales'         => $sales,
            'warehouses'    => $warehouses,
            'outlets'       => $outlets,
            'platforms'     => $platforms,
            'items'         => $items,
            'categories'    => $categories,
            'lastTransactions' => $lastTransactions,
            'itemModel'     => $this->itemModel // Pass model instance for helper methods
        ];

        return $this->view($this->theme->getThemePath() . '/transaksi/jual/cashier', $data);
    }

    /**
     * Display create sales transaction form
     */
    public function create()
    {
        // Check shift status first
        if (!$this->checkActiveShift()) {
            return redirect()->to(base_url('transaksi/shift/open'));
        }

        // Get related data for dropdowns
        $customers  = $this->pelangganModel->where('status_blokir', '0')->findAll();
        $sales      = $this->karyawanModel->where('status', '1')->findAll();
        $warehouses = $this->gudangModel->where('status', '1')->where('status_otl', '0')->where('status_hps', '0')->findAll();
        $outlets    = $this->gudangModel->getOutlets(); // Uses: status=1, status_otl=1, status_hps=0
        $platforms  = $this->platformModel->where('status', '1')->findAll();
        $items      = $this->itemModel->getItemsWithRelationsActive(100); // Get items with relations

        $data = [
            'title'         => 'Buat Transaksi Penjualan',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'customers'     => $customers,
            'sales'         => $sales,
            'warehouses'    => $warehouses,
            'outlets'       => $outlets,
            'platforms'     => $platforms,
            'items'         => $items
        ];

        return $this->view($this->theme->getThemePath() . '/transaksi/jual/create', $data);
    }

    /**
     * Store new sales transaction
     */
    public function store()
    {
        // Validation rules
        $rules = [
            'id_pelanggan'        => 'permit_empty|integer',
            'id_sales'            => 'permit_empty|integer',
            'id_gudang'           => 'required|integer',
            'no_nota'             => 'required|max_length[50]',
            'tgl_order'           => 'required|valid_date',
            'tgl_pengiriman'      => 'permit_empty|valid_date',
            'no_ref_pelanggan'    => 'permit_empty|max_length[100]',
            'harga_include_pajak' => 'required|in_list[0,1]',
            'pesan_pelanggan'     => 'permit_empty|max_length[500]',
            'catatan'             => 'permit_empty|max_length[500]',
            'subtotal'            => 'required|decimal',
            'jml_subtotal'        => 'required|decimal',
            'jml_total'           => 'required|decimal',
            'diskon'              => 'permit_empty|decimal',
            'jml_diskon'          => 'permit_empty|decimal',
            'ppn'                 => 'permit_empty|decimal',
            'jml_ppn'             => 'permit_empty|decimal',
            'penyesuaian'         => 'permit_empty|decimal',
            'jml_gtotal'          => 'required|decimal',
            'print_order'         => 'permit_empty|in_list[0,1]',
            'print_surat_jalan'   => 'permit_empty|in_list[0,1]',
        ];

        // Run validation
        if (!$this->validate($rules)) {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        // Get user id
        $id_user = $this->ionAuth->user()->row()->id;

        // Get form data
        $id_pelanggan      = $this->request->getPost('id_pelanggan') ?: null;
        $id_sales          = $this->request->getPost('id_sales') ?: null;
        $id_gudang         = $this->request->getPost('id_gudang');
        $no_nota           = $this->request->getPost('no_nota');
        $tgl_order         = $this->request->getPost('tgl_order');
        $tgl_pengiriman    = $this->request->getPost('tgl_pengiriman') ?: null;
        $no_ref_pelanggan  = $this->request->getPost('no_ref_pelanggan') ?: null;
        $harga_include_pajak = $this->request->getPost('harga_include_pajak');
        $pesan_pelanggan   = $this->request->getPost('pesan_pelanggan') ?: null;
        $catatan           = $this->request->getPost('catatan') ?: null;
        $jml_subtotal      = $this->request->getPost('jml_subtotal');
        $jml_total         = $this->request->getPost('jml_total');
        $diskon            = $this->request->getPost('diskon') ?: 0;
        $jml_diskon        = $this->request->getPost('jml_diskon') ?: 0;
        $ppn               = $this->request->getPost('ppn') ?: 0;
        $jml_ppn           = $this->request->getPost('jml_ppn') ?: 0;
        $penyesuaian       = $this->request->getPost('penyesuaian') ?: 0;
        $jml_gtotal        = $this->request->getPost('jml_gtotal');
        $print_order       = $this->request->getPost('print_order') ?: 0;
        $print_surat_jalan = $this->request->getPost('print_surat_jalan') ?: 0;
        $voucher_code      = $this->request->getPost('voucher_code') ?: null;
        $voucher_discount  = $this->request->getPost('voucher_discount') ?: 0;
        $voucher_id        = $this->request->getPost('voucher_id') ?: null;
        $voucher_type      = $this->request->getPost('voucher_type') ?: null;
        $voucher_discount_amount = $this->request->getPost('voucher_discount_amount') ?: 0;
        $metode_bayar      = $this->request->getPost('metode_bayar') ?: null;
        $id_platform      = $this->request->getPost('id_platform') ?: null;

        $transactionData = [
            'id_user'           => $id_user,
            'id_sales'          => $id_sales,
            'id_pelanggan'      => $id_pelanggan,
            'id_gudang'         => $id_gudang,
            'no_nota'           => $no_nota,
            'tgl_masuk'         => $tgl_order, // tgl_masuk = tgl_order
            'jml_subtotal'      => $jml_subtotal,
            'jml_total'         => $jml_total,
            'diskon'            => $diskon,
            'jml_diskon'        => $jml_diskon,
            'ppn'               => $ppn,
            'jml_ppn'           => $jml_ppn,
            'jml_gtotal'        => $jml_gtotal,
            'metode_bayar'      => $metode_bayar,
            'voucher_code'      => $voucher_code,
            'voucher_discount'  => $voucher_discount,
            'voucher_id'        => $voucher_id,
            'voucher_type'      => $voucher_type,
            'voucher_discount_amount' => $voucher_discount_amount,
            'status'            => '0', // Draft
            'status_bayar'      => '0', // Belum lunas
        ];

        // Get items data
        $items = $this->request->getPost('items');
        $platforms = $this->request->getPost('platforms');
        
        // Decode platforms JSON if it's a string
        if (is_string($platforms)) {
            $platforms = json_decode($platforms, true);
        }

        try {
            $this->db->transStart();

            // Insert main transaction
            $this->transJualModel->insert($transactionData);
            $transactionId = $this->transJualModel->getInsertID();

            // Insert transaction details
            if ($items && is_array($items)) {
                foreach ($items as $item) {
                    if (!empty($item['id_item']) && !empty($item['qty'])) {
                        // Get item details to check PPN status
                        $itemDetails = $this->itemModel->find($item['id_item']);
                        $ppnRate = $this->pengaturan->ppn ?? 11; // Default to 11%
                        
                        // Calculate per-item PPN based on item's status_ppn
                        $itemPpnAmount = 0;
                        if ($itemDetails && $itemDetails->status_ppn == '1') {
                            // Item is taxable, calculate PPN
                            if ($harga_include_pajak == '1') {
                                // PPN included in price
                                $itemPpnAmount = ($item['jumlah'] / (1 + ($ppnRate / 100))) * ($ppnRate / 100);
                            } else {
                                // PPN added to price
                                $itemPpnAmount = $item['jumlah'] * ($ppnRate / 100);
                            }
                        }

                        $detailData = [
                            'id_penjualan'   => $transactionId,
                            'id_item'        => $item['id_item'],
                            'id_satuan'      => $item['id_satuan']     ?? null,
                            'id_kategori'    => $item['id_kategori']   ?? null,
                            'id_merk'        => $item['id_merk']       ?? null,
                            'no_nota'        => $transactionData['no_nota'],
                            'kode'           => $item['kode']          ?? null,
                            'produk'         => $item['produk'],
                            'satuan'         => $item['satuan']        ?? null,
                            'keterangan'     => $item['keterangan']    ?? null,
                            'harga'          => $item['harga'],
                            'harga_beli'     => $item['harga_beli']    ?? 0,
                            'jml'            => $item['qty'],
                            'jml_satuan'     => $item['qty_satuan']    ?? $item['qty'],
                            'disk1'          => $item['disk1']         ?? 0,
                            'disk2'          => $item['disk2']         ?? 0,
                            'disk3'          => $item['disk3']         ?? 0,
                            'diskon'         => $item['diskon']        ?? 0,
                            'potongan'       => $item['potongan']      ?? 0,
                            'subtotal'       => $item['jumlah'],
                            'ppn_amount'     => $itemPpnAmount,
                            'status'         => 1
                        ];

                        $this->transJualDetModel->insert($detailData);
                    }
                }
            }

            // Insert platform payments
            if ($platforms && is_array($platforms)) {
                foreach ($platforms as $platform) {
                    if (!empty($platform['id_platform']) && !empty($platform['nominal'])) {
                        $platformData = [
                            'id_penjualan' => $transactionId,
                            'id_platform'  => $platform['id_platform'],
                            'no_nota'      => $transactionData['no_nota'],
                            'platform'     => $platform['platform'],
                            'keterangan'   => $platform['keterangan'] ?? null,
                            'nominal'      => $platform['nominal']
                        ];

                        $this->transJualPlatModel->insert($platformData);
                    }
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return redirect()->back()
                                ->withInput()
                                ->with('error', 'Gagal menyimpan transaksi. Silakan coba lagi.');
            }

            // Success message
            $message = 'Transaksi berhasil disimpan';
            // if ($transactionData['print_order']) {
            //     $message .= ' dan akan dicetak';
            // }

            return redirect()->to('transaksi/jual')
                            ->with('success', $message);

        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Process cashier transaction (AJAX)
     */
    public function processTransaction()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        // Get transaction data from POST
        $cart               = $this->request->getPost('cart');
        $customerId         = $this->request->getPost('customer_id') ?: null;
        $warehouseId        = $this->request->getPost('warehouse_id');
        $discountPercent    = $this->request->getPost('discount_percent') ?: 0;
        $voucherCode        = $this->request->getPost('voucher_code') ?: null;
        $voucherDiscount    = $this->request->getPost('voucher_discount') ?: 0;
        $voucherType        = $this->request->getPost('voucher_type') ?: null;
        $voucherDiscountAmount = $this->request->getPost('voucher_discount_amount') ?: 0;
        $paymentMethods     = $this->request->getPost('payment_methods') ?: [];
        $totalAmountReceived = $this->request->getPost('total_amount_received') ?: 0;
        $grandTotal         = $this->request->getPost('grand_total') ?: 0;
        $isDraft            = $this->request->getPost('is_draft') ?: false;
        $draftId            = $this->request->getPost('draft_id') ?: null; // ID of draft being converted

        // Convert string boolean to actual boolean
        if (is_string($isDraft)) {
            $isDraft = ($isDraft === 'true' || $isDraft === '1');
        }

        // Validate required data
        if (empty($cart) || !is_array($cart) || count($cart) === 0) {
            return $this->response->setJSON(['error' => 'Keranjang belanja kosong']);
        }

        if (empty($warehouseId)) {
            return $this->response->setJSON(['error' => 'Gudang harus dipilih']);
        }

        // Skip payment validation for drafts; otherwise, validate
        if (!$isDraft) {
            if (empty($paymentMethods) || !is_array($paymentMethods)) {
                return $this->response->setJSON(['error' => 'Metode pembayaran harus diisi']);
            }

            // Enforce blocked-member rule for Piutang Anggota and validate spending limit
            $guardResult = $this->paymentGuard->allowPayment(
                $customerId ? (int) $customerId : null, 
                $paymentMethods,
                (float) $grandTotal // Pass transaction amount for limit validation
            );
            if (!$guardResult['allowed']) {
                return $this->response->setJSON(['error' => $guardResult['message']]);
            }

            if ($totalAmountReceived < $grandTotal) {
                return $this->response->setJSON(['error' => 'Jumlah bayar kurang dari total']);
            }
        }

        try {
            $this->db = \Config\Database::connect();
            $this->db->transStart();

            $noNota = $this->transJualModel->generateKode();
            $Pengaturan = $this->pengaturan;

            $pelanggan = $this->pelangganModel->find($customerId);

            $status_ppn = 1; // included

            // Check if pengaturan is loaded
            if (!$Pengaturan) {
                log_message('error', 'Pengaturan not loaded - pengaturan property is null');
                throw new \Exception('Pengaturan tidak dapat dimuat. Silakan refresh halaman.');
            }

            // If converting from draft, use existing draft data
            if ($draftId && !$isDraft) {
                // Get existing draft
                $existingDraft = $this->transJualModel->find($draftId);
                if (!$existingDraft || $existingDraft->status != '0') {
                    throw new \Exception('Draft tidak ditemukan atau sudah diproses');
                }

                // Check if user owns this draft
                if ($existingDraft->id_user != $this->ionAuth->user()->row()->id) {
                    throw new \Exception('Anda tidak memiliki akses ke draft ini');
                }

                $noNota = $existingDraft->no_nota; // Use existing nota number
            }

            // Calculate totals
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += ($item['price'] * $item['quantity']);
            }

            // Calculate jml_total (total cart before voucher input)
            $jml_total = 0;
            foreach ($cart as $item) {
                $jml_total += ($item['price'] * $item['quantity']);
            }

            // Calculate diskon (discount %)
            $discountAmount = $jml_total * ($discountPercent / 100);

            // Check if voucher is used as payment method (in payment_methods array)
            $voucherAsPayment = false;
            $voucherPaymentAmount = 0;
            if (!$isDraft && !empty($paymentMethods)) {
                foreach ($paymentMethods as $payment) {
                    if (isset($payment['type']) && ($payment['type'] == '4' || $payment['type'] == 'voucher')) {
                        $voucherAsPayment = true;
                        $voucherPaymentAmount = (float)($payment['amount'] ?? 0);
                        break;
                    }
                }
            }

            // Calculate voucher as discount (if not used as payment method)
            $voucherAmount = 0;
            if (!empty($voucherCode) && $voucherDiscount > 0 && !$voucherAsPayment) {
                if ($voucherType === 'nominal' && $voucherDiscountAmount > 0) {
                    // Nominal voucher - use the provided discount amount
                    $voucherAmount = (float) $voucherDiscountAmount;
                } else if ($voucherType === 'persen' || empty($voucherType)) {
                    // Percentage voucher - calculate from percentage
                    $voucherAmount = $jml_total * ($voucherDiscount / 100);
                }
            }

            // jml_diskon = total diskon (voucher as discount + diskon % or nominal)
            // Note: If voucher is used as payment method, it's not included in discount
            $jml_diskon = $discountAmount + $voucherAmount;

            // Grand total (jml_gtotal) before PPN breakdown
            $jml_gtotal = $jml_total - $jml_diskon;

            // PPN calculation for status_ppn = 1 (included)
            $ppnRate = $Pengaturan->ppn ?? 11; // Default to 11% if not set

            if ($status_ppn == 1) {
                // PPN included: jml_subtotal = jml_gtotal / 1.11, jml_ppn = jml_gtotal - jml_subtotal
                $jml_subtotal = $jml_gtotal / (1 + ($ppnRate / 100));
                $taxAmount = $jml_gtotal - $jml_subtotal;
            } else {
                // Fallback (should not happen in this context)
                $jml_subtotal = $jml_gtotal;
                $taxAmount = 0;
            }

            log_message('debug', 'PPN calculation (INCLUDED) - jml_gtotal: ' . $jml_gtotal . ', jml_subtotal: ' . $jml_subtotal . ', ppn_rate: ' . $ppnRate . ', taxAmount: ' . $taxAmount);

            // Grand total (already calculated as jml_gtotal)
            $finalTotal = $jml_gtotal;

            // Change
            $change = $totalAmountReceived - $finalTotal;
            $change = $change < 0 ? 0 : $change;

            // Check if any payment method is Piutang (value='3') - only for non-draft transactions
            $hasPiutang = false;
            if (!$isDraft && !empty($paymentMethods)) {
                foreach ($paymentMethods as $payment) {
                    if (isset($payment['type']) && $payment['type'] == '3') {
                        $hasPiutang = true;
                        break;
                    }
                }
            }

            // Prepare transaction data
            $transactionData = [
                'id_user'           => $this->ionAuth->user()->row()->id,
                'id_sales'          => $warehouseId ?? 0, // Can be added later if needed
                'id_pelanggan'      => $pelanggan->id_user ?? 2,
                'id_gudang'         => $warehouseId,
                'id_shift'          => session()->get('kasir_shift'),
                'no_nota'           => $noNota,
                'tgl_masuk'         => date('Y-m-d H:i:s'),
                'tgl_bayar'         => $isDraft ? null : date('Y-m-d H:i:s'),
                'jml_total'         => $jml_total,         // total cart before voucher input
                'jml_subtotal'      => $jml_subtotal,      // jml_gtotal / 1.11
                'diskon'            => $discountPercent,
                'jml_diskon'        => $jml_diskon,        // voucher + diskon % or nominal
                'ppn'               => $ppnRate,           // dari pengaturan
                'jml_ppn'           => $taxAmount,         // jml_gtotal - jml_subtotal
                'jml_gtotal'        => $jml_gtotal,        // total setelah diskon dan voucher, sudah termasuk PPN
                'jml_bayar'         => $isDraft ? 0 : $totalAmountReceived,
                'jml_kembali'       => $isDraft ? 0 : $change,
                'metode_bayar'      => $isDraft ? 'draft' : 'multiple', // Draft or Multiple payment methods
                'status'            => $isDraft ? '0' : '1', // 0=Draft, 1=Completed
                'status_nota'       => $isDraft ? '0' : '1', // 0=Draft, 1=Completed
                'status_bayar'      => $isDraft ? '0' : ($hasPiutang ? '0' : '1'), // 0=Unpaid/Draft, 1=Paid
                'status_ppn'        => '1',  // PPN included
                'voucher_code'      => $voucherCode,       // Voucher code from frontend
                'voucher_discount'  => $voucherDiscount,   // Voucher discount percentage
                'voucher_id'        => $this->request->getPost('voucher_id') ?: null, // Voucher ID if available
                'voucher_type'      => $this->request->getPost('voucher_type') ?: null, // Voucher type (persen/nominal)
                'voucher_discount_amount' => $voucherAmount // Calculated voucher amount in currency
            ];

            // Insert or update main transaction
            if ($draftId && !$isDraft) {
                // Update existing draft to completed transaction
                $this->transJualModel->update($draftId, $transactionData);
                $transactionId = $draftId;
            } else {
                // Insert new transaction
                $this->transJualModel->insert($transactionData);
                $transactionId = $this->transJualModel->getInsertID();
            }

            // Insert transaction details
            if ($draftId && !$isDraft) {
                // Delete existing draft details first
                $this->transJualDetModel->where('id_penjualan', $draftId)->delete();
            }
            
            foreach ($cart as $item) {
                // Get item details from database
                $itemDetails = $this->itemModel->find($item['id']);
                if (!$itemDetails) {
                    throw new \Exception("Item dengan ID {$item['id']} tidak ditemukan");
                }

                // Check stock for stockable items
                $isStockable = true;
                if (isset($itemDetails->status_stok)) {
                    $isStockable = ((int) $itemDetails->status_stok) === 1;
                } elseif (isset($itemDetails->is_stockable)) {
                    $isStockable = ((int) $itemDetails->is_stockable) === 1;
                }

                if ($isStockable && $warehouseId) {
                    // Get current stock for this item in this warehouse
                    $currentStock = $this->itemStokModel
                        ->where('id_item', $item['id'])
                        ->where('id_gudang', $warehouseId)
                        ->first();
                    
                    $availableStock = $currentStock ? (int)($currentStock->jml ?? 0) : 0;
                    $requestedQuantity = (int)($item['quantity'] ?? 0);

                    if ($availableStock <= 0) {
                        throw new \Exception("Item '{$itemDetails->item}' tidak dapat dijual karena stok habis");
                    }

                    if ($requestedQuantity > $availableStock) {
                        throw new \Exception("Stok '{$itemDetails->item}' tidak mencukupi. Stok tersedia: {$availableStock}, diminta: {$requestedQuantity}");
                    }
                }

                $detailData = [
                    'id_penjualan'   => $transactionId,
                    'id_item'        => $item['id'],
                    'id_satuan'      => $itemDetails->id_satuan,
                    'id_kategori'    => $itemDetails->id_kategori,
                    'id_merk'        => $itemDetails->id_merk,
                    'no_nota'        => $noNota,
                    'kode'           => $itemDetails->kode,
                    'produk'         => $item['name'],
                    'satuan'         => $itemDetails->satuan ?? 'PCS',
                    'keterangan'     => null,
                    'harga'          => $item['price'],
                    'harga_beli'     => $itemDetails->harga_beli ?? 0,
                    'jml'            => $item['quantity'],
                    'jml_satuan'     => 1,
                    'disk1'          => 0,
                    'disk2'          => 0,
                    'disk3'          => 0,
                    'diskon'         => 0,
                    'potongan'       => 0,
                    'subtotal'       => $item['price'] * $item['quantity'],
                    'status'         => 1
                ];

                $this->transJualDetModel->insert($detailData);

                // Update stock (decrease stock) - only for completed transactions and stockable items
                if (!$isDraft && $warehouseId) {
                    $stockable = true;
                    if (isset($itemDetails->is_stockable)) {
                        $stockable = ((int) $itemDetails->is_stockable) === 1;
                    }
                    if ($stockable) {
                        $this->updateStock($item['id'], $warehouseId, $item['quantity'], 'decrease');
                    }
                }

                // Insert item history record (Stok Keluar Penjualan - status 4) only if stockable
                $stockableForHistory = true;
                if (isset($itemDetails->is_stockable)) {
                    $stockableForHistory = ((int) $itemDetails->is_stockable) === 1;
                }
                if ($stockableForHistory) {
                    $historyData = [
                        'id_item'        => $item['id'],
                        'id_satuan'      => $itemDetails->id_satuan,
                        'id_gudang'      => $warehouseId,
                        'id_user'        => $this->ionAuth->user()->row()->id,
                        'id_pelanggan'   => $customerId,
                        'id_penjualan'   => $transactionId,
                        'tgl_masuk'      => date('Y-m-d H:i:s'),
                        'no_nota'        => $noNota,
                        'kode'           => $itemDetails->kode,
                        'item'           => $item['name'],
                        'keterangan'     => 'Penjualan - ' . $noNota,
                        'nominal'        => $item['price'],
                        'jml'            => $item['quantity'],
                        'jml_satuan'     => 1,
                        'satuan'         => $itemDetails->satuan ?? 'PCS',
                        'status'         => '4', // Stok Keluar Penjualan
                        'sp'             => null
                    ];
                    $this->itemHistModel->insert($historyData);
                }
            }

            // Insert multiple platform payments - only for completed transactions, not drafts
            if (!$isDraft && !empty($paymentMethods)) {
                // If converting from draft, delete existing platform payments first
                if ($draftId) {
                    $this->transJualPlatModel->where('id_penjualan', $draftId)->delete();
                }
                
                // Debug: Log payment methods received
                log_message('debug', 'Payment methods received: ' . json_encode($paymentMethods));
                log_message('debug', 'Payment methods count: ' . count($paymentMethods));
                log_message('debug', 'Payment methods type: ' . gettype($paymentMethods));
                
                foreach ($paymentMethods as $index => $payment) {
                    // Debug: Log each payment
                    log_message('debug', 'Processing payment: ' . json_encode($payment));
                    
                    // Handle voucher as payment method
                    if (isset($payment['type']) && ($payment['type'] == '4' || $payment['type'] == 'voucher')) {
                        // Voucher used as payment method - create platform entry for voucher
                        if (!empty($voucherCode) && $voucherPaymentAmount > 0) {
                            $platformData = [
                                'id_penjualan' => $transactionId,
                                'id_platform'  => null, // Voucher doesn't have platform ID
                                'no_nota'      => $noNota,
                                'platform'     => 'Voucher: ' . $voucherCode,
                                'keterangan'   => (!empty($payment['keterangan']) ? $payment['keterangan'] : 'Voucher ' . $voucherCode),
                                'nominal'      => $voucherPaymentAmount
                            ];
                            
                            log_message('debug', 'Inserting voucher as payment: ' . json_encode($platformData));
                            $this->transJualPlatModel->insert($platformData);
                        }
                        continue; // Skip to next payment method
                    }
                    
                    // Debug: Check all available keys in payment array
                    log_message('debug', 'Payment keys: ' . json_encode(array_keys($payment)));
                    
                    // Debug: Check exact values and types
                    $platformId = $payment['platform_id'] ?? null;
                    $amount = $payment['amount'] ?? null;
                    $platformIdType = gettype($platformId);
                    $amountType = gettype($amount);
                    
                    log_message('debug', "Platform ID: '$platformId' (type: $platformIdType), Amount: '$amount' (type: $amountType)");
                    
                    // Use multiple checks to ensure values are valid
                    $platformIdValid = isset($payment['platform_id']) && $payment['platform_id'] !== '' && $payment['platform_id'] !== null;
                    $amountValid = isset($payment['amount']) && $payment['amount'] !== '' && $payment['amount'] !== null && $payment['amount'] > 0;
                    
                    log_message('debug', "Platform ID valid: " . ($platformIdValid ? 'YES' : 'NO') . ", Amount valid: " . ($amountValid ? 'YES' : 'NO'));
                    
                    if ($platformIdValid && $amountValid) {
                        $platform = $this->platformModel->find($payment['platform_id']);
                        $platformData = [
                            'id_penjualan' => $transactionId,
                            'id_platform'  => $payment['platform_id'],
                            'no_nota'      => $noNota,
                            'platform'     => $platform->platform ?? $payment['type'],
                            'keterangan'   => (!empty($payment['keterangan']) ? $payment['keterangan'] : ''),
                            'nominal'      => $payment['amount']
                        ];

                        // Debug: Log platform data being inserted
                        log_message('debug', 'Inserting platform data: ' . json_encode($platformData));

                        $this->transJualPlatModel->insert($platformData);

                        $this->transJualModel->update($transactionId, ['metode_bayar' => $payment['type']]);
                    } else {
                        // Debug: Log why payment was skipped with more detail
                        log_message('debug', "Payment skipped - platform_id empty: " . (empty($payment['platform_id']) ? 'YES' : 'NO') . 
                                           ", amount empty: " . (empty($payment['amount']) ? 'YES' : 'NO'));
                        log_message('debug', 'Payment skipped - platform_id: ' . ($payment['platform_id'] ?? 'NULL') . ', amount: ' . ($payment['amount'] ?? 'NULL'));
                    }
                }
            }

            // Mark voucher as used if transaction is completed and voucher was applied
            if (!$isDraft && !empty($voucherCode) && !empty($this->request->getPost('voucher_id'))) {
                $voucherId = $this->request->getPost('voucher_id');
                $voucherUsed = $this->voucherModel->useVoucher($voucherId);
                if (!$voucherUsed) {
                    log_message('warning', 'Failed to mark voucher as used. Voucher ID: ' . $voucherId);
                    // Don't throw error - transaction is already completed
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            return $this->response->setJSON([
                    'success'        => true,
                    'message'        => 'Transaksi berhasil diproses',
                    'transaction_id' => $transactionId,
                    'no_nota'        => $noNota,
                    'total'          => $finalTotal,
                    'change'         => $change
            ]);

        } catch (\Exception $e) {
            if ($this->db->transStatus() !== false) {
                $this->db->transRollback();
            }
            
            log_message('error', 'Cashier transaction failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => true,
                'message' => 'Gagal memproses transaksi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update stock for an item in a warehouse
     */
    private function updateStock($itemId, $warehouseId, $quantity, $action = 'decrease')
    {
        $builder = $this->db->table('tbl_m_item_stok');
        
        // Get current stock
        $currentStock = $builder->where('id_item', $itemId)
                               ->where('id_gudang', $warehouseId)
                               ->get()
                               ->getRow();

        if ($currentStock) {
            // Update existing stock
            $newQuantity = $action === 'decrease' 
                ? $currentStock->jml - $quantity 
                : $currentStock->jml + $quantity;
            
            $newQuantity = max(0, $newQuantity); // Ensure stock doesn't go negative
            
            $builder->where('id_item', $itemId)
                   ->where('id_gudang', $warehouseId)
                   ->update(['jml' => $newQuantity]);
        } else {
            // Create new stock record if doesn't exist
            $newQuantity = $action === 'decrease' ? 0 : $quantity;
            
            $builder->insert([
                'id_item' => $itemId,
                'id_gudang' => $warehouseId,
                'jml' => $newQuantity,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Get item variants for a given item (AJAX)
     */
    public function get_variants($item_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        try {
            $itemVarianModel = new \App\Models\ItemVarianModel();
            $variants = $itemVarianModel->getVariantsWithPrice($item_id);

            return $this->response->setJSON([
                'success' => true,
                'variants' => $variants
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal mengambil data varian'
            ]);
        }
    }

    /**
     * QR Scanner page for Piutang transactions
     * Mobile-optimized barcode/QR scanner interface
     */
    public function qrScanner($transactionId = null)
    {
        if (!$transactionId) {
            return redirect()->to('transaksi/jual')->with('error', 'ID Transaksi tidak valid');
        }

        // Get transaction details
        $transaction = $this->transJualModel
            ->select('tbl_trans_jual.*, tbl_m_pelanggan.nama as customer_name')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
            ->where('tbl_trans_jual.id', $transactionId)
            ->first();

        if (!$transaction) {
            return redirect()->to('transaksi/jual')->with('error', 'Transaksi tidak ditemukan');
        }

        $data = [
            'title' => 'QR Scanner - Piutang',
            'transaction' => $transaction,
            'transactionId' => $transactionId,
            'Pengaturan' => $this->pengaturan
        ];

        return view('admin-lte-3/transaksi/jual/qr_scanner', $data);
    }

    /**
     * Refresh session to keep authentication alive
     */
    public function refreshSession()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        // Check if user is still logged in
        if (!$this->ionAuth->loggedIn()) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Session expired']);
        }

        // Get user info to refresh session
        $user = $this->ionAuth->user()->row();
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Session refreshed',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email
            ]
        ]);
    }

    /**
     * Process QR scan data for Piutang transactions
     */
    public function processQrScan()
    {
        // Disable CSRF check for this method
        if (isset($this->request)) {
            $this->request->setGlobal('csrf_test_name', null);
        }
        
        $transactionId = $this->request->getPost('transaction_id');
        $scanData = $this->request->getPost('scan_data');

        if (!$transactionId || !$scanData) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }

        try {
            // Get transaction
            $transaction = $this->transJualModel->find($transactionId);
            if (!$transaction) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan'
                ]);
            }

            // Log the QR scan event
            $db = \Config\Database::connect();
            
            // Create scan log data
            $currentTime = date('Y-m-d H:i:s');
            $userId = null;
            
            // Safely get user ID (may not be available via API route)
            try {
                if (isset($this->ionAuth) && $this->ionAuth && $this->ionAuth->loggedIn()) {
                    $user = $this->ionAuth->user()->row();
                    $userId = $user ? $user->id : null;
                }
            } catch (\Exception $e) {
                // Continue without user ID if ionAuth fails or not available
                $userId = null;
            }
            
            $logData = [
                'transaction_id' => $transactionId,
                'scan_data' => $scanData,
                'scan_time' => $currentTime,
                'user_id' => $userId,
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ];

            // Insert into scan log
            $db->table('tbl_trans_jual_scan_log')->insert($logData);

            // Try to update transaction with scan confirmation (columns may not exist)
            try {
                $this->transJualModel->update($transactionId, [
                    'qr_scanned' => '1',
                    'qr_scan_time' => $currentTime,
                    'updated_at' => $currentTime
                ]);
            } catch (\Exception $e) {
                // Continue if columns don't exist - log is still recorded
                log_message('info', 'QR scan columns not available in tbl_trans_jual: ' . $e->getMessage());
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'QR Code berhasil discan dan dicatat',
                'data' => [
                    'transaction_id' => $transactionId,
                    'scan_data' => $scanData,
                    'scan_time' => date('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'QR Scan Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal memproses scan QR'
            ]);
        }
    }

    /**
     * Get list of draft transactions
     */
    public function getDrafts()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        try {
            // Get draft transactions (status = 0)
            $drafts = $this->transJualModel
                ->select('tbl_trans_jual.*, tbl_m_outlet.nama as outlet_name')
                ->join('tbl_m_outlet', 'tbl_m_outlet.id = tbl_trans_jual.id_gudang', 'left')
                ->where('tbl_trans_jual.status', '0') // Draft status
                ->where('tbl_trans_jual.id_user', $this->ionAuth->user()->row()->id) // Only current user's drafts
                ->orderBy('tbl_trans_jual.created_at', 'DESC')
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'drafts' => $drafts
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil daftar draft: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get specific draft transaction with details
     */
    public function getDraft($draftId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        try {
            // Get draft transaction
            $draft = $this->transJualModel->find($draftId);
            if (!$draft) {
                return $this->response->setJSON(['success' => false, 'message' => 'Draft tidak ditemukan']);
            }

            // Check if it's a draft
            if ($draft->status != '0') {
                return $this->response->setJSON(['success' => false, 'message' => 'Transaksi ini bukan draft']);
            }

            // Check if user owns this draft
            if ($draft->id_user != $this->ionAuth->user()->row()->id) {
                return $this->response->setJSON(['success' => false, 'message' => 'Anda tidak memiliki akses ke draft ini']);
            }

            // Debug: Log draft object properties
            log_message('debug', 'Draft object properties: ' . print_r($draft, true));

            // Get transaction details with category and brand names
            $items = $this->transJualDetModel
                ->select('tbl_trans_jual_det.*, tbl_m_kategori.kategori as nama_kategori, tbl_m_merk.merk as nama_merk')
                ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_trans_jual_det.id_kategori', 'left')
                ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_trans_jual_det.id_merk', 'left')
                ->where('tbl_trans_jual_det.id_penjualan', $draftId)
                ->findAll();

            // Debug: Log items
            log_message('debug', 'Items found: ' . count($items));

            // Format items for cart
            $cartItems = [];
            foreach ($items as $item) {
                $cartItems[] = [
                    'id' => $item->id_item,
                    'name' => $item->produk,
                    'quantity' => $item->jml,
                    'price' => $item->harga,
                    'total' => $item->subtotal,
                    'kode' => $item->kode,
                    'harga_beli' => $item->harga_beli,
                    'satuan' => $item->satuan,
                    'kategori' => $item->nama_kategori ?: '',
                    'merk' => $item->nama_merk ?: ''
                ];
            }

            // Get customer info
            $customer = null;
            if ($draft->id_pelanggan) {
                $customer = $this->pelangganModel->find($draft->id_pelanggan);
                // Debug: Log customer
                log_message('debug', 'Customer found: ' . ($customer ? 'yes' : 'no'));
            }

            $draftData = [
                'id' => $draft->id,
                'no_nota' => $draft->no_nota,
                'customer_id' => $draft->id_pelanggan,
                'customer_name' => $customer ? $customer->nama : null,
                'customer_type' => $draft->id_pelanggan ? 'anggota' : 'umum',
                'items' => $cartItems,
                'discount_percent' => $draft->jml_diskon > 0 ? ($draft->jml_diskon / $draft->jml_subtotal * 100) : 0,
                'voucher_code' => $draft->voucher_code,
                'total' => $draft->jml_gtotal,
                'created_at' => $draft->created_at
            ];

            return $this->response->setJSON([
                'success' => true,
                'draft' => $draftData
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in getDraft: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil draft: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete draft transaction
     */
    public function deleteDraft($draftId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        // CSRF validation - temporarily disabled for testing
        /*
        if (!$this->validate([
            'csrf_test_name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'CSRF token tidak valid'
                ]
            ]
        ])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'CSRF token tidak valid'
            ]);
        }
        */

        try {
            // Get draft transaction
            $draft = $this->transJualModel->find($draftId);
            if (!$draft) {
                return $this->response->setJSON(['success' => false, 'message' => 'Draft tidak ditemukan']);
            }

            // Check if it's a draft
            if ($draft->status != '0') {
                return $this->response->setJSON(['success' => false, 'message' => 'Transaksi ini bukan draft']);
            }

            // Check if user owns this draft
            if ($draft->id_user != $this->ionAuth->user()->row()->id) {
                return $this->response->setJSON(['success' => false, 'message' => 'Anda tidak memiliki akses ke draft ini']);
            }

            $this->db->transStart();

            // Delete transaction details first
            $this->transJualDetModel->where('id_penjualan', $draftId)->delete();

            // Delete platform payments if any
            $this->transJualPlatModel->where('id_penjualan', $draftId)->delete();

            // Delete main transaction
            $this->transJualModel->delete($draftId);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus draft']);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Draft berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus draft: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Search customer by id_user, kode, nama, or no_telp
     */
    public function searchCustomer()
    {
        try {
            $searchTerm = $this->request->getGet('q') ?? '';
            
            if (empty($searchTerm)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Search term is required'
                ]);
            }

            $customers = $this->pelangganModel->searchCustomer($searchTerm);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $customers
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mencari customer: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get customer by id_user
     */
    public function getCustomerByIdUser()
    {
        try {
            $idUser = $this->request->getGet('id_user') ?? '';
            
            if (empty($idUser)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID User is required'
                ]);
            }

            $customer = $this->pelangganModel->getCustomerByIdUser($idUser);
            
            if (!$customer) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Customer tidak ditemukan'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $customer
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data customer: ' . $e->getMessage()
            ]);
        }
    }
} 