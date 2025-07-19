<?php
/**
 * Merk Controller
 * 
 * Controller for managing brands (merk)
 * Handles CRUD operations and other related functionalities
 * 
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @date      2025-01-12
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\MerkModel;

class Merk extends BaseController
{
    protected $merkModel;
    protected $validation;

    public function __construct()
    {
        $this->merkModel = new MerkModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_merk') ?? 1;
        $perPage = 10;
        $keyword = $this->request->getVar('keyword');

        if ($keyword) {
            $this->merkModel->groupStart()
                ->like('merk', $keyword)
                ->orLike('kode', $keyword)
                ->orLike('keterangan', $keyword)
                ->groupEnd();
        }

        $data = [
            'title'         => 'Data Merk',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'merk'          => $this->merkModel->paginate($perPage, 'merk'),
            'pager'         => $this->merkModel->pager,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,
            'keyword'       => $keyword,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Merk</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/merk/index', $data);
    }

    public function create()
    {
        $data = [
            'title'         => 'Form Merk',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/merk') . '">Merk</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/merk/create', $data);
    }

    public function store()
    {
        $merk   = $this->request->getPost('merk');
        $ket    = $this->request->getPost('keterangan');
        $status = $this->request->getPost('status');

        // Validation rules
        $rules = [
            'merk' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Merk harus diisi',
                    'max_length' => 'Merk maksimal 160 karakter'
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

        // Generate brand code
        $kode   = $this->merkModel->generateKode($merk);

        $data = [
            'kode'       => $kode,
            'merk'       => $merk,
            'keterangan' => $ket,
            'status'     => $status
        ];

        if ($this->merkModel->insert($data)) {
            return redirect()->to(base_url('master/merk'))
                ->with('success', 'Data merk berhasil ditambahkan');
        }

        return redirect()->to(base_url('master/merk'))
            ->with('error', 'Gagal menambahkan data merk')
            ->withInput();
    }

    public function edit($id)
    {
        $data = [
            'title'         => 'Form Merk',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/merk') . '">Merk</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        $data['merk'] = $this->merkModel->find($id);

        if (empty($data['merk'])) {
            return redirect()->to(base_url('master/merk'))
                ->with('error', 'Data merk tidak ditemukan');
        }

        return view($this->theme->getThemePath() . '/master/merk/edit', $data);
    }

    public function update($id)
    {
        // Validation rules
        $rules = [
            'merk' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Merk harus diisi',
                    'max_length' => 'Merk maksimal 160 karakter'
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
            'merk'       => $this->request->getPost('merk'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status'     => $this->request->getPost('status')
        ];

        if ($this->merkModel->update($id, $data)) {
            return redirect()->to(base_url('master/merk'))
                ->with('success', 'Data merk berhasil diubah!');
        }

        return redirect()->back()
            ->with('error', 'Gagal mengupdate data merk')
            ->withInput();
    }

    public function delete($id)
    {
        if ($this->merkModel->delete($id)) {
            return redirect()->to(base_url('master/merk'))
                ->with('success', 'Data merk berhasil dihapus');
        }

        return redirect()->back()
            ->with('error', 'Gagal menghapus data merk');
    }
} 