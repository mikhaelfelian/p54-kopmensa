<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use App\Models\KategoriModel;
use App\Models\MerkModel;
use App\Models\SatuanModel;

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
    protected $kategoriModel;
    protected $merkModel;
    protected $pengaturan;
    protected $ionAuth;
    protected $validation;
    protected $db;

    public function __construct()
    {
        $this->itemModel     = new ItemModel();
        $this->itemStokModel = new ItemStokModel();
        $this->gudangModel   = new GudangModel();
        $this->outletModel   = new OutletModel();
        $this->kategoriModel = new KategoriModel();
        $this->merkModel     = new MerkModel();
        $this->satuanModel   = new SatuanModel();
        $this->validation    = \Config\Services::validation();
        $this->db            = \Config\Database::connect();
    }

    public function index()
    {
        $curr_page  = $this->request->getVar('page_items') ?? 1;
        $per_page   = 10;
        $kat        = $this->request->getVar('kategori');
        $stok       = $this->request->getVar('stok');
        $query      = $this->request->getVar('keyword') ?? '';

        // Get trash count (use a clone so it doesn't affect the main query)
        $trashCount = (clone $this->itemModel)->where('status_hps', '1')->countAllResults();

        // Now filter only active items for the main list
        $this->itemModel->where('tbl_m_item.status_hps', '0');

        if ($kat) {
            $this->itemModel->where('tbl_m_item.id_kategori', $kat);
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

        $data = [
            'title'         => 'Data Item',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'items'         => $this->itemModel->getItemsWithRelations($per_page, $query, $curr_page, $kat, $stok),
            'pager'         => $this->itemModel->pager,
            'currentPage'   => $curr_page,
            'perPage'       => $per_page,
            'keyword'       => $query,
            'kat'           => $kat,
            'stok'          => $stok,
            'trashCount'    => $trashCount,
            'kategori'      => $this->kategoriModel->findAll(),
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
                'kode'        => $this->itemModel->generateKode($id_kategori, $tipe),
                'barcode'     => $barcode,
                'item'        => $item,
                'deskripsi'   => $deskripsi,
                'id_kategori' => $id_kategori,
                'id_merk'     => $id_merk,
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

            // Insert item stock records for all outlets
            $outletModel = new \App\Models\OutletModel();
            
            // Get all active outlets
            $outletData = $outletModel->where('status', '1')->findAll();
            
            foreach ($outletData as $outlet) {
                $itemStokModel->insert([
                    'id_item'    => $newItemId,
                    'id_outlet'  => $outlet->id,
                    'jml'        => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'status'     => $outlet->status,
                ]);
            }

            $tempFotoPath = $this->request->getVar('foto');

            if (!empty($tempFotoPath) && strpos($tempFotoPath, 'file/item/temp/') === 0) {
                $fileName = basename($tempFotoPath);
                $finalDir = 'file/item/' . $newItemId . '/';
                $finalPath = $finalDir . $fileName;
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

    public function edit($id)
    {
        $data = [
            'title'         => 'Form Item',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'item'          => $this->itemModel->find($id),
            'kategori'      => $this->kategoriModel->findAll(),
            'merk'          => $this->merkModel->findAll(),
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

        return view($this->theme->getThemePath() . '/master/item/edit', $data);
    }

    public function edit_upload($id)
    {
        $data = [
            'title'         => 'Form Item',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'item'          => $this->itemModel->find($id),
            'kategori'      => $this->kategoriModel->findAll(),
            'merk'          => $this->merkModel->findAll(),
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
            return redirect()->back()
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
} 