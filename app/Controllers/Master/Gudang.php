<?php
/**
 * Gudang Controller
 * 
 * Controller for managing warehouses (gudang)
 * Handles CRUD operations and other related functionalities
 * 
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @date      2025-01-12
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\GudangModel;

class Gudang extends BaseController
{
    protected $gudangModel;
    protected $validation;

    public function __construct()
    {
        $this->gudangModel = new GudangModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_gudang') ?? 1;
        $perPage = 10;
        $keyword = $this->request->getVar('keyword');

        $query = $this->gudangModel;

        if ($keyword) {
            $query->groupStart()
                ->like('gudang', $keyword)
                ->orLike('kode', $keyword)
                ->orLike('keterangan', $keyword)
                ->groupEnd();
        }

        $data = [
            'title'         => 'Data Gudang',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'gudang'        => $query->paginate($perPage, 'gudang'),
            'pager'         => $this->gudangModel->pager,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,
            'keyword'       => $keyword,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Gudang</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/gudang/index', $data);
    }

    public function create()
    {
        $data = [
            'title'         => 'Form Gudang',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/gudang') . '">Gudang</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/gudang/create', $data);
    }

    public function store()
    {
        // Validation rules
        $rules = [
            'gudang' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Nama gudang harus diisi',
                    'max_length' => 'Nama gudang maksimal 160 karakter'
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
            'kode'       => $this->gudangModel->generateKode(),
            'gudang'     => $this->request->getPost('gudang'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status'     => $this->request->getPost('status'),
            'status_gd'  => $this->request->getPost('status_gd')
        ];

        if ($this->gudangModel->insert($data)) {
            return redirect()->to(base_url('master/gudang'))
                ->with('success', 'Data gudang berhasil ditambahkan');
        }

        return redirect()->back()
            ->with('error', 'Gagal menambahkan data gudang')
            ->withInput();
    }

    public function edit($id)
    {
        $data = [
            'title'         => 'Form Gudang',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/gudang') . '">Gudang</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        $data['gudang'] = $this->gudangModel->find($id);

        if (empty($data['gudang'])) {
            return redirect()->to(base_url('master/gudang'))
                ->with('error', 'Data gudang tidak ditemukan');
        }

        return view($this->theme->getThemePath() . '/master/gudang/edit', $data);
    }

    public function update($id)
    {
        // Validation rules
        $rules = [
            'gudang' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Nama gudang harus diisi',
                    'max_length' => 'Nama gudang maksimal 160 karakter'
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
            'gudang'     => $this->request->getPost('gudang'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status'     => $this->request->getPost('status'),
            'status_gd'  => $this->request->getPost('status_gd')
        ];

        if ($this->gudangModel->update($id, $data)) {
            return redirect()->to(base_url('master/gudang'))
                ->with('success', 'Data gudang berhasil diubah');
        }

        return redirect()->back()
            ->with('error', 'Gagal mengupdate data gudang')
            ->withInput();
    }

    public function delete($id)
    {
        if ($this->gudangModel->delete($id)) {
            return redirect()->to(base_url('master/gudang'))
                ->with('success', 'Data gudang berhasil dihapus');
        }

        return redirect()->back()
            ->with('error', 'Gagal menghapus data gudang');
    }
} 