<?php

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github : github.com/mikhaelfelian
 * Description : Controller for managing product variants (varian)
 * This file represents the Varian controller.
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\VarianModel;

class Varian extends BaseController
{
    protected $varianModel;
    protected $validation;

    public function __construct()
    {
        $this->varianModel = new VarianModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_varian') ?? 1;
        $perPage = 10;
        $keyword = $this->request->getVar('keyword');

        if ($keyword) {
            $this->varianModel->groupStart()
                ->like('nama', $keyword)
                ->orLike('kode', $keyword)
                ->orLike('keterangan', $keyword)
                ->groupEnd();
        }

        $data = [
            'title'         => 'Data Varian',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'varian'        => $this->varianModel->paginate($perPage, 'varian'),
            'pager'         => $this->varianModel->pager,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,
            'keyword'       => $keyword,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Varian</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/varian/index', $data);
    }

    public function create()
    {
        $data = [
            'title'         => 'Form Varian',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/varian') . '">Varian</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/varian/create', $data);
    }

    public function store()
    {
        $nama = $this->request->getPost('nama');
        $ket  = $this->request->getPost('keterangan');
        $status = $this->request->getPost('status');

        // Validation rules
        $rules = [
            'nama' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Nama varian harus diisi',
                    'max_length' => 'Nama varian maksimal 100 karakter'
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

        // Generate kode
        $kode = $this->varianModel->generateCode();

        // Generate data
        $data = [
            'kode'       => $kode,
            'nama'       => $nama,
            'keterangan' => $ket,
            'status'     => $status
        ];

        if ($this->varianModel->insert($data)) {
            return redirect()->to(base_url('master/varian'))
                ->with('success', 'Data varian berhasil ditambahkan');
        }

        return redirect()->back()
            ->with('error', 'Gagal menambahkan data varian')
            ->withInput();
    }

    public function edit($id)
    {
        $data = [
            'title'         => 'Form Varian',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/varian') . '">Varian</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];
        $data['varian'] = $this->varianModel->find($id);

        if (empty($data['varian'])) {
            return redirect()->to(base_url('master/varian'))
                ->with('error', 'Data varian tidak ditemukan');
        }

        return view($this->theme->getThemePath() . '/master/varian/edit', $data);
    }

    public function update($id)
    {
        // Validation rules
        $rules = [
            'nama' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Nama varian harus diisi',
                    'max_length' => 'Nama varian maksimal 100 karakter'
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
            'nama'       => $this->request->getPost('nama'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status'     => $this->request->getPost('status')
        ];

        if ($this->varianModel->update($id, $data)) {
            return redirect()->to(base_url('master/varian'))
                ->with('success', 'Data varian berhasil diubah!');
        }

        return redirect()->back()
            ->with('error', 'Gagal mengupdate data varian')
            ->withInput();
    }

    public function delete($id)
    {
        if ($this->varianModel->delete($id)) {
            return redirect()->to(base_url('master/varian'))
                ->with('success', 'Data varian berhasil dihapus');
        }

        return redirect()->back()
            ->with('error', 'Gagal menghapus data varian');
    }
} 