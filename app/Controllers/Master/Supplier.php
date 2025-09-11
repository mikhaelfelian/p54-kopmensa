<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * Supplier Controller
 * 
 * Controller for managing supplier data
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\SupplierModel;
use App\Models\PengaturanModel;
use App\Models\ItemModel;
use App\Models\KategoriModel;
use App\Models\MerkModel;
use App\Models\SatuanModel;

class Supplier extends BaseController
{
    protected $supplierModel;
    protected $validation;
    protected $pengaturan;
    protected $itemModel;
    protected $kategoriModel;
    protected $merkModel;
    protected $satuanModel;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->pengaturan = new PengaturanModel();
        $this->validation = \Config\Services::validation();
        $this->itemModel = new ItemModel();
        $this->kategoriModel = new KategoriModel();
        $this->merkModel = new MerkModel();
        $this->satuanModel = new SatuanModel();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_supplier') ?? 1;
        $perPage = $this->pengaturan->pagination_limit ?? 10;

        // Start with the model query
        $query = $this->supplierModel;

        // Only show non-deleted suppliers (active records)
        $query->where('status_hps', '0');

        // Filter by name/code/npwp
        $search = $this->request->getVar('search');
        if ($search) {
            $query->groupStart()
                ->like('nama', $search)
                ->orLike('kode', $search)
                ->orLike('npwp', $search)
                ->groupEnd();
        }

        // Filter by type
        $selectedTipe = $this->request->getVar('tipe');
        if ($selectedTipe !== null && $selectedTipe !== '') {
            $query->where('tipe', $selectedTipe);
        }

        // Get total records for pagination
        $total = $query->countAllResults(false);

        $data = [
            'title'          => 'Data Supplier',
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'suppliers'      => $query->paginate($perPage, 'supplier'),
            'pager'          => $this->supplierModel->pager,
            'currentPage'    => $currentPage,
            'perPage'        => $perPage,
            'total'          => $total,
            'search'         => $search,
            'selectedTipe'   => $selectedTipe,
            'selectedStatus' => null,  // Set to null since we're not using status filter
            'getTipeLabel'   => function($tipe) {
                return $this->supplierModel->getTipeLabel($tipe);
            },
            'getStatusLabel' => function($status) {
                return $this->supplierModel->getStatusLabel($status);
            },
            'breadcrumbs'    => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Supplier</li>
            ',
            'trashCount'     => $this->supplierModel->onlyDeleted()->countAllResults()
        ];

