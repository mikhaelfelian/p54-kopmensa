<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github: github.com/mikhaelfelian
 * Description: Controller for handling sales return (retur penjualan)
 * This file represents the Controller.
 */

namespace App\Controllers\Transaksi;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use App\Models\ItemHistModel;
use App\Models\PelangganModel;
use App\Models\TransJualModel;
use App\Models\TransReturJualModel;
use App\Models\TransReturJualDetModel;
use App\Models\TransJualDetModel;
use App\Models\GudangModel;
use App\Models\KaryawanModel;

class ReturJual extends BaseController
{
    protected $pelangganModel;
    protected $transJualModel;
    protected $transJualDetModel;
    protected $returJualModel;
    protected $returJualDetModel;
    protected $itemModel;
    protected $itemHistModel;
    protected $gudangModel;
    protected $karyawanModel;
    
    public function __construct()
    {
        $this->pelangganModel = new PelangganModel();
        $this->transJualModel = new TransJualModel();
        $this->transJualDetModel = new TransJualDetModel();
        $this->returJualModel = new TransReturJualModel();
        $this->returJualDetModel = new TransReturJualDetModel();
        $this->itemModel = new ItemModel();
        $this->itemHistModel = new ItemHistModel();
        $this->gudangModel = new GudangModel();
        $this->karyawanModel = new KaryawanModel();
    }

    public function index()
    {
        // Get current page for pagination
        $currentPage = $this->request->getVar('page_retur') ?? 1;
        $perPage     = $this->pengaturan->pagination_limit;
        
        // Get search parameter
        $search = $this->request->getVar('search');

        // Get returns with pagination
        $offset  = ($currentPage - 1) * $perPage;
        $returns = $this->returJualModel->getReturnsWithRelations($perPage, $offset, $search);

        // Get total count for pagination
        $totalReturns = $this->returJualModel->countAllResults();

        // Create pagination
        $pager = \Config\Services::pager();

        $data = [
            'title'        => 'Daftar Retur Penjualan',
            'Pengaturan'   => $this->pengaturan,
            'user'         => $this->ionAuth->user()->row(),
            'returns'      => $returns,
            'currentPage'  => $currentPage,
            'perPage'      => $perPage,
            'pager'        => $pager,
            'totalReturns' => $totalReturns,
            'search'       => $search
        ];
        return $this->view($this->theme->getThemePath() . '/transaksi/retur/jual/index', $data);
    }

    public function refund()
    {
        // Get active customers
        $customers = $this->pelangganModel->where('status', '1')->findAll();
        
        // Get sales transactions that haven't been returned
        $salesTransactions = $this->transJualModel
            ->select('tbl_trans_jual.*, tbl_m_pelanggan.nama as customer_nama')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
            ->where('tbl_trans_jual.status_retur', '0')
            ->where('tbl_trans_jual.status_nota', '1') // Only completed sales
            ->orderBy('tbl_trans_jual.created_at', 'DESC')
            ->findAll();

        $data = [
            'title'      => 'Retur Penjualan - Refund',
            'Pengaturan' => $this->pengaturan,
            'user'       => $this->ionAuth->user()->row(),
            'customers'  => $customers,
            'users'      => $this->ionAuth->users()->result(),
            'sales_transactions' => $salesTransactions,
            'retur_type' => 'refund'
        ];
        
        return $this->view($this->theme->getThemePath() . '/transaksi/retur/jual/create', $data);
    }

    public function exchange()
    {
        // Get active customers
        $customers = $this->pelangganModel->where('status', '1')->findAll();
        
        // Get sales transactions that haven't been returned
        $salesTransactions = $this->transJualModel
            ->select('tbl_trans_jual.*, tbl_m_pelanggan.nama as customer_nama')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
            ->where('tbl_trans_jual.status_retur', '0')
            ->where('tbl_trans_jual.status_nota', '1') // Only completed sales
            ->orderBy('tbl_trans_jual.created_at', 'DESC')
            ->findAll();

        // Get available items for exchange
        $items = $this->itemModel->getItemsWithStock();

        $data = [
            'title'      => 'Retur Penjualan - Tukar Barang',
            'Pengaturan' => $this->pengaturan,
            'user'       => $this->ionAuth->user()->row(),
            'customers'  => $customers,
            'users'      => $this->ionAuth->users()->result(),
            'sales_transactions' => $salesTransactions,
            'items'      => $items,
            'retur_type' => 'exchange'
        ];
        
        return $this->view($this->theme->getThemePath() . '/transaksi/retur/jual/create', $data);
    }

