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
use App\Models\KaryawanModel;
use App\Models\GudangModel;
use App\Models\PlatformModel;
use App\Models\OutletModel;
use App\Models\ItemHistModel;


class TransJual extends BaseController
{
    protected $transJualModel;
    protected $transJualDetModel;
    protected $transJualPlatModel;
    protected $pelangganModel;
    protected $itemModel;
    protected $karyawanModel;
    protected $gudangModel;
    protected $platformModel;
    protected $outletModel;
    protected $itemHistModel;


    public function __construct()
    {
        $this->transJualModel      = new TransJualModel();
        $this->transJualDetModel   = new TransJualDetModel();
        $this->transJualPlatModel  = new TransJualPlatModel();
        $this->pelangganModel      = new PelangganModel();
        $this->itemModel           = new ItemModel();
        $this->karyawanModel       = new KaryawanModel();
        $this->gudangModel         = new GudangModel();
        $this->platformModel       = new PlatformModel();
        $this->outletModel         = new OutletModel();
        $this->itemHistModel       = new ItemHistModel();

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
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        // Handle both GET and POST requests
        $search = $this->request->getVar('search');
        $warehouseId = $this->request->getVar('warehouse_id');

        log_message('info', 'Search term: ' . $search . ', Warehouse ID: ' . $warehouseId);

        if (empty($search)) {
            // If no search term, return items with relations
            $items = $this->itemModel->getItemsWithRelationsActive(10);
            log_message('info', 'Returning ' . count($items) . ' items without search');
            return $this->response->setJSON(['items' => $items]);
        }

        // Use the getItemsWithRelationsActive method for search
        $items = $this->itemModel->getItemsWithRelationsActive(10, $search);

        // If warehouse filter is applied, we need to join with stock table
        if ($warehouseId) {
            $builder = $this->itemModel->db->table('tbl_m_item mi');
            $builder->select('mi.*, mis.stok, mk.kategori, mm.merk, ms.nama as satuan, msup.nama as supplier');
            $builder->join('tbl_m_item_stok mis', 'mis.id_item = mi.id AND mis.id_gudang = ' . $warehouseId, 'left');
            $builder->join('tbl_m_kategori mk', 'mk.id = mi.id_kategori', 'left');
            $builder->join('tbl_m_merk mm', 'mm.id = mi.id_merk', 'left');
            $builder->join('tbl_m_satuan ms', 'ms.id = mi.id_satuan', 'left');
            $builder->join('tbl_m_supplier msup', 'msup.id = mi.id_supplier', 'left');

            $builder->groupStart()
                    ->like('mi.kode', $search)
                    ->orLike('mi.item', $search)
                    ->orLike('mi.barcode', $search)
                    ->orLike('mk.kategori', $search)
                    ->orLike('mm.merk', $search)
                    ->orLike('msup.nama', $search)
                    ->groupEnd();
            
            $builder->where('mi.status', '1');
            $builder->where('mi.status_hps', '0');
            $builder->limit(10);
            
            $items = $builder->get()->getResult();
        }

        return $this->response->setJSON(['items' => $items]);
    }

    /**
     * Get customer information (AJAX)
     */
    public function getCustomerInfo($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $customer = $this->pelangganModel->find($id);
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

        // For now, implement a simple voucher validation
        // You can replace this with actual database lookup
        $validVouchers = [
            'DISKON10' => 10,
            'DISKON20' => 20,
            'DISKON25' => 25,
            'HAPPY2024' => 15,
            'NEWYEAR2024' => 30
        ];

        if (array_key_exists(strtoupper($voucherCode), $validVouchers)) {
            return $this->response->setJSON([
                'valid' => true,
                'discount' => $validVouchers[strtoupper($voucherCode)],
                'message' => 'Voucher valid'
            ]);
        } else {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Kode voucher tidak valid'
            ]);
        }
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
        $jml_subtotal      = $this->request->getPost('subtotal');
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
            'diskon'            => $diskon,
            'jml_diskon'        => $jml_diskon,
            'ppn'               => $ppn,
            'jml_ppn'           => $jml_ppn,
            'jml_gtotal'        => $jml_gtotal,
            'metode_bayar'      => $metode_bayar,
            'status'            => '0', // Draft
            'status_bayar'      => '0', // Belum lunas
        ];

        // Get items data
        $items = $this->request->getPost('items');
        $platforms = $this->request->getPost('platforms');

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

        // Validate required data
        if (empty($cart) || !is_array($cart) || count($cart) === 0) {
            return $this->response->setJSON(['error' => 'Keranjang belanja kosong']);
        }

        if (empty($warehouseId)) {
            return $this->response->setJSON(['error' => 'Gudang harus dipilih']);
        }

        if (empty($paymentMethods) || !is_array($paymentMethods)) {
            return $this->response->setJSON(['error' => 'Metode pembayaran harus diisi']);
        }

        if ($totalAmountReceived < $grandTotal) {
            return $this->response->setJSON(['error' => 'Jumlah bayar kurang dari total']);
        }

        try {
            $this->db = \Config\Database::connect();
            $this->db->transStart();

            $noNota = $this->transJualModel->generateKode();

            // Calculate totals
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += ($item['price'] * $item['quantity']);
            }

            $discountAmount = $subtotal * ($discountPercent / 100);
            $afterDiscount  = $subtotal - $discountAmount;
            $voucherAmount  = $afterDiscount * ($voucherDiscount / 100);
            $afterVoucher   = $afterDiscount - $voucherAmount;
            $taxAmount      = $afterVoucher * 0.11; // 11% PPN
            $finalTotal     = $afterVoucher + $taxAmount;
            $change         = $totalAmountReceived - $finalTotal;
            $change         = $change < 0 ? 0 : $change;

            // Prepare transaction data
            $transactionData = [
                'id_user'           => $this->ionAuth->user()->row()->id,
                'id_sales'          => null, // Can be added later if needed
                'id_pelanggan'      => $customerId,
                'id_gudang'         => $warehouseId,
                'no_nota'           => $noNota,
                'tgl_masuk'         => date('Y-m-d H:i:s'),
                'tgl_bayar'         => date('Y-m-d H:i:s'),
                'jml_subtotal'      => $subtotal,
                'diskon'            => $discountPercent,
                'jml_diskon'        => $discountAmount,
                'ppn'               => 11, // 11%
                'jml_ppn'           => $taxAmount,
                'jml_gtotal'        => $finalTotal,
                'jml_bayar'         => $totalAmountReceived,
                'jml_kembali'       => $change,
                'metode_bayar'      => 'multiple', // Multiple payment methods
                'status'            => '1', // Completed
                'status_nota'       => '1', // Completed
                'status_bayar'      => '1', // Paid
                'status_ppn'        => '1'  // PPN included
            ];

            // Insert main transaction
            $this->transJualModel->insert($transactionData);
            $transactionId = $this->transJualModel->getInsertID();

            // Insert transaction details
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

                // Update stock (decrease stock)
                if ($warehouseId) {
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

            // Insert multiple platform payments
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
} 