        return $this->view($this->theme->getThemePath() . '/master/supplier/index', $data);
    }

    /**
     * Display create form
     */
    public function create()
    {
        $data = [
            'title'       => 'Tambah Supplier',
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'validation'  => $this->validation,
            'kode'        => $this->supplierModel->generateKode(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/supplier') . '">Supplier</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/supplier/create', $data);
    }

    /**
     * Store new supplier data
     */
    public function store()
    {
        try {
            $data = [
                'kode'       => $this->supplierModel->generateKode(),
                'nama'       => $this->request->getPost('nama'),
                'npwp'       => $this->request->getPost('npwp'),
                'alamat'     => $this->request->getPost('alamat'),
                'rt'         => $this->request->getPost('rt'),
                'rw'         => $this->request->getPost('rw'),
                'kelurahan'  => $this->request->getPost('kelurahan'),
                'kecamatan'  => $this->request->getPost('kecamatan'),
                'kota'       => $this->request->getPost('kota'),
                'no_tlp'     => $this->request->getPost('no_tlp'),
                'no_hp'      => $this->request->getPost('no_hp'),
                'tipe'       => $this->request->getPost('tipe'),
                'status'     => '1',
                'status_hps' => '0'
            ];

            if (!$this->supplierModel->insert($data)) {
                throw new \RuntimeException('Gagal menyimpan data supplier');
            }

            return redirect()->to(base_url('master/supplier'))
                           ->with('success', 'Data supplier berhasil ditambahkan');

        } catch (\Exception $e) {
            log_message('error', '[Supplier::store] ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menyimpan data supplier');
        }
    }

    /**
     * Display edit form
     */
    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->to('master/supplier')
                           ->with('error', 'ID supplier tidak ditemukan');
        }

        $supplier = $this->supplierModel->find($id);
        if (!$supplier) {
            return redirect()->to('master/supplier')
                           ->with('error', 'Data supplier tidak ditemukan');
        }

        $data = [
            'title'       => 'Edit Supplier',
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'validation'  => $this->validation,
            'supplier'    => $supplier,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/supplier') . '">Supplier</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/supplier/edit', $data);
    }

    /**
     * Update supplier data
     */
    public function update($id = null)
    {
        if (!$id) {
            return redirect()->to('master/supplier')
                           ->with('error', 'ID supplier tidak ditemukan');
        }

        try {
            $data = [
                'nama'       => $this->request->getPost('nama'),
                'npwp'       => $this->request->getPost('npwp'),
                'alamat'     => $this->request->getPost('alamat'),
                'rt'         => $this->request->getPost('rt'),
                'rw'         => $this->request->getPost('rw'),
                'kelurahan'  => $this->request->getPost('kelurahan'),
                'kecamatan'  => $this->request->getPost('kecamatan'),
                'kota'       => $this->request->getPost('kota'),
                'no_tlp'     => $this->request->getPost('no_tlp'),
                'no_hp'      => $this->request->getPost('no_hp'),
                'tipe'       => $this->request->getPost('tipe')
            ];

            if (!$this->supplierModel->update($id, $data)) {
                throw new \RuntimeException('Gagal mengupdate data supplier');
            }

            return redirect()->to(base_url('master/supplier'))
                           ->with('success', 'Data supplier berhasil diupdate');

        } catch (\Exception $e) {
            log_message('error', '[Supplier::update] ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal mengupdate data supplier');
        }
    }

    /**
     * Display supplier details
     */
    public function detail($id = null)
    {
        if (!$id) {
            return redirect()->to('master/supplier')
                           ->with('error', 'ID supplier tidak ditemukan');
        }

        $supplier = $this->supplierModel->find($id);
        if (!$supplier) {
            return redirect()->to('master/supplier')
                           ->with('error', 'Data supplier tidak ditemukan');
        }

        $data = [
            'title'       => 'Detail Supplier',
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'supplier'    => $supplier,
            'getTipeLabel'   => function($tipe) {
                return $this->supplierModel->getTipeLabel($tipe);
            },
            'getStatusLabel' => function($status) {
                return $this->supplierModel->getStatusLabel($status);
            },
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/supplier') . '">Supplier</a></li>
                <li class="breadcrumb-item active">Detail</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/supplier/detail', $data);
    }

    /**
     * Delete supplier data
     */
    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->to('master/supplier')
                           ->with('error', 'ID supplier tidak ditemukan');
        }

        try {
            $supplier = $this->supplierModel->find($id);
            if (!$supplier) {
                throw new \RuntimeException('Data supplier tidak ditemukan');
            }

            // Soft delete by updating status_hps
            if (!$this->supplierModel->update($id, ['status_hps' => '1'])) {
                throw new \RuntimeException('Gagal menghapus data supplier');
            }

            return redirect()->to(base_url('master/supplier'))
                           ->with('success', 'Data supplier berhasil dihapus');

        } catch (\Exception $e) {
            log_message('error', '[Supplier::delete] ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Gagal menghapus data supplier');
        }
    }

    /**
     * Display list of trashed suppliers
     */
    public function trash()
    {
        $filters = [
            'status' => $this->request->getGet('status'),
            'tipe'   => $this->request->getGet('tipe'),
            'q'      => $this->request->getGet('q')
        ];

        $query = $this->supplierModel->onlyDeleted();

        // Apply filters
        if (!empty($filters['q'])) {
            $query->groupStart()
                ->like('nama', $filters['q'])
                ->orLike('kode', $filters['q'])
                ->orLike('npwp', $filters['q'])
                ->groupEnd();
        }

        if (!empty($filters['tipe'])) {
            $query->where('tipe', $filters['tipe']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        $data = [
            'title'         => 'Data Sampah Supplier',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'suppliers'     => $query->paginate(10, 'suppliers'),
            'pager'         => $this->supplierModel->pager,
            'selectedTipe'  => $filters['tipe'],
            'selectedStatus'=> $filters['status'],
            'search'        => $filters['q'],
            'trashCount'    => $this->supplierModel->onlyDeleted()->countAllResults(),
            'getTipeLabel'  => function($tipe) {
                return $this->supplierModel->getTipeLabel($tipe);
            },
            'getStatusLabel' => function($status) {
                return $this->supplierModel->getStatusLabel($status);
            }
        ];

        return $this->view($this->theme->getThemePath() . '/master/supplier/trash', $data);
    }

    /**
     * Display and manage items for a supplier
     */
    public function items($id = null)
    {
        if (!$id) {
            return redirect()->to('master/supplier')
                           ->with('error', 'ID supplier tidak ditemukan');
        }

        $supplier = $this->supplierModel->find($id);
        if (!$supplier) {
            return redirect()->to('master/supplier')
                           ->with('error', 'Data supplier tidak ditemukan');
        }

        // Get items associated with this supplier
        $keyword = $this->request->getVar('keyword') ?? '';
        $kategori = $this->request->getVar('kategori');
        $merk = $this->request->getVar('merk');
        $perPage = $this->request->getVar('per_page') ?? 20;

        // Build query for items
        $itemsQuery = $this->itemModel->select('
                tbl_m_item.*,
                tbl_m_kategori.kategori as kategori_nama,
                tbl_m_merk.merk as merk_nama,
                tbl_m_satuan.SatuanBesar as satuan_nama
            ')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->where('tbl_m_item.status_hps', '0');

        // Filter by supplier
        $itemsQuery->where('tbl_m_item.id_supplier', $id);

        // Apply filters
        if ($keyword) {
            $itemsQuery->groupStart()
                ->like('tbl_m_item.item', $keyword)
                ->orLike('tbl_m_item.kode', $keyword)
                ->orLike('tbl_m_item.barcode', $keyword)
                ->groupEnd();
        }

        if ($kategori) {
            $itemsQuery->where('tbl_m_item.id_kategori', $kategori);
        }

        if ($merk) {
            $itemsQuery->where('tbl_m_item.id_merk', $merk);
        }

        $items = $itemsQuery->paginate($perPage, 'items');

        // Get filter options
        $kategoriList = $this->kategoriModel->where('status', '1')->findAll();
        $merkList = $this->merkModel->where('status', '1')->findAll();

        $data = [
            'title' => 'Item Settings - ' . $supplier->nama,
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'supplier' => $supplier,
            'items' => $items,
            'pager' => $this->itemModel->pager,
            'keyword' => $keyword,
            'kategori' => $kategori,
            'merk' => $merk,
            'perPage' => $perPage,
            'kategoriList' => $kategoriList,
            'merkList' => $merkList,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/supplier') . '">Supplier</a></li>
                <li class="breadcrumb-item active">Item Settings</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/supplier/items', $data);
    }

    /**
     * Add new item for supplier
     */
    public function addItem($supplierId = null)
    {
        if (!$supplierId) {
            return redirect()->to('master/supplier')
                           ->with('error', 'ID supplier tidak ditemukan');
        }

        $supplier = $this->supplierModel->find($supplierId);
        if (!$supplier) {
            return redirect()->to('master/supplier')
                           ->with('error', 'Data supplier tidak ditemukan');
        }

        // Get filter options
        $kategoriList = $this->kategoriModel->where('status', '1')->findAll();
        $merkList = $this->merkModel->where('status', '1')->findAll();
        $satuanList = $this->satuanModel->findAll();

        $data = [
            'title' => 'Tambah Item - ' . $supplier->nama,
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'supplier' => $supplier,
            'validation' => $this->validation,
            'kategoriList' => $kategoriList,
            'merkList' => $merkList,
            'satuanList' => $satuanList,
            'kode' => $this->itemModel->generateKode(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/supplier') . '">Supplier</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/supplier/items/' . $supplierId) . '">Item Settings</a></li>
                <li class="breadcrumb-item active">Tambah Item</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/supplier/add_item', $data);
    }

    /**
     * Store new item for supplier
     */
    public function storeItem($supplierId = null)
    {
        if (!$supplierId) {
            return redirect()->to('master/supplier')
                           ->with('error', 'ID supplier tidak ditemukan');
        }

        $supplier = $this->supplierModel->find($supplierId);
        if (!$supplier) {
            return redirect()->to('master/supplier')
                           ->with('error', 'Data supplier tidak ditemukan');
        }

        // Validation rules
        $rules = [
            'item' => 'required|max_length[255]',
            'id_kategori' => 'required|integer',
            'id_merk' => 'required|integer',
            'id_satuan' => 'required|integer',
            'harga_beli' => 'required|numeric|greater_than_equal_to[0]',
            'harga_jual' => 'required|numeric|greater_than_equal_to[0]',
            'min_stok' => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        try {
            // Check for duplicate item name
            $existingItem = $this->itemModel->where('item', $this->request->getPost('item'))
                                          ->where('status_hps', '0')
                                          ->first();
            
            if ($existingItem) {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Item dengan nama tersebut sudah ada. Silakan gunakan nama yang berbeda.');
            }

            $data = [
                'kode' => $this->itemModel->generateKode(),
                'item' => $this->request->getPost('item'),
                'barcode' => $this->request->getPost('barcode'),
                'id_kategori' => $this->request->getPost('id_kategori'),
                'id_merk' => $this->request->getPost('id_merk'),
                'id_satuan' => $this->request->getPost('id_satuan'),
                'id_supplier' => $supplierId,
                'harga_beli' => $this->request->getPost('harga_beli'),
                'harga_jual' => $this->request->getPost('harga_jual'),
                'min_stok' => $this->request->getPost('min_stok'),
                'keterangan' => $this->request->getPost('keterangan'),
                'status' => '1',
                'status_hps' => '0'
            ];

            if (!$this->itemModel->insert($data)) {
                throw new \RuntimeException('Gagal menyimpan data item');
            }

            return redirect()->to(base_url('master/supplier/items/' . $supplierId))
                           ->with('success', 'Item berhasil ditambahkan ke supplier');

        } catch (\Exception $e) {
            log_message('error', '[Supplier::storeItem] ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menyimpan data item: ' . $e->getMessage());
        }
    }
} 