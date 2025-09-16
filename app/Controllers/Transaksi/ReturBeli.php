<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-25
 * Github : github.com/mikhaelfelian
 * description : Controller for handling purchase return (retur pembelian)
 * This file represents the Controller.
 */

namespace App\Controllers\Transaksi;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use App\Models\ItemHistModel;
use App\Models\SupplierModel;
use App\Models\TransBeliModel;
use App\Models\TransReturBeliModel;
use App\Models\TransReturBeliDetModel;

class ReturBeli extends BaseController
{
    protected $supplierModel;
    protected $transBeliModel;
    protected $returBeliModel;
    protected $returBeliDetModel;
    protected $itemModel;
    protected $itemHistModel;
    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->transBeliModel = new TransBeliModel();
        $this->returBeliModel = new TransReturBeliModel();
        $this->returBeliDetModel = new TransReturBeliDetModel();
        $this->itemModel = new ItemModel();
        $this->itemHistModel = new ItemHistModel();
    }

    public function index()
    {
        // Get current page for pagination
        $currentPage = $this->request->getVar('page_retur') ?? 1;
        $perPage     = $this->pengaturan->pagination_limit;

        // Get returns with pagination
        $offset  = ($currentPage - 1) * $perPage;
        $returns = $this->returBeliModel->getReturnsWithRelations($perPage, $offset);

        // Get total count for pagination
        $totalReturns = $this->returBeliModel->countAllResults();

        // Create pagination
        $pager = \Config\Services::pager();

        $data = [
            'title'        => 'Daftar Retur Pembelian',
            'Pengaturan'   => $this->pengaturan,
            'user'         => $this->ionAuth->user()->row(),
            'returns'      => $returns,
            'currentPage'  => $currentPage,
            'perPage'      => $perPage,
            'pager'        => $pager,
            'totalReturns' => $totalReturns
        ];
        return $this->view($this->theme->getThemePath() . '/transaksi/retur/beli/index', $data);
    }

    public function create()
    {
        // Get active suppliers from tbl_m_supplier
        $suppliers = $this->supplierModel->where('status', '1')->findAll();
        
        // Get purchase transactions that can be returned
        // First try to get transactions that haven't been fully returned
        $purchaseTransactions = $this->transBeliModel
            ->select('tbl_trans_beli.id, tbl_trans_beli.no_nota, tbl_trans_beli.id_supplier, tbl_trans_beli.status_retur, tbl_trans_beli.status_nota, tbl_trans_beli.created_at, tbl_m_supplier.nama as supplier_nama')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli.id_supplier', 'left')
            ->groupStart()
                ->where('tbl_trans_beli.status_retur', '0')
                ->orWhereIn('tbl_trans_beli.status_retur', ['', null])
            ->groupEnd()
            ->orderBy('tbl_trans_beli.created_at', 'DESC')
            ->findAll();

        // If no results with strict conditions, try with more relaxed conditions
        if (empty($purchaseTransactions)) {
            log_message('info', 'No purchase transactions found with strict conditions, trying relaxed query...');
            $purchaseTransactions = $this->transBeliModel
                ->select('tbl_trans_beli.id, tbl_trans_beli.no_nota, tbl_trans_beli.id_supplier, tbl_trans_beli.status_retur, tbl_trans_beli.status_nota, tbl_trans_beli.created_at, tbl_m_supplier.nama as supplier_nama')
                ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli.id_supplier', 'left')
                ->orderBy('tbl_trans_beli.created_at', 'DESC')
                ->findAll();
        }

        // If still no results, check if there are any purchase records at all
        if (empty($purchaseTransactions)) {
            log_message('warning', 'No purchase transactions found in database. Please check if there are any purchase records.');
            
            // Try to get any records without join to see if the issue is with the join
            $simpleQuery = $this->transBeliModel
                ->select('id, no_nota, id_supplier, status_retur, status_nota, created_at')
                ->orderBy('created_at', 'DESC')
                ->findAll();
            
            if (!empty($simpleQuery)) {
                log_message('info', 'Found ' . count($simpleQuery) . ' purchase records without join. Issue might be with supplier join.');
                // Add supplier names manually
                $supplierModel = new SupplierModel();
                foreach ($simpleQuery as $transaction) {
                    $supplier = $supplierModel->find($transaction->id_supplier);
                    $transaction->supplier_nama = $supplier ? $supplier->nama : 'Unknown Supplier';
                }
                $purchaseTransactions = $simpleQuery;
            }
        }

        log_message('info', 'Final purchase transactions count: ' . count($purchaseTransactions));

        $data = [
            'title'      => 'Buat Retur Pembelian',
            'Pengaturan' => $this->pengaturan,
            'user'       => $this->ionAuth->user()->row(),
            'suppliers'  => $suppliers,
            'users'      => $this->ionAuth->users()->result(),
            'sql_beli'   => $purchaseTransactions,
        ];
        
        return $this->view($this->theme->getThemePath() . '/transaksi/retur/beli/trans_retur', $data);
    }

    public function store()
    {
        // Validation rules
        $rules = [
            'id_beli'        => 'required|integer',
            'id_supplier'    => 'required|integer',
            'tgl_retur'      => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('transaksi/retur/beli/create'))
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        // Get form data
        $id_beli         = $this->request->getPost('id_beli');
        $id_supplier     = $this->request->getPost('id_supplier');
        $id_user         = $this->ionAuth->user()->row()->id;
        $id_user_terima  = $this->request->getPost('id_user_terima');
        $tgl_retur       = $this->request->getPost('tgl_retur');
        $no_nota_retur   = $this->request->getPost('no_nota_retur');
        $no_nota_asal    = $this->request->getPost('no_nota_asal');
        $alasan_retur    = $this->request->getPost('alasan_retur');
        $status_ppn      = $this->request->getPost('status_ppn') ?? '0';
        $status_retur    = $this->request->getPost('status_retur') ?? '0';
        $catatan         = $this->request->getPost('catatan');

        $data = [
            'id_beli'        => $id_beli,
            'id_supplier'    => $id_supplier,
            'id_user'        => $id_user,
            'id_user_terima' => $id_user_terima,
            'tgl_retur'      => $tgl_retur,
            'no_nota_retur'  => $no_nota_retur,
            'no_nota_asal'   => $no_nota_asal,
            'alasan_retur'   => $alasan_retur,
            'status_ppn'     => $status_ppn,
            'status_retur'   => $status_retur,
            'catatan'        => $catatan
        ];

        // Get items data (if coming from AJAX/JSON)
        $items = $this->request->getPost('items');
        if (is_string($items)) {
            $items = json_decode($items, true);
        }

        try {
            $this->db->transStart();

            // Calculate totals
            $subtotal = 0;
            if ($items && is_array($items)) {
                foreach ($items as $item) {
                    if (isset($item['qty']) && isset($item['harga'])) {
                        $subtotal += ($item['qty'] * $item['harga']);
                    }
                }
            }

            $ppnAmount = 0;
            if ($data['status_ppn'] == '1') {
                $ppnAmount = $subtotal * 0.11; // 11% PPN
            }

            $total = $subtotal + $ppnAmount;

            // Format and add calculated amounts to data
            $data['jml_subtotal'] = $subtotal;
            $data['jml_ppn']      = $ppnAmount;
            $data['jml_total']    = $total;

            // Save main return record
            $this->returBeliModel->save($data);
            $returId = $this->returBeliModel->getInsertID();

            // Save return detail items
            if ($items && is_array($items)) {
                foreach ($items as $item) {
                    if (!empty($item['kode']) && !empty($item['nama'])) {
                        $detailData = [
                            'id_retur'   => $returId,
                            'id_user'    => $data['id_user'],
                            'kode'       => $item['kode'],
                            'item'       => $item['nama'],
                            'satuan'     => $item['satuan'] ?? 'PCS',
                            'jml'  => $item['qty'] ?? 1,
                            'harga'      => $item['harga'] ?? 0,
                            'subtotal'   => ($item['qty'] ?? 1) * ($item['harga'] ?? 0),
                            'tgl_keluar' => $data['tgl_retur'],
                        ];

                        $this->returBeliDetModel->save($detailData);
                    }
                }
            }

            // Update purchase transaction status
            if ($data['status_retur'] == '1') {
                $this->transBeliModel->update($data['id_beli'], ['status_retur' => '1']);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            // After success, redirect to edit page
            return redirect()->to('transaksi/retur/beli/edit/' . $returId)
                            ->with('success', 'Retur pembelian berhasil disimpan dengan nomor: ' . $data['no_nota_retur']);

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Return creation failed: ' . $e->getMessage());
            return redirect()->to(base_url('transaksi/retur/beli/create'))
                            ->withInput()
                            ->with('error', 'Gagal menyimpan retur pembelian: ' . $e->getMessage());
        }
    }

    public function update($id)
    {
        $retur = $this->returBeliModel->find($id);
        
        if (!$retur) {
            return redirect()->to('transaksi/retur/beli')
                            ->with('error', 'Data retur tidak ditemukan');
        }

        // Only allow editing draft returns
        if ($retur->status_retur == '1') {
            return redirect()->to('transaksi/retur/beli')
                            ->with('error', 'Retur yang sudah selesai tidak dapat diedit');
        }

        // Validation rules
        $rules = [
            'id_beli' => 'required|integer',
            'id_supplier' => 'required|integer',
            'tgl_retur' => 'required|valid_date',
            'no_nota_retur' => 'required|max_length[160]',
            'alasan_retur' => 'permit_empty|max_length[500]',
            'status_ppn' => 'permit_empty|in_list[0,1,2]',
            'status_retur' => 'permit_empty|in_list[0,1]',
            'catatan' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        // Get items data and validate
        $items = $this->request->getPost('items');
        log_message('debug', 'Raw items data: ' . print_r($items, true));
        
        if (is_string($items)) {
            $items = json_decode($items, true);
            log_message('debug', 'Decoded items data: ' . print_r($items, true));
        }

        // Also try to get all POST data to see what's being submitted
        $allPostData = $this->request->getPost();
        log_message('debug', 'All POST data: ' . print_r($allPostData, true));

        // Validate items data
        if (empty($items) || !is_array($items)) {
            log_message('error', 'Items validation failed - items is empty or not array');
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Data barang retur harus diisi. Debug: ' . (empty($items) ? 'Items is empty' : 'Items is not array: ' . gettype($items)));
        }

        // Check if at least one valid item exists
        $hasValidItems = false;
        foreach ($items as $item) {
            if (!empty($item['id_item']) && !empty($item['qty']) && $item['qty'] > 0) {
                $hasValidItems = true;
                break;
            }
        }

        if (!$hasValidItems) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Minimal satu produk dengan quantity valid harus ditambahkan');
        }

        // Get form data
        $data = [
            'id_beli' => $this->request->getPost('id_beli'),
            'id_supplier' => $this->request->getPost('id_supplier'),
            'id_user_terima' => $this->request->getPost('id_user_terima'),
            'tgl_retur' => $this->request->getPost('tgl_retur'),
            'no_nota_retur' => $this->request->getPost('no_nota_retur'),
            'no_nota_asal' => $this->request->getPost('no_nota_asal'),
            'alasan_retur' => $this->request->getPost('alasan_retur'),
            'status_ppn' => $this->request->getPost('status_ppn') ?? '0',
            'status_retur' => $this->request->getPost('status_retur') ?? '0',
            'catatan' => $this->request->getPost('catatan')
        ];

        try {
            $this->db->transStart();

            // Calculate totals
            $subtotal = 0;
            if ($items && is_array($items)) {
                foreach ($items as $item) {
                    if (isset($item['qty']) && isset($item['harga']) && !empty($item['id_item'])) {
                        // Convert autonumber formatted price to numeric value
                        $harga = $item['harga'];
                        if (is_string($harga)) {
                            // Remove thousand separators and convert comma to dot for decimal
                            $harga = str_replace(['.', ','], ['', '.'], $harga);
                            $harga = (float) $harga;
                        }
                        
                        $qty = (float) $item['qty'];
                        $subtotal += ($qty * $harga);
                    }
                }
            }

            $ppnAmount = 0;
            if ($data['status_ppn'] == '1') {
                $ppnAmount = $subtotal * 0.11; // 11% PPN
            }

            $total = $subtotal + $ppnAmount;

            // Add calculated amounts to data
            $data['jml_subtotal'] = $subtotal;
            $data['jml_ppn'] = $ppnAmount;
            $data['jml_total'] = $total;

            // Update main return record
            $this->returBeliModel->update($id, $data);

            // Delete existing return detail items
            $this->returBeliDetModel->where('id_retur', $id)->delete();

            // Insert updated return detail items
            $returDetailData = [];
            if ($items && is_array($items)) {
                foreach ($items as $item) {
                    // Check if item has valid data
                    if (!empty($item['id_item']) && !empty($item['qty']) && $item['qty'] > 0) {
                        // Convert autonumber formatted price to numeric value
                        $harga = $item['harga'] ?? 0;
                        if (is_string($harga)) {
                            // Remove thousand separators and convert comma to dot for decimal
                            $harga = str_replace(['.', ','], ['', '.'], $harga);
                            $harga = (float) $harga;
                        }

                        $qty = (float) ($item['qty'] ?? 1);
                        $subtotal = $qty * $harga;

                        $detailData = [
                            'id_retur' => $id,
                            'id_user' => $this->ionAuth->user()->row()->id,
                            'id_item' => $item['id_item'],
                            'kode' => $item['kode'] ?? '',
                            'item' => $item['produk'] ?? '',
                            'satuan' => $item['satuan'] ?? 'PCS',
                            'jml' => $qty,
                            'harga' => $harga,
                            'subtotal' => $subtotal,
                            'tgl_keluar' => $data['tgl_retur']
                        ];

                        $this->returBeliDetModel->insert($detailData);

                        // Save for item history if needed
                        $returDetailData[] = [
                            'id_item'           => $item['id_item'],
                            'id_satuan'         => $item['id_satuan']        ?? 0,
                            'id_gudang'         => $item['id_gudang']        ?? null,
                            'id_user'           => $this->ionAuth->user()->row()->id,
                            'id_supplier'       => $data['id_supplier']      ?? 0,
                            'id_pembelian'      => $data['id_beli']          ?? 0,
                            'id_pembelian_det'  => $item['id_beli_det']      ?? 0,
                            'created_at'        => date('Y-m-d H:i:s'),
                            'updated_at'        => null,
                            'tgl_masuk'         => null,
                            'no_nota'           => $data['no_nota_retur']    ?? null,
                            'kode'              => $item['kode']             ?? null,
                            'item'              => $item['produk']           ?? null,
                            'keterangan'        => $data['alasan_retur']     ?? null,
                            'nominal'           => $harga,
                            'jml'               => (int) $qty,
                            'jml_satuan'        => (int) $qty,
                            'satuan'            => $item['satuan']           ?? null,
                            'status'            => '7', // Stok keluar karena retur
                            'sp'                => '0'
                        ];

                        // Don't save here, collect data for batch insert later
                    }
                }
            }

            // Update purchase transaction status if return is completed
            if ($data['status_retur'] == '1') {
                $this->transBeliModel->update($data['id_beli'], ['status_retur' => '1']);
            } else {
                // Reset purchase status if return is back to draft
                $this->transBeliModel->update($data['id_beli'], ['status_retur' => '0']);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            // Insert to tbl_m_item_hist for each item (status=7, stok keluar karena retur)
            if (!empty($returDetailData)) {
                foreach ($returDetailData as $histData) {
                    $this->itemHistModel->insert($histData);
                }
            }

            return redirect()->to('transaksi/retur/beli')
                            ->with('success', 'Retur pembelian berhasil diupdate dengan nomor: ' . $data['no_nota_retur']);

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Return update failed: ' . $e->getMessage());
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Gagal mengupdate retur pembelian: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $retur = $this->returBeliModel->getReturWithDetails($id);
        
        if (!$retur) {
            return redirect()->to('transaksi/retur/beli')
                            ->with('error', 'Data retur tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Retur Pembelian',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'retur' => $retur
        ];
        
        return $this->view($this->theme->getThemePath() . '/transaksi/retur/beli/show', $data);
    }

    public function edit($id)
    {
        // Get return with details and items
        $retur = $this->returBeliModel->getReturWithDetails($id);
        
        if (!$retur) {
            return redirect()->to('transaksi/retur/beli')
                            ->with('error', 'Data retur tidak ditemukan');
        }

        // Only allow editing draft returns
        if ($retur->status_retur == '1') {
            return redirect()->to('transaksi/retur/beli')
                            ->with('error', 'Retur yang sudah selesai tidak dapat diedit');
        }

        // Get related data
        $suppliers = $this->supplierModel->where('status', '1')->findAll();
        
        // Get purchase transactions - include the current return's purchase transaction
        $purchaseTransactions = $this->transBeliModel
            ->select('tbl_trans_beli.id, tbl_trans_beli.no_nota, tbl_trans_beli.id_supplier, tbl_trans_beli.status_retur, tbl_trans_beli.status_nota, tbl_trans_beli.created_at, tbl_m_supplier.nama as supplier_nama')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli.id_supplier', 'left')
            ->groupStart()
                ->groupStart()
                    ->where('tbl_trans_beli.status_retur', '0')
                    ->orWhereIn('tbl_trans_beli.status_retur', ['', null])
                ->groupEnd()
                ->orWhere('tbl_trans_beli.id', $retur->id_beli) // Always include current return's purchase
            ->groupEnd()
            ->orderBy('tbl_trans_beli.created_at', 'DESC')
            ->findAll();

        // If no results with strict conditions, try with more relaxed conditions (same as create method)
        if (empty($purchaseTransactions)) {
            log_message('info', 'No purchase transactions found for edit with strict conditions, trying relaxed query...');
            $purchaseTransactions = $this->transBeliModel
                ->select('tbl_trans_beli.id, tbl_trans_beli.no_nota, tbl_trans_beli.id_supplier, tbl_trans_beli.status_retur, tbl_trans_beli.status_nota, tbl_trans_beli.created_at, tbl_m_supplier.nama as supplier_nama')
                ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli.id_supplier', 'left')
                ->orderBy('tbl_trans_beli.created_at', 'DESC')
                ->findAll();
        }

        // If still no results, check if there are any purchase records at all (same as create method)
        if (empty($purchaseTransactions)) {
            log_message('warning', 'No purchase transactions found for edit. Trying without join...');
            
            $simpleQuery = $this->transBeliModel
                ->select('id, no_nota, id_supplier, status_retur, status_nota, created_at')
                ->orderBy('created_at', 'DESC')
                ->findAll();
            
            if (!empty($simpleQuery)) {
                log_message('info', 'Found ' . count($simpleQuery) . ' purchase records without join for edit. Issue might be with supplier join.');
                // Add supplier names manually
                $supplierModel = new SupplierModel();
                foreach ($simpleQuery as $transaction) {
                    $supplier = $supplierModel->find($transaction->id_supplier);
                    $transaction->supplier_nama = $supplier ? $supplier->nama : 'Unknown Supplier';
                }
                $purchaseTransactions = $simpleQuery;
            }
        }

        log_message('info', 'Final purchase transactions count for edit: ' . count($purchaseTransactions));

        $data = [
            'title'       => 'Edit Retur Pembelian',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'suppliers'   => $suppliers,
            'users'       => $this->ionAuth->users()->result(),
            'sql_beli'    => $purchaseTransactions,
            'retur'       => $retur,
        ];
        
        return $this->view($this->theme->getThemePath() . '/transaksi/retur/beli/trans_retur_edit', $data);
    }

    public function delete($id)
    {
        $retur = $this->returBeliModel->find($id);
        
        if (!$retur) {
            return redirect()->to('transaksi/retur/beli')
                            ->with('error', 'Data retur tidak ditemukan');
        }

        // Only allow deleting draft returns
        if ($retur->status_retur == '1') {
            return redirect()->to('transaksi/retur/beli')
                            ->with('error', 'Retur yang sudah selesai tidak dapat dihapus');
        }

        try {
            $this->db->transStart();

            // Delete return details first
            $this->returBeliDetModel->where('id_retur', $id)->delete();
            
            // Delete main return record
            $this->returBeliModel->delete($id);

            $this->db->transComplete();

            return redirect()->to('transaksi/retur/beli')
                            ->with('success', 'Retur pembelian berhasil dihapus');

        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->to('transaksi/retur/beli')
                            ->with('error', 'Gagal menghapus retur pembelian: ' . $e->getMessage());
        }
    }
} 