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
        try {
            // Use archive to set status_hps='1' and deleted_at
            $now = date('Y-m-d H:i:s');
            $this->db->table('tbl_m_pelanggan_grup')
                ->where('id', $id)
                ->set(['status_hps' => '1', 'deleted_at' => $now])
                ->update();

            return redirect()->to(base_url('master/customer-group'))
                ->with('success', 'Data grup pelanggan berhasil diarsipkan');

        } catch (\Exception $e) {
            log_message('error', '[PelangganGrup::delete] ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal mengarsipkan data grup pelanggan');
        }
    }

    public function bulk_delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request',
                'csrfHash' => csrf_hash()
            ]);
        }

        $itemIds = $this->request->getPost('item_ids');

        if (empty($itemIds) || !is_array($itemIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data yang dipilih untuk diarsipkan',
                'csrfHash' => csrf_hash()
            ]);
        }

        try {
            $this->db->transStart();

            // Use archiveMany to set status_hps='1' and deleted_at
            $archived = $this->pelangganGrupModel->archiveMany($itemIds);

            $this->db->transComplete();

            if (!$archived || $this->db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengarsipkan grup pelanggan',
                    'csrfHash' => csrf_hash()
                ]);
            }

            $count = count($itemIds);
            return $this->response->setJSON([
                'success' => true,
                'message' => "Berhasil mengarsipkan {$count} grup pelanggan",
                'archived_count' => $count,
                'csrfHash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            log_message('error', '[PelangganGrup::bulk_delete] ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengarsipkan data: ' . $e->getMessage(),
                'csrfHash' => csrf_hash()
            ]);
        }
    }

    /**
     * Bulk restore groups
     */
    public function bulk_restore()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request',
                'csrfHash' => csrf_hash()
            ]);
        }

        $itemIds = $this->request->getPost('item_ids');

        if (empty($itemIds) || !is_array($itemIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data yang dipilih untuk dipulihkan',
                'csrfHash' => csrf_hash()
            ]);
        }

        try {
            $this->db->transStart();

            // Use restoreMany to set status_hps='0' and deleted_at=null
            $restored = $this->pelangganGrupModel->restoreMany($itemIds);

            $this->db->transComplete();

            if (!$restored || $this->db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memulihkan grup pelanggan',
                    'csrfHash' => csrf_hash()
                ]);
            }

            $count = count($itemIds);
            return $this->response->setJSON([
                'success' => true,
                'message' => "Berhasil memulihkan {$count} grup pelanggan",
                'restored_count' => $count,
                'csrfHash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            log_message('error', '[PelangganGrup::bulk_restore] ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memulihkan data: ' . $e->getMessage(),
                'csrfHash' => csrf_hash()
            ]);
        }
    }

    /**
     * Display trash (deleted groups)
     */
    public function trash()
    {
        $currentPage = $this->request->getVar('page_grup') ?? 1;
        $perPage = 10;

        $query = $this->pelangganGrupModel;

        // Use withDeleted() to include soft-deleted items
        $query->withDeleted();

        // Show items where status_hps = '1' OR deleted_at IS NOT NULL
        $query->groupStart()
            ->where('status_hps', '1')
            ->orWhere('deleted_at IS NOT NULL', null, false)
            ->groupEnd();

        $search = $this->request->getVar('search');
        if ($search) {
            $query->groupStart()
                ->like('grup', $search)
                ->orLike('deskripsi', $search)
                ->groupEnd();
        }

        // Order by deleted_at descending
        $query->orderBy('deleted_at', 'DESC');

        $total = $query->countAllResults(false);
        $trashCount = $this->pelangganGrupModel->countArchived();

        $data = [
            'title'       => 'Trash Grup Pelanggan',
            'grup_list'   => $query->findAll(),
            'pager'       => $this->pelangganGrupModel->pager,
            'currentPage' => $currentPage,
            'perPage'     => $perPage,
            'total'       => $total,
            'search'      => $search,
            'trashCount'  => $trashCount,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/customer-group') . '">Grup Pelanggan</a></li>
                <li class="breadcrumb-item active">Trash</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/pelanggan_grup/trash', $data);
    }

    /**
     * Restore deleted group
     */
    public function restore($id = null)
    {
        if (!$id) {
            return redirect()->to('master/customer-group/trash')
                           ->with('error', 'ID grup tidak ditemukan');
        }

        try {
            // Use restoreMany with single ID
            if (!$this->pelangganGrupModel->restoreMany([$id])) {
                throw new \RuntimeException('Gagal mengembalikan data grup');
            }

            return redirect()->to(base_url('master/customer-group/trash'))
                           ->with('success', 'Data grup berhasil dikembalikan');

        } catch (\Exception $e) {
            log_message('error', '[PelangganGrup::restore] ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Gagal mengembalikan data grup');
        }
    }

    /**
     * Permanently delete group
     */
    public function delete_permanent($id = null)
    {
        if (!$id) {
            return redirect()->to('master/customer-group/trash')
                           ->with('error', 'ID grup tidak ditemukan');
        }

        try {
            if (!$this->pelangganGrupModel->delete($id, true)) {
                throw new \RuntimeException('Gagal menghapus permanen data grup');
            }

            return redirect()->to(base_url('master/customer-group/trash'))
                           ->with('success', 'Data grup berhasil dihapus permanen');

        } catch (\Exception $e) {
            log_message('error', '[PelangganGrup::delete_permanent] ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Gagal menghapus permanen data grup');
        }
    }
}