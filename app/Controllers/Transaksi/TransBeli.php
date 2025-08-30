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
use App\Models\ItemModel;


class TransBeli extends BaseController
{
    protected $transPOModel;
    protected $transBeliModel;
    protected $supplierModel;
    protected $itemModel;

    public function __construct()
    {
        $this->transPOModel       = new TransBeliPOModel();
        $this->transBeliModel     = new TransBeliModel();
        $this->transBeliDetModel  = new TransBeliDetModel();
        $this->transBeliPOModel   = new TransBeliPODetModel();
        $this->transBeliPODetModel= new TransBeliPODetModel();
        $this->supplierModel      = new SupplierModel();
        $this->itemModel          = new ItemModel();

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
        $id_po        = $this->request->getPost('id_po');
        $id_supplier  = $this->request->getPost('id_supplier');
        $tgl_masuk    = $this->request->getPost('tgl_masuk');
        $tgl_keluar   = $this->request->getPost('tgl_keluar');
        $status_ppn   = $this->request->getPost('status_ppn');

        // Generate no. nota if not provided
        $no_nota_post = $this->request->getPost('no_nota');
        if (!empty($no_nota_post)) {
            $no_nota = $this->transBeliModel->generateKode();
        } else {
            $no_nota = $no_nota_post;
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
            return redirect()->to('transaksi/beli/create')
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        // Get form data
        $data = [
            'id_po'         => $id_po,
            'id_supplier'   => $id_supplier,
            'id_user'       => $this->ionAuth->user()->row()->id,
            'tgl_masuk'     => $tgl_masuk,
            'tgl_keluar'    => $tgl_keluar,
            'no_nota'       => $no_nota,
            'status_ppn'    => $status_ppn,
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
     * Add item to purchase transaction cart
     * 
     * @param int $id Transaction ID
     * @return \CodeIgniter\HTTP\Response
     */
    public function cart_add($id)
    {
        try {
            // Check if transaction exists
            $transaksi = $this->transBeliModel->find($id);
            if (!$transaksi) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan'
                ])->setStatusCode(404);
            }

            // Check if transaction is in draft status
            if ($transaksi->status_nota != '0') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya transaksi draft yang dapat diubah'
                ])->setStatusCode(400);
            }

            // Validation rules
            $rules = [
                'id_item' => [
                    'rules'  => 'required|numeric',
                    'errors' => [
                        'required' => 'Item harus dipilih',
                        'numeric'  => 'Item tidak valid'
                    ]
                ],
                'jml' => [
                    'rules'  => 'required|numeric|greater_than[0]',
                    'errors' => [
                        'required'     => 'Jumlah harus diisi',
                        'numeric'      => 'Jumlah harus berupa angka',
                        'greater_than' => 'Jumlah harus lebih dari 0'
                    ]
                ],
                'id_satuan' => [
                    'rules'  => 'required|numeric',
                    'errors' => [
                        'required' => 'Satuan harus dipilih',
                        'numeric'  => 'Satuan tidak valid'
                    ]
                ],
                'harga' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required'     => 'Harga harus diisi'
                    ]
                ]
            ];

            // Run validation
            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors'  => $this->validator->getErrors()
                ])->setStatusCode(400);
            }

            // Custom validation for harga after cleaning
            $harga_clean = str_replace(['.', ','], ['', '.'], $this->request->getPost('harga'));
            if (!is_numeric($harga_clean) || $harga_clean <= 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Harga harus berupa angka yang valid dan lebih dari 0'
                ])->setStatusCode(400);
            }

            // Get form data
            $id_item   = $this->request->getPost('id_item');
            $jml       = (float) $this->request->getPost('jml');
            $id_satuan = $this->request->getPost('id_satuan');
            $harga     = (float) str_replace(['.', ','], ['', '.'], $this->request->getPost('harga'));
            $potongan  = (float) str_replace(['.', ','], ['', '.'], $this->request->getPost('potongan') ?? '0');
            $disk1     = (float) ($this->request->getPost('disk1') ?? 0);
            $disk2     = (float) ($this->request->getPost('disk2') ?? 0);
            $disk3     = (float) ($this->request->getPost('disk3') ?? 0);

            // Get item details
            $item = $this->db->table('tbl_m_item')
                ->select('tbl_m_item.*, tbl_m_satuan.satuanBesar')
                ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
                ->where('tbl_m_item.id', $id_item)
                ->get()
                ->getRow();

            if (!$item) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Item tidak ditemukan'
                ])->setStatusCode(404);
            }

            // Get satuan details
            $satuan = $this->db->table('tbl_m_satuan')
                ->where('id', $id_satuan)
                ->get()
                ->getRow();

            if (!$satuan) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Satuan tidak ditemukan'
                ])->setStatusCode(404);
            }

            // Calculate subtotal
            $subtotal = $jml * $harga;

            // Apply discounts
            $total_disk = $disk1 + $disk2 + $disk3;
            if ($total_disk > 0) {
                $subtotal = $subtotal - ($subtotal * ($total_disk / 100));
            }

            // Apply potongan
            if ($potongan > 0) {
                $subtotal = $subtotal - $potongan;
            }

            // Calculate harga_beli (unit price after all discounts and potongan)
            $harga_beli = 0;
            if ($jml > 0) {
                $harga_beli = $subtotal / $jml;
            }

            // Check if item already exists in cart
            $existingItem = $this->transBeliDetModel
                ->where('id_pembelian', $id)
                ->where('id_item', $id_item)
                ->first();

            $this->db->transStart();

            if ($existingItem) {
                // Update existing item
                $updateData = [
                    'jml'        => $jml,
                    'id_satuan'  => $id_satuan,
                    'satuan'     => $satuan->satuanBesar,
                    'jml_satuan' => $satuan->jml ?? 1,
                    'harga'      => $harga,
                    'potongan'   => $potongan,
                    'disk1'      => $disk1,
                    'disk2'      => $disk2,
                    'disk3'      => $disk3,
                    'subtotal'   => $subtotal,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->transBeliDetModel->update($existingItem->id, $updateData);
                $action = 'updated';
            } else {
                // Insert new item
                $insertData = [
                    'id_user'     => $this->ionAuth->user()->row()->id,
                    'id_pembelian'=> $id,
                    'id_item'     => $id_item,
                    'id_satuan'   => $id_satuan,
                    'tgl_masuk'   => $transaksi->tgl_masuk,
                    'kode'        => $item->kode,
                    'item'        => $item->item,
                    'jml'         => $jml,
                    'jml_satuan'  => $satuan->jml ?? 1,
                    'satuan'      => $satuan->satuanBesar,
                    'harga'       => $harga,
                    'potongan'    => $potongan,
                    'disk1'       => $disk1,
                    'disk2'       => $disk2,
                    'disk3'       => $disk3,
                    'subtotal'    => $subtotal,
                    'created_at'  => date('Y-m-d H:i:s')
                ];

                $this->transBeliDetModel->insert($insertData);
                $action = 'added';
            }

            // Save harga_beli (final price after discount and potongan) to tbl_m_item.harga_beli
            $this->itemModel->update($id_item, ['harga_beli' => $harga_beli]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan item');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Item berhasil ' . ($action == 'added' ? 'ditambahkan' : 'diupdate'),
                'data'    => [
                    'item'   => $item->item,
                    'action' => $action
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Redirect GET requests to cart_add back to edit page
     * 
     * @param int $id Transaction ID
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function cart_add_redirect($id)
    {
        return redirect()->to("transaksi/beli/edit/{$id}")
                        ->with('error', 'Metode tidak diizinkan. Gunakan form untuk menambahkan item.');
    }

    /**
     * Update cart item
     * 
     * @param int $id Item ID
     * @return \CodeIgniter\HTTP\Response
     */
    public function cart_update($id)
    {
        try {
            // Get item data
            $item = $this->transBeliDetModel->find($id);
            if (!$item) {
                throw new \Exception('Item tidak ditemukan');
            }

            // Get form data
            $jml = $this->request->getPost('jml');
            $id_satuan = $this->request->getPost('id_satuan');
            $harga = $this->request->getPost('harga');
            $potongan = $this->request->getPost('potongan');

            // Validation
            if (empty($jml) || empty($harga)) {
                throw new \Exception('Jumlah dan harga harus diisi');
            }

            // Format angka ke format database
            $harga = format_angka_db($harga);
            $potongan = format_angka_db($potongan);

            // Get satuan data
            $satuan = $this->db->table('tbl_m_satuan')->where('id', $id_satuan)->get()->getRow();

            // Calculate subtotal
            $subtotal = ($jml * $harga) - $potongan;

            // Hitung harga_beli per item
            $harga_beli = $jml > 0 ? $subtotal / $jml : 0;

            // Update item
            $updateData = [
                'jml'         => $jml,
                'id_satuan'   => $id_satuan,
                'satuan'      => $satuan ? $satuan->satuanBesar : 'PCS',
                'harga'       => $harga,
                'potongan'    => $potongan,
                'subtotal'    => $subtotal,
                'updated_at'  => date('Y-m-d H:i:s'),
            ];

            $this->transBeliDetModel->update($id, $updateData);
            $this->itemModel->update($item->id_item, ['harga_beli' => $harga_beli]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Item berhasil diupdate '.$item->id_item
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete cart item
     * 
     * @param int $id Item ID
     * @return \CodeIgniter\HTTP\Response
     */
    public function cart_delete($id)
    {
        try {
            // Get item data
            $item = $this->transBeliDetModel->find($id);
            if (!$item) {
                throw new \Exception('Item tidak ditemukan');
            }

            // Delete item
            $this->transBeliDetModel->delete($id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Item berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
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
            $items = $this->transBeliDetModel->select('SUM(diskon) as jml_diskon, SUM(potongan) as jml_potongan, SUM(subtotal) as jml_gtotal')->where('id_pembelian', $id)->first();
            if (empty($items)) {
                throw new \Exception('Transaksi tidak memiliki item');
            }

            // Hitung total-totalan dari detail
            $jml_total     = $items->jml_gtotal - $items->jml_potongan - $items->jml_diskon;
            $jml_potongan  = $items->jml_potongan;
            $jml_diskon    = $items->jml_diskon;
            $jml_subtotal  = $items->jml_gtotal;
            // Hitung DPP, PPN, dan jml_ppn sesuai status_ppn
            // status_ppn: 1 = include, 2 = exclude (added)
            $jml_dpp = 0;
            $ppn = 0;
            $jml_ppn = 0;

            // Gunakan nilai PPN dari pengaturan
            $ppn = $this->pengaturan->ppn;
            if ($transaksi->status_ppn == 1) {
                // PPN included (sudah termasuk di subtotal)
                // DPP = subtotal / (1 + (ppn/100)), PPN = $ppn%
                $jml_dpp = $jml_subtotal / (1 + ($ppn / 100));
                $jml_ppn = $jml_subtotal - $jml_dpp;
            } elseif ($transaksi->status_ppn == 2) {
                // PPN added (di luar subtotal)
                // DPP = subtotal, PPN = $ppn%
                $jml_dpp = $jml_subtotal;
                $jml_ppn = $jml_dpp * ($ppn / 100);
            }else{
                $ppn = 0;
            }
            
            $jml_gtotal    = $jml_subtotal + $jml_ppn;

            // Start transaction
            $this->db->transStart();

            // Update transaction status to processed + update total fields
            $data = [
                'status_nota'   => '1', // Processed
                'status_bayar'  => '0', // Paid
                'jml_total'     => $jml_total,
                'jml_potongan'  => $jml_potongan,
                'jml_diskon'    => $jml_diskon,
                'jml_subtotal'  => $jml_subtotal,
                'jml_dpp'       => $jml_dpp,
                'ppn'           => $ppn,
                'jml_ppn'       => $jml_ppn,
                'jml_gtotal'    => $jml_gtotal,
            ];
            $this->transBeliModel->update($id, $data);

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
            
            return redirect()->to('transaksi/beli')
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