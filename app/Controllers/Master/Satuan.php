<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-13
 * 
 * Satuan Controller
 * 
 * Controller for managing measurement units (satuan)
 * Handles CRUD operations and other related functionalities
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\SatuanModel;
use App\Models\PengaturanModel;

class Satuan extends BaseController
{
    protected $satuanModel;
    protected $validation;
    protected $pengaturan;

    public function __construct()
    {
        $this->satuanModel = new SatuanModel();
        $this->pengaturan = new PengaturanModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_satuan') ?? 1;
        $perPage = 10;

        // Start with the model query
        $query = $this->satuanModel;

        // Filter by search term
        $search = $this->request->getVar('search');
        if ($search) {
            $query->groupStart()
                ->like('satuanKecil', $search)
                ->orLike('satuanBesar', $search)
                ->groupEnd();
        }

        // Filter by status
        $selectedStatus = $this->request->getVar('status');
        if ($selectedStatus !== null && $selectedStatus !== '') {
            $query->where('status', $selectedStatus);
        }

        $data = [
            'title'          => 'Data Satuan',
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'satuan'         => $query->paginate($perPage, 'satuan'),
            'pager'          => $this->satuanModel->pager,
            'currentPage'    => $currentPage,
            'perPage'        => $perPage,
            'search'         => $search,
            'selectedStatus' => $selectedStatus,
            'breadcrumbs'    => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Satuan</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/satuan/index', $data);
    }

    public function create()
    {
        $data = [
            'title'         => 'Tambah Satuan',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/satuan') . '">Satuan</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/satuan/create', $data);
    }

    public function store()
    {
        // Validation rules
        $rules = [
            env('security.tokenName') => [
                'rules' => 'required',
                'errors' => [
                    'required' => env('csrf.name') . ' harus diisi'
                ]
            ],
            'satuanKecil' => [
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required' => 'Satuan kecil harus diisi',
                    'max_length' => 'Satuan kecil maksimal 50 karakter'
                ]
            ],
            'satuanBesar' => [
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required' => 'Satuan besar harus diisi',
                    'max_length' => 'Satuan besar maksimal 50 karakter'
                ]
            ],
            'status' => [
                'rules' => 'required|in_list[0,1]',
                'errors' => [
                    'required' => 'Status harus dipilih',
                    'in_list' => 'Status tidak valid'
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
                'satuanKecil' => $this->request->getPost('satuanKecil'),
                'satuanBesar' => $this->request->getPost('satuanBesar'),
                'jml'         => $this->request->getPost('jml'),
                'status'      => $this->request->getPost('status')
            ];

            if (!$this->satuanModel->insert($data)) {
                throw new \Exception('Gagal menyimpan data satuan');
            }

            return redirect()->to(base_url('master/satuan'))
                ->with('success', 'Data satuan berhasil ditambahkan');

        } catch (\Exception $e) {
            log_message('error', '[Satuan::store] ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menyimpan data satuan');
        }
    }

    public function delete($id)
    {
        try {
            if (!$id || !is_numeric($id)) {
                throw new \Exception('ID satuan tidak valid');
            }

            if (!$this->satuanModel->delete($id)) {
                throw new \Exception('Gagal menghapus data satuan');
            }

            return redirect()->to(base_url('master/satuan'))
                ->with('success', 'Data satuan berhasil dihapus');

        } catch (\Exception $e) {
            log_message('error', '[Satuan::delete] ' . $e->getMessage());

            return redirect()->back()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menghapus data satuan');
        }
    }

    /**
     * Display edit form for satuan
     */
    public function edit($id)
    {
        $satuan = $this->satuanModel->find($id);
        if (!$satuan) {
            return redirect()->to('master/satuan')
                           ->with('error', 'Data satuan tidak ditemukan');
        }

        $data = [
            'title'       => 'Edit Satuan',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'validation'  => $this->validation,
            'satuan'      => $satuan,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/satuan') . '">Satuan</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/satuan/edit', $data);
    }

    /**
     * Update satuan data
     */
    public function update($id)
    {
        // Validation rules
        $rules = [
            env('security.tokenName') => [
                'rules' => 'required',
                'errors' => [
                    'required' => env('csrf.name') . ' harus diisi'
                ]
            ],
            'satuanKecil' => [
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required' => 'Satuan kecil harus diisi',
                    'max_length' => 'Satuan kecil maksimal 50 karakter'
                ]
            ],
            'satuanBesar' => [
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required' => 'Satuan besar harus diisi',
                    'max_length' => 'Satuan besar maksimal 50 karakter'
                ]
            ],
            'status' => [
                'rules' => 'required|in_list[0,1]',
                'errors' => [
                    'required' => 'Status harus dipilih',
                    'in_list' => 'Status tidak valid'
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
                'satuanKecil' => $this->request->getPost('satuanKecil'),
                'satuanBesar' => $this->request->getPost('satuanBesar'),
                'jml'         => $this->request->getPost('jml'),
                'status'      => $this->request->getPost('status')
            ];

            if (!$this->satuanModel->update($id, $data)) {
                throw new \Exception('Gagal mengupdate data satuan');
            }

            return redirect()->to(base_url('master/satuan'))
                           ->with('success', 'Data satuan berhasil diupdate');

        } catch (\Exception $e) {
            log_message('error', '[Satuan::update] ' . $e->getMessage());
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal mengupdate data satuan');
        }
    }
} 