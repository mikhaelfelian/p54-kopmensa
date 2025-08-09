<?php

namespace App\Controllers\Gudang;

use App\Controllers\BaseController;
use App\Models\InputStokModel;
use App\Models\InputStokDetModel;
use App\Models\SupplierModel;
use App\Models\GudangModel;
use App\Models\KaryawanModel;
use App\Models\ItemModel;
use App\Models\SatuanModel;
use App\Models\ItemStokModel;
use App\Models\ItemHistModel;

class InputStok extends BaseController
{
    protected $inputStokModel;
    protected $inputStokDetModel;
    protected $supplierModel;
    protected $gudangModel;
    protected $karyawanModel;
    protected $itemModel;
    protected $satuanModel;
    protected $itemStokModel;
    protected $itemHistModel;
    protected $ionAuth;

    public function __construct()
    {
        parent::__construct();
        $this->inputStokModel = new InputStokModel();
        $this->inputStokDetModel = new InputStokDetModel();
        $this->supplierModel = new SupplierModel();
        $this->gudangModel = new GudangModel();
        $this->karyawanModel = new KaryawanModel();
        $this->itemModel = new ItemModel();
        $this->satuanModel = new SatuanModel();
        $this->itemStokModel = new ItemStokModel();
        $this->itemHistModel = new ItemHistModel();
    }

    public function index()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idSupplier = $this->request->getGet('id_supplier');
        $idGudang = $this->request->getGet('id_gudang');

        // Build query
        $builder = $this->inputStokModel->getWithRelations();

        // Apply filters
        if ($startDate && $endDate) {
            $builder->where('tbl_input_stok.tgl_terima >=', $startDate . ' 00:00:00')
                   ->where('tbl_input_stok.tgl_terima <=', $endDate . ' 23:59:59');
        }

        if ($idSupplier) {
            $builder->where('tbl_input_stok.id_supplier', $idSupplier);
        }

        if ($idGudang) {
            $builder->where('tbl_input_stok.id_gudang', $idGudang);
        }

        $inputStoks = $builder->findAll();

        // Get filter options
        $supplierList = $this->supplierModel->where('status', '1')->findAll();
        $gudangList = $this->gudangModel->where('status', '1')->findAll();

        $data = [
            'title' => 'Input Stok',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'inputStoks' => $inputStoks,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'idSupplier' => $idSupplier,
            'idGudang' => $idGudang,
            'supplierList' => $supplierList,
            'gudangList' => $gudangList,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Gudang</li>
                <li class="breadcrumb-item active">Input Stok</li>
            ',
        ];

