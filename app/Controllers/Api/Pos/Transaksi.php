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
                                   ->where('kasir_user_id', $user_id)
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
        
        // If no id_pelanggan provided, get it from request
        if ($id_pelanggan === null) {
            $id_pelanggan = $this->request->getGet('id_pelanggan');
        }
        
        // Validate id_pelanggan parameter
        if (!$id_pelanggan) {
            return $this->failValidationErrors('Parameter id_pelanggan is required');
        }
        
        try {
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
                
                $formattedTransactions[] = [
                    'id'             => (int)$transaction->id,
                    'id_user'        => (int)$transaction->id_user,
                    'id_sales'       => (int)$transaction->id_sales,
                    'id_pelanggan'   => (int)$transaction->id_pelanggan,
                    'id_gudang'      => (int)$transaction->id_gudang,
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
                    'details'        => $formattedDetails
                ];
            }
            
            $data = [
                'success'      => true,
                'message'      => 'Transactions retrieved successfully',
                'total'        => count($formattedTransactions),
                'id_pelanggan' => (int) $id_pelanggan,
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
     */
    /**
     * Store new transaction from mobile/android using JSON method
     * Accepts JSON payload as described in the prompt, processes transaction, and returns JSON response.
     */
    public function store()
    {
        $input = $this->request->getJSON(true);

        // Check if shift is open before allowing transaction
        if (!$this->isShiftOpen($input['id_gudang'], $input['id_user'])) {
            return $this->respond([
                'success' => false,
                'message' => 'Shift tidak terbuka. Silakan buka shift terlebih dahulu.'
            ], 400);
        }

        // Validate required fields
        if (
            empty($input['id_user']) ||
            empty($input['id_sales']) ||
            empty($input['id_pelanggan']) ||
            empty($input['id_gudang']) ||
            empty($input['no_nota']) ||
            empty($input['tgl_masuk']) ||
            empty($input['jml_total']) ||
            empty($input['jml_gtotal']) ||
            empty($input['cart']) ||
            !is_array($input['cart']) ||
            count($input['cart']) === 0
        ) {
            return $this->respond([
                'success' => false,
                'message' => 'Data transaksi utama atau cart tidak lengkap'
            ], 400);
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            $mTransJual     = $this->mTransJual;
            $mTransJualDet  = $this->mTransJualDet;
            $mTransJualPlat = $this->mTransJualPlat;
            $mItemHist      = $this->mItemHist;

            // Insert main transaction
            $mainData = [
                'id_user'        => $input['id_user'],
                'id_sales'       => $input['id_sales'],
                'id_pelanggan'   => $input['id_pelanggan'],
                'id_gudang'      => $input['id_gudang'],
                'id_shift'       => $input['id_shift'],
                'no_nota'        => $input['no_nota'],
                'tgl_masuk'      => $input['tgl_masuk'],
                'tgl_bayar'      => isset($input['tgl_bayar']) ? $input['tgl_bayar'] : null,
                'jml_total'      => $input['jml_total'],
                'jml_subtotal'   => isset($input['jml_subtotal']) ? $input['jml_subtotal'] : 0,
                'diskon'         => isset($input['diskon']) ? $input['diskon'] : 0,
                'jml_diskon'     => isset($input['jml_diskon']) ? $input['jml_diskon'] : 0,
                'ppn'            => isset($input['ppn']) ? $input['ppn'] : 0,
                'jml_ppn'        => isset($input['jml_ppn']) ? $input['jml_ppn'] : 0,
                'jml_gtotal'     => $input['jml_gtotal'],
                'jml_bayar'      => isset($input['jml_bayar']) ? $input['jml_bayar'] : 0,
                'jml_kembali'    => isset($input['jml_kembali']) ? $input['jml_kembali'] : 0,
                'metode_bayar'   => isset($input['metode_bayar']) ? $input['metode_bayar'] : 'multiple',
                'status'         => isset($input['status']) ? $input['status'] : '1',
                'status_nota'    => isset($input['status_nota']) ? $input['status_nota'] : '1',
                'status_bayar'   => isset($input['status_bayar']) ? $input['status_bayar'] : '1',
                'status_ppn'     => isset($input['status_ppn']) ? $input['status_ppn'] : '1',
            ];

            $mTransJual->insert($mainData);
            $transactionId = $mTransJual->getInsertID();

            // Insert cart details
            foreach ($input['cart'] as $item) {
                $detailData = [
                    'id_penjualan'   => $transactionId,
                    'id_item'        => $item['id_item'],
                    'id_satuan'      => $item['id_satuan'],
                    'id_kategori'    => $item['id_kategori'],
                    'id_merk'        => $item['id_merk'],
                    'no_nota'        => $item['no_nota'],
                    'kode'           => $item['kode'],
                    'produk'         => $item['produk'],
                    'satuan'         => $item['satuan'],
                    'keterangan'     => isset($item['keterangan']) ? $item['keterangan'] : null,
                    'harga'          => $item['harga'],
                    'harga_beli'     => isset($item['harga_beli']) ? $item['harga_beli'] : 0,
                    'jml'            => $item['jml'],
                    'jml_satuan'     => $item['jml_satuan'],
                    'disk1'          => isset($item['disk1']) ? $item['disk1'] : 0,
                    'disk2'          => isset($item['disk2']) ? $item['disk2'] : 0,
                    'disk3'          => isset($item['disk3']) ? $item['disk3'] : 0,
                    'diskon'         => isset($item['diskon']) ? $item['diskon'] : 0,
                    'potongan'       => isset($item['potongan']) ? $item['potongan'] : 0,
                    'subtotal'       => $item['subtotal'],
                    'status'         => isset($item['status']) ? $item['status'] : 1
                ];
                $mTransJualDet->insert($detailData);
            }

            // Insert platform payments if any
            if (!empty($input['platform']) && is_array($input['platform'])) {
                foreach ($input['platform'] as $plat) {
                    $platData = [
                        'id_penjualan' => $transactionId,
                        'id_platform'  => $plat['id_platform'],
                        'no_nota'      => $plat['no_nota'],
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
                [
                    'success'         => true,
                    'message'         => 'Transaksi berhasil diproses',
                    'transaction_id'  => $transactionId,
                    'no_nota'         => $input['no_nota'],
                    'total'           => $input['jml_gtotal'],
                    'change'          => isset($input['jml_kembali']) ? $input['jml_kembali'] : 0
                ]
            ]);
        } catch (\Exception $e) {
            if (isset($db) && $db->transStatus() !== false) {
                $db->transRollback();
            }
            return $this->respond([
                'success' => false,
                'message' => 'Gagal memproses transaksi: ' . $e->getMessage()
            ], 500);
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
}