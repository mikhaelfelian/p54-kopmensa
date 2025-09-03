<?php

namespace App\Controllers\Transaksi;

use App\Controllers\BaseController;
use App\Models\PettyCategoryModel;
use CodeIgniter\HTTP\ResponseInterface;

class PettyCategory extends BaseController
{
    protected $categoryModel;
    protected $ionAuth;
    protected $pengaturan;

    public function __construct()
    {
        $this->categoryModel = new PettyCategoryModel();
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
        $this->pengaturan = new \App\Models\PengaturanModel();
    }

    public function index()
    {
        $categories = $this->categoryModel->findAll();

        $data = [
            'title' => 'Kategori Petty Cash',
            'categories' => $categories,
            'user' => $this->ionAuth->user()->row(),
            'Pengaturan' => $this->pengaturan
        ];

        return view('admin-lte-3/petty/category/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Kategori Petty Cash',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row()
        ];

        return view('admin-lte-3/petty/category/create', $data);
    }

    public function store()
    {
        // Validation rules
        $rules = [
            'kode' => 'required',
            'nama' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('transaksi/petty/category/create')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Prepare data using variables for each input
        $kode      = strtoupper(trim($this->request->getPost('kode')));
        $nama      = trim($this->request->getPost('nama'));
        $deskripsi = trim($this->request->getPost('deskripsi'));
        $status    = '1';

        $data = [
            'kode'      => $kode,
            'nama'      => $nama,
            'deskripsi' => $deskripsi,
            'status'    => $status,
        ];

        // Save to database
        if ($this->categoryModel->insert($data)) {
            return redirect()->to('transaksi/petty/category')
                ->with('success', 'Kategori berhasil ditambahkan');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan kategori');
        }
    }

    public function edit($id)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) {
            return redirect()->to('transaksi/petty/category')
                ->with('error', 'Data kategori tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Kategori Petty Cash',
            'category' => $category,
            'Pengaturan' => $this->pengaturan
        ];

        return view('admin-lte-3/petty/category/edit', $data);
    }

    public function update($id)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) {
            return redirect()->to('transaksi/petty/category')
                ->with('error', 'Data kategori tidak ditemukan');
        }

        // Validation rules
        $rules = [
            'kode' => 'required|min_length[2]|max_length[10]|is_unique[tbl_m_petty_category.kode,id,' . $id . ']',
            'nama' => 'required|min_length[3]|max_length[100]',
            'deskripsi' => 'permit_empty|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Prepare data
        $data = [
            'kode' => strtoupper($this->request->getPost('kode')),
            'nama' => $this->request->getPost('nama'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Update database
        if ($this->categoryModel->update($id, $data)) {
            return redirect()->to('transaksi/petty/category')
                ->with('success', 'Kategori berhasil diupdate');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate kategori');
        }
    }

    public function delete($id)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) {
            return redirect()->to('transaksi/petty/category')
                ->with('error', 'Data kategori tidak ditemukan');
        }

        // Check if category is used in transactions
        if ($this->categoryModel->isCategoryUsed($id)) {
            return redirect()->to('transaksi/petty/category')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan dalam transaksi');
        }

        if ($this->categoryModel->delete($id)) {
            return redirect()->to('transaksi/petty/category')
                ->with('success', 'Kategori berhasil dihapus');
        } else {
            return redirect()->to('transaksi/petty/category')
                ->with('error', 'Gagal menghapus kategori');
        }
    }

    public function toggleStatus($id)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) {
            return redirect()->to('transaksi/petty/category')
                ->with('error', 'Data kategori tidak ditemukan');
        }

        $newStatus = $category->status == '1' ? '0' : '1';
        $statusText = $newStatus == '1' ? 'diaktifkan' : 'dinonaktifkan';

        $data = [
            'status' => $newStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->categoryModel->update($id, $data)) {
            return redirect()->to('transaksi/petty/category')
                ->with('success', 'Kategori berhasil ' . $statusText);
        } else {
            return redirect()->to('transaksi/petty/category')
                ->with('error', 'Gagal mengubah status kategori');
        }
    }

    public function getCategories()
    {
        $categories = $this->categoryModel->getActiveCategories();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $categories
        ]);
    }
}
