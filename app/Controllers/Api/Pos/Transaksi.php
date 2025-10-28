<?php

namespace App\Controllers\Api\Pos;

use App\Controllers\BaseController;
use App\Models\TransJualModel;
use App\Models\TransJualDetModel;
use App\Models\TransJualPlatModel;
use App\Models\ItemModel;
use App\Models\ItemHistModel;
use App\Models\PlatformModel;
use App\Models\PelangganModel;
use App\Models\VoucherModel;
use App\Models\ShiftModel;
use CodeIgniter\API\ResponseTrait;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-08-06
 * Github: github.com/mikhaelfelian
 * description: API controller for managing Transactions (Transaksi) for the POS.
 * This file represents the Transaksi API controller.
 */
class Transaksi extends BaseController
{
    use ResponseTrait;

    protected $mTransJual;
    protected $mTransJualDet;
    protected $mTransJualPlat;
    protected $mItem;
    protected $mItemHist;
    protected $mPlatform;
    protected $mPelanggan;
    protected $mVoucher;
    protected $mShift;

    /**
     * Initialize all model properties in the constructor for reuse.
     */
    public function __construct()
    {
        $this->mTransJual      = new TransJualModel();
        $this->mTransJualDet   = new TransJualDetModel();
        $this->mTransJualPlat  = new TransJualPlatModel();
        $this->mItem           = new ItemModel();
        $this->mItemHist       = new ItemHistModel();
        $this->mPlatform       = new PlatformModel();
        $this->mPelanggan      = new PelangganModel();
        $this->mVoucher        = new VoucherModel();
        $this->mShift          = new ShiftModel();
    }

    /**
     * Check if shift is open for the current user and outlet
     * 
     * @param int $outlet_id
     * @param int $user_id
     * @return bool
     */
    private function isShiftOpen($outlet_id, $user_id)
    {
        $activeShift = $this->mShift->where('outlet_id', $outlet_id)
                                   ->where('user_open_id', $user_id)
                                   ->where('status', 'open')
                                   ->first();
        
        return $activeShift !== null;
    }

