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
use App\Models\OutletModel;
use App\Models\ItemHistModel;
use App\Models\VoucherModel;
use App\Models\PengaturanModel;
use App\Services\PrinterService;


class TransJual extends BaseController
{
    protected $transJualModel;
    protected $transJualDetModel;
    protected $transJualPlatModel;
    protected $pelangganModel;
    protected $itemModel;
    protected $itemStokModel;
    protected $karyawanModel;
    protected $gudangModel;
    protected $platformModel;
    protected $outletModel;
    protected $itemHistModel;
    protected $voucherModel;
    protected $pengaturanModel;
    protected $printerService;


    public function __construct()
    {
        $this->transJualModel      = new TransJualModel();
        $this->transJualDetModel   = new TransJualDetModel();
        $this->transJualPlatModel  = new TransJualPlatModel();
        $this->pelangganModel      = new PelangganModel();
        $this->itemModel           = new ItemModel();
        $this->itemStokModel       = new ItemStokModel();
        $this->karyawanModel       = new KaryawanModel();
        $this->gudangModel         = new GudangModel();
        $this->platformModel       = new PlatformModel();
        $this->outletModel         = new OutletModel();
        $this->itemHistModel       = new ItemHistModel();
        $this->voucherModel        = new VoucherModel();
        $this->Pengaturan     = new PengaturanModel();
        $this->printerService      = new PrinterService();

    }

