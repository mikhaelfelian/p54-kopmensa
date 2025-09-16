<?php

namespace App\Controllers\Api\Pos;

use App\Controllers\BaseController;
use App\Models\TransReturJualModel;
use App\Models\TransReturJualDetModel;
use App\Models\TransJualModel;
use App\Models\TransJualDetModel;
use App\Models\ItemModel;
use App\Models\ItemHistModel;
use App\Models\PelangganModel;
use App\Models\GudangModel;
use App\Models\KaryawanModel;
use App\Models\ShiftModel;
use CodeIgniter\API\ResponseTrait;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github: github.com/mikhaelfelian
 * Description: API controller for handling sales return (retur penjualan) for POS
 * This file represents the ReturJual API controller.
 */
class ReturJual extends BaseController
{
    use ResponseTrait;

    protected $mReturJual;
    protected $mReturJualDet;
    protected $mTransJual;
    protected $mTransJualDet;
    protected $mItem;
    protected $mItemHist;
    protected $mPelanggan;
    protected $mGudang;
    protected $mKaryawan;
    protected $mShift;

    /**
     * Initialize all model properties in the constructor for reuse.
     */
    public function __construct()
    {
        $this->mReturJual      = new TransReturJualModel();
        $this->mReturJualDet   = new TransReturJualDetModel();
        $this->mTransJual      = new TransJualModel();
        $this->mTransJualDet   = new TransJualDetModel();
        $this->mItem           = new ItemModel();
        $this->mItemHist       = new ItemHistModel();
        $this->mPelanggan      = new PelangganModel();
        $this->mGudang         = new GudangModel();
        $this->mKaryawan       = new KaryawanModel();
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
     * Get sales returns by customer ID or all returns
     * 
     * @param int|null $id_pelanggan Customer ID to filter returns
     * @return \CodeIgniter\HTTP\Response
     */
    public function getReturns($id_pelanggan = null)
    {
        // If no id_pelanggan provided, get it from request
        if ($id_pelanggan === null) {
            $id_pelanggan = $this->request->getGet('id_pelanggan');
        }

        try {
            $builder = $this->mReturJual
                ->select('tbl_trans_retur_jual.*, 
                         tbl_m_pelanggan.nama as customer_nama,
                         tbl_trans_jual.no_nota as original_nota')
                ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_retur_jual.id_pelanggan', 'left')
                ->join('tbl_trans_jual', 'tbl_trans_jual.id = tbl_trans_retur_jual.id_penjualan', 'left');

            // Filter by customer if provided
            if ($id_pelanggan) {
                $builder->where('tbl_trans_retur_jual.id_pelanggan', $id_pelanggan);
            }

            $returns = $builder->orderBy('tbl_trans_retur_jual.created_at', 'DESC')
                              ->findAll();

            // Format the response data
            $formattedReturns = [];
            foreach ($returns as $return) {
                // Get return details
                $details = $this->mReturJualDet->where('id_retur_jual', $return->id)->findAll();

                // Format return details
                $formattedDetails = [];
                foreach ($details as $detail) {
                    $formattedDetails[] = [
                        'id'             => (int)$detail->id,
                        'id_retur_jual'  => (int)$detail->id_retur_jual,
                        'id_item'        => (int)$detail->id_item,
                        'id_satuan'      => (int)$detail->id_satuan,
                        'id_gudang'      => (int)$detail->id_gudang,
                        'kode'           => $detail->kode,
                        'item'           => $detail->item,
                        'jml'            => (float)$detail->jml,
                        'satuan'         => $detail->satuan,
                        'harga'          => (float)$detail->harga,
                        'subtotal'       => (float)$detail->subtotal,
                        'status_item'    => (int)$detail->status_item,
                        'created_at'     => $detail->created_at,
                        'updated_at'     => $detail->updated_at
                    ];
                }

                $formattedReturns[] = [
                    'id'             => (int)$return->id,
                    'no_retur'       => $return->no_retur,
                    'id_penjualan'   => (int)$return->id_penjualan,
                    'id_user'        => (int)$return->id_user,
                    'id_pelanggan'   => (int)$return->id_pelanggan,
                    'id_sales'       => (int)$return->id_sales,
                    'id_gudang'      => (int)$return->id_gudang,
                    'customer_nama'  => $return->customer_nama ?? 'Umum',
                    'original_nota'  => $return->original_nota,
                    'no_nota'        => $return->no_nota,
                    'tgl_masuk'      => $return->tgl_masuk,
                    'keterangan'     => $return->keterangan,
                    'status'         => (int)$return->status,
                    'status_retur'   => (int)$return->status_retur,
                    'status_terima'  => (int)$return->status_terima,
                    'created_at'     => $return->created_at,
                    'updated_at'     => $return->updated_at,
                    'details'        => $formattedDetails
                ];
            }

            $data = [
                'success' => true,
                'message' => 'Returns retrieved successfully',
                'total'   => count($formattedReturns),
                'returns' => $formattedReturns
            ];

            return $this->respond($data);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to retrieve returns: ' . $e->getMessage());
        }
    }

    /**
     * Get sales transactions available for return
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function getSalesForReturn()
    {
        $id_pelanggan = $this->request->getGet('id_pelanggan');

        try {
            $builder = $this->mTransJual
                ->select('tbl_trans_jual.*, tbl_m_pelanggan.nama as customer_nama')
                ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
                ->where('tbl_trans_jual.status_retur', '0')
                ->where('tbl_trans_jual.status_nota', '1'); // Only completed sales

            // Filter by customer if provided
            if ($id_pelanggan) {
                $builder->where('tbl_trans_jual.id_pelanggan', $id_pelanggan);
            }

            $salesTransactions = $builder->orderBy('tbl_trans_jual.created_at', 'DESC')
                                        ->findAll();

            // Format the response data
            $formattedSales = [];
            foreach ($salesTransactions as $sale) {
                $formattedSales[] = [
                    'id'             => (int)$sale->id,
                    'no_nota'        => $sale->no_nota,
                    'id_pelanggan'   => (int)$sale->id_pelanggan,
                    'customer_nama'  => $sale->customer_nama ?? 'Umum',
                    'tgl_masuk'      => $sale->tgl_masuk,
                    'jml_gtotal'     => (float)$sale->jml_gtotal,
                    'status'         => (int)$sale->status,
                    'status_nota'    => (int)$sale->status_nota,
                    'status_retur'   => (int)$sale->status_retur,
                    'created_at'     => $sale->created_at
                ];
            }

            return $this->respond([
                'success' => true,
                'message' => 'Sales transactions retrieved successfully',
                'total'   => count($formattedSales),
                'sales'   => $formattedSales
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to retrieve sales transactions: ' . $e->getMessage());
        }
    }

    /**
     * Get items from a specific sales transaction
     * 
     * @param int $sales_id Sales transaction ID
     * @return \CodeIgniter\HTTP\Response
     */
    public function getSalesItems($sales_id)
    {
        if (!$sales_id) {
            return $this->failValidationErrors('Sales ID is required');
        }

        try {
            $items = $this->mTransJualDet->where('id_penjualan', $sales_id)->findAll();

            // Format the response data
            $formattedItems = [];
            foreach ($items as $item) {
                $formattedItems[] = [
                    'id'             => (int)$item->id,
                    'id_penjualan'   => (int)$item->id_penjualan,
                    'id_item'        => (int)$item->id_item,
                    'id_satuan'      => (int)$item->id_satuan,
                    'kode'           => $item->kode,
                    'produk'         => $item->produk,
                    'satuan'         => $item->satuan,
                    'harga'          => (float)$item->harga,
                    'harga_beli'     => (float)$item->harga_beli,
                    'jml'            => (float)$item->jml,
                    'jml_satuan'     => (float)$item->jml_satuan,
                    'subtotal'       => (float)$item->subtotal,
                    'status'         => (int)$item->status
                ];
            }

            return $this->respond([
                'success' => true,
                'message' => 'Sales items retrieved successfully',
                'total'   => count($formattedItems),
                'items'   => $formattedItems
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to retrieve sales items: ' . $e->getMessage());
        }
    }

    /**
     * Store new sales return from mobile/android using JSON method
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function store()
    {
        $input = $this->request->getJSON(true);

        // Check if shift is open before allowing return
        if (!empty($input['id_gudang']) && !empty($input['id_user'])) {
            if (!$this->isShiftOpen($input['id_gudang'], $input['id_user'])) {
                return $this->failValidationErrors('Shift tidak terbuka. Silakan buka shift terlebih dahulu.');
            }
        }

        // Validate required fields
        if (
            empty($input['id_penjualan']) ||
            empty($input['retur_type']) ||
            !in_array($input['retur_type'], ['refund', 'exchange']) ||
            empty($input['retur_items']) ||
            !is_array($input['retur_items']) ||
            count($input['retur_items']) === 0
        ) {
            return $this->failValidationErrors('Data retur tidak lengkap atau format salah');
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Get sales transaction for validation
            $salesTransaction = $this->mTransJual->find($input['id_penjualan']);
            if (!$salesTransaction) {
                return $this->failValidationErrors('Transaksi penjualan tidak ditemukan');
            }

            // Generate return number
            $no_retur = $this->generateReturNumber();
            
            // Determine status_retur: '1' = refund, '2' = exchange
            $status_retur = $input['retur_type'] === 'refund' ? '1' : '2';

            // Prepare main return data
            $returData = [
                'no_retur'       => $no_retur,
                'id_penjualan'   => $input['id_penjualan'],
                'id_user'        => $input['id_user'] ?? 1,
                'id_pelanggan'   => $salesTransaction->id_pelanggan ?? null,
                'id_sales'       => $input['id_sales'] ?? null,
                'id_gudang'      => $input['id_gudang'] ?? 1,
                'no_nota'        => $salesTransaction->no_nota ?? $no_retur,
                'tgl_masuk'      => $input['tgl_retur'] ?? date('Y-m-d'),
                'keterangan'     => $input['keterangan'] ?? null,
                'status'         => '0', // Draft by default
                'status_retur'   => $status_retur,
                'status_terima'  => '0'
            ];

            // Insert return header
            $this->mReturJual->insert($returData);
            $returId = $this->mReturJual->getInsertID();

            if (!$returId) {
                throw new \Exception('Failed to create return record');
            }

            // Process return items
            $returDetailData = [];
            foreach ($input['retur_items'] as $item) {
                if (!empty($item['id_item']) && !empty($item['qty']) && $item['qty'] > 0) {
                    $itemData = [
                        'id_retur_jual' => $returId,
                        'id_item'       => $item['id_item'],
                        'id_satuan'     => $item['id_satuan'] ?? null,
                        'id_gudang'     => $returData['id_gudang'],
                        'kode'          => $item['kode'] ?? '',
                        'item'          => $item['produk'] ?? '',
                        'jml'           => $item['qty'],
                        'satuan'        => $item['satuan'] ?? '',
                        'harga'         => $item['harga'] ?? 0,
                        'subtotal'      => $item['subtotal'] ?? 0,
                        'status_item'   => '1'
                    ];

                    $this->mReturJualDet->insert($itemData);

                    // Prepare data for item history (stock returns to inventory)
                    $returDetailData[] = [
                        'id_item'       => $item['id_item'],
                        'id_satuan'     => $item['id_satuan'] ?? null,
                        'id_gudang'     => $returData['id_gudang'],
                        'id_user'       => $returData['id_user'],
                        'no_nota'       => $no_retur,
                        'tgl_nota'      => $returData['tgl_masuk'],
                        'jml_keluar'    => 0,
                        'jml_masuk'     => $item['qty'], // Stock returns to inventory
                        'nominal'       => $item['harga'] ?? 0,
                        'keterangan'    => 'Retur Penjualan ' . ucfirst($input['retur_type']),
                        'status'        => '3' // Stock in from sales return
                    ];
                }
            }

            // Process exchange items if type is exchange
            if ($input['retur_type'] === 'exchange' && !empty($input['exchange_items'])) {
                foreach ($input['exchange_items'] as $item) {
                    if (!empty($item['id_item']) && !empty($item['qty']) && $item['qty'] > 0) {
                        // Add exchange item as negative quantity (item going out)
                        $exchangeData = [
                            'id_retur_jual' => $returId,
                            'id_item'       => $item['id_item'],
                            'id_satuan'     => $item['id_satuan'] ?? null,
                            'id_gudang'     => $returData['id_gudang'],
                            'kode'          => $item['kode'] ?? '',
                            'item'          => ($item['produk'] ?? '') . ' (Tukar)',
                            'jml'           => -$item['qty'], // Negative for exchange out
                            'satuan'        => $item['satuan'] ?? '',
                            'harga'         => $item['harga'] ?? 0,
                            'subtotal'      => -($item['subtotal'] ?? 0),
                            'status_item'   => '1'
                        ];

                        $this->mReturJualDet->insert($exchangeData);

                        // Add to item history (stock out for exchange)
                        $returDetailData[] = [
                            'id_item'       => $item['id_item'],
                            'id_satuan'     => $item['id_satuan'] ?? null,
                            'id_gudang'     => $returData['id_gudang'],
                            'id_user'       => $returData['id_user'],
                            'no_nota'       => $no_retur,
                            'tgl_nota'      => $returData['tgl_masuk'],
                            'jml_keluar'    => $item['qty'], // Stock goes out for exchange
                            'jml_masuk'     => 0,
                            'nominal'       => $item['harga'] ?? 0,
                            'keterangan'    => 'Tukar Barang Retur Penjualan',
                            'status'        => '4' // Stock out for sales
                        ];
                    }
                }
            }

            // Update sales transaction status if return is refund
            if ($status_retur == '1') {
                $this->mTransJual->update($input['id_penjualan'], ['status_retur' => '1']);
            }

            // Insert item history records
            if (!empty($returDetailData)) {
                foreach ($returDetailData as $histData) {
                    $this->mItemHist->insert($histData);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            $returnType = $input['retur_type'] === 'refund' ? 'Refund' : 'Tukar Barang';

            return $this->respond([
                'success'     => true,
                'message'     => "Retur penjualan ($returnType) berhasil disimpan",
                'retur_id'    => $returId,
                'no_retur'    => $no_retur,
                'retur_type'  => $input['retur_type']
            ]);

        } catch (\Exception $e) {
            if (isset($db) && $db->transStatus() !== false) {
                $db->transRollback();
            }
            return $this->failServerError('Gagal menyimpan retur penjualan: ' . $e->getMessage());
        }
    }

    /**
     * Update sales return status (approve/reject)
     * 
     * @param int $id Return ID
     * @return \CodeIgniter\HTTP\Response
     */
    public function updateStatus($id)
    {
        $input = $this->request->getJSON(true);

        if (!$id) {
            return $this->failValidationErrors('Return ID is required');
        }

        if (empty($input['status']) || !in_array($input['status'], ['0', '1', '2'])) {
            return $this->failValidationErrors('Valid status is required (0=draft, 1=approved, 2=rejected)');
        }

        try {
            $retur = $this->mReturJual->find($id);
            if (!$retur) {
                return $this->failNotFound('Return not found');
            }

            // Update return status
            $updateData = [
                'status'      => $input['status'],
                'status_terima' => $input['status'] === '1' ? '1' : '0',
                'updated_at'  => date('Y-m-d H:i:s')
            ];

            if (!empty($input['keterangan'])) {
                $updateData['keterangan'] = $input['keterangan'];
            }

            $this->mReturJual->update($id, $updateData);

            $statusText = [
                '0' => 'Draft',
                '1' => 'Approved',
                '2' => 'Rejected'
            ];

            return $this->respond([
                'success' => true,
                'message' => 'Return status updated to ' . $statusText[$input['status']],
                'retur_id' => $id,
                'status' => $input['status']
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to update return status: ' . $e->getMessage());
        }
    }

    /**
     * Get return details by ID
     * 
     * @param int $id Return ID
     * @return \CodeIgniter\HTTP\Response
     */
    public function show($id)
    {
        if (!$id) {
            return $this->failValidationErrors('Return ID is required');
        }

        try {
            $retur = $this->mReturJual
                ->select('tbl_trans_retur_jual.*, 
                         tbl_m_pelanggan.nama as customer_nama,
                         tbl_trans_jual.no_nota as original_nota')
                ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_retur_jual.id_pelanggan', 'left')
                ->join('tbl_trans_jual', 'tbl_trans_jual.id = tbl_trans_retur_jual.id_penjualan', 'left')
                ->find($id);

            if (!$retur) {
                return $this->failNotFound('Return not found');
            }

            // Get return details
            $details = $this->mReturJualDet->where('id_retur_jual', $id)->findAll();

            // Format return details
            $formattedDetails = [];
            foreach ($details as $detail) {
                $formattedDetails[] = [
                    'id'             => (int)$detail->id,
                    'id_retur_jual'  => (int)$detail->id_retur_jual,
                    'id_item'        => (int)$detail->id_item,
                    'id_satuan'      => (int)$detail->id_satuan,
                    'id_gudang'      => (int)$detail->id_gudang,
                    'kode'           => $detail->kode,
                    'item'           => $detail->item,
                    'jml'            => (float)$detail->jml,
                    'satuan'         => $detail->satuan,
                    'harga'          => (float)$detail->harga,
                    'subtotal'       => (float)$detail->subtotal,
                    'status_item'    => (int)$detail->status_item,
                    'created_at'     => $detail->created_at,
                    'updated_at'     => $detail->updated_at
                ];
            }

            $returData = [
                'id'             => (int)$retur->id,
                'no_retur'       => $retur->no_retur,
                'id_penjualan'   => (int)$retur->id_penjualan,
                'id_user'        => (int)$retur->id_user,
                'id_pelanggan'   => (int)$retur->id_pelanggan,
                'id_sales'       => (int)$retur->id_sales,
                'id_gudang'      => (int)$retur->id_gudang,
                'customer_nama'  => $retur->customer_nama ?? 'Umum',
                'original_nota'  => $retur->original_nota,
                'no_nota'        => $retur->no_nota,
                'tgl_masuk'      => $retur->tgl_masuk,
                'keterangan'     => $retur->keterangan,
                'status'         => (int)$retur->status,
                'status_retur'   => (int)$retur->status_retur,
                'status_terima'  => (int)$retur->status_terima,
                'created_at'     => $retur->created_at,
                'updated_at'     => $retur->updated_at,
                'details'        => $formattedDetails
            ];

            return $this->respond([
                'success' => true,
                'message' => 'Return details retrieved successfully',
                'retur'   => $returData
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to retrieve return details: ' . $e->getMessage());
        }
    }

    /**
     * Search available items for exchange
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function searchItems()
    {
        $search = $this->request->getPost('search') ?? $this->request->getGet('search');
        $id_gudang = $this->request->getPost('id_gudang') ?? $this->request->getGet('id_gudang');

        try {
            $builder = $this->mItem->select('
                tbl_m_item.*,
                tbl_m_item_stok.jml as stok,
                tbl_m_kategori.kategori,
                tbl_m_merk.merk,
                tbl_m_satuan.satuan
            ')
            ->join('tbl_m_item_stok', 'tbl_m_item_stok.id_item = tbl_m_item.id', 'left')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->where('tbl_m_item.status', '1');

            if ($id_gudang) {
                $builder->where('tbl_m_item_stok.id_gudang', $id_gudang);
            }

            if (!empty($search)) {
                $builder->groupStart()
                       ->like('tbl_m_item.kode', $search)
                       ->orLike('tbl_m_item.item', $search)
                       ->orLike('tbl_m_item.barcode', $search)
                       ->groupEnd();
            }

            $items = $builder->findAll();

            // Format the response data
            $formattedItems = [];
            foreach ($items as $item) {
                $formattedItems[] = [
                    'id'           => (int)$item->id,
                    'kode'         => $item->kode,
                    'item'         => $item->item,
                    'barcode'      => $item->barcode,
                    'harga_jual'   => (float)$item->harga_jual,
                    'harga_beli'   => (float)$item->harga_beli,
                    'stok'         => (float)($item->stok ?? 0),
                    'kategori'     => $item->kategori,
                    'merk'         => $item->merk,
                    'satuan'       => $item->satuan,
                    'status'       => (int)$item->status
                ];
            }

            return $this->respond([
                'success' => true,
                'message' => 'Items retrieved successfully',
                'total'   => count($formattedItems),
                'items'   => $formattedItems
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to search items: ' . $e->getMessage());
        }
    }

    /**
     * Generate Return Number (SAP Style)
     * Format: RTJ-YYYYMMDD-XXXX
     * Where XXXX is a zero-padded running number per day
     */
    private function generateReturNumber()
    {
        $prefix = 'RTJ';
        $date = date('Ymd');

        // Get last return number for today
        $lastRetur = $this->mReturJual
            ->like('no_retur', $prefix . '-' . $date . '-', 'after')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastRetur && preg_match('/^' . $prefix . '-' . $date . '-(\d{4})$/', $lastRetur->no_retur, $matches)) {
            $lastNumber = (int)$matches[1];
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $date, $newNumber);
    }

    /**
     * Test endpoint to check if authentication and routing works
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function testEndpoint()
    {
        return $this->respond([
            'success' => true,
            'message' => 'ReturJual API Test endpoint working!',
            'method' => $this->request->getMethod(),
            'uri' => $this->request->getUri()->getPath(),
            'timestamp' => date('Y-m-d H:i:s'),
            'user' => session()->get('user_id') ?? 'Not authenticated'
        ]);
    }
}