    /**
     * Get transactions by customer ID (id_pelanggan).
     * 
     * @param int|null $id_pelanggan Customer ID to filter transactions
     * @return \CodeIgniter\HTTP\Response
     */
    public function getTransaction($id_pelanggan = null)
    {
        $mTransJual = $this->mTransJual;
        $mTransJualPlat = $this->mTransJualPlat;
        $mPelanggan = $this->mPelanggan;

        // If no id_pelanggan provided, get it from request
        if ($id_pelanggan === null) {
            $id_pelanggan = $this->request->getGet('id_pelanggan');
        }

        // Validate id_pelanggan parameter
        if (!$id_pelanggan) {
            return $this->failValidationErrors('Parameter id_pelanggan is required');
        }

        try {
            // Get pelanggan name from tbl_m_pelanggan
            $pelanggan = $mPelanggan->where('id_user', $id_pelanggan)->first();
            $nama_pelanggan = $pelanggan ? $pelanggan->nama : null;

            // Get transactions from tbl_trans_jual where id_pelanggan matches
            $transactions = $mTransJual->where('id_pelanggan', $id_pelanggan)->findAll();

            // Use property for TransJualDetModel
            $mTransJualDet = $this->mTransJualDet;

            // Format the response data
            $formattedTransactions = [];
            foreach ($transactions as $transaction) {
                // Get transaction details from tbl_trans_jual_det where id_penjualan matches
                $details = $mTransJualDet->where('id_penjualan', $transaction->id)->findAll();

                // Format transaction details
                $formattedDetails = [];
                foreach ($details as $detail) {
                    $formattedDetails[] = [
                        'id'             => (int)$detail->id,
                        'id_penjualan'   => (int)$detail->id_penjualan,
                        'id_item'        => (int)$detail->id_item,
                        'id_satuan'      => (int)$detail->id_satuan,
                        'id_kategori'    => (int)$detail->id_kategori,
                        'id_merk'        => (int)$detail->id_merk,
                        'created_at'     => $detail->created_at,
                        'updated_at'     => $detail->updated_at,
                        'no_nota'        => $detail->no_nota,
                        'kode'           => $detail->kode,
                        'produk'         => $detail->produk,
                        'satuan'         => $detail->satuan,
                        'keterangan'     => $detail->keterangan,
                        'harga'          => (float)$detail->harga,
                        'harga_beli'     => (float)$detail->harga_beli,
                        'jml'            => (int)$detail->jml,
                        'jml_satuan'     => (int)$detail->jml_satuan,
                        'disk1'          => (float)$detail->disk1,
                        'disk2'          => (float)$detail->disk2,
                        'disk3'          => (float)$detail->disk3,
                        'diskon'         => (float)$detail->diskon,
                        'potongan'       => (float)$detail->potongan,
                        'subtotal'       => (float)$detail->subtotal,
                        'status'         => (int)$detail->status
                    ];
                }

                // Get platform data for this transaction using mTransJualPlat
                $platformData = $mTransJualPlat->where('id_penjualan', $transaction->id)->findAll();

                $formattedTransactions[] = [
                    'id'             => (int)$transaction->id,
                    'id_user'        => (int)$transaction->id_user,
                    'id_sales'       => (int)$transaction->id_sales,
                    'id_pelanggan'   => (int)$transaction->id_pelanggan,
                    'id_gudang'      => (int)$transaction->id_gudang,
                    'pelanggan'      => $nama_pelanggan ?? 'Umum',
                    'no_nota'        => $transaction->no_nota,
                    'created_at'     => $transaction->created_at,
                    'updated_at'     => $transaction->updated_at,
                    'deleted_at'     => $transaction->deleted_at,
                    'tgl_bayar'      => $transaction->tgl_bayar,
                    'tgl_masuk'      => $transaction->tgl_masuk,
                    'tgl_keluar'     => $transaction->tgl_keluar,
                    'jml_total'      => (float)$transaction->jml_total,
                    'jml_biaya'      => (float)$transaction->jml_biaya,
                    'jml_ongkir'     => (float)$transaction->jml_ongkir,
                    'jml_retur'      => (float)$transaction->jml_retur,
                    'diskon'         => (float)$transaction->diskon,
                    'jml_diskon'     => (float)$transaction->jml_diskon,
                    'jml_subtotal'   => (float)$transaction->jml_subtotal,
                    'ppn'            => (int)$transaction->ppn,
                    'jml_ppn'        => (float)$transaction->jml_ppn,
                    'jml_gtotal'     => (float)$transaction->jml_gtotal,
                    'jml_bayar'      => (float)$transaction->jml_bayar,
                    'jml_kembali'    => (float)$transaction->jml_kembali,
                    'jml_kurang'     => (float)$transaction->jml_kurang,
                    'disk1'          => (float)$transaction->disk1,
                    'jml_disk1'      => (float)$transaction->jml_disk1,
                    'disk2'          => (float)$transaction->disk2,
                    'jml_disk2'      => (float)$transaction->jml_disk2,
                    'disk3'          => (float)$transaction->disk3,
                    'jml_disk3'      => (float)$transaction->jml_disk3,
                    'metode_bayar'   => $transaction->metode_bayar,
                    'status'         => $transaction->status,
                    'status_nota'    => $transaction->status_nota,
                    'status_ppn'     => $transaction->status_ppn,
                    'status_bayar'   => $transaction->status_bayar,
                    'status_retur'   => $transaction->status_retur,
                    'details'        => $formattedDetails,
                    'platform'       => $platformData
                ];
            }

            $data = [
                'success'      => true,
                'message'      => 'Transactions retrieved successfully',
                'total'        => count($formattedTransactions),
                'transactions' => $formattedTransactions,
            ];

            return $this->respond($data);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to retrieve transactions: ' . $e->getMessage());
        }
    }

