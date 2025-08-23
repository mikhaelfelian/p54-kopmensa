<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use App\Models\ItemStokModel;
use App\Models\ItemVarianModel;
use App\Models\GudangModel;
use App\Models\OutletModel;
use App\Models\KategoriModel;
use App\Models\MerkModel;
use App\Models\SatuanModel;
use App\Models\SupplierModel;
use App\Models\VarianModel;
use App\Models\PelangganGrupModel;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-18
 * Github : github.com/mikhaelfelian
 * description : Controller for managing item/product data
 * This file represents the Controller for Items management.
 */
class Item extends BaseController
{
    protected $itemModel;
    protected $itemVarianModel;
    protected $kategoriModel;
    protected $merkModel;
    protected $supplierModel;
    protected $itemHargaModel;
    protected $pengaturan;
    protected $ionAuth;
    protected $validation;
    protected $varianModel;
    protected $pelangganGrupModel;
    protected $db;

    public function __construct()
    {
        $this->itemModel        = new ItemModel();
        $this->itemStokModel    = new ItemStokModel();
        $this->itemVarianModel  = new ItemVarianModel();
        $this->gudangModel      = new GudangModel();
        $this->outletModel      = new OutletModel();
        $this->kategoriModel    = new KategoriModel();
        $this->merkModel        = new MerkModel();
        $this->supplierModel    = new SupplierModel();
        $this->satuanModel      = new SatuanModel();
        $this->itemHargaModel   = new \App\Models\ItemHargaModel();
        $this->varianModel      = new VarianModel();
        $this->pelangganGrupModel = new PelangganGrupModel();
        $this->validation       = \Config\Services::validation();
        $this->db               = \Config\Database::connect();
    }