    /**
     * Display cashier interface for sales transactions
     */
    public function index()
    {
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
     * Get transaction details by ID (AJAX)
     */
    public function getTransactionDetails($id)
    {
        $transaction = $this->transJualModel->find($id);
        if (!$transaction) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['error' => 'Transaction not found']);
            } else {
                return redirect()->to(base_url('transaksi/jual'))->with('error', 'Transaction not found');
            }
        }

        $details = $this->transJualDetModel->getDetailsWithItem($id);
        $platforms = $this->transJualPlatModel->getPlatformsWithInfo($id);

        // If AJAX request, return JSON
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'     => true,
                'transaction' => $transaction,
                'details'     => $details,
                'platforms'   => $platforms,
            ]);
        }

        // If direct browser access, redirect to main transaction list with search
        return redirect()->to(base_url('transaksi/jual?search=' . $transaction->no_nota));
    }

    /**
     * Search items for cashier (AJAX)
     */
    public function searchItems()
    {
        // if (!$this->request->isAJAX()) {
        //     return $this->response->setJSON(['error' => 'Invalid request']);
        // }

        // Handle both GET and POST requests
        $search = $this->request->getVar('search');
        $warehouseId = $this->request->getVar('warehouse_id');
        
        // Log the search parameters
        log_message('info', 'Search request - search: "' . $search . '", warehouse_id: ' . $warehouseId);

        // If warehouse filter is applied, use ItemModel to get items by warehouse
        if ($warehouseId) {
            try {
                $items = $this->itemModel->getItemsByWarehouse($warehouseId, $search);
            } catch (\Exception $e) {
                log_message('error', 'Error loading warehouse items: ' . $e->getMessage());
                log_message('error', 'Stack trace: ' . $e->getTraceAsString());
                return $this->response->setJSON([
                    'error' => 'Failed to load warehouse items: ' . $e->getMessage()
                ])->setStatusCode(500);
            }
        } else {
            // No warehouse selected, use default method
            if (empty($search)) {
                // If no search term, return items with relations
                $items = $this->itemModel->getItemsWithRelationsActive(10);
            } else {
                $items = $this->itemModel->getItemsWithRelationsActive(10, $search);
            }
        }

        return $this->response->setJSON(['items' => $items]);
    }

    /**
     * Test method to debug warehouse loading
     */
    public function testWarehouse($warehouseId = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        try {
            if ($warehouseId) {
                log_message('info', 'Testing warehouse loading for ID: ' . $warehouseId);
                
                // Test database connection
                $db = \Config\Database::connect();
                log_message('info', 'Database connected successfully');
                
                // Test if tables exist
                $tables = $db->listTables();
                log_message('info', 'Available tables: ' . implode(', ', $tables));
                
                // Test if specific tables exist
                $itemStokExists = in_array('tbl_m_item_stok', $tables);
                $itemExists = in_array('tbl_m_item', $tables);
                $kategoriExists = in_array('tbl_m_kategori', $tables);
                $merkExists = in_array('tbl_m_merk', $tables);
                $satuanExists = in_array('tbl_m_satuan', $tables);
                
                log_message('info', 'Table existence - ItemStok: ' . ($itemStokExists ? 'Yes' : 'No') . 
                           ', Item: ' . ($itemExists ? 'Yes' : 'No') . 
                           ', Kategori: ' . ($kategoriExists ? 'Yes' : 'No') . 
                           ', Merk: ' . ($merkExists ? 'Yes' : 'No') . 
                           ', Satuan: ' . ($satuanExists ? 'Yes' : 'No'));
                
                if ($itemStokExists && $itemExists) {
                    // Test the actual query
                    $items = $this->itemStokModel->getStockByWarehouse($warehouseId);
                    log_message('info', 'Query successful, returned ' . count($items) . ' items');
                    
                    return $this->response->setJSON([
                        'success' => true,
                        'items' => $items,
                        'count' => count($items),
                        'debug' => [
                            'warehouse_id' => $warehouseId,
                            'tables_exist' => [
                                'item_stok' => $itemStokExists,
                                'item' => $itemExists,
                                'kategori' => $kategoriExists,
                                'merk' => $merkExists,
                                'satuan' => $satuanExists
                            ]
                        ]
                    ]);
                } else {
                    return $this->response->setJSON([
                        'error' => 'Required tables do not exist',
                        'debug' => [
                            'tables_exist' => [
                                'item_stok' => $itemStokExists,
                                'item' => $itemExists,
                                'kategori' => $kategoriExists,
                                'merk' => $merkExists,
                                'satuan' => $satuanExists
                            ]
                        ]
                    ]);
                }
            } else {
                return $this->response->setJSON(['error' => 'Warehouse ID required']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Test warehouse error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'error' => 'Test failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ])->setStatusCode(500);
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

        $customer = $this->pelangganModel->where('id_user', $id)->first();
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
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Voucher tidak valid atau sudah habis'
            ]);
        }

        // Return voucher details
        $discountValue = $voucher->jenis_voucher === 'persen' ? $voucher->nominal : 0;
        $discountAmount = $voucher->jenis_voucher === 'nominal' ? $voucher->nominal : 0;
        
        return $this->response->setJSON([
            'valid' => true,
            'discount' => $discountValue, // Percentage for percentage vouchers
            'discount_amount' => $discountAmount, // Fixed amount for nominal vouchers
            'jenis_voucher' => $voucher->jenis_voucher,
            'voucher_id' => $voucher->id,
            'message' => 'Voucher valid'
        ]);
    }

    /**
     * Display cashier interface
     */
    public function cashier()
    {
        // Get related data for dropdowns (formatted)
        $customers  = $this->pelangganModel->where('status_blokir', '0')->findAll();
        $sales      = $this->karyawanModel->where('status', '1')->findAll();
        $warehouses = $this->gudangModel->where('status', '1')->where('status_otl', '0')->where('status_hps', '0')->findAll();
        $outlets    = $this->gudangModel->where('status', '1')->where('status_otl', '1')->where('status_hps', '0')->findAll();
        
        $platforms  = $this->platformModel->where('status', '1')->findAll();
        $items      = $this->itemModel->getItemsWithRelationsActive(100); // Get items with relations
        
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
            'lastTransactions' => $lastTransactions
        ];

        return $this->view($this->theme->getThemePath() . '/transaksi/jual/cashier', $data);
    }

    /**
     * Display create sales transaction form
     */
    public function create()
    {
        // Get related data for dropdowns
        $customers  = $this->pelangganModel->where('status_blokir', '0')->findAll();
        $sales      = $this->karyawanModel->where('status', '1')->findAll();
        $warehouses = $this->gudangModel->where('status', '1')->where('status_otl', '0')->where('status_hps', '0')->findAll();
        $outlets    = $this->gudangModel->where('status', '1')->where('status_otl', '1')->where('status_hps', '0')->findAll();
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
        $paymentMethods     = $this->request->getPost('payment_methods') ?: [];
        $totalAmountReceived = $this->request->getPost('total_amount_received') ?: 0;
        $grandTotal         = $this->request->getPost('grand_total') ?: 0;
        $isDraft            = $this->request->getPost('is_draft') ?: false;
        $draftId            = $this->request->getPost('draft_id') ?: null; // ID of draft being converted

        // Convert string boolean to actual boolean
        if (is_string($isDraft)) {
            $isDraft = ($isDraft === 'true' || $isDraft === '1');
        }

        // Debug: Log all POST data
        log_message('debug', 'POST data - is_draft: ' . ($isDraft ? 'true' : 'false') . ', draft_id: ' . ($draftId ?: 'null'));
        log_message('debug', 'POST data type - is_draft: ' . gettype($isDraft) . ', draft_id: ' . gettype($draftId));

        // Validate required data
        if (empty($cart) || !is_array($cart) || count($cart) === 0) {
            return $this->response->setJSON(['error' => 'Keranjang belanja kosong']);
        }

        if (empty($warehouseId)) {
            return $this->response->setJSON(['error' => 'Gudang harus dipilih']);
        }

        // Skip payment validation for drafts
        if (!$isDraft) {
            if (empty($paymentMethods) || !is_array($paymentMethods)) {
                return $this->response->setJSON(['error' => 'Metode pembayaran harus diisi']);
            }

            if ($totalAmountReceived < $grandTotal) {
                return $this->response->setJSON(['error' => 'Jumlah bayar kurang dari total']);
            }
        }

        try {
            $this->db = \Config\Database::connect();
            $this->db->transStart();

            // Debug: Log the values
            log_message('debug', 'processTransaction - draftId: ' . ($draftId ?: 'null') . ', isDraft: ' . ($isDraft ? 'true' : 'false'));

            $noNota = $this->transJualModel->generateKode();
            $Pengaturan = $this->Pengaturan->first();

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

            // Calculate voucher (assume voucherDiscount is percent, adjust if nominal)
            $voucherAmount = 0;
            if ($voucherDiscount > 0) {
                // If voucherDiscount is percent (e.g. 10 for 10%)
                $voucherAmount = $jml_total * ($voucherDiscount / 100);
            }

            // jml_diskon = total diskon (voucher + diskon % or nominal)
            $jml_diskon = $discountAmount + $voucherAmount;

            // jml_subtotal = jml_total - jml_diskon
            $jml_subtotal = $jml_total - $jml_diskon;

            // PPN 11%
            $taxAmount = $jml_subtotal * ($Pengaturan['ppn'] / 100); // 11% PPN

            // Grand total
            $finalTotal = $jml_subtotal + $taxAmount;

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
                'id_sales'          => $this->ionAuth->user()->row()->id, // Can be added later if needed
                'id_pelanggan'      => $customerId,
                'id_gudang'         => $warehouseId,
                'no_nota'           => $noNota,
                'tgl_masuk'         => date('Y-m-d H:i:s'),
                'tgl_bayar'         => $isDraft ? null : date('Y-m-d H:i:s'),
                'jml_total'         => $jml_total,         // total cart before voucher input
                'jml_subtotal'      => $jml_subtotal,      // jml_total - jml_diskon
                'diskon'            => $discountPercent,
                'jml_diskon'        => $jml_diskon,        // voucher + diskon % or nominal
                'ppn'               => $Pengaturan['ppn'], // dinamis dari db
                'jml_ppn'           => $taxAmount,
                'jml_gtotal'        => $finalTotal,
                'jml_bayar'         => $isDraft ? 0 : $totalAmountReceived,
                'jml_kembali'       => $isDraft ? 0 : $change,
                'metode_bayar'      => $isDraft ? 'draft' : 'multiple', // Draft or Multiple payment methods
                'status'            => $isDraft ? '0' : '1', // 0=Draft, 1=Completed
                'status_nota'       => $isDraft ? '0' : '1', // 0=Draft, 1=Completed
                'status_bayar'      => $isDraft ? '0' : ($hasPiutang ? '0' : '1'), // 0=Unpaid/Draft, 1=Paid
                'status_ppn'        => '1'  // PPN included
            ];

            // Insert or update main transaction
            if ($draftId && !$isDraft) {
                // Update existing draft to completed transaction
                log_message('debug', 'Updating existing draft ID: ' . $draftId);
                $this->transJualModel->update($draftId, $transactionData);
                $transactionId = $draftId;
            } else {
                // Insert new transaction
                log_message('debug', 'Inserting new transaction - draftId: ' . ($draftId ?: 'null') . ', isDraft: ' . ($isDraft ? 'true' : 'false'));
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
                    'jml_satuan'     => $item['quantity'],
                    'disk1'          => 0,
                    'disk2'          => 0,
                    'disk3'          => 0,
                    'diskon'         => 0,
                    'potongan'       => 0,
                    'subtotal'       => $item['price'] * $item['quantity'],
                    'status'         => 1
                ];

                $this->transJualDetModel->insert($detailData);

                // Update stock (decrease stock) - only for completed transactions, not drafts
                if (!$isDraft && $warehouseId) {
                    $this->updateStock($item['id'], $warehouseId, $item['quantity'], 'decrease');
                }

                // Insert item history record (Stok Keluar Penjualan - status 4)
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
                    'jml_satuan'     => $item['quantity'],
                    'satuan'         => $itemDetails->satuan ?? 'PCS',
                    'status'         => '4', // Stok Keluar Penjualan
                    'sp'             => null
                ];

                $this->itemHistModel->insert($historyData);
            }

            // Insert multiple platform payments - only for completed transactions, not drafts
            if (!$isDraft && !empty($paymentMethods)) {
                // If converting from draft, delete existing platform payments first
                if ($draftId) {
                    $this->transJualPlatModel->where('id_penjualan', $draftId)->delete();
                }
                
                foreach ($paymentMethods as $payment) {
                    if (!empty($payment['platform_id']) && !empty($payment['amount'])) {
                        $platform = $this->platformModel->find($payment['platform_id']);
                        $platformData = [
                            'id_penjualan' => $transactionId,
                            'id_platform'  => $payment['platform_id'],
                            'no_nota'      => $noNota,
                            'platform'     => $platform->platform ?? $payment['type'],
                            'keterangan'   => 'Pembayaran via ' . $payment['type'] . 
                                            (!empty($payment['reference']) ? ' - ' . $payment['reference'] : ''),
                            'nominal'      => $payment['amount']
                        ];

                        $this->transJualPlatModel->insert($platformData);

                        $this->transJualModel->update($transactionId, ['metode_bayar' => $payment['type']]);
                    }
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Transaksi berhasil diproses',
                'transaction_id' => $transactionId,
                'no_nota' => $noNota,
                'total' => $finalTotal,
                'change' => $change
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
     * Print receipt for transaction
     */
    public function printReceipt($transactionId = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        try {
            // Get transaction data
            $transaction = $this->transJualModel->find($transactionId);
            
            if (!$transaction) {
                return $this->response->setJSON(['success' => false, 'message' => 'Transaksi tidak ditemukan']);
            }
            
            // Get transaction details
            $items = $this->transJualDetModel->where('id_trans_jual', $transactionId)->findAll();
            
            // Get customer info
            $customer = null;
            if ($transaction->id_pelanggan) {
                $customer = $this->pelangganModel->find($transaction->id_pelanggan);
            }

            // Get kasir info
            $kasir = $this->karyawanModel->find($transaction->id_karyawan);

            // Prepare data for printing
            $printData = [
                'no_nota' => $transaction->no_nota,
                'tgl_masuk' => $transaction->created_at,
                'kasir' => $kasir ? $kasir->nama : 'Unknown',
                'customer_name' => $customer ? $customer->nama : null,
                'jml_subtotal' => $transaction->jml_subtotal,
                'jml_diskon' => $transaction->jml_diskon,
                'jml_ppn' => $transaction->jml_ppn,
                'jml_gtotal' => $transaction->jml_gtotal,
                'items' => []
            ];

            // Format items for printing
            foreach ($items as $item) {
                $printData['items'][] = [
                    'name' => $item->nama_item,
                    'quantity' => $item->qty,
                    'price' => $item->harga_jual,
                    'total' => $item->jml_total
                ];
            }

            // Print receipt
            $this->printerService->printReceipt($printData);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Struk berhasil dicetak'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mencetak struk: ' . $e->getMessage()
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
     * Get transaction data for printing
     */
    public function getTransactionForPrint($transactionId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        try {
            // Get transaction data
            $transaction = $this->transJualModel->find($transactionId);
            
            if (!$transaction) {
                return $this->response->setJSON(['success' => false, 'message' => 'Transaksi tidak ditemukan']);
            }
            
            // Get transaction details - using correct column names
            $items = $this->transJualDetModel
                ->select('tbl_trans_jual_det.*')
                ->where('id_penjualan', $transactionId)
                ->findAll();
            
            // Get payment methods
            $paymentMethods = $this->transJualPlatModel
                ->where('id_penjualan', $transactionId)
                ->findAll();
            
            // Get outlet name
            $outlet = null;
            if ($transaction->id_gudang) {
                $outlet = $this->gudangModel->find($transaction->id_gudang);
            }
            
            // Get customer info
            $customer = null;
            if ($transaction->id_pelanggan) {
                $customer = $this->pelangganModel->find($transaction->id_pelanggan);
            }

            // Prepare data for printing
            $printData = [
                'no_nota' => $transaction->no_nota,
                'customer_name' => $customer ? $customer->nama_pelanggan : 'Umum',
                'customer_type' => $customer ? $customer->tipe_pelanggan : 'umum',
                'outlet' => $outlet ? $outlet->nama_gudang : 'Outlet',
                'date' => date('d/m/Y H:i', strtotime($transaction->created_at)),
                'subtotal' => $transaction->jml_subtotal,
                'discount' => $transaction->jml_diskon > 0 ? ($transaction->jml_diskon / $transaction->jml_subtotal) * 100 : 0,
                'voucher' => $transaction->voucher_code ?? '',
                'ppn' => 11, // 11% PPN
                'total' => $transaction->jml_gtotal,
                'items' => [],
                'payment_methods' => []
            ];

            // Format items for printing - using correct column names
            foreach ($items as $item) {
                $printData['items'][] = [
                    'name' => $item->produk, // Using 'produk' instead of 'nama_item'
                    'quantity' => $item->jml, // Using 'jml' instead of 'qty'
                    'price' => $item->harga, // Using 'harga' instead of 'harga_jual'
                    'total' => $item->subtotal // Using 'subtotal' instead of 'jml_total'
                ];
            }

            // Format payment methods for printing
            foreach ($paymentMethods as $payment) {
                $printData['payment_methods'][] = [
                    'type' => $payment->type,
                    'amount' => $payment->amount
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'transaction' => $printData
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat data transaksi: ' . $e->getMessage()
            ]);
        }
    }
} 