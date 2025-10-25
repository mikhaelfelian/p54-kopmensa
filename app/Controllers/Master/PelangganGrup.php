<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\PelangganGrupModel;
use App\Models\PelangganModel;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-08-23
 * Github : github.com/mikhaelfelian
 * description : Controller for managing customer group data
 * This file represents the Controller for Customer Group management.
 */
class PelangganGrup extends BaseController
{
    protected $pelangganGrupModel;
    protected $pelangganModel;
    protected $pengaturan;
    protected $ionAuth;
    protected $validation;
    protected $db;

    public function __construct()
    {
        $this->pelangganGrupModel = new PelangganGrupModel();
        $this->pelangganModel = new PelangganModel();
        $this->validation = \Config\Services::validation();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $curr_page = $this->request->getVar('page_grup') ?? 1;
        $per_page = 10;
        $query = $this->request->getVar('keyword') ?? '';
        $status = $this->request->getVar('status') ?? '';

        // Get trash count
        $trashCount = (clone $this->pelangganGrupModel)->where('status', '0')->countAllResults();

        // Filter active records for main list
        $this->pelangganGrupModel->where('tbl_m_pelanggan_grup.status', '1');

        if ($query) {
            $this->pelangganGrupModel->groupStart()
                ->like('grup', $query)
                ->orLike('deskripsi', $query)
                ->groupEnd();
        }

        if ($status !== null && $status !== '') {
            $this->pelangganGrupModel->where('tbl_m_pelanggan_grup.status', $status);
        }

        $data = [
            'title' => 'Data Grup Pelanggan',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'grup_list' => $this->pelangganGrupModel->getGroupsWithMemberCount($per_page, $query, $curr_page),
            'pager' => $this->pelangganGrupModel->pager,
            'currentPage' => $curr_page,
            'perPage' => $per_page,
            'keyword' => $query,
            'status' => $status,
            'trashCount' => $trashCount,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Grup Pelanggan</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/pelanggan_grup/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Form Grup Pelanggan',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'validation' => $this->validation,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/customer-group') . '">Grup Pelanggan</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/pelanggan_grup/create', $data);
    }

    public function store()
    {
        $grup = $this->request->getVar('grup');
        $deskripsi = $this->request->getVar('deskripsi');
        $status = $this->request->getVar('status') ?? '1';

        // Validation rules
        $rules = [
            csrf_token() => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'CSRF token tidak valid'
                ]
            ],
            'grup' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Nama grup harus diisi',
                    'max_length' => 'Nama grup maksimal 100 karakter'
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
                'grup' => $grup,
                'deskripsi' => $deskripsi,
                'status' => $status
            ];

            if (!$this->pelangganGrupModel->insert($data)) {
                throw new \Exception('Gagal menambahkan data grup pelanggan');
            }

            return redirect()->to(base_url('master/customer-group'))
                ->with('success', 'Data grup pelanggan berhasil ditambahkan');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal menambahkan data grup pelanggan');
        }
    }

    public function edit($id)
    {
        $grup = $this->pelangganGrupModel->find($id);
        if (!$grup) {
            return redirect()->to(base_url('master/customer-group'))
                ->with('error', 'Data grup pelanggan tidak ditemukan');
        }

        $data = [
            'title' => 'Form Grup Pelanggan',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'validation' => $this->validation,
            'grup' => $grup,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/customer-group') . '">Grup Pelanggan</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/pelanggan_grup/edit', $data);
    }

    public function update($id)
    {
        $grup = $this->request->getVar('grup');
        $deskripsi = $this->request->getVar('deskripsi');
        $status = $this->request->getVar('status') ?? '1';

        // Validation rules
        $rules = [
            csrf_token() => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'CSRF token tidak valid'
                ]
            ],
            'grup' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Nama grup harus diisi',
                    'max_length' => 'Nama grup maksimal 100 karakter'
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
                'grup' => $grup,
                'deskripsi' => $deskripsi,
                'status' => $status
            ];

            if (!$this->pelangganGrupModel->update($id, $data)) {
                throw new \Exception('Gagal mengubah data grup pelanggan');
            }

            return redirect()->to(base_url('master/customer-group'))
                ->with('success', 'Data grup pelanggan berhasil diubah');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', ENVIRONMENT === 'development' ? $e->getMessage() : 'Gagal mengubah data grup pelanggan');
        }
    }

    public function delete($id)
    {
        $data = [
            'status' => '0',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->pelangganGrupModel->update($id, $data)) {
            return redirect()->to(base_url('master/customer-group'))
                ->with('success', 'Data grup pelanggan berhasil dihapus');
        }

        return redirect()->back()
            ->with('error', 'Gagal menghapus data grup pelanggan');
    }

    public function bulk_delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $itemIds = $this->request->getPost('item_ids');

        if (empty($itemIds) || !is_array($itemIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data yang dipilih untuk dihapus'
            ]);
        }

        try {
            $deletedCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($itemIds as $id) {
                try {
                    $data = [
                        'status' => '0',
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    
                    if ($this->pelangganGrupModel->update($id, $data)) {
                        $deletedCount++;
                    } else {
                        $failedCount++;
                        $errors[] = "Gagal menghapus data ID: {$id}";
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = "Error menghapus data ID {$id}: " . $e->getMessage();
                }
            }

            $message = "Berhasil menghapus {$deletedCount} data";
            if ($failedCount > 0) {
                $message .= ", {$failedCount} data gagal dihapus";
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount,
                'failed_count' => $failedCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            log_message('error', '[Bulk Delete] ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ]);
        }
    }
}