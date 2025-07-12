<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-04
 * 
 * TransBeli Controller
 * Handles purchase transaction operations
 */

namespace App\Controllers\Transaksi;

use App\Controllers\BaseController;
use App\Models\TransBeliPOModel;
use App\Models\TransBeliPODetModel;
use App\Models\TransBeliModel;
use App\Models\TransBeliDetModel;
use App\Models\SupplierModel;


class TransBeli extends BaseController
{
    protected $transPOModel;
    protected $transBeliModel;
    protected $supplierModel;

    public function __construct()
    {
        $this->transPOModel       = new TransBeliPOModel();
        $this->transBeliModel     = new TransBeliModel();
        $this->transBeliDetModel  = new TransBeliDetModel();
        $this->transBeliPOModel   = new TransBeliPODetModel();
        $this->transBeliPODetModel= new TransBeliPODetModel();
        $this->supplierModel      = new SupplierModel();

    }


    /**
     * Display list of purchase transactions
     */
    public function index()
    {
        $currentPage = $this->request->getVar('page_transbeli') ?? 1;
        $perPage = $this->pengaturan->pagination_limit;


        $data = [
            'title'         => 'Data Pembelian',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'transaksi'     => $this->transBeliModel->paginate($perPage, 'transbeli'),
            'pager'         => $this->transBeliModel->pager,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,
        ];

        return $this->view($this->theme->getThemePath() . '/transaksi/beli/index', $data);
    }


    /**
     * Display create purchase transaction form
     */
    public function create()
    {
        // Get id_po from URL if exists
        $id_po = $this->request->getGet('id_po');
        
        // Get PO data if id_po exists
        $selected_po = null;
        if ($id_po) {
            $selected_po = $this->transPOModel->find($id_po);
        }

        $data = [
            'title'         => 'Buat Faktur',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'suppliers'     => $this->supplierModel->where('status_hps', '0')->findAll(),
            'po_list'       => $this->transPOModel->where('status', '4')->findAll(), // Only processed POs
            'selected_po'   => $selected_po
        ];

        return $this->view($this->theme->getThemePath() . '/transaksi/beli/trans_beli', $data);
    }

