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
        $curr_page  = $this->request->getVar('page_merk') ?? 1;
        $per_page   = 10;
        $query      = $this->request->getVar('keyword') ?? '';

        // Apply search filter if keyword exists
        if ($query) {
            $this->merkModel->groupStart()
                ->like('merk', $query)
                ->orLike('kode', $query)
                ->orLike('keterangan', $query)
                ->groupEnd();
        }

        $data = [
            'title'         => 'Data Merk',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'merk'          => $this->merkModel->paginate($per_page, 'merk'),
            'pager'         => $this->merkModel->pager,
            'currentPage'   => $curr_page,
            'perPage'       => $per_page,
            'keyword'       => $query,
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
        $status = $this->request->getPost('status') ?? '1'; // Default to active if not provided

        // Validation rules
        $rules = [
            'merk' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Merk harus diisi',
                    'max_length' => 'Merk maksimal 160 karakter'
                ]
            ],
            'status' => [
                'rules' => 'in_list[0,1]',
                'errors' => [
                    'in_list' => 'Status harus 0 atau 1'
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
            'status'     => $status,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            if ($this->merkModel->insert($data)) {
                return redirect()->to(base_url('master/merk'))
                    ->with('success', 'Data merk berhasil ditambahkan');
            } else {
                // Get the last error from the model
                $errors = $this->merkModel->errors();
                $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Gagal menambahkan data merk';
                
                return redirect()->to(base_url('master/merk'))
                    ->with('error', $errorMessage)
                    ->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->to(base_url('master/merk'))
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
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