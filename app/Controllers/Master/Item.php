<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use App\Models\KategoriModel;
use App\Models\MerkModel;

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

    public function __construct()
    {
        $this->itemModel = new ItemModel();
        $this->kategoriModel = new KategoriModel();
        $this->merkModel = new MerkModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_items') ?? 1;
        $perPage = 10;
        $keyword = $this->request->getVar('keyword');

        // Get trash count (use a clone so it doesn't affect the main query)
        $trashCount = (clone $this->itemModel)->where('status_hps', '1')->countAllResults();

        // Now filter only active items for the main list
        $this->itemModel->where('status_hps', '0');

        if ($keyword) {
            $this->itemModel->groupStart()
                ->like('item', $keyword)
                ->orLike('kode', $keyword)
                ->orLike('barcode', $keyword)
                ->orLike('deskripsi', $keyword)
                ->groupEnd();
        }

        $data = [
            'title'         => 'Data Item',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'items'         => $this->itemModel->paginate($perPage, 'items'),
            'pager'         => $this->itemModel->pager,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,
            'keyword'       => $keyword,
            'trashCount'    => $trashCount,
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
        $id_user    = $this->ionAuth->user()->row()->id ?? 0;
        $foto       = $this->request->getVar('foto') ?? null;

        // Validation rules
        $rules = [
            'item' => [
                'rules' => 'required|max_length[128]',
                'errors' => [
                    'required' => 'Nama item harus diisi',
                    'max_length' => 'Nama item maksimal 128 karakter'
                ]
            ],
            env('security.tokenName', 'csrf_test_name') => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'CSRF token tidak valid'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validasi gagal');
        }

        $data = [
            'kode'        => $this->itemModel->generateKode(),
            'barcode'     => $barcode,
            'item'        => $item,
            'deskripsi'   => $deskripsi,
            'id_kategori' => $id_kategori,
            'id_merk'     => $id_merk,
            'jml_min'     => $jml_min,
            'harga_beli'  => $harga_beli,
            'harga_jual'  => $harga_jual,
            'foto'        => $foto,
            'tipe'        => $tipe,
            'status'      => $status,
            'id_user'     => $id_user
        ];

        if ($this->itemModel->insert($data)) {
            $item_id = $this->itemModel->getInsertID();
            // If there is a foto uploaded in temp, move it to the new item folder
            if ($foto && file_exists(FCPATH . 'file/item/temp/' . $foto)) {
                $newDir = FCPATH . 'file/item/' . $item_id . '/';
                if (!is_dir($newDir)) {
                    mkdir($newDir, 0755, true);
                }
                rename(FCPATH . 'file/item/temp/' . $foto, $newDir . $foto);
                // Optionally, remove temp folder if empty
            }
            return redirect()->to(base_url('master/item'))
                ->with('success', 'Data item berhasil ditambahkan');
        }

        return redirect()->back()
            ->with('error', 'Gagal menambahkan data item')
            ->withInput();
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

    public function update($id)
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
        $foto       = $this->request->getVar('foto') ?? null;

        // Validation rules
        $rules = [
            'item' => [
                'rules' => 'required|max_length[128]',
                'errors' => [
                    'required' => 'Nama item harus diisi',
                    'max_length' => 'Nama item maksimal 128 karakter'
                ]
            ],
            env('security.tokenName', 'csrf_test_name') => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'CSRF token tidak valid'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validasi gagal');
        }

        $data = [
            'barcode'     => $barcode,
            'item'        => $item,
            'deskripsi'   => $deskripsi,
            'id_kategori' => $id_kategori,
            'id_merk'     => $id_merk,
            'jml_min'     => $jml_min,
            'harga_beli'  => $harga_beli,
            'harga_jual'  => $harga_jual,
            'foto'        => $foto,
            'tipe'        => $tipe,
            'status'      => $status
        ];

        if ($this->itemModel->update($id, $data)) {
            return redirect()->to(base_url('master/item'))
                ->with('success', 'Data item berhasil diubah');
        }

        return redirect()->back()
            ->with('error', 'Gagal mengubah data item')
            ->withInput();
    }

    public function upload_image()
    {
        $file = $this->request->getFile('file');
        $item_id = $this->request->getVar('item_id');

        if (!$file->isValid() || $file->hasMoved()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File tidak valid'
            ]);
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tipe file tidak diizinkan. Hanya JPG, PNG, dan GIF yang diizinkan.'
            ]);
        }

        // Validate file size (max 2MB)
        if ($file->getSize() > 2 * 1024 * 1024) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ukuran file terlalu besar. Maksimal 2MB.'
            ]);
        }

        // Generate unique filename
        $newName = $file->getRandomName();

        // Determine upload path - use public/file/item/ directory
        if ($item_id) {
            $uploadPath = FCPATH . 'file/item/' . $item_id . '/';
            $urlPath = base_url('file/item/' . $item_id . '/' . $newName);
        } else {
            $uploadPath = FCPATH . 'file/item/temp/';
            $urlPath = base_url('file/item/temp/' . $newName);
        }
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if ($file->move($uploadPath, $newName)) {
            return $this->response->setJSON([
                'success' => true,
                'filename' => $newName,
                'url' => $urlPath
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal mengupload file'
        ]);
    }

    public function delete_image()
    {
        $filename = $this->request->getVar('filename');
        
        if ($filename) {
            $filePath = FCPATH . 'uploads/items/' . $filename;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'File berhasil dihapus'
        ]);
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