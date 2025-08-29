<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PettyCategoryModel;
use CodeIgniter\HTTP\ResponseInterface;

class PettyCategoryController extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new PettyCategoryModel();
    }

    /**
     * Display category index page
     */
    public function index()
    {
        $data = [
            'title' => 'Kategori Petty Cash',
            'categories' => $this->categoryModel->findAll()
        ];

        return view('admin-lte-3/petty/category/index', $data);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Kategori Petty Cash'
        ];

        return view('admin-lte-3/petty/category/create', $data);
    }

    /**
     * Store new category
     */
    public function store()
    {
        if (!$this->validate($this->categoryModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'kode' => strtoupper($this->request->getPost('kode')),
            'nama' => $this->request->getPost('nama'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'status' => '1'
        ];

        if ($this->categoryModel->insert($data)) {
            return redirect()->to('petty/category')->with('success', 'Kategori berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan kategori');
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            return redirect()->to('petty/category')->with('error', 'Data tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Kategori Petty Cash',
            'category' => $category
        ];

        return view('admin-lte-3/petty/category/edit', $data);
    }

    /**
     * Update category
     */
    public function update($id)
    {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            return redirect()->to('petty/category')->with('error', 'Data tidak ditemukan');
        }

        if (!$this->validate($this->categoryModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'kode' => strtoupper($this->request->getPost('kode')),
            'nama' => $this->request->getPost('nama'),
            'deskripsi' => $this->request->getPost('deskripsi')
        ];

        if ($this->categoryModel->update($id, $data)) {
            return redirect()->to('petty/category')->with('success', 'Kategori berhasil diupdate');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate kategori');
        }
    }

    /**
     * Delete category
     */
    public function delete($id)
    {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            return redirect()->to('petty/category')->with('error', 'Data tidak ditemukan');
        }

        // Check if category is used in transactions
        if ($this->categoryModel->isCategoryUsed($id)) {
            return redirect()->to('petty/category')->with('error', 'Kategori tidak dapat dihapus karena masih digunakan dalam transaksi');
        }

        if ($this->categoryModel->delete($id)) {
            return redirect()->to('petty/category')->with('success', 'Kategori berhasil dihapus');
        } else {
            return redirect()->to('petty/category')->with('error', 'Gagal menghapus kategori');
        }
    }

    /**
     * Toggle category status
     */
    public function toggleStatus($id)
    {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            return redirect()->to('petty/category')->with('error', 'Data tidak ditemukan');
        }

        $newStatus = $category->status == '1' ? '0' : '1';
        
        if ($this->categoryModel->update($id, ['status' => $newStatus])) {
            $statusText = $newStatus == '1' ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->to('petty/category')->with('success', "Kategori berhasil {$statusText}");
        } else {
            return redirect()->to('petty/category')->with('error', 'Gagal mengubah status kategori');
        }
    }

    /**
     * Get categories for AJAX request
     */
    public function getCategories()
    {
        $categories = $this->categoryModel->getActiveCategories();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $categories
        ]);
    }
}