    public function index()
    {
        $curr_page  = $this->request->getVar('page_items') ?? 1;
        $per_page   = 10;
        $kat        = $this->request->getVar('kategori');
        $merk       = $this->request->getVar('merk');
        $stok       = $this->request->getVar('stok');
        $supplier   = $this->request->getVar('supplier');
        $query      = $this->request->getVar('keyword') ?? '';
        
        // Min stock filter
        $min_stok_operator = $this->request->getVar('min_stok_operator') ?? '';
        $min_stok_value = $this->request->getVar('min_stok_value') ?? '';
        
        // Harga Beli filter
        $harga_beli_operator = $this->request->getVar('harga_beli_operator') ?? '';
        $harga_beli_value = $this->request->getVar('harga_beli_value') ?? '';
        
        // Harga Jual filter
        $harga_jual_operator = $this->request->getVar('harga_jual_operator') ?? '';
        $harga_jual_value = $this->request->getVar('harga_jual_value') ?? '';

        // Get trash count (use a clone so it doesn't affect the main query)
        $trashCount = (clone $this->itemModel)->where('status_hps', '1')->countAllResults();

        // Now filter only active items for the main list
        $this->itemModel->where('tbl_m_item.status_hps', '0');

        if ($kat) {
            $this->itemModel->where('tbl_m_item.id_kategori', $kat);
        }
        if ($merk) {
            $this->itemModel->where('tbl_m_item.id_merk', $merk);
        }
        if ($stok !== null && $stok !== '') {
            $this->itemModel->where('tbl_m_item.status_stok', $stok);
        }
        if ($query) {
            $this->itemModel->groupStart()
                ->like('tbl_m_item.item', $query)
                ->orLike('tbl_m_item.kode', $query)
                ->orLike('tbl_m_item.barcode', $query)
                ->orLike('tbl_m_item.deskripsi', $query)
                ->groupEnd();
        }
        
        // Apply min stock filter
        if ($min_stok_operator && $min_stok_value !== '') {
            $this->itemModel->where("tbl_m_item.jml_min {$min_stok_operator}", $min_stok_value);
        }
        
        // Apply harga beli filter
        if ($harga_beli_operator && $harga_beli_value !== '') {
            $this->itemModel->where("tbl_m_item.harga_beli {$harga_beli_operator}", format_angka_db($harga_beli_value));
        }
        
        // Apply harga jual filter
        if ($harga_jual_operator && $harga_jual_value !== '') {
            $this->itemModel->where("tbl_m_item.harga_jual {$harga_jual_operator}", format_angka_db($harga_jual_value));
        }

        $data = [
            'title'         => 'Data Item',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'items'         => $this->itemModel->getItemsWithRelations($per_page, $query, $curr_page, $kat, $stok, $supplier),
            'pager'         => $this->itemModel->pager,
            'currentPage'   => $curr_page,
            'perPage'       => $per_page,
            'keyword'       => $query,
            'kat'           => $kat,
            'merk'          => $merk,
            'stok'          => $stok,
            'supplier'      => $supplier,
            'min_stok_operator' => $min_stok_operator,
            'min_stok_value' => $min_stok_value,
            'harga_beli_operator' => $harga_beli_operator,
            'harga_beli_value' => $harga_beli_value,
            'harga_jual_operator' => $harga_jual_operator,
            'harga_jual_value' => $harga_jual_value,
            'trashCount'    => $trashCount,
            'kategori'      => $this->kategoriModel->findAll(),
            'merk_list'     => $this->merkModel->findAll(),
            'supplier_list' => $this->supplierModel->findAll(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Item</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/item/index', $data);
    }

    public function create()
    {
        $data = [
            'title'         => 'Form Item',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'kategori'      => $this->kategoriModel->findAll(),
            'merk'          => $this->merkModel->findAll(),
            'supplier'      => $this->supplierModel->findAll(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/item') . '">Item</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/item/create', $data);
    }

    public function store()
    {
        $item       = $this->request->getVar('item');
        $barcode    = $this->request->getVar('barcode');
        $deskripsi  = $this->request->getVar('deskripsi');
        $id_kategori = $this->request->getVar('id_kategori');
        $id_merk    = $this->request->getVar('id_merk');
        $id_supplier = $this->request->getVar('id_supplier');
        $jml_min    = $this->request->getVar('jml_min') ?? 0;
        $harga_beli = $this->request->getVar('harga_beli') ?? 0;
        $harga_jual = $this->request->getVar('harga_jual') ?? 0;
        $tipe       = $this->request->getVar('tipe') ?? '1';
        $status     = $this->request->getVar('status') ?? '1';
        $status_stok = $this->request->getVar('status_stok') ?? '0';
        $id_user    = $this->ionAuth->user()->row()->id ?? 0;
        $foto       = $this->request->getVar('foto') ?? null;

        // Validation rules
        $rules = [
            env('security.tokenName', 'csrf_test_name') => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'CSRF token tidak valid'
                ]
            ],
            'item' => [
                'rules' => 'required|max_length[128]',
                'errors' => [
                    'required' => 'Nama item harus diisi',
                    'max_length' => 'Nama item maksimal 128 karakter'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validasi gagal');
        }
        
        try {
            $data = [
                'id_supplier' => $id_supplier,
                'kode'        => $this->itemModel->generateKode($id_kategori, $tipe),
                'barcode'     => $barcode,
                'item'        => $item,
                'deskripsi'   => $deskripsi,
                'id_kategori' => $id_kategori,
                'id_merk'     => $id_merk,
                'id_supplier' => $id_supplier,
                'jml_min'     => $jml_min,
                'harga_beli'  => format_angka_db($harga_beli),
                'harga_jual'  => format_angka_db($harga_jual),
                'tipe'        => $tipe,
                'status'      => $status,
                'status_stok' => $status_stok,
                'id_user'     => $id_user,
                'foto'        => null
            ];

            $this->db->transStart();
            $this->itemModel->insert($data);

            // Get the newly inserted item ID
            $newItemId = $this->itemModel->getInsertID();

            // Insert item stock records for all warehouses (gudang)
            $gudangModel = new \App\Models\GudangModel();
            $itemStokModel = new \App\Models\ItemStokModel();
            
            // Get all active warehouses
            $gudangData = $gudangModel->where('status', '1')->findAll();
            
            foreach ($gudangData as $gudang) {
                $itemStokModel->insert([
                    'id_item'    => $newItemId,
                    'id_gudang'  => $gudang->id,
                    'jml'        => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'status'     => $gudang->status,
                ]);
            }

            $tempFotoPath = $this->request->getVar('foto');

            if (!empty($tempFotoPath) && strpos($tempFotoPath, 'file/item/temp/') === 0) {
                $fileName      = basename($tempFotoPath);
                $finalDir      = 'file/item/' . $newItemId . '/';
                $finalPath     = $finalDir . $fileName;
                $finalFullPath = FCPATH . $finalDir;
                
                if (!is_dir($finalFullPath)) {
                    mkdir($finalFullPath, 0777, true);
                }
                if (file_exists(FCPATH . $tempFotoPath) && rename(FCPATH . $tempFotoPath, FCPATH . $finalPath)) {
                    $this->itemModel->update($newItemId, ['foto' => $finalPath]);
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal menambahkan data item karena kegagalan transaksi.');
            }
            return redirect()->to(base_url('master/item/edit/' . $newItemId))->with('success', 'Data item berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
    public function store_price($item_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed', 'csrfHash' => csrf_hash()]);
        }

        $prices = $this->request->getPost('prices');
        if (!is_array($prices) || empty($prices)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No price data received.', 'csrfHash' => csrf_hash()]);
        }

        $itemHargaModel = new \App\Models\ItemHargaModel();
        $itemHargaModel->where('id_item', $item_id)->delete();

        foreach ($prices as $row) {
            $itemHargaModel->insert([
                'id_item'    => $item_id,
                'nama'       => $row['nama'] ?? '',
                'jml_min'    => $row['jml_min'] ?? 1,
                'harga'      => format_angka_db($row['harga'] ?? 0),
                'keterangan' => $row['keterangan'] ?? null,
            ]);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Pricing rules berhasil disimpan!', 'csrfHash' => csrf_hash()]);
    }
    public function edit($id)
    {
        try {
            // Check if user is logged in
            if (!$this->ionAuth->loggedIn()) {
                return redirect()->to('/auth/login');
            }

            // Get user data safely
            $user = $this->ionAuth->user()->row();
            if (!$user) {
                return redirect()->to('/auth/login')->with('error', 'User session invalid');
            }

            // Get item data safely
            $item = $this->itemModel->getItemWithRelations($id);
            if (!$item) {
                return redirect()->to(base_url('master/item'))
                    ->with('error', 'Data item tidak ditemukan');
            }

            // Get other data safely
            $kategori = $this->kategoriModel->findAll();
            $merk = $this->merkModel->findAll();
            $supplier = $this->supplierModel->findAll();
            $satuan = $this->satuanModel->findAll();
            $item_harga_list = $this->itemHargaModel->getPricesByItemId($id);

            // Get active variants
            $active_variants = $this->varianModel->getActiveVariants();

            // Get customer groups for pricing rules
            $customer_groups = $this->pelangganGrupModel->getUniqueGroupNames();

            $data = [
                'title'         => 'Form Item',
                'Pengaturan'    => $this->pengaturan,
                'user'          => $user,
                'validation'    => $this->validation,
                'item'          => $item,
                'kategori'      => $kategori,
                'merk'          => $merk,
                'supplier'      => $supplier,
                'satuan'        => $satuan,
                'item_harga_list' => $item_harga_list,
                'sql_varian' => $active_variants,
                'customer_groups' => $customer_groups,
                'kode_varian'     => $this->itemVarianModel->generateKode(),
                'breadcrumbs'   => '
                    <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                    <li class="breadcrumb-item">Master</li>
                    <li class="breadcrumb-item"><a href="' . base_url('master/item') . '">Item</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                '
            ];

            return view($this->theme->getThemePath() . '/master/item/edit', $data);
        } catch (\Exception $e) {
            log_message('error', 'Error in Item::edit: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return redirect()->to(base_url('master/item'))
                ->with('error', 'Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

    public function edit_upload($id)
    {
        $data = [
            'title'         => 'Form Item',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'item'          => $this->itemModel->getItemWithRelations($id),
            'kategori'      => $this->kategoriModel->findAll(),
            'merk'          => $this->merkModel->findAll(),
            'supplier'      => $this->supplierModel->findAll(),
            'satuan'        => $this->satuanModel->findAll(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/item') . '">Item</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        if (empty($data['item'])) {
            return redirect()->to(base_url('master/item'))
                ->with('error', 'Data item tidak ditemukan');
        }

        return view($this->theme->getThemePath() . '/master/item/edit_upload', $data);
    }

    public function update($id)
    {
        $id_kategori    = $this->request->getVar('id_kategori') ?? 0;
        $id_merk        = $this->request->getVar('id_merk') ?? 0;
        $id_supplier    = $this->request->getVar('id_supplier') ?? 0;
        $id_satuan      = $this->request->getVar('satuan') ?? 0;
        $barcode        = $this->request->getVar('barcode');
        $item           = $this->request->getVar('item');
        $deskripsi      = $this->request->getVar('deskripsi');
        $jml_min        = $this->request->getVar('jml_min') ?? 0;
        $harga_beli     = $this->request->getVar('harga_beli') ?? 0;
        $harga_jual     = $this->request->getVar('harga_jual') ?? 0;
        $tipe           = $this->request->getVar('tipe') ?? '1';
        $status         = $this->request->getVar('status') ?? '1';
        $status_stok    = $this->request->getVar('status_stok') ?? '0';

        // Validation rules
        $rules = [
            csrf_token() => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'CSRF token tidak valid'
                ]
            ],
            'item' => [
                'rules' => 'required|max_length[128]',
                'errors' => [
                    'required' => 'Nama item harus diisi',
                    'max_length' => 'Nama item maksimal 128 karakter'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation_errors', $this->validator->getErrors())
                ->with('error', 'Validasi gagal. Silakan periksa kembali input Anda.');
        }

        try {
            $data = [
                'id_kategori' => $id_kategori,
                'id_merk'     => $id_merk,
                'id_supplier' => $id_supplier,
                'id_satuan'   => $id_satuan,
                'barcode'     => $barcode,
                'item'        => $item,
                'deskripsi'   => $deskripsi,
                'jml_min'     => $jml_min,
                'harga_beli'  => format_angka_db($harga_beli),
                'harga_jual'  => format_angka_db($harga_jual),
                'tipe'        => $tipe,
                'status'      => $status,
                'status_stok' => $status_stok,
            ];

            if (!$this->itemModel->update($id, $data)) {
                throw new \Exception('Gagal mengubah data item');
            }

            return redirect()->to(base_url('master/item'))
                ->with('success', 'Data item berhasil diubah');

        } catch (\Exception $e) {            
            return redirect()->to(base_url('master/item/edit/' . $id))
                ->withInput()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal mengubah data item');
        }
    }

    public function upload_image()
    {
        // CSRF validation
        if (!$this->validate([
            csrf_token() => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'CSRF token tidak valid'
                ]
            ]
        ])) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'CSRF token tidak valid'
            ]);
        }

        $file       = $this->request->getFile('file');
        $item_id    = $this->request->getVar('item_id');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName    = $file->getRandomName();
            $uploadDir  = $item_id ? '../public/file/item/' . $item_id . '/' : '../public/file/item/temp/';
            $uploadPath = FCPATH . $uploadDir;
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            if ($file->move($uploadPath, $newName)) {
                $relativePath = $uploadDir . $newName;
                if ($item_id) {
                    $currentItem = $this->itemModel->find($item_id);
                    if ($currentItem && !empty($currentItem->foto) && file_exists(realpath($currentItem->foto))) {
                        unlink(realpath($currentItem->foto));
                    }
                    $this->itemModel->update($item_id, ['foto' => $relativePath]);
                }
                return $this->response->setJSON(['success' => true, 'filename' => $relativePath]);
            }
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengunggah file.']);
    }

    public function delete_image()
    {
        $filename = $this->request->getVar('filename');
        $item_id = $this->request->getVar('item_id');
        if ($filename) {
            $filePath = FCPATH . $filename;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            if ($item_id) {
                $this->itemModel->update($item_id, ['foto' => null]);
            }
        }
        return $this->response->setJSON(['success' => true]);
    }

    public function trash()
    {
        $currentPage = $this->request->getVar('page_items') ?? 1;
        $perPage = 10;
        $keyword = $this->request->getVar('keyword');

        $this->itemModel->where('status_hps', '1');

        if ($keyword) {
            $this->itemModel->groupStart()
                ->like('item', $keyword)
                ->orLike('kode', $keyword)
                ->orLike('barcode', $keyword)
                ->orLike('deskripsi', $keyword)
                ->groupEnd();
        }

        $data = [
            'title'         => 'Data Item Terhapus',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'items'         => $this->itemModel->paginate($perPage, 'items'),
            'pager'         => $this->itemModel->pager,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,
            'keyword'       => $keyword,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/item') . '">Item</a></li>
                <li class="breadcrumb-item active">Tempat Sampah</li>
            ',
            'theme'         => $this->theme,
        ];

        return view($this->theme->getThemePath() . '/master/item/trash', $data);
    }

    public function delete($id)
    {
        $data = [
            'status_hps' => '1',
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        if ($this->itemModel->update($id, $data)) {
            return redirect()->to(base_url('master/item'))
                ->with('success', 'Data item berhasil dihapus');
        }

        return redirect()->back()
            ->with('error', 'Gagal menghapus data item');
    }

    public function restore($id)
    {
        $data = [
            'status_hps' => '0',
            'deleted_at' => null
        ];

        if ($this->itemModel->update($id, $data)) {
            return redirect()->to(base_url('master/item/trash'))
                ->with('success', 'Data item berhasil dikembalikan');
        }

        return redirect()->back()
            ->with('error', 'Gagal mengembalikan data item');
    }

    public function delete_permanent($id)
    {
        if ($this->itemModel->delete($id, true)) {
            return redirect()->to(base_url('master/item/trash'))
                ->with('success', 'Data item berhasil dihapus permanen');
        }

        return redirect()->back()
            ->with('error', 'Gagal menghapus permanen data item');
    }

    public function delete_price($price_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed', 'csrfHash' => csrf_hash()]);
        }

        $itemHargaModel = new \App\Models\ItemHargaModel();
        $deleted = $itemHargaModel->delete($price_id);

        if ($deleted) {
            return $this->response->setJSON(['success' => true, 'message' => 'Harga berhasil dihapus!', 'csrfHash' => csrf_hash()]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus harga.', 'csrfHash' => csrf_hash()]);
        }
    }

    public function bulk_delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        $itemIds = $this->request->getVar('item_ids');
        
        if (empty($itemIds) || !is_array($itemIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada item yang dipilih']);
        }

        try {
            $this->db->transStart();
            
            $data = [
                'status_hps' => '1',
                'deleted_at' => date('Y-m-d H:i:s')
            ];

            $successCount = 0;
            foreach ($itemIds as $id) {
                if ($this->itemModel->update($id, $data)) {
                    $successCount++;
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus item']);
            }

            return $this->response->setJSON([
                'success' => true, 
                'message' => "Berhasil menghapus {$successCount} item ke arsip",
                'csrfHash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menghapus item'
            ]);
        }
    }

    public function export_to_excel()
    {
        // Get filter parameters
        $kat = $this->request->getVar('kategori');
        $merk = $this->request->getVar('merk');
        $stok = $this->request->getVar('stok');
        $query = $this->request->getVar('keyword') ?? '';
        
        // Min stock filter
        $min_stok_operator = $this->request->getVar('min_stok_operator') ?? '';
        $min_stok_value = $this->request->getVar('min_stok_value') ?? '';
        
        // Harga Beli filter
        $harga_beli_operator = $this->request->getVar('harga_beli_operator') ?? '';
        $harga_beli_value = $this->request->getVar('harga_beli_value') ?? '';
        
        // Harga Jual filter
        $harga_jual_operator = $this->request->getVar('harga_jual_operator') ?? '';
        $harga_jual_value = $this->request->getVar('harga_jual_value') ?? '';

        // Apply filters
        $this->itemModel->where('tbl_m_item.status_hps', '0');

        if ($kat) {
            $this->itemModel->where('tbl_m_item.id_kategori', $kat);
        }
        if ($merk) {
            $this->itemModel->where('tbl_m_item.id_merk', $merk);
        }
        if ($stok !== null && $stok !== '') {
            $this->itemModel->where('tbl_m_item.status_stok', $stok);
        }
        if ($query) {
            $this->itemModel->groupStart()
                ->like('tbl_m_item.item', $query)
                ->orLike('tbl_m_item.kode', $query)
                ->orLike('tbl_m_item.barcode', $query)
                ->orLike('tbl_m_item.deskripsi', $query)
                ->groupEnd();
        }
        
        // Apply min stock filter
        if ($min_stok_operator && $min_stok_value !== '') {
            $this->itemModel->where("tbl_m_item.jml_min {$min_stok_operator}", $min_stok_value);
        }
        
        // Apply harga beli filter
        if ($harga_beli_operator && $harga_beli_value !== '') {
            $this->itemModel->where("tbl_m_item.harga_beli {$harga_beli_operator}", format_angka_db($harga_beli_value));
        }
        
        // Apply harga jual filter
        if ($harga_jual_operator && $harga_jual_value !== '') {
            $this->itemModel->where("tbl_m_item.harga_jual {$harga_jual_operator}", format_angka_db($harga_jual_value));
        }

        // Get all filtered data (no pagination)
        $items = $this->itemModel->select('tbl_m_item.*, tbl_m_kategori.kategori, tbl_m_merk.merk')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->orderBy('tbl_m_item.id', 'DESC')
            ->findAll();

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'DATA ITEM');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set headers
        $headers = [
            'No', 'Kode', 'Barcode', 'Nama Item', 'Kategori', 'Merk', 'Deskripsi', 
            'Stok Min', 'Harga Beli', 'Harga Jual', 'Status Stok', 'Status Item'
        ];

        $col = 'A';
        $row = 3;
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $col++;
        }

        // Add data
        $row = 4;
        $no = 1;
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item->kode);
            $sheet->setCellValue('C' . $row, $item->barcode);
            $sheet->setCellValue('D' . $row, $item->item);
            $sheet->setCellValue('E' . $row, $item->kategori);
            $sheet->setCellValue('F' . $row, $item->merk);
            $sheet->setCellValue('G' . $row, $item->deskripsi);
            $sheet->setCellValue('H' . $row, $item->jml_min);
            $sheet->setCellValue('I' . $row, format_angka($item->harga_beli));
            $sheet->setCellValue('J' . $row, format_angka($item->harga_jual));
            $sheet->setCellValue('K' . $row, $item->status_stok == '1' ? 'Stockable' : 'Non Stockable');
            $sheet->setCellValue('L' . $row, $item->status == '1' ? 'Aktif' : 'Non Aktif');
            
            $row++;
            $no++;
        }

        // Auto size columns
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A3:L' . ($row - 1))->applyFromArray($styleArray);

        // Set filename
        $filename = 'Data_Item_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Create Excel writer
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function store_variant($item_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        $variants = $this->request->getVar('variants');
        
        if (empty($variants) || !is_array($variants)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada varian yang dikirim']);
        }

        try {
            $this->db->transStart();

            // Use models from constructor
            $item       = $this->itemModel;
            $itemVarian = $this->itemVarianModel;
            
            // Insert new variants
            foreach ($variants as $variant) {
                $itemRow    = $item->where('id', $item_id)->first();
                $harga_dsr  = isset($variant['harga_dasar']) ? format_angka_db($variant['harga_dasar']) : 0;
                $harga_jual = $itemRow->harga_jual + $harga_dsr;
                
                // Prepare data for saving
                $data = [
                    'id_item'        => $item_id,
                    'id_item_harga'  => isset($variant['id_item_harga']) ? $variant['id_item_harga'] : 0,
                    'kode'           => $itemVarian->generateKode(),
                    'varian'         => $variant['nama'] ?? '',
                    'harga_beli'     => isset($variant['harga_beli']) ? format_angka_db($variant['harga_beli']) : 0,
                    'harga_dasar'    => $harga_dsr,
                    'harga_jual'     => $harga_jual, // fallback, can be updated later
                    'barcode'        => $variant['barcode'] ?? null,
                    'status'         => isset($variant['status']) ? $variant['status'] : '1'
                ];

                // If updating existing variant, include its id
                if (!empty($variant['id']) && is_numeric($variant['id'])) {
                    $data['id'] = $variant['id'];
                }

                if (!$itemVarian->save($data)) {
                    throw new \Exception('Failed to save variant: ' . json_encode($itemVarian->errors()));
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan varian']);
            }

            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Varian berhasil disimpan. [' .$variant['nama'].' - '. format_angka($itemRow->harga_jual).']',
                'csrfHash' => csrf_hash()
            ]);

        } catch (\Exception $e) {           
            return $this->response->setJSON([
                'success' => false, 
                'message' => ENVIRONMENT === 'development' ? $e->getMessage().$item_id : 'Gagal menyimpan varian'
            ]);
        }
    }

    public function get_variants($item_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        try {
            $itemVarianModel = new \App\Models\ItemVarianModel();
            $variants = $itemVarianModel->getVariantsWithPrice($item_id);

            return $this->response->setJSON([
                'success' => true,
                'variants' => $variants
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal mengambil data varian'
            ]);
        }
    }

    public function delete_variant($variant_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        try {
            $itemVarianModel = new \App\Models\ItemVarianModel();
            $deleted = $itemVarianModel->delete($variant_id);

            if ($deleted) {
                return $this->response->setJSON([
                    'success' => true, 
                    'message' => 'Varian berhasil dihapus',
                    'csrfHash' => csrf_hash()
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Gagal menghapus varian'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menghapus varian'
            ]);
        }
    }

    /**
     * Get pricing rules for checkout
     */
    public function get_pricing_rules($item_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        try {
            $customerGroup = $this->request->getVar('customer_group');
            $quantity = $this->request->getVar('quantity') ?? 1;

            $pricingRules = $this->itemHargaModel->getPricingRulesForCheckout($item_id, $customerGroup, $quantity);
            $bestPrice = $this->itemHargaModel->getBestPrice($item_id, $customerGroup, $quantity);

            return $this->response->setJSON([
                'success' => true,
                'pricing_rules' => $pricingRules,
                'best_price' => $bestPrice,
                'customer_group' => $customerGroup,
                'quantity' => $quantity
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal mengambil aturan harga'
            ]);
        }
    }
}