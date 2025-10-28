<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-29
 * 
 * TransBeliPO Controller
 * 
 * Controller for managing Purchase Orders
 */

namespace App\Controllers\Transaksi;

use App\Controllers\BaseController;
use App\Models\TransBeliPOModel;
use App\Models\TransBeliPODetModel;
use App\Models\ItemModel;
use App\Models\SupplierModel;
use App\Models\PengaturanModel;
use App\Models\SatuanModel;
use TCPDF;

class TransBeliPO extends BaseController
{
    protected $transBeliPOModel;
    protected $transBeliPODetModel;
    protected $itemModel;
    protected $supplierModel;
    protected $pengaturanModel;
    protected $satuanModel;
    protected $db;
    protected $validation;

    public function __construct()
    {
        $this->transBeliPOModel = new TransBeliPOModel();
        $this->transBeliPODetModel = new TransBeliPODetModel();
        $this->itemModel = new ItemModel();
        $this->supplierModel = new SupplierModel();
        $this->pengaturanModel = new PengaturanModel();
        $this->satuanModel = new SatuanModel();
        $this->db = \Config\Database::connect();
        $this->validation = \Config\Services::validation();
    }

    /**
     * Display list of purchase orders
     */
    public function index()
    {
        $filters = [
            'supplier' => $this->request->getGet('supplier'),
            'status'   => $this->request->getGet('status'),
            'q'        => $this->request->getGet('q')
        ];

        $data = [
            'title'         => 'Data Purchase Order',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'po_list'       => $this->transBeliPOModel->getWithRelations($filters)->paginate(10, 'po'),
            'pager'         => $this->transBeliPOModel->pager,
            'suppliers'     => $this->supplierModel->where('status_hps', '0')->findAll(),
            'filters'       => $filters,
            'validation'    => $this->validation,
            'request'       => $this->request,
            'transBeliPOModel' => $this->transBeliPOModel
        ];

        return $this->view($this->theme->getThemePath() . '/transaksi/po/index', $data);
    }

    /**
     * Display purchase order creation form
     */
    public function create()
    {
        $data = [
            'title'      => 'Buat Purchase Order',
            'Pengaturan' => $this->pengaturan,
            'user'       => $this->ionAuth->user()->row(),
            'suppliers'  => $this->supplierModel->where('status_hps', '0')->findAll(),
            'validation' => $this->validation,
            'po_number'  => $this->transBeliPOModel->generateNoNota()
        ];

        return $this->view($this->theme->getThemePath() . '/transaksi/po/trans_po', $data);
    }