    public function store()
    {
        // Validation rules
        $rules = [
            'id_penjualan'   => 'required|integer',
            'retur_type'     => 'required|in_list[refund,exchange]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        $this->db->transStart();

        try {
            // Get form data
            $retur_type = $this->request->getPost('retur_type');
            // status_retur: '1' = refund, '2' = retur barang (exchange)
            $status_retur = $retur_type === 'refund' ? '1' : '2';
            


            // Get sales transaction for no_nota
            $salesTransaction = $this->transJualModel->find($this->request->getPost('id_penjualan'));
            
            // Compose data for tbl_trans_retur_jual
            $data = [
                'no_retur'       => $this->generateReturNumber(),
                'id_penjualan'   => $this->request->getPost('id_penjualan'),
                'id_user'        => $this->ionAuth->user()->row()->id,
                'id_pelanggan'   => $salesTransaction->id_pelanggan ?? null,
                'id_sales'       => $this->request->getPost('id_sales') ?: null,
                'id_gudang'      => $this->request->getPost('id_gudang') ?: 1,
                'no_nota'        => $salesTransaction->no_nota ?? $this->generateReturNumber(),
                'tgl_masuk'      => $this->request->getPost('tgl_retur') ?: date('Y-m-d'),
                'keterangan'     => $this->request->getPost('catatan') ?: null,
                'status'         => '0', // Draft by default
                'status_retur'   => $status_retur,
                'status_terima'  => '0', // Default
            ];
            
            // Insert return header
            $returId = $this->returJualModel->insert($data);

            if (!$returId) {
                $errors = $this->returJualModel->errors();
                $errorMsg = !empty($errors) ? json_encode($errors) : 'Unknown database error';
                throw new \Exception('Failed to create return record. Validation errors: ' . $errorMsg);
            }
            


            // Process return items
            $returItems = $this->request->getPost('retur_items');
            $returDetailData = [];

            if (!empty($returItems)) {
                foreach ($returItems as $item) {
                    if (!empty($item['id_item']) && !empty($item['qty']) && $item['qty'] > 0) {
                        $itemData = [
                            'id_retur_jual' => $returId,
                            'id_item'       => $item['id_item'],
                            'id_satuan'     => $item['id_satuan'] ?? null,
                            'id_gudang'     => $data['id_gudang'] ?? 1,
                            'kode'          => $item['kode'] ?? '',
                            'item'          => $item['produk'] ?? '',
                            'jml'           => $item['qty'],
                            'satuan'        => $item['satuan'] ?? '',
                            'harga'         => $item['harga'] ?? 0,
                            'subtotal'      => $item['subtotal'] ?? 0,
                            'status_item'   => '1',
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s')
                        ];

                        $this->returJualDetModel->insert($itemData);

                        // Prepare data for item history
                        $returDetailData[] = [
                            'id_item'       => $item['id_item'],
                            'id_satuan'     => $item['id_satuan'] ?? null,
                            'id_gudang'     => $data['id_gudang'] ?? 1,
                            'id_user'       => $this->ionAuth->user()->row()->id,
                            'no_nota'       => $data['no_retur'],
                            'tgl_nota'      => $data['tgl_masuk'],
                            'jml_keluar'    => 0,
                            'jml_masuk'     => $item['qty'], // Stock returns to inventory
                            'nominal'       => $item['harga'] ?? 0,
                            'keterangan'    => 'Retur Penjualan ' . ucfirst($retur_type),
                            'status'        => '3', // Stock in from sales return
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s')
                        ];
                    }
                }
            }

            // Process exchange items if type is exchange
            if ($retur_type === 'exchange') {
                $exchangeItems = $this->request->getPost('exchange_items');
                if (!empty($exchangeItems)) {
                    foreach ($exchangeItems as $item) {
                        if (!empty($item['id_item']) && !empty($item['qty']) && $item['qty'] > 0) {
                            // Add exchange item as negative quantity (item going out)
                            $exchangeData = [
                                'id_retur_jual' => $returId,
                                'id_item'       => $item['id_item'],
                                'id_satuan'     => $item['id_satuan'] ?? null,
                                'id_gudang'     => $data['id_gudang'] ?? 1,
                                'kode'          => $item['kode'] ?? '',
                                'item'          => ($item['produk'] ?? '') . ' (Tukar)',
                                'jml'           => -$item['qty'], // Negative for exchange out
                                'satuan'        => $item['satuan'] ?? '',
                                'harga'         => $item['harga'] ?? 0,
                                'subtotal'      => -($item['subtotal'] ?? 0),
                                'status_item'   => '1',
                                'created_at'    => date('Y-m-d H:i:s'),
                                'updated_at'    => date('Y-m-d H:i:s')
                            ];

                            $this->returJualDetModel->insert($exchangeData);

                            // Add to item history (stock out for exchange)
                            $returDetailData[] = [
                                'id_item'       => $item['id_item'],
                                'id_satuan'     => $item['id_satuan'] ?? null,
                                'id_gudang'     => $data['id_gudang'] ?? 1,
                                'id_user'       => $this->ionAuth->user()->row()->id,
                                'no_nota'       => $data['no_retur'],
                                'tgl_nota'      => $data['tgl_masuk'],
                                'jml_keluar'    => $item['qty'], // Stock goes out for exchange
                                'jml_masuk'     => 0,
                                'nominal'       => $item['harga'] ?? 0,
                                'keterangan'    => 'Tukar Barang Retur Penjualan',
                                'status'        => '4', // Stock out for sales
                                'created_at'    => date('Y-m-d H:i:s'),
                                'updated_at'    => date('Y-m-d H:i:s')
                            ];
                        }
                    }
                }
            }

            // Update sales transaction status if return is refund
            if ($status_retur == '1') {
                $this->transJualModel->update($data['id_penjualan'], ['status_retur' => '1']);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            // Insert item history records (commented out for now)
            if (!empty($returDetailData)) {
                foreach ($returDetailData as $histData) {
                    $this->itemHistModel->insert($histData);
                }
            }

            $returnType = $retur_type === 'refund' ? 'Refund' : 'Tukar Barang';
            
            
            return redirect()->to('transaksi/retur/jual')
                            ->with('success', "Retur penjualan ($returnType) berhasil disimpan dengan nomor: " . $data['no_retur']);

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Sales return creation failed: ' . $e->getMessage());
            
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Gagal menyimpan retur penjualan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $retur = $this->returJualModel->getReturWithDetails($id);
        
        if (!$retur) {
            return redirect()->to('transaksi/retur/jual')
                            ->with('error', 'Data retur tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Retur Penjualan',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'retur' => $retur
        ];
        
        return $this->view($this->theme->getThemePath() . '/transaksi/retur/jual/show', $data);
    }

    public function edit($id)
    {
        $retur = $this->returJualModel->getReturWithDetails($id);
        
        if (!$retur) {
            return redirect()->to('transaksi/retur/jual')
                            ->with('error', 'Data retur tidak ditemukan');
        }

        // Only allow editing draft returns
        if ($retur->status_retur == '1') {
            return redirect()->to('transaksi/retur/jual')
                            ->with('error', 'Retur yang sudah selesai tidak dapat diedit');
        }

        // Get related data
        $customers = $this->pelangganModel->where('status', '1')->findAll();
        $salesTransactions = $this->transJualModel
            ->select('tbl_trans_jual.*, tbl_m_pelanggan.nama as customer_nama')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
            ->where('tbl_trans_jual.status_retur', '0')
            ->orWhere('tbl_trans_jual.id', $retur->id_penjualan)
            ->where('tbl_trans_jual.status_nota', '1')
            ->orderBy('tbl_trans_jual.created_at', 'DESC')
            ->findAll();

        $data = [
            'title'      => 'Edit Retur Penjualan',
            'Pengaturan' => $this->pengaturan,
            'user'       => $this->ionAuth->user()->row(),
            'customers'  => $customers,
            'users'      => $this->ionAuth->users()->result(),
            'sales_transactions' => $salesTransactions,
            'retur'      => $retur,
            'retur_type' => $retur->retur_type
        ];
        
        return $this->view($this->theme->getThemePath() . '/transaksi/retur/jual/edit', $data);
    }

    public function update($id)
    {
        $retur = $this->returJualModel->find($id);
        
        if (!$retur) {
            return redirect()->to('transaksi/retur/jual')
                            ->with('error', 'Data retur tidak ditemukan');
        }

        // Only allow editing draft returns
        if ($retur->status_retur == '1') {
            return redirect()->to('transaksi/retur/jual')
                            ->with('error', 'Retur yang sudah selesai tidak dapat diedit');
        }

        // Validation rules
        $rules = [
            'id_penjualan'   => 'required|integer',
            'id_pelanggan'   => 'permit_empty|integer',
            'tgl_retur'      => 'permit_empty|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        $this->db->transStart();

        try {
            // Update return header
            $data = [
                'id_penjualan'   => $this->request->getPost('id_penjualan'),
                'id_pelanggan'   => $this->request->getPost('id_pelanggan'),
                'tgl_masuk'      => $this->request->getPost('tgl_retur') ?: date('Y-m-d'),
                'keterangan'     => $this->request->getPost('catatan'),
                'status'         => $this->request->getPost('status_retur') ?: '0',
                'updated_at'     => date('Y-m-d H:i:s')
            ];

            $this->returJualModel->update($id, $data);

            // Delete existing return items
            $this->returJualDetModel->where('id_retur_jual', $id)->delete();

            // Process return items
            $returItems = $this->request->getPost('retur_items');
            $returDetailData = [];

            if (!empty($returItems)) {
                foreach ($returItems as $item) {
                    if (!empty($item['id_item']) && !empty($item['qty']) && $item['qty'] > 0) {
                        $itemData = [
                            'id_retur_jual' => $id,
                            'id_item'       => $item['id_item'],
                            'id_satuan'     => $item['id_satuan'] ?? null,
                            'id_gudang'     => $this->request->getPost('id_gudang') ?? 1,
                            'kode'          => $item['kode'] ?? '',
                            'item'          => $item['produk'] ?? '',
                            'jml'           => $item['qty'],
                            'satuan'        => $item['satuan'] ?? '',
                            'harga'         => $item['harga'] ?? 0,
                            'subtotal'      => $item['subtotal'] ?? 0,
                            'status_item'   => '1',
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s')
                        ];

                        $this->returJualDetModel->insert($itemData);
                    }
                }
            }

            // Update sales transaction status
            if ($data['status_retur'] == '1') {
                $this->transJualModel->update($data['id_penjualan'], ['status_retur' => '1']);
            } else {
                $this->transJualModel->update($data['id_penjualan'], ['status_retur' => '0']);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            return redirect()->to('transaksi/retur/jual')
                            ->with('success', 'Retur penjualan berhasil diupdate');

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Sales return update failed: ' . $e->getMessage());
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Gagal mengupdate retur penjualan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $retur = $this->returJualModel->find($id);
        
        if (!$retur) {
            return redirect()->to('transaksi/retur/jual')
                            ->with('error', 'Data retur tidak ditemukan');
        }

        // Only allow deleting draft returns
        if ($retur->status_retur == '1') {
            return redirect()->to('transaksi/retur/jual')
                            ->with('error', 'Retur yang sudah selesai tidak dapat dihapus');
        }

        $this->db->transStart();

        try {
            // Delete return details first
            $this->returJualDetModel->where('id_retur_jual', $id)->delete();
            
            // Delete return header
            $this->returJualModel->delete($id);

            // Reset sales transaction status
            $this->transJualModel->update($retur->id_penjualan, ['status_retur' => '0']);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            return redirect()->to('transaksi/retur/jual')
                            ->with('success', 'Retur penjualan berhasil dihapus');

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Sales return deletion failed: ' . $e->getMessage());
            return redirect()->to('transaksi/retur/jual')
                            ->with('error', 'Gagal menghapus retur penjualan: ' . $e->getMessage());
        }
    }

    // AJAX Methods
    public function getSalesItems($salesId)
    {
        $items = $this->transJualDetModel->getSalesItems($salesId);
        
        return $this->response->setJSON([
            'success' => true,
            'items' => $items
        ]);
    }

    public function searchItems()
    {
        
        // Accept search parameter from both GET and POST
        $search = $this->request->getPost('search') ?? $this->request->getGet('search');
        
        // If no search parameter or empty search, load all items
        if (empty($search)) {
            $search = null; // This will make searchItems return all items
        }
        
        try {
            // Use different methods based on search parameter  
            $method_used = '';
            if ($search === null) {
                // Load all items with accurate stock totals
                $items = $this->itemModel->getItemsWithStock();
                $method_used = 'getItemsWithStock';
            } else {
                // Search with specific term
                $items = $this->itemModel->searchItems($search);
                $method_used = 'searchItems';
            }
            
            return $this->response->setJSON([
                'success' => true,
                'search' => $search,
                'items' => $items,
                'count' => count($items),
                'debug' => [
                    'method' => $this->request->getMethod(),
                    'original_search' => $this->request->getPost('search') ?? $this->request->getGet('search'),
                    'processed_search' => $search,
                    'method_used' => $method_used
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Test endpoint to check if authentication and routing works
     */
    public function testEndpoint()
    {
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Test endpoint working!',
            'method' => $this->request->getMethod(),
            'uri' => $this->request->getUri()->getPath(),
            'timestamp' => date('Y-m-d H:i:s'),
            'user' => session()->get('user_id') ?? 'Not authenticated'
        ]);
    }

    // Helper Methods
    /**
     * Generate Return Number (SAP Style)
     * Format: RTJ-YYYYMMDD-XXXX
     * Where XXXX is a zero-padded running number per day
     */
    private function generateReturNumber()
    {
        $prefix = 'RTJ';
        $date = date('Ymd');

        // Get last return number for today (SAP style: RTJ-YYYYMMDD-XXXX)
        $lastRetur = $this->returJualModel
            ->like('no_retur', $prefix . '-' . $date . '-', 'after')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastRetur && preg_match('/^' . $prefix . '-' . $date . '-(\d{4})$/', $lastRetur->no_retur, $matches)) {
            $lastNumber = (int)$matches[1];
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $returNumber = sprintf('%s-%s-%04d', $prefix, $date, $newNumber);
        return $returNumber;
    }
} 