        return view($this->theme->getThemePath() . '/gudang/input_stok/index', $data);
    }

    public function create()
    {
        $supplierList = $this->supplierModel->where('status', '1')->findAll();
        $gudangList = $this->gudangModel->where('status', '1')->findAll();
        $itemList = $this->itemModel->where('status_hps', '0')->findAll();
        $satuanList = $this->satuanModel->findAll();

        $data = [
            'title' => 'Tambah Input Stok',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'supplierList' => $supplierList,
            'gudangList' => $gudangList,
            'itemList' => $itemList,
            'satuanList' => $satuanList,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Gudang</li>
                <li class="breadcrumb-item"><a href="' . base_url('gudang/input_stok') . '">Input Stok</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            ',
        ];

        return view($this->theme->getThemePath() . '/gudang/input_stok/create', $data);
    }

    public function store()
    {
        $rules = [
            'tgl_terima' => 'required',
            'id_supplier' => 'required|integer',
            'id_gudang' => 'required|integer',
            'items' => 'required',
            'items.*.id_item' => 'required|integer',
            'items.*.id_satuan' => 'required|integer',
            'items.*.jml' => 'required|numeric|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Generate number automatically
            $noTerima = $this->inputStokModel->generateNoTerima();
            
            // Insert header
            $headerData = [
                'no_terima' => $noTerima,
                'tgl_terima' => $this->request->getPost('tgl_terima'),
                'id_supplier' => $this->request->getPost('id_supplier'),
                'id_gudang' => $this->request->getPost('id_gudang'),
                'id_penerima' => $this->ionAuth->user()->row()->id, // Auto-set to current user
                'keterangan' => $this->request->getPost('keterangan'),
                'status' => '1',
                'status_hps' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $inputStokId = $this->inputStokModel->insert($headerData);

            if (!$inputStokId) {
                throw new \Exception('Gagal menyimpan data input stok');
            }

            // Insert details and update stock
            $items = $this->request->getPost('items');
            foreach ($items as $item) {
                // Insert detail
                $detailData = [
                    'id_input_stok' => $inputStokId,
                    'id_item' => $item['id_item'],
                    'id_satuan' => $item['id_satuan'],
                    'jml' => $item['jml'],
                    'keterangan' => $item['keterangan'] ?? '',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->inputStokDetModel->insert($detailData);

                // Update stock in tbl_m_item_stok
                $existingStock = $this->itemStokModel
                    ->where('id_item', $item['id_item'])
                    ->where('id_gudang', $this->request->getPost('id_gudang'))
                    ->first();

                if ($existingStock) {
                    // Update existing stock
                    $newQuantity = $existingStock->jml + $item['jml'];
                    $this->itemStokModel->update($existingStock->id, [
                        'jml' => $newQuantity,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                } else {
                    // Create new stock record
                    $this->itemStokModel->insert([
                        'id_item' => $item['id_item'],
                        'id_gudang' => $this->request->getPost('id_gudang'),
                        'jml' => $item['jml'],
                        'status' => '1',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }

                // Log to item history
                $this->itemHistModel->insert([
                    'id_item' => $item['id_item'],
                    'id_gudang' => $this->request->getPost('id_gudang'),
                    'jml_masuk' => $item['jml'],
                    'jml_keluar' => 0,
                    'keterangan' => 'Input Stok - ' . $noTerima,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Transaksi gagal');
            }

            return redirect()->to('gudang/input_stok')->with('success', 'Input stok berhasil disimpan');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function detail($id)
    {
        $inputStok = $this->inputStokModel->getWithRelations($id);

        if (!$inputStok) {
            return redirect()->to('gudang/input_stok')->with('error', 'Data input stok tidak ditemukan');
        }

        $items = $this->inputStokDetModel->getByInputStokId($id);

        $data = [
            'title' => 'Detail Input Stok - ' . $inputStok->no_terima,
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'inputStok' => $inputStok,
            'items' => $items,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Gudang</li>
                <li class="breadcrumb-item"><a href="' . base_url('gudang/input_stok') . '">Input Stok</a></li>
                <li class="breadcrumb-item active">Detail</li>
            ',
        ];

        return view($this->theme->getThemePath() . '/gudang/input_stok/detail', $data);
    }

    public function edit($id)
    {
        $inputStok = $this->inputStokModel->find($id);

        if (!$inputStok) {
            return redirect()->to('gudang/input_stok')->with('error', 'Data input stok tidak ditemukan');
        }

        $items = $this->inputStokDetModel->getByInputStokId($id);
        $supplierList = $this->supplierModel->where('status', '1')->findAll();
        $gudangList = $this->gudangModel->where('status', '1')->findAll();
        $karyawanList = $this->karyawanModel->where('status', '0')->findAll();
        $itemList = $this->itemModel->where('status_hps', '0')->findAll();
        $satuanList = $this->satuanModel->findAll();

        $data = [
            'title' => 'Edit Input Stok - ' . $inputStok->no_terima,
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'inputStok' => $inputStok,
            'items' => $items,
            'supplierList' => $supplierList,
            'gudangList' => $gudangList,
            'karyawanList' => $karyawanList,
            'itemList' => $itemList,
            'satuanList' => $satuanList,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Gudang</li>
                <li class="breadcrumb-item"><a href="' . base_url('gudang/input_stok') . '">Input Stok</a></li>
                <li class="breadcrumb-item active">Edit</li>
            ',
        ];

        return view($this->theme->getThemePath() . '/gudang/input_stok/edit', $data);
    }

    public function update($id)
    {
        $inputStok = $this->inputStokModel->find($id);

        if (!$inputStok) {
            return redirect()->to('gudang/input_stok')->with('error', 'Data input stok tidak ditemukan');
        }

        $rules = [
            'no_terima' => 'required|max_length[50]',
            'tgl_terima' => 'required|valid_date',
            'id_supplier' => 'required|integer',
            'id_gudang' => 'required|integer',
            'id_penerima' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'no_terima' => $this->request->getPost('no_terima'),
            'tgl_terima' => $this->request->getPost('tgl_terima'),
            'id_supplier' => $this->request->getPost('id_supplier'),
            'id_gudang' => $this->request->getPost('id_gudang'),
            'id_penerima' => $this->request->getPost('id_penerima'),
            'keterangan' => $this->request->getPost('keterangan'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->inputStokModel->update($id, $data)) {
            return redirect()->to('gudang/input_stok')->with('success', 'Input stok berhasil diperbarui');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui input stok');
        }
    }

    public function delete($id)
    {
        $inputStok = $this->inputStokModel->find($id);

        if (!$inputStok) {
            return redirect()->to('gudang/input_stok')->with('error', 'Data input stok tidak ditemukan');
        }

        // Soft delete
        if ($this->inputStokModel->update($id, ['status_hps' => '1'])) {
            return redirect()->to('gudang/input_stok')->with('success', 'Input stok berhasil dihapus');
        } else {
            return redirect()->to('gudang/input_stok')->with('error', 'Gagal menghapus input stok');
        }
    }


}