    /**
     * Store new purchase transaction
     */
    public function store()
    {
        // Validation rules
        $rules = [
            'id_supplier' => [
                'rules'  => 'required|numeric',
                'errors' => [
                    'required' => 'Supplier harus dipilih',
                    'numeric'  => 'Supplier tidak valid'
                ]
            ],
            'tgl_masuk' => [
                'rules'  => 'required|valid_date',
                'errors' => [
                    'required'   => 'Tanggal faktur harus diisi',
                    'valid_date' => 'Tanggal faktur tidak valid'
                ]
            ],
            'no_nota' => [
                'rules'  => 'required|is_unique[tbl_trans_beli.no_nota]',
                'errors' => [
                    'required'  => 'No. Faktur harus diisi',
                    'is_unique' => 'No. Faktur sudah digunakan'
                ]
            ],
            'status_ppn' => [
                'rules'  => 'required|in_list[0,1,2]',
                'errors' => [
                    'required'  => 'Status PPN harus dipilih',
                    'in_list'  => 'Status PPN tidak valid'
                ]
            ]
        ];

        // Run validation
        if (!$this->validate($rules)) {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        // Get form data
        $data = [
            'id_po'         => $this->request->getPost('id_po'),
            'id_supplier'   => $this->request->getPost('id_supplier'),
            'id_user'       => $this->ionAuth->user()->row()->id,
            'tgl_masuk'     => $this->request->getPost('tgl_masuk'),
            'tgl_keluar'    => $this->request->getPost('tgl_keluar'),
            'no_nota'       => $this->request->getPost('no_nota'),
            'status_ppn'    => $this->request->getPost('status_ppn'),
            'status_nota'   => 0, // Draft
        ];

        // If no_nota is empty, generate new one
        if (empty($data['no_nota'])) {
            $data['no_nota'] = $this->transBeliModel->generateKode();
        }

        // Get PO data if exists
        if (!empty($data['id_po'])) {
            $po = $this->transPOModel->find($data['id_po']);
            if ($po) {
                $data['no_po']      = $po->no_nota;
                $data['supplier']   = $po->supplier;
            }
        }

        // Save transaction
        try {
            $this->db->transStart();
            
            // Insert main transaction
            $this->transBeliModel->insert($data);
            $id = $this->transBeliModel->getInsertID();

            // Get items based on whether PO exists or not
            if (!empty($data['id_po'])) {
                // If PO exists, get items from tbl_trans_beli_po_det
                $po_det = $this->transBeliPODetModel->getItemByPO($data['id_po']);
                
                // Check and insert items
                foreach ($po_det as $item) {
                    // Check if item already exists in trans_beli_det
                    $existingItem = $this->transBeliDetModel
                        ->where('id_pembelian', $id)
                        ->where('id_item', $item->id_item)
                        ->first();

                    if (!$existingItem) {
                        // Insert new item
                        $itemData = [
                            'id_user'       => $this->ionAuth->user()->row()->id,
                            'id_pembelian'  => $id,
                            'id_item'       => $item->id_item,
                            'id_satuan'     => $item->id_satuan,
                            'tgl_masuk'     => $data['tgl_masuk'],
                            'kode'          => $item->kode,
                            'item'          => $item->item,
                            'jml'           => $item->jml,
                            'jml_satuan'    => $item->jml_satuan,
                            'satuan'        => $item->satuan
                        ];

                        $this->transBeliDetModel->insert($itemData);
                    }
                }
            } else {
                // If no PO, get items from tbl_m_item where status=1
                $items = $this->db->table('tbl_m_item')
                                 ->where('status', '1')
                                 ->get()
                                 ->getResult();

                foreach ($items as $item) {
                    // Get default satuan for the item
                    $satuan = $this->db->table('tbl_m_satuan')
                                      ->where('id', $item->id_satuan)
                                      ->get()
                                      ->getRow();

                    // Insert item
                    $itemData = [
                        'id_user'       => $this->ionAuth->user()->row()->id,
                        'id_pembelian'  => $id,
                        'id_item'       => $item->id,
                        'id_satuan'     => $item->id_satuan ?? 1, // Default to first satuan if not set
                        'tgl_masuk'     => $data['tgl_masuk'],
                        'kode'          => $item->kode,
                        'item'          => $item->item,
                        'jml'           => 0, // Default quantity
                        'jml_satuan'    => $satuan ? $satuan->jml : 1,
                        'satuan'        => $satuan ? $satuan->satuanBesar : 'PCS'
                    ];

                    $this->transBeliDetModel->insert($itemData);
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan transaksi');
            }

            return redirect()->to('transaksi/beli/edit/' . $id)
                            ->with('success', 'Transaksi berhasil disimpan');

        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', $e->getMessage());
        }
    }

    /**
     * Edit purchase transaction
     * 
     * @param int $id Transaction ID
     */
    public function edit($id)
    {
        // Check if transaction exists
        $transaksi = $this->transBeliModel->find($id);
        if (!$transaksi) {
            return redirect()->back()
                            ->with('error', 'Transaksi tidak ditemukan');
        }

        // Get transaction items
        $transaksi->items = $this->transBeliDetModel->select('
                tbl_trans_beli_det.*,
                tbl_m_item.kode as item_kode,
                tbl_m_satuan.satuanBesar as satuan_name
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_beli_det.id_item', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_trans_beli_det.id_satuan', 'left')
            ->where('id_pembelian', $id)
            ->findAll();

        // Calculate totals
        $subtotal = 0;
        $dpp = 0;
        $ppn = 0;
        foreach ($transaksi->items as $item) {
            $subtotal += $item->subtotal;
        }
        
        // Calculate DPP and PPN based on status_ppn
        if ($transaksi->status_ppn == '1') { // Tambah PPN
            $dpp = $subtotal;
            $ppn = $dpp * 0.11;
        } else if ($transaksi->status_ppn == '2') { // Include PPN
            $dpp = $subtotal / 1.11;
            $ppn = $subtotal - $dpp;
        } else { // Non PPN
            $dpp = $subtotal;
            $ppn = 0;
        }

        $transaksi->jml_subtotal = $subtotal;
        $transaksi->jml_dpp = $dpp;
        $transaksi->jml_ppn = $ppn;
        $transaksi->jml_total = $subtotal + $ppn;

        // Get PO list and suppliers
        $po_list = $this->transPOModel->findAll();
        $suppliers = $this->supplierModel->findAll();

        // Prepare data for view
        $data = [
            'title'      => 'Edit Transaksi Pembelian',
            'Pengaturan' => $this->pengaturan,
            'user'       => $this->ionAuth->user()->row(),
            'transaksi'  => $transaksi,
            'po_list'    => $po_list,
            'suppliers'  => $suppliers,
        ];

        return view('admin-lte-3/transaksi/beli/trans_beli_edit', $data);
    }

    /**
     * Update purchase transaction
     * 
     * @param int $id Transaction ID
     */
    public function update($id)
    {
        // Check if transaction exists
        $transaksi = $this->transBeliModel->find($id);
        if (!$transaksi) {
            return redirect()->back()
                            ->with('error', 'Transaksi tidak ditemukan');
        }

        // Validation rules
        $rules = [
            'id_supplier' => [
                'rules'  => 'required|numeric',
                'errors' => [
                    'required' => 'Supplier harus dipilih',
                    'numeric'  => 'Supplier tidak valid'
                ]
            ],
            'tgl_masuk' => [
                'rules'  => 'required|valid_date',
                'errors' => [
                    'required'   => 'Tanggal faktur harus diisi',
                    'valid_date' => 'Tanggal faktur tidak valid'
                ]
            ],
            'no_nota' => [
                'rules'  => "required|is_unique[tbl_trans_beli.no_nota,id,{$id}]",
                'errors' => [
                    'required'  => 'No. Faktur harus diisi',
                    'is_unique' => 'No. Faktur sudah digunakan'
                ]
            ],
            'status_ppn' => [
                'rules'  => 'required|in_list[0,1,2]',
                'errors' => [
                    'required'  => 'Status PPN harus dipilih',
                    'in_list'  => 'Status PPN tidak valid'
                ]
            ]
        ];

        // Run validation
        if (!$this->validate($rules)) {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        // Get form data
        $data = [
            'id_po'         => $this->request->getPost('id_po'),
            'id_supplier'   => $this->request->getPost('id_supplier'),
            'tgl_masuk'     => $this->request->getPost('tgl_masuk'),
            'tgl_keluar'    => $this->request->getPost('tgl_keluar'),
            'no_nota'       => $this->request->getPost('no_nota'),
            'status_ppn'    => $this->request->getPost('status_ppn')
        ];

        // Get PO data if exists and changed
        if (!empty($data['id_po']) && $data['id_po'] != $transaksi->id_po) {
            $po = $this->transPOModel->find($data['id_po']);
            if ($po) {
                $data['no_po']      = $po->no_nota;
                $data['supplier']   = $po->supplier;
            }
        }

        // Save transaction
        try {
            $this->db->transStart();
            
            $this->transBeliModel->update($id, $data);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal mengupdate transaksi');
            }

            return redirect()->back()
                            ->with('success', 'Transaksi berhasil diupdate');

        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', $e->getMessage());
        }
    }

    /**
     * Get items for purchase transaction
     * 
     * @param int $id Transaction ID
     * @return \CodeIgniter\HTTP\Response
     */
    public function getItems($id)
    {
        try {
            // Get transaction data
            $transaksi = $this->transBeliModel->find($id);
            if (!$transaksi) {
                throw new \Exception('Transaksi tidak ditemukan');
            }

            // Get all active items from tbl_m_item where status=1 and status_stok=1
            $items = $this->db->table('tbl_m_item')
                             ->select('tbl_m_item.*, tbl_m_satuan.satuanBesar, tbl_m_satuan.jml as jml_satuan')
                             ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
                             ->where('tbl_m_item.status', '1')
                             ->where('tbl_m_item.status_stok', '1')
                             ->get()
                             ->getResult();

            $formattedItems = [];
            foreach ($items as $item) {
                $formattedItems[] = [
                    'id' => $item->id,
                    'kode' => $item->kode,
                    'item' => $item->item,
                    'id_satuan' => $item->id_satuan ?? 1,
                    'satuan' => $item->satuanBesar ?? 'PCS',
                    'jml_satuan' => $item->jml_satuan ?? 1
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'items' => $formattedItems
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Process purchase transaction
     * 
     * @param int $id Transaction ID
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function proses($id)
    {
        try {
            // Get transaction data
            $transaksi = $this->transBeliModel->find($id);
            if (!$transaksi) {
                throw new \Exception('Transaksi tidak ditemukan');
            }

            // Check if transaction is in draft status
            if ($transaksi->status_nota != '0') {
                throw new \Exception('Hanya transaksi draft yang dapat diproses');
            }

            // Get transaction items
            $items = $this->transBeliDetModel->where('id_pembelian', $id)->findAll();
            if (empty($items)) {
                throw new \Exception('Transaksi tidak memiliki item');
            }

            // Start transaction
            $this->db->transStart();

            // Update transaction status to processed
            $this->transBeliModel->update($id, [
                'status_nota' => '1', // Processed
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // If PO exists, update PO status
            if (!empty($transaksi->id_po)) {
                $this->transPOModel->update($transaksi->id_po, [
                    'status' => '3', // Completed
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal memproses transaksi');
            }

            return redirect()->to('transaksi/beli')
                            ->with('success', 'Transaksi berhasil diproses');

        } catch (\Exception $e) {
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
            }
            
            return redirect()->back()
                            ->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Display purchase transaction details
     * 
     * @param int $id Transaction ID
     * @return mixed
     */
    public function detail($id)
    {
        try {
            // Get transaction data
            $transaksi = $this->transBeliModel->find($id);
            if (!$transaksi) {
                throw new \Exception('Transaksi tidak ditemukan');
            }

            // Get transaction items with item and satuan data
            $items = $this->transBeliDetModel->select('
                    tbl_trans_beli_det.*,
                    tbl_m_item.kode as item_kode,
                    tbl_m_item.item as item_name,
                    tbl_m_satuan.satuanBesar as satuan_name
                ')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_beli_det.id_item', 'left')
                ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_trans_beli_det.id_satuan', 'left')
                ->where('id_pembelian', $id)
                ->findAll();

            // Calculate totals
            $subtotal = 0;
            $totalDiskon = 0;
            $totalPotongan = 0;
            
            foreach ($items as $item) {
                $subtotal += $item->subtotal ?? 0;
                $totalDiskon += ($item->disk1 ?? 0) + ($item->disk2 ?? 0) + ($item->disk3 ?? 0);
                $totalPotongan += $item->potongan ?? 0;
            }

            // Calculate DPP and PPN based on status_ppn
            $dpp = 0;
            $ppn = 0;
            
            if ($transaksi->status_ppn == '1') { // Tambah PPN
                $dpp = $subtotal;
                $ppn = $dpp * 0.11;
            } else if ($transaksi->status_ppn == '2') { // Include PPN
                $dpp = $subtotal / 1.11;
                $ppn = $subtotal - $dpp;
            } else { // Non PPN
                $dpp = $subtotal;
                $ppn = 0;
            }

            $total = $subtotal + $ppn;

            // Get supplier data
            $supplier = $this->supplierModel->find($transaksi->id_supplier);

            $data = [
                'title'         => 'Detail Transaksi Pembelian',
                'Pengaturan'    => $this->pengaturan,
                'user'          => $this->ionAuth->user()->row(),
                'transaksi'     => $transaksi,
                'items'         => $items,
                'supplier'      => $supplier,
                'subtotal'      => $subtotal,
                'total_diskon'  => $totalDiskon,
                'total_potongan' => $totalPotongan,
                'dpp'           => $dpp,
                'ppn'           => $ppn,
                'total'         => $total
            ];

            return $this->view($this->theme->getThemePath() . '/transaksi/beli/detail', $data);

        } catch (\Exception $e) {
            return redirect()->to('transaksi/beli')
                            ->with('error', 'Gagal memuat detail transaksi: ' . $e->getMessage());
        }
    }
} 