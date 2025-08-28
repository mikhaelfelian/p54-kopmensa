<?php

namespace App\Controllers\Transaksi;

use App\Controllers\BaseController;
use App\Models\PettyCategoryModel;

class PettyCategoryController extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new PettyCategoryModel();
    }

    public function index()
    {
        $data = array_merge($this->data, [
            'title' => 'Petty Cash Categories',
            'categories' => $this->categoryModel->getCategoriesWithUsage()
        ]);
        
        return view('admin-lte-3/petty_category/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'name' => 'required|max_length[100]',
                'is_active' => 'required|in_list[0,1]'
            ];

            if ($this->validate($rules)) {
                $data = [
                    'name' => $this->request->getPost('name'),
                    'is_active' => $this->request->getPost('is_active')
                ];

                if ($this->categoryModel->insert($data)) {
                    session()->setFlashdata('success', 'Kategori berhasil ditambahkan');
                    return redirect()->to('/transaksi/petty-category');
                } else {
                    session()->setFlashdata('error', 'Gagal menambahkan kategori');
                }
            } else {
                session()->setFlashdata('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
            }
        }

        $data = array_merge($this->data, [
            'title' => 'Tambah Kategori Petty Cash'
        ]);
        
        return view('admin-lte-3/petty_category/create', $data);
    }

    public function edit($id)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) {
            session()->setFlashdata('error', 'Kategori tidak ditemukan');
            return redirect()->to('/transaksi/petty-category');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'name' => 'required|max_length[100]',
                'is_active' => 'required|in_list[0,1]'
            ];

            if ($this->validate($rules)) {
                $data = [
                    'name' => $this->request->getPost('name'),
                    'is_active' => $this->request->getPost('is_active')
                ];

                if ($this->categoryModel->update($id, $data)) {
                    session()->setFlashdata('success', 'Kategori berhasil diupdate');
                    return redirect()->to('/transaksi/petty-category');
                } else {
                    session()->setFlashdata('error', 'Gagal mengupdate kategori');
                }
            } else {
                session()->setFlashdata('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
            }
        }

        $data = array_merge($this->data, [
            'title' => 'Edit Kategori Petty Cash',
            'category' => $category
        ]);
        
        return view('admin-lte-3/petty_category/edit', $data);
    }

    public function toggleStatus($id)
    {
        if ($this->categoryModel->toggleStatus($id)) {
            session()->setFlashdata('success', 'Status kategori berhasil diubah');
        } else {
            session()->setFlashdata('error', 'Gagal mengubah status kategori');
        }
        
        return redirect()->to('/transaksi/petty-category');
    }

    public function delete($id)
    {
        if (!$this->categoryModel->canDelete($id)) {
            session()->setFlashdata('error', 'Kategori tidak dapat dihapus karena masih digunakan');
            return redirect()->to('/transaksi/petty-category');
        }

        if ($this->categoryModel->delete($id)) {
            session()->setFlashdata('success', 'Kategori berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus kategori');
        }
        
        return redirect()->to('/transaksi/petty-category');
    }

    public function search()
    {
        $keyword = $this->request->getGet('keyword');
        $categories = $this->categoryModel->searchCategories($keyword);
        
        $data = array_merge($this->data, [
            'title' => 'Search Categories',
            'categories' => $categories,
            'keyword' => $keyword
        ]);
        
        return view('admin-lte-3/petty_category/search', $data);
    }

    public function getCategoriesForDropdown()
    {
        $categories = $this->categoryModel->getCategoriesForDropdown();
        
        return $this->response->setJSON($categories);
    }
}
