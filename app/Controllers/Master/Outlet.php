<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\OutletModel;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-17
 * Github : github.com/mikhaelfelian
 * description : Controller for managing outlet data
 * This file represents the Controller for Outlet management.
 */
class Outlet extends BaseController
{
    protected $outletModel;
    protected $pengaturan;
    protected $ionAuth;
    protected $db;
    protected $validation;

    public function __construct()
    {
        $this->outletModel = new OutletModel();
        $this->validation = \Config\Services::validation();
    }

    private function trashCount()
    {
        return $this->outletModel->where('status_hps', '1')->countAllResults();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_outlet') ?? 1;
        $perPage = 10;
        $keyword = $this->request->getVar('keyword');

        $this->outletModel->where('status_hps', '0');

        if ($keyword) {
            $this->outletModel->groupStart()
                ->like('nama', $keyword)
                ->orLike('kode', $keyword)
                ->orLike('deskripsi', $keyword)
                ->groupEnd();
        }

        $data = [
            'title'         => 'Data Outlet',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'outlet'        => $this->outletModel->paginate($perPage, 'outlet'),
            'pager'         => $this->outletModel->pager,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,
            'keyword'       => $keyword,
            'trashCount'    => $this->trashCount(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Outlet</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/outlet/index', $data);
    }

    public function create()
    {
        $data = [
            'title'         => 'Form Outlet',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/outlet') . '">Outlet</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/outlet/create', $data);
    }

    public function store()
    {
        $nama      = $this->request->getVar('nama');
        $deskripsi = $this->request->getVar('deskripsi');
        $status    = $this->request->getVar('status') ?? 1;
        $id_user   = $this->ionAuth->user()->row()->id ?? 0;

        // Validation rules
        $rules = [
            'nama' => [
                'rules' => 'required|max_length[128]',
                'errors' => [
                    'required' => 'Nama outlet harus diisi',
                    'max_length' => 'Nama outlet maksimal 128 karakter'
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
            'kode'       => $this->outletModel->generateKode(),
            'nama'       => $nama,
            'deskripsi'  => $deskripsi,
            'status'     => $status,
            'id_user'    => $id_user
        ];

        if ($this->outletModel->insert($data)) {
            return redirect()->to(base_url('master/outlet'))
                ->with('success', 'Data outlet berhasil ditambahkan');
        }

        return redirect()->back()
            ->with('error', 'Gagal menambahkan data outlet')
            ->withInput();
    }

    public function edit($id)
    {
        $data = [
            'title'         => 'Form Outlet',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'outlet'        => $this->outletModel->find($id),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/outlet') . '">Outlet</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        if (empty($data['outlet'])) {
            return redirect()->to(base_url('master/outlet'))
                ->with('error', 'Data outlet tidak ditemukan');
        }

        return view($this->theme->getThemePath() . '/master/outlet/edit', $data);
    }

    public function update($id)
    {
        $nama      = $this->request->getVar('nama');
        $deskripsi = $this->request->getVar('deskripsi');
        $status    = $this->request->getVar('status') ?? 1;

        // Validation rules
        $rules = [
            'nama' => [
                'rules' => 'required|max_length[128]',
                'errors' => [
                    'required' => 'Nama outlet harus diisi',
                    'max_length' => 'Nama outlet maksimal 128 karakter'
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
            'nama'       => $nama,
            'deskripsi'  => $deskripsi,
            'status'     => $status
        ];

        if ($this->outletModel->update($id, $data)) {
            return redirect()->to(base_url('master/outlet'))
                ->with('success', 'Data outlet berhasil diubah');
        }

        return redirect()->back()
            ->with('error', 'Gagal mengubah data outlet')
            ->withInput();
    }

    public function delete($id)
    {
        $data = [
            'status_hps' => '1',
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        if ($this->outletModel->update($id, $data)) {
            return redirect()->to(base_url('master/outlet'))
                ->with('success', 'Data outlet berhasil dihapus');
        }

        return redirect()->back()
            ->with('error', 'Gagal menghapus data outlet');
    }

    public function delete_permanent($id)
    {
        if ($this->outletModel->delete($id, true)) {
            return redirect()->to(base_url('master/outlet/trash'))
                ->with('success', 'Data outlet berhasil dihapus permanen');
        }

        return redirect()->back()
            ->with('error', 'Gagal menghapus permanen data outlet');
    }

    public function trash()
    {
        $currentPage = $this->request->getVar('page_outlet') ?? 1;
        $perPage = 10;
        $keyword = $this->request->getVar('keyword');

        $this->outletModel->where('status_hps', '1');

        if ($keyword) {
            $this->outletModel->groupStart()
                ->like('nama', $keyword)
                ->orLike('kode', $keyword)
                ->orLike('deskripsi', $keyword)
                ->groupEnd();
        }

        $data = [
            'title'         => 'Data Outlet Terhapus',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'outlet'        => $this->outletModel->paginate($perPage, 'outlet'),
            'pager'         => $this->outletModel->pager,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,
            'keyword'       => $keyword,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/outlet') . '">Outlet</a></li>
                <li class="breadcrumb-item active">Tempat Sampah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/outlet/trash', $data);
    }

    public function restore($id)
    {
        $data = [
            'status_hps' => '0',
            'deleted_at' => null
        ];

        if ($this->outletModel->update($id, $data)) {
            return redirect()->to(base_url('master/outlet/trash'))
                ->with('success', 'Data outlet berhasil dikembalikan');
        }

        return redirect()->back()
            ->with('error', 'Gagal mengembalikan data outlet');
    }
} 