    /**
     * Store new transaction (based on TransJual::processTransaction)
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    /**
     * Store new transaction from mobile android using JSON method
     * 
     * Accepts JSON payload from mobile app, processes transaction, and returns JSON response.
     * Supports both draft and completed transactions based on 'is_draft' parameter.
     */
    public function store()
    {
        $input = $this->request->getJSON(true);

        // Get draft parameters
        $isDraft = isset($input['is_draft']) ? (bool)$input['is_draft'] : false;
        $draftId = $input['draft_id'] ?? null;

        // Check if shift is open before allowing transaction (skip for drafts)
        if (!$isDraft && !$this->isShiftOpen($input['id_gudang'], $input['id_user'])) {
            return $this->failValidationErrors('Shift tidak terbuka. Silakan buka shift terlebih dahulu.');
        }

        // Validate required fields (more lenient for drafts)
        if (
            empty($input['id_user']) ||
            empty($input['id_gudang']) ||
            empty($input['cart']) ||
            !is_array($input['cart']) ||
            count($input['cart']) === 0
        ) {
            return $this->failValidationErrors('Data transaksi utama atau cart tidak lengkap');
        }

        // Additional validation for completed transactions (not drafts)
        if (!$isDraft) {
            if (
                empty($input['id_sales']) ||
                empty($input['id_pelanggan']) ||
                empty($input['no_nota']) ||
                empty($input['tgl_masuk']) ||
                empty($input['jml_total']) ||
                empty($input['jml_gtotal'])
            ) {
                return $this->failValidationErrors('Data transaksi tidak lengkap untuk transaksi final');
            }

            if (empty($input['payment_methods']) || !is_array($input['payment_methods'])) {
                return $this->failValidationErrors('Metode pembayaran harus diisi');
            }

            // Enforce blocked-member rule for Piutang Anggota
            $paymentGuard = new \App\Libraries\PaymentGuard();
            $guardResult = $paymentGuard->allowPayment(isset($input['id_pelanggan']) ? (int) $input['id_pelanggan'] : null, $input['payment_methods']);
            if (!$guardResult['allowed']) {
                return $this->respond([
                    'success' => false,
                    'error' => $guardResult['message']
                ], 400);
            }
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            $mTransJual     = $this->mTransJual;
            $mTransJualDet  = $this->mTransJualDet;
            $mTransJualPlat = $this->mTransJualPlat;
            $mItemHist      = $this->mItemHist;

            // Generate nota number if not provided or if creating draft
            $noNota = $input['no_nota'] ?? $this->generateNotaNumber();
            
            // If converting from draft, use existing draft data
            if ($draftId && !$isDraft) {
                // Get existing draft
                $existingDraft = $mTransJual->find($draftId);
                if (!$existingDraft || $existingDraft->status != '0') {
                    throw new \Exception('Draft tidak ditemukan atau sudah diproses');
                }
                $noNota = $existingDraft->no_nota; // Use existing nota number
            }

            // Insert main transaction
            $mainData = [
                'id_user'        => $input['id_user'],
                'id_sales'       => $input['id_sales'] ?? null,
                'id_pelanggan'   => $input['id_pelanggan'] ?? null,
                'id_gudang'      => $input['id_gudang'],
                'id_shift'       => $input['id_shift'] ?? null,
                'no_nota'        => $noNota,
                'tgl_masuk'      => $input['tgl_masuk'] ?? date('Y-m-d H:i:s'),
                'tgl_bayar'      => $isDraft ? null : (isset($input['tgl_bayar']) ? $input['tgl_bayar'] : date('Y-m-d H:i:s')),
                'jml_total'      => $input['jml_total'] ?? 0,
                'jml_subtotal'   => isset($input['jml_subtotal']) ? $input['jml_subtotal'] : 0,
                'diskon'         => isset($input['diskon']) ? $input['diskon'] : 0,
                'jml_diskon'     => isset($input['jml_diskon']) ? $input['jml_diskon'] : 0,
                'ppn'            => isset($input['ppn']) ? $input['ppn'] : 0,
                'jml_ppn'        => isset($input['jml_ppn']) ? $input['jml_ppn'] : 0,
                'jml_gtotal'     => $input['jml_gtotal'] ?? 0,
                'jml_bayar'      => $isDraft ? 0 : (isset($input['jml_bayar']) ? $input['jml_bayar'] : 0),
                'jml_kembali'    => $isDraft ? 0 : (isset($input['jml_kembali']) ? $input['jml_kembali'] : 0),
                'metode_bayar'   => $isDraft ? 'draft' : (isset($input['metode_bayar']) ? $input['metode_bayar'] : 'multiple'),
                'status'         => $isDraft ? '0' : (isset($input['status']) ? $input['status'] : '1'),
                'status_nota'    => $isDraft ? '0' : (isset($input['status_nota']) ? $input['status_nota'] : '1'),
                'status_bayar'   => $isDraft ? '0' : (isset($input['status_bayar']) ? $input['status_bayar'] : '1'),
                'status_ppn'     => isset($input['status_ppn']) ? $input['status_ppn'] : '1',
                'voucher_code'   => $input['voucher_code'] ?? null,
                'voucher_discount' => $input['voucher_discount'] ?? 0,
                'voucher_id'     => $input['voucher_id'] ?? null,
                'voucher_type'   => $input['voucher_type'] ?? null,
                'voucher_discount_amount' => $input['voucher_discount_amount'] ?? 0
            ];

            // Insert or update main transaction
            if ($draftId && !$isDraft) {
                // Update existing draft to completed transaction
                $mTransJual->update($draftId, $mainData);
                $transactionId = $draftId;
            } else {
                // Insert new transaction
                $mTransJual->insert($mainData);
                $transactionId = $mTransJual->getInsertID();
            }

            // Insert cart details
            // If converting from draft, delete existing details first
            if ($draftId && !$isDraft) {
                $mTransJualDet->where('id_penjualan', $draftId)->delete();
            }
            
            foreach ($input['cart'] as $item) {
                $detailData = [
                    'id_penjualan'   => $transactionId,
                    'id_item'        => $item['id_item'],
                    'id_satuan'      => $item['id_satuan'] ?? null,
                    'id_kategori'    => $item['id_kategori'] ?? null,
                    'id_merk'        => $item['id_merk'] ?? null,
                    'no_nota'        => $noNota,
                    'kode'           => $item['kode'] ?? null,
                    'produk'         => $item['produk'],
                    'satuan'         => $item['satuan'] ?? null,
                    'keterangan'     => isset($item['keterangan']) ? $item['keterangan'] : null,
                    'harga'          => $item['harga'],
                    'harga_beli'     => isset($item['harga_beli']) ? $item['harga_beli'] : 0,
                    'jml'            => $item['jml'],
                    'jml_satuan'     => $item['jml_satuan'] ?? 1,
                    'disk1'          => isset($item['disk1']) ? $item['disk1'] : 0,
                    'disk2'          => isset($item['disk2']) ? $item['disk2'] : 0,
                    'disk3'          => isset($item['disk3']) ? $item['disk3'] : 0,
                    'diskon'         => isset($item['diskon']) ? $item['diskon'] : 0,
                    'potongan'       => isset($item['potongan']) ? $item['potongan'] : 0,
                    'subtotal'       => $item['subtotal'],
                    'status'         => isset($item['status']) ? $item['status'] : 1
                ];
                $mTransJualDet->insert($detailData);

                // Update stock (decrease stock) - only for completed transactions, not drafts
                if (!$isDraft) {
                    $this->updateStock($item['id_item'], $input['id_gudang'], $item['jml'], 'decrease');
                }
            }

            // Insert platform payments if any - only for completed transactions, not drafts
            if (!$isDraft && !empty($input['platform']) && is_array($input['platform'])) {
                // If converting from draft, delete existing platform payments first
                if ($draftId) {
                    $mTransJualPlat->where('id_penjualan', $draftId)->delete();
                }
                
                foreach ($input['platform'] as $plat) {
                    $platData = [
                        'id_penjualan' => $transactionId,
                        'id_platform'  => $plat['id_platform'],
                        'no_nota'      => $noNota,
                        'platform'     => $plat['platform'],
                        'keterangan'   => isset($plat['keterangan']) ? $plat['keterangan'] : null,
                        'nominal'      => $plat['nominal']
                    ];
                    $mTransJualPlat->insert($platData);
                }
            }

            // Insert item history if any
            if (!empty($input['hist']) && is_array($input['hist'])) {
                foreach ($input['hist'] as $hist) {
                    $histData = [
                        'id_item'        => $hist['id_item'],
                        'id_satuan'      => $hist['id_satuan'],
                        'id_gudang'      => $hist['id_gudang'],
                        'id_user'        => $hist['id_user'],
                        'id_pelanggan'   => $hist['id_pelanggan'],
                        'id_penjualan'   => $transactionId,
                        'tgl_masuk'      => $hist['tgl_masuk'],
                        'no_nota'        => $hist['no_nota'],
                        'kode'           => $hist['kode'],
                        'item'           => $hist['item'],
                        'keterangan'     => isset($hist['keterangan']) ? $hist['keterangan'] : null,
                        'nominal'        => $hist['nominal'],
                        'jml'            => $hist['jml'],
                        'jml_satuan'     => $hist['jml_satuan'],
                        'satuan'         => $hist['satuan'],
                        'status'         => $hist['status'],
                        'sp'             => isset($hist['sp']) ? $hist['sp'] : null
                    ];
                    $mItemHist->insert($histData);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            return $this->respond([
                'success'         => true,
                'message'         => $isDraft ? 'Draft berhasil disimpan' : 'Transaksi berhasil diproses',
                'transaction_id'  => $transactionId,
                'no_nota'         => $noNota,
                'total'           => $input['jml_gtotal'] ?? 0,
                'change'          => isset($input['jml_kembali']) ? $input['jml_kembali'] : 0,
                'is_draft'        => $isDraft
            ]);
        } catch (\Exception $e) {
            if (isset($db) && $db->transStatus() !== false) {
                $db->transRollback();
            }
            return $this->failServerError('Gagal memproses transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Get payment methods
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function getPaymentMethods()
    {
        try {
            $mPlatform = $this->mPlatform;
            $platforms = $mPlatform->where('status', '1')->findAll();

            $paymentMethods = [
                [
                    'id' => '1',
                    'name' => 'Tunai',
                    'type' => 'cash'
                ],
                [
                    'id' => '2', 
                    'name' => 'Transfer',
                    'type' => 'transfer'
                ],
                [
                    'id' => '3',
                    'name' => 'Piutang',
                    'type' => 'credit'
                ]
            ];

            // Add platforms as payment methods
            foreach ($platforms as $platform) {
                $paymentMethods[] = [
                    'id' => $platform->id,
                    'name' => $platform->platform,
                    'type' => 'platform'
                ];
            }

            return $this->respond([
                'success' => true,
                'payment_methods' => $paymentMethods
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to get payment methods: ' . $e->getMessage());
        }
    }

    /**
     * Validate voucher code
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function validateVoucher()
    {
        $voucherCode = $this->request->getPost('voucher_code');
        
        if (empty($voucherCode)) {
            return $this->failValidationErrors('Kode voucher tidak boleh kosong');
        }

        try {
            $mVoucher = $this->mVoucher;
            $voucher = $mVoucher->getVoucherByCode($voucherCode);
            
            if (!$voucher) {
                return $this->respond([
                    'valid' => false,
                    'message' => 'Kode voucher tidak ditemukan'
                ]);
            }

            // Check if voucher is valid and available
            if (!$mVoucher->isVoucherValid($voucherCode)) {
                return $this->respond([
                    'valid' => false,
                    'message' => 'Voucher tidak valid atau sudah habis'
                ]);
            }

            // Return voucher details
            $discountValue = $voucher->jenis_voucher === 'persen' ? $voucher->nominal : 0;
            $discountAmount = $voucher->jenis_voucher === 'nominal' ? $voucher->nominal : 0;
            
            return $this->respond([
                'valid' => true,
                'discount' => $discountValue,
                'discount_amount' => $discountAmount,
                'jenis_voucher' => $voucher->jenis_voucher,
                'voucher_id' => $voucher->id,
                'message' => 'Voucher valid'
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to validate voucher: ' . $e->getMessage());
        }
    }

    /**
     * Validate customer
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function validateCustomer()
    {
        $customerId = $this->request->getPost('customer_id');
        
        if (empty($customerId)) {
            return $this->failValidationErrors('Customer ID tidak boleh kosong');
        }

        try {
            $mPelanggan = $this->mPelanggan;
            $customer = $mPelanggan->find($customerId);
            
            if (!$customer) {
                return $this->respond([
                    'valid' => false,
                    'message' => 'Customer tidak ditemukan'
                ]);
            }

            // Check if customer is blocked
            if ($customer->status_blokir == '1') {
                return $this->respond([
                    'valid' => false,
                    'message' => 'Customer diblokir'
                ]);
            }

            return $this->respond([
                'valid' => true,
                'customer' => [
                    'id' => $customer->id,
                    'nama' => $customer->nama,
                    'tipe' => $customer->tipe ?? 'umum',
                    'status_blokir' => $customer->status_blokir
                ],
                'message' => 'Customer valid'
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to validate customer: ' . $e->getMessage());
        }
    }

    /**
     * Update stock for an item in a warehouse
     */
    private function updateStock($itemId, $warehouseId, $quantity, $action = 'decrease')
    {
        $builder = \Config\Database::connect()->table('tbl_m_item_stok');
        
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
     * Get voucher information by ID or code
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function getVoucher()
    {
        $voucherId = $this->request->getGet('id') ?? $this->request->getPost('id');
        $voucherCode = $this->request->getGet('code') ?? $this->request->getPost('code');
        
        if (empty($voucherId) && empty($voucherCode)) {
            return $this->failValidationErrors('Voucher ID or code is required');
        }

        try {
            $mVoucher = $this->mVoucher;
            $voucher = null;
            
            if ($voucherId) {
                $voucher = $mVoucher->find($voucherId);
            } elseif ($voucherCode) {
                $voucher = $mVoucher->getVoucherByCode($voucherCode);
            }
            
            if (!$voucher) {
                return $this->failNotFound('Voucher not found');
            }

            // Check if voucher is valid and available
            $isValid = $mVoucher->isVoucherValid($voucher->kode ?? $voucher->id);
            
            $voucherData = [
                'id' => $voucher->id,
                'kode_voucher' => $voucher->kode,
                'nama_voucher' => $voucher->keterangan ?? 'Voucher',
                'jenis_voucher' => $voucher->jenis_voucher,
                'nominal' => (float) $voucher->nominal,
                'min_pembelian' => 0, // Not available in current schema
                'max_diskon' => (float) $voucher->jml_max,
                'kuota' => (int) $voucher->jml,
                'kuota_terpakai' => (int) $voucher->jml_keluar,
                'tgl_mulai' => $voucher->tgl_masuk,
                'tgl_berakhir' => $voucher->tgl_keluar,
                'status' => $voucher->status,
                'is_valid' => $isValid,
                'sisa_kuota' => max(0, $voucher->jml - $voucher->jml_keluar)
            ];

            return $this->respond($voucherData);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to get voucher: ' . $e->getMessage());
        }
    }

    /**
     * Get all vouchers with pagination and filtering
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function getAllVouchers()
    {
        try {
            $mVoucher = $this->mVoucher;
            
            // Get pagination parameters
            $page = (int) ($this->request->getGet('page') ?? 1);
            $perPage = (int) ($this->request->getGet('per_page') ?? 10);
            $search = $this->request->getGet('search') ?? '';
            $status = $this->request->getGet('status') ?? '';
            
            // Build query
            $builder = $mVoucher->builder();
            
            // Apply search filter
            if (!empty($search)) {
                $builder->groupStart()
                        ->like('kode', $search)
                        ->orLike('keterangan', $search)
                        ->groupEnd();
            }
            
            // Apply status filter
            if ($status !== '') {
                $builder->where('status', $status);
            }
            
            // Get total count
            $totalVouchers = $builder->countAllResults(false);
            
            // Apply pagination
            $offset = ($page - 1) * $perPage;
            $vouchers = $builder->orderBy('created_at', 'DESC')
                               ->limit($perPage, $offset)
                               ->get()
                               ->getResult();
            
            // Format vouchers data
            $formattedVouchers = [];
            foreach ($vouchers as $voucher) {
                $isValid = $mVoucher->isVoucherValid($voucher->kode ?? $voucher->id);
                
                $formattedVouchers[] = [
                    'id' => $voucher->id,
                    'kode_voucher' => $voucher->kode,
                    'nama_voucher' => $voucher->keterangan ?? 'Voucher',
                    'jenis_voucher' => $voucher->jenis_voucher,
                    'nominal' => (float) $voucher->nominal,
                    'min_pembelian' => 0, // Not available in current schema
                    'max_diskon' => (float) $voucher->jml_max,
                    'kuota' => (int) $voucher->jml,
                    'kuota_terpakai' => (int) $voucher->jml_keluar,
                    'tgl_mulai' => $voucher->tgl_masuk,
                    'tgl_berakhir' => $voucher->tgl_keluar,
                    'status' => $voucher->status,
                    'is_valid' => $isValid,
                    'sisa_kuota' => max(0, $voucher->jml - $voucher->jml_keluar),
                    'created_at' => $voucher->created_at ?? null,
                    'updated_at' => $voucher->updated_at ?? null
                ];
            }
            
            $totalPages = ceil($totalVouchers / $perPage);
            
            return $this->respond([
                'success' => true,
                'message' => 'Vouchers retrieved successfully',
                'data' => [
                    'vouchers' => $formattedVouchers,
                    'pagination' => [
                        'current_page' => $page,
                        'per_page' => $perPage,
                        'total' => $totalVouchers,
                        'total_pages' => $totalPages,
                        'has_next' => $page < $totalPages,
                        'has_prev' => $page > 1
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to get vouchers: ' . $e->getMessage());
        }
    }

    /**
     * Generate transaction number
     * 
     * @return string
     */
    private function generateNotaNumber()
    {
        $prefix = 'INV';
        $date = date('Ymd');
        $lastTransaction = $this->mTransJual->where('DATE(created_at)', date('Y-m-d'))
                                           ->orderBy('id', 'DESC')
                                           ->first();

        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->no_nota, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get list of draft transactions for current user
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function getDrafts()
    {
        // Get user_id from request or session
        $userId = $this->request->getGet('user_id');
        
        if (!$userId) {
            return $this->failValidationErrors('User ID is required');
        }

        try {
            // Get draft transactions (status = 0)
            $drafts = $this->mTransJual
                ->select('tbl_trans_jual.*, tbl_m_gudang.nama as gudang_name')
                ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_trans_jual.id_gudang', 'left')
                ->where('tbl_trans_jual.status', '0') // Draft status
                ->where('tbl_trans_jual.id_user', $userId) // Only current user's drafts
                ->orderBy('tbl_trans_jual.created_at', 'DESC')
                ->findAll();

            return $this->respond([
                'success' => true,
                'message' => 'Draft transactions retrieved successfully',
                'total'   => count($drafts),
                'drafts'  => $drafts
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to retrieve drafts: ' . $e->getMessage());
        }
    }

    /**
     * Get specific draft transaction with details
     * 
     * @param int $draftId
     * @return \CodeIgniter\HTTP\Response
     */
    public function getDraft($draftId)
    {
        // Get user_id from request
        $userId = $this->request->getGet('user_id');
        
        if (!$userId) {
            return $this->failValidationErrors('User ID is required');
        }

        try {
            // Get draft transaction
            $draft = $this->mTransJual->find($draftId);
            if (!$draft) {
                return $this->failNotFound('Draft not found');
            }

            // Check if it's a draft
            if ($draft->status != '0') {
                return $this->failValidationErrors('This transaction is not a draft');
            }

            // Check if user owns this draft
            if ($draft->id_user != $userId) {
                return $this->failForbidden('You do not have access to this draft');
            }

            // Get transaction details with category and brand names
            $items = $this->mTransJualDet
                ->select('tbl_trans_jual_det.*, tbl_m_kategori.kategori as nama_kategori, tbl_m_merk.merk as nama_merk')
                ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_trans_jual_det.id_kategori', 'left')
                ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_trans_jual_det.id_merk', 'left')
                ->where('tbl_trans_jual_det.id_penjualan', $draftId)
                ->findAll();

            // Format items for cart
            $cartItems = [];
            foreach ($items as $item) {
                $cartItems[] = [
                    'id_item'    => $item->id_item,
                    'produk'     => $item->produk,
                    'kode'       => $item->kode,
                    'jml'        => $item->jml,
                    'harga'      => $item->harga,
                    'subtotal'   => $item->subtotal,
                    'harga_beli' => $item->harga_beli,
                    'satuan'     => $item->satuan,
                    'kategori'   => $item->nama_kategori ?: '',
                    'merk'       => $item->nama_merk ?: ''
                ];
            }

            // Get customer info
            $customer = null;
            if ($draft->id_pelanggan) {
                $customer = $this->mPelanggan->find($draft->id_pelanggan);
            }

            $draftData = [
                'id'             => $draft->id,
                'no_nota'        => $draft->no_nota,
                'id_pelanggan'   => $draft->id_pelanggan,
                'customer_name'  => $customer ? $customer->nama : null,
                'id_gudang'      => $draft->id_gudang,
                'cart'           => $cartItems,
                'jml_total'      => $draft->jml_total,
                'jml_subtotal'   => $draft->jml_subtotal,
                'jml_diskon'     => $draft->jml_diskon,
                'jml_gtotal'     => $draft->jml_gtotal,
                'voucher_code'   => $draft->voucher_code,
                'voucher_discount' => $draft->voucher_discount,
                'created_at'     => $draft->created_at
            ];

            return $this->respond([
                'success' => true,
                'message' => 'Draft retrieved successfully',
                'draft'   => $draftData
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to retrieve draft: ' . $e->getMessage());
        }
    }

    /**
     * Delete draft transaction
     * 
     * @param int $draftId
     * @return \CodeIgniter\HTTP\Response
     */
    public function deleteDraft($draftId)
    {
        // Get user_id from request
        $userId = $this->request->getPost('user_id') ?? $this->request->getGet('user_id');
        
        if (!$userId) {
            return $this->failValidationErrors('User ID is required');
        }

        try {
            // Get draft transaction
            $draft = $this->mTransJual->find($draftId);
            if (!$draft) {
                return $this->failNotFound('Draft not found');
            }

            // Check if it's a draft
            if ($draft->status != '0') {
                return $this->failValidationErrors('This transaction is not a draft');
            }

            // Check if user owns this draft
            if ($draft->id_user != $userId) {
                return $this->failForbidden('You do not have access to this draft');
            }

            $db = \Config\Database::connect();
            $db->transStart();

            // Delete transaction details first
            $this->mTransJualDet->where('id_penjualan', $draftId)->delete();

            // Delete platform payments if any
            $this->mTransJualPlat->where('id_penjualan', $draftId)->delete();

            // Delete main transaction
            $this->mTransJual->delete($draftId);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            return $this->respond([
                'success' => true,
                'message' => 'Draft deleted successfully'
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to delete draft: ' . $e->getMessage());
        }
    }
}