    /**
     * Store new purchase order
     */
    public function store()
    {
        // Validation rules
        $rules = [
            'supplier_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Supplier harus dipilih'
                ]
            ],
            'tgl_po' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Tanggal PO harus diisi',
                    'valid_date' => 'Format tanggal tidak valid'
                ]
            ],
            'alamat_pengiriman' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Alamat pengiriman harus diisi',
                    'max_length' => 'Alamat pengiriman maksimal 160 karakter'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('transaksi/po/create'))
                            ->withInput()
                            ->with('errors', $this->validation->getErrors())
                            ->with('error', 'Validasi gagal, periksa kembali input Anda');
        }

        try {
            $supplier = $this->supplierModel->find($this->request->getPost('supplier_id'));
            if (!$supplier) {
                throw new \RuntimeException('Supplier tidak ditemukan');
            }

            $data = [
                'id_supplier'   => $supplier->id,
                'id_user'       => $this->ionAuth->user()->row()->id,
                'tgl_masuk'     => $this->request->getPost('tgl_po'),
                'no_nota'       => $this->transBeliPOModel->generateNoNota(),
                'supplier'      => $supplier->nama,
                'keterangan'    => $this->request->getPost('keterangan'),
                'pengiriman'    => $this->request->getPost('alamat_pengiriman'),
                'status'        => 0 // Draft status
            ];

            $this->db->transStart();

            if (!$this->transBeliPOModel->insert($data)) {
                throw new \RuntimeException('Gagal menyimpan PO');
            }

            $po_id = $this->transBeliPOModel->getInsertID();

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Gagal menyimpan PO');
            }

            return redirect()->to('transaksi/po/edit/' . $po_id)
                            ->with('success', 'Purchase Order berhasil dibuat');

        } catch (\Exception $e) {
            log_message('error', '[TransBeliPO::store] ' . $e->getMessage());
            return redirect()->to(base_url('transaksi/po/create'))
                            ->withInput()
                            ->with('error', 'Gagal membuat Purchase Order: ' . $e->getMessage());
        }
    }

    /**
     * Display purchase order edit form
     */
    public function edit($id = null)
    {
        // try {
            // Get PO data
            $po = $this->transBeliPOModel->find($id);
            if (!$po) {
                throw new \RuntimeException('Data PO tidak ditemukan');
            }

            // Get PO items
            $items = $this->transBeliPODetModel->where('id_pembelian', $id)->findAll();

            $data = [
                'title'      => 'Edit Purchase Order',
                'Pengaturan' => $this->pengaturan,
                'user'       => $this->ionAuth->user()->row(),
                'po'         => $po,
                'po_details' => $items,
                'suppliers'  => $this->supplierModel->where('status_hps', '0')->findAll(),
                'satuans'    => $this->satuanModel->findAll(),
                'validation' => $this->validation
            ];

            return $this->view($this->theme->getThemePath() . '/transaksi/po/trans_po_edit', $data);

        // } catch (\Exception $e) {
        //     log_message('error', '[TransBeliPO::edit] ' . $e->getMessage());
        //     return redirect()->back()
        //                    ->with('error', 'Gagal memuat form edit PO');
        // }
    }

    /**
     * Update purchase order
     */
    public function update($id)
    {
        // Validation rules
        $rules = [
            'supplier_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Supplier harus dipilih'
                ]
            ],
            'tgl_po' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Tanggal PO harus diisi',
                    'valid_date' => 'Format tanggal tidak valid'
                ]
            ],
            'alamat_pengiriman' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Alamat pengiriman harus diisi',
                    'max_length' => 'Alamat pengiriman maksimal 160 karakter'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('transaksi/po/edit/' . $id))
                            ->withInput()
                            ->with('errors', $this->validation->getErrors())
                            ->with('error', 'Validasi gagal, periksa kembali input Anda');
        }

        try {
            $po = $this->transBeliPOModel->find($id);
            if (!$po) {
                throw new \RuntimeException('Data PO tidak ditemukan');
            }

            // Only allow editing draft POs
            if ($po->status != 0) {
                throw new \RuntimeException('Hanya PO dengan status draft yang dapat diedit');
            }

            $supplier = $this->supplierModel->find($this->request->getPost('supplier_id'));
            if (!$supplier) {
                throw new \RuntimeException('Supplier tidak ditemukan');
            }

            $data = [
                'id_supplier'   => $supplier->id,
                'tgl_masuk'     => $this->request->getPost('tgl_po'),
                'supplier'      => $supplier->nama,
                'keterangan'    => $this->request->getPost('keterangan'),
                'pengiriman'    => $this->request->getPost('alamat_pengiriman')
            ];

            $this->db->transStart();

            if (!$this->transBeliPOModel->update($id, $data)) {
                throw new \RuntimeException('Gagal mengupdate PO');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Gagal mengupdate PO');
            }

            return redirect()->to('transaksi/po')
                            ->with('success', 'Purchase Order berhasil diupdate');

        } catch (\Exception $e) {
            log_message('error', '[TransBeliPO::update] ' . $e->getMessage());
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Gagal mengupdate Purchase Order: ' . $e->getMessage());
        }
    }

    /**
     * Display purchase order details
     */
    public function detail($id)
    {
        try {
            $po = $this->transBeliPOModel->getWithRelations(['tbl_trans_beli_po.id' => $id]);
            if (!$po) {
                throw new \RuntimeException('Data PO tidak ditemukan');
            }

            // Get PO items
            $items = $this->transBeliPODetModel->getWithRelations($id);

            $data = [
                'title'         => 'Detail Purchase Order',
                'Pengaturan'    => $this->pengaturan,
                'user'          => $this->ionAuth->user()->row(),
                'po'            => $po,
                'items'         => $items
            ];

            return $this->view($this->theme->getThemePath() . '/transaksi/po/trans_po_detail', $data);

        } catch (\Exception $e) {
            log_message('error', '[TransBeliPO::detail] ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Gagal memuat detail PO');
        }
    }

    /**
     * Add item to PO cart
     * 
     * @param int $po_id The PO ID
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function cart_add($po_id = null)
    {
        try {
            // Validate PO ID
            if (!$po_id) {
                throw new \RuntimeException('ID PO tidak valid');
            }

            // Check if PO exists and is editable
            $po = $this->transBeliPOModel->find($po_id);
            if (!$po) {
                throw new \RuntimeException('Data PO tidak ditemukan');
            }
            if ($po->status != 0) {
                throw new \RuntimeException('PO tidak dapat diubah');
            }

            // Validate input
            $rules = [
                'id_item' => [
                    'label' => 'Item',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih'
                    ]
                ],
                'jumlah' => [
                    'label' => 'Jumlah',
                    'rules' => 'required|numeric|greater_than[0]',
                    'errors' => [
                        'required' => '{field} harus diisi',
                        'numeric' => '{field} harus berupa angka',
                        'greater_than' => '{field} harus lebih dari 0'
                    ]
                ],
                'satuan' => [
                    'label' => 'Satuan',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih'
                    ]
                ]
            ];

            if (!$this->validate($rules)) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Validasi gagal',
                        'errors'  => $this->validator->getErrors(),
                        'csrfHash'=> csrf_hash(),
                    ])->setStatusCode(422);
                }
                return redirect()->to(base_url("transaksi/po/edit/{$po_id}"))
                               ->withInput()
                               ->with('errors', $this->validator->getErrors())
                               ->with('error', 'Validasi gagal, periksa kembali input Anda');
            }

            // Get validated data
            $item_id    = $this->request->getPost('id_item');
            $jumlah     = $this->request->getPost('jumlah');
            $satuan     = $this->request->getPost('satuan');
            $keterangan = $this->request->getPost('keterangan');

            // Check if item exists
            $item = $this->itemModel->find($item_id);
            if (!$item) {
                throw new \RuntimeException('Item tidak ditemukan');
            }

            $satuan = $this->satuanModel->find($satuan);
            if (!$satuan) {
                throw new \RuntimeException('Satuan tidak ditemukan');
            }

            // Begin transaction
            $this->db->transBegin();

            // Add item to PO detail
            $data = [
                'id_pembelian' => $po_id,
                'id_item'      => $item_id,
                'id_satuan'    => $satuan->id,
                'id_user'      => $this->ionAuth->user()->row()->id,
                'tgl_masuk'    => $po->tgl_masuk,
                'kode'         => $item->kode,
                'item'         => $item->item,
                'jml'          => (int)$jumlah,
                'jml_satuan'   => (int)$satuan->jml,
                'satuan'       => $satuan->satuanBesar,
                'keterangan'   => $keterangan
            ];

            if (!$this->transBeliPODetModel->insert($data)) {
                throw new \RuntimeException('Gagal menambahkan item');
            }

            // Commit transaction
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                throw new \RuntimeException('Gagal menambahkan item');
            }
            $this->db->transCommit();

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Item berhasil ditambahkan',
                    'csrfHash'=> csrf_hash(),
                ]);
            }
            return redirect()->to(base_url("transaksi/po/edit/$po_id"))
                           ->with('success', 'Item berhasil ditambahkan');
        } catch (\Exception $e) {
            // Log error
            log_message('error', '[TransBeliPO::cart_add] ' . $e->getMessage());
            
            // Rollback transaction if active
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
            }

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menambahkan item: ' . $e->getMessage(),
                    'csrfHash'=> csrf_hash(),
                ])->setStatusCode(500);
            }
            return redirect()->to(base_url("transaksi/po/edit/{$po_id}"))
                           ->withInput()
                           ->with('error', 'Gagal menambahkan item: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Search items for Select2
     */
    public function itemsSearch()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }

        $q = trim((string) $this->request->getGet('q'));
        $limit = (int) ($this->request->getGet('limit') ?? 20);

        $builder = $this->itemModel->builder();
        if ($q !== '') {
            $builder->groupStart()
                ->like('kode', $q)
                ->orLike('item', $q)
            ->groupEnd();
        }
        $items = $builder->where('status_hps', '0')->limit($limit)->orderBy('item', 'ASC')->get()->getResult();

        $results = [];
        foreach ($items as $it) {
            $results[] = [
                'id'   => $it->id,
                'text' => '[' . ($it->kode ?? '-') . '] ' . ($it->item ?? '-')
            ];
        }

        return $this->response->setJSON(['results' => $results]);
    }

    /**
     * AJAX: Get suppliers by item using mapping table
     */
    public function suppliersByItem($itemId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request'])->setStatusCode(400);
        }

        try {
            if (empty($itemId)) {
                throw new \RuntimeException('Item ID tidak valid');
            }

            $suppliers = $this->supplierModel->getSuppliersByItem($itemId);
            if (!$suppliers) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'data'   => [],
                    'count'  => 0,
                    'csrfHash'=> csrf_hash(),
                ]);
            }

            $data = [];
            foreach ($suppliers as $s) {
                $data[] = [
                    'id'            => $s->supplier_id ?? $s->id_supplier,
                    'kode_supplier' => $s->supplier_kode ?? '',
                    'nama_supplier' => $s->supplier_nama ?? '',
                    'harga_beli'    => (float) ($s->harga_beli ?? 0),
                    'prioritas'     => (int) ($s->prioritas ?? 0),
                ];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $data,
                'count'  => count($data),
                'csrfHash'=> csrf_hash(),
            ]);
        } catch (\Exception $e) {
            log_message('error', '[TransBeliPO::suppliersByItem] ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'csrfHash'=> csrf_hash(),
            ])->setStatusCode(500);
        }
    }

    /**
     * AJAX: Get PO details (to refresh table without reload)
     */
    public function detailsJson($poId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request'])->setStatusCode(400);
        }

        try {
            $items = $this->transBeliPODetModel->select('tbl_trans_beli_po_det.*, tbl_m_item.kode as item_kode, tbl_m_item.item as item_nama, tbl_m_satuan.satuanBesar as satuan_nama')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_beli_po_det.id_item', 'left')
                ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_trans_beli_po_det.id_satuan', 'left')
                ->where('id_pembelian', $poId)
                ->orderBy('tbl_trans_beli_po_det.id', 'DESC')
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data'    => $items,
                'count'   => count($items),
                'csrfHash'=> csrf_hash(),
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
                'csrfHash'=> csrf_hash(),
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete item from PO cart permanently
     * 
     * @param int $id ID of PO detail item to delete
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function cart_delete($id)
    {
        try {
            // Get the PO ID from query string
            $po_id = $this->request->getGet('id');
            
            if (!$po_id) {
                throw new \Exception('ID PO tidak ditemukan');
            }

            // Delete the item permanently
            $this->transBeliPODetModel->delete($id);

            return redirect()->to("transaksi/po/edit/$po_id")
                           ->with('success', 'Item berhasil dihapus !!');
                           
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menghapus item: ' . $e->getMessage());
        }
    }

    /**
     * Delete PO
     */
    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->to('transaksi/po')
                           ->with('error', 'ID PO tidak ditemukan');
        }

        try {
            $po = $this->transBeliPOModel->find($id);
            if (!$po) {
                throw new \RuntimeException('Data PO tidak ditemukan');
            }

            // Only allow deletion of draft POs
            if ($po->status != 0) {
                throw new \RuntimeException('Hanya PO draft yang dapat dihapus');
            }

            // Start transaction
            $this->db->transStart();

            // Delete PO details first
            $this->transBeliPODetModel->where('id_pembelian', $id)->delete();

            // Delete PO
            $this->transBeliPOModel->delete($id);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Gagal menghapus PO');
            }

            return redirect()->to('transaksi/po')
                           ->with('success', 'PO berhasil dihapus');

        } catch (\Exception $e) {
            log_message('error', '[TransBeliPO::delete] ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', $e->getMessage());
        }
    }

    /**
     * Process PO and update its status
     * 
     * @param int $id PO ID to process
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function proses($id)
    {
        try {
            // Get PO data
            $po = $this->transBeliPOModel->find($id);
            
            if (!$po) {
                throw new \Exception('PO tidak ditemukan');
            }

            // Update PO status to processed (2)
            $this->transBeliPOModel->update($id, [
                'status' => '4'  // 2 = Processed
            ]);

            return redirect()->to('transaksi/po')
                           ->with('success', 'PO berhasil diproses');
                           
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal memproses PO: ' . $e->getMessage());
        }
    }

    /**
     * Create invoice from PO
     * 
     * @param int $id PO ID
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function buatFaktur($id)
    {
        try {
            // Start transaction
            $this->db->transStart();

            // Get PO data
            $po = $this->transBeliPOModel->find($id);
            if (!$po) {
                throw new \RuntimeException('Data PO tidak ditemukan');
            }

            // Verify PO is in processed status
            if ($po->status != '1') {
                throw new \RuntimeException('Hanya PO yang sudah diproses yang dapat dibuatkan faktur');
            }

            // Get PO details
            $poDetails = $this->transBeliPODetModel->where('id_pembelian', $id)->findAll();
            if (empty($poDetails)) {
                throw new \RuntimeException('PO tidak memiliki item');
            }

            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber();

            // Create invoice header
            $invoiceData = [
                'no_faktur' => $invoiceNumber,
                'id_po' => $po->id,
                'id_supplier' => $po->id_supplier,
                'tgl_faktur' => date('Y-m-d'),
                'total' => 0, // Will be calculated from details
                'status' => '0', // Draft status
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Insert invoice header
            $this->db->table('tbl_trans_beli_faktur')->insert($invoiceData);
            $invoiceId = $this->db->insertID();

            // Calculate total and create invoice details using master prices
            $total = 0;
            $itemHargaModel = new \App\Models\ItemHargaModel();
            
            foreach ($poDetails as $detail) {
                // Get master price for this item
                $masterPrice = $itemHargaModel->getPriceByQuantity($detail->id_item, $detail->jml);
                
                // Use master price if available, otherwise use PO price
                $harga = $masterPrice ? $masterPrice->harga : ($detail->harga ?? 0);
                
                $detailData = [
                    'id_faktur'     => $invoiceId,
                    'id_item'       => $detail->id_item,
                    'jml'           => $detail->jml,
                    'harga'         => $harga,
                    'subtotal'      => ($detail->jml * $harga),
                    'created_at'    => date('Y-m-d H:i:s')
                ];
                
                $this->db->table('tbl_trans_beli_faktur_det')->insert($detailData);
                $total += $detailData['subtotal'];
            }

            // Update invoice total
            $this->db->table('tbl_trans_beli_faktur')
                     ->where('id', $invoiceId)
                     ->update(['total' => $total]);

            // Update PO status to invoice created (2)
            $this->transBeliPOModel->update($id, [
                'status' => '2',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Commit transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Gagal membuat faktur');
            }

            return redirect()->to("transaksi/pembelian/faktur/edit/{$invoiceId}")
                            ->with('success', 'Faktur berhasil dibuat');

        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->db->transRollback();
            
            log_message('error', '[TransBeliPO::buatFaktur] ' . $e->getMessage());
            
            return redirect()->back()
                            ->with('error', $e->getMessage());
        }
    }

    /**
     * Generate invoice number
     * 
     * @return string
     */
    private function generateInvoiceNumber()
    {
        $prefix = 'FKT' . date('Ym');
        
        // Get last invoice number
        $lastInvoice = $this->db->table('tbl_trans_beli_faktur')
                               ->select('no_faktur')
                               ->like('no_faktur', $prefix, 'after')
                               ->orderBy('id', 'DESC')
                               ->get()
                               ->getRow();

        if (!$lastInvoice) {
            return $prefix . '0001';
        }

        // Extract number and increment
        $lastNumber = (int)substr($lastInvoice->no_faktur, -4);
        $newNumber = $lastNumber + 1;
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Approve or reject PO
     * 
     * @param int $id PO ID
     * @param string $action 'approve' or 'reject'
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function approve($id, $action = 'approve')
    {
        try {
            // Get PO data
            $po = $this->transBeliPOModel->find($id);
            
            if (!$po) {
                throw new \Exception('PO tidak ditemukan');
            }

            // Check if PO is in processed status
            if ($po->status != '4') {
                throw new \Exception('Hanya PO yang sudah diproses yang dapat diapprove/reject');
            }

            $newStatus = ($action === 'approve') ? '1' : '3'; // 1 = Approved, 3 = Rejected
            $statusText = ($action === 'approve') ? 'disetujui' : 'ditolak';

            // Update PO status
            $this->transBeliPOModel->update($id, [
                'status' => $newStatus,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return redirect()->to('transaksi/po')
                           ->with('success', "PO berhasil {$statusText}");
                           
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal memproses PO: ' . $e->getMessage());
        }
    }

    /**
     * Get PO statistics for dashboard
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function getStats()
    {
        try {
            $stats = [
                'total_po' => $this->transBeliPOModel->countAll(),
                'draft_po' => $this->transBeliPOModel->where('status', '0')->countAllResults(),
                'processed_po' => $this->transBeliPOModel->where('status', '4')->countAllResults(),
                'approved_po' => $this->transBeliPOModel->where('status', '1')->countAllResults(),
                'rejected_po' => $this->transBeliPOModel->where('status', '3')->countAllResults(),
                'invoice_created' => $this->transBeliPOModel->where('status', '2')->countAllResults(),
                'this_month' => $this->transBeliPOModel->where('MONTH(tgl_masuk)', date('m'))
                                                      ->where('YEAR(tgl_masuk)', date('Y'))
                                                      ->countAllResults(),
                'total_value' => $this->transBeliPOModel->selectSum('total')
                                                       ->where('status !=', '0')
                                                       ->get()
                                                       ->getRow()
                                                       ->total ?? 0
            ];

            return $this->response->setJSON($stats);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => true,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Bulk delete POs
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function bulkDelete()
    {
        try {
            $poIds = $this->request->getPost('po_ids');
            
            if (!$poIds || !is_array($poIds)) {
                throw new \Exception('Tidak ada PO yang dipilih');
            }

            $deletedCount = 0;
            $this->db->transStart();

            foreach ($poIds as $poId) {
                $po = $this->transBeliPOModel->find($poId);
                
                if ($po && $po->status == '0') { // Only delete draft POs
                    // Delete PO details first
                    $this->transBeliPODetModel->where('id_pembelian', $poId)->delete();
                    
                    // Delete PO
                    $this->transBeliPOModel->delete($poId);
                    $deletedCount++;
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal menghapus PO');
            }

            return redirect()->to('transaksi/po')
                           ->with('success', "Berhasil menghapus {$deletedCount} PO");

        } catch (\Exception $e) {
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
            }
            
            return redirect()->back()
                           ->with('error', 'Gagal menghapus PO: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF for Purchase Order
     * 
     * @param int $id PO ID to print
     * @return mixed
     */
    public function print($id)
    {            
        // Header
        // Fetch pengaturan (settings) from database
        $pengaturan = $this->pengaturanModel->first();

        // Use pengaturan values or fallback defaults
        $judul      = $pengaturan->judul ?? '';
        $alamat     = $pengaturan->alamat ?? '';
        $judul_app  = $pengaturan->judul_app ?? 'PURCHASE ORDER';
        $kota       = $pengaturan->kota ?? 'Semarang';
        $apt_apa    = $pengaturan->apt_apa ?? '';
        $apt_sipa   = $pengaturan->apt_sipa ?? '';
        
        try {
            // Get PO data with relations
            $po = $this->transBeliPOModel->getWithRelations(['id' => $id])->first();
            if (!$po) {
                throw new \Exception('PO tidak ditemukan');
            }

            // Get PO details
            $po_details = $this->transBeliPODetModel->select('
                    tbl_trans_beli_po_det.*,
                    tbl_m_item.kode as kode,
                    tbl_m_item.item as deskripsi
                ')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_beli_po_det.id_item')
                ->where('id_pembelian', $id)
                ->findAll();

            // Initialize PDF
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            
            // Set document information
            $pdf->SetCreator($judul_app);
            $pdf->SetAuthor($judul);
            $pdf->SetTitle('Purchase Order - ' . $po->no_nota);
            
            // Set margins
            $pdf->SetMargins(20, 20, 20);
            $pdf->SetHeaderMargin(0);
            $pdf->SetFooterMargin(0);
            
            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            
            // Add page
            $pdf->AddPage();
            
            // Set font
            $pdf->SetFont('helvetica', 'B', 12);

            $pdf->Cell(120, 6, $judul, 0, 0, 'L');
            $pdf->Cell(50, 6, $judul_app, 0, 1, 'R');
            
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(170, 5, $alamat, 0, 1, 'L');
            $pdf->Line(20, 33, 190, 33);
            $pdf->Ln(5);

            // APA & SIPA (if available)
            if (!empty($apt_apa)) {
                $pdf->Cell(15, 5, 'APA', 0, 0);
                $pdf->Cell(5, 5, ':', 0, 0);
                $pdf->Cell(170, 5, $apt_apa, 0, 1);
            }
            
            if (!empty($apt_sipa)) {
                $pdf->Cell(15, 5, 'SIPA', 0, 0);
                $pdf->Cell(5, 5, ':', 0, 0);
                $pdf->Cell(170, 5, $apt_sipa, 0, 1);
            }
            
            if (!empty($apt_apa) || !empty($apt_sipa)) {
                $pdf->Ln(5);
            }

            // Supplier Info & PO Details
            $pdf->Cell(20, 5, 'Kepada Yth.', 0, 1);
            $pdf->Cell(100, 5, $po->supplier ?? 'N/A', 0, 0);
            $pdf->Cell(25, 5, 'No. PO', 0, 0);
            $pdf->Cell(5, 5, ':', 0, 0);
            $pdf->Cell(40, 5, $po->no_nota, 0, 1);
            
            $pdf->Cell(100, 5, '', 0, 0);
            $pdf->Cell(25, 5, 'Tanggal', 0, 0);
            $pdf->Cell(5, 5, ':', 0, 0);
            $pdf->Cell(40, 5, date('Y-m-d', strtotime($po->tgl_masuk)), 0, 1);
            
            $pdf->Cell(100, 5, '', 0, 0);
            $pdf->Cell(25, 5, 'Oleh', 0, 0);
            $pdf->Cell(5, 5, ':', 0, 0);
            $pdf->Cell(40, 5, '', 0, 1);
            $pdf->Ln(5);

            // Table Header
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(35, 7, 'KODE', 1, 0, 'C');
            $pdf->Cell(85, 7, 'DESKRIPSI', 1, 0, 'C');
            $pdf->Cell(20, 7, 'JML', 1, 0, 'C');
            $pdf->Cell(30, 7, 'KETERANGAN', 1, 1, 'C');

            // Table Content
            $pdf->SetFont('helvetica', '', 10);
            foreach ($po_details as $item) {
                $pdf->Cell(35, 7, $item->kode ?? 'N/A', 1, 0, 'L');
                $pdf->Cell(85, 7, $item->deskripsi ?? 'N/A', 1, 0, 'L');
                $pdf->Cell(20, 7, $item->jml ?? '0', 1, 0, 'C');
                $pdf->Cell(30, 7, $item->keterangan ?? '', 1, 1, 'L');
            }

            // Footer
            $pdf->Ln(20);
            $pdf->Cell(85, 5, 'Pemesan', 0, 0, 'C');
            $pdf->Cell(85, 5, $kota . ', ' . date('d F Y', strtotime($po->tgl_masuk)), 0, 1, 'C');
            
            $pdf->Ln(20);
            $pdf->Cell(85, 5, $apt_apa ?: 'Pemesan', 0, 1, 'C');

            // Output PDF
            $pdf->Output('PO_' . $po->no_nota . '.pdf', 'I');
            exit;
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal mencetak PO: ' . $e->getMessage());
        }
    }
} 