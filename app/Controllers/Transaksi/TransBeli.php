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
            'po_list'       => $this->transPOModel->where('status', '1')->findAll(), // Only processed POs
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

        $po_det = $this->transBeliPODetModel->getItemByPO($data['id_po']);

        // Save transaction
        try {
            $this->db->transStart();
            
            // Insert main transaction
            $this->transBeliModel->insert($data);
            $id = $this->transBeliModel->getInsertID();

            // Get PO items
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
} 