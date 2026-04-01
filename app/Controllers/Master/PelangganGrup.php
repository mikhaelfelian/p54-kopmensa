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

        $trashCount = $this->pelangganGrupModel->countArchived();

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
        $currentPage = (int) ($this->request->getVar('page_grup') ?? 1);
        $perPage = 10;

        $query = $this->pelangganGrupModel->withDeleted();
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

        $query->orderBy('deleted_at', 'DESC');

        $grup_list = $query->paginate($perPage, 'page_grup', $currentPage);
        $pager = $this->pelangganGrupModel->pager;

        $data = [
            'title'       => 'Trash Grup Pelanggan',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'grup_list'   => $grup_list,
            'pager'       => $pager,
            'currentPage' => $currentPage,
            'perPage'     => $perPage,
            'search'      => $search,
            'keyword'     => $search,
            'trashCount'  => $this->pelangganGrupModel->countArchived(),
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

    public function detail($id = null)
    {
        if (! $id) {
            return redirect()->to('master/customer-group')->with('error', 'ID grup tidak ditemukan');
        }

        $grup = $this->pelangganGrupModel->getGroupWithMemberCount($id);
        if (! $grup) {
            return redirect()->to('master/customer-group')->with('error', 'Data grup tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Grup Pelanggan',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'grup' => $grup,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/customer-group') . '">Grup Pelanggan</a></li>
                <li class="breadcrumb-item active">Detail</li>
            ',
        ];

        return view($this->theme->getThemePath() . '/master/pelanggan_grup/detail', $data);
    }

    public function members($id = null)
    {
        if (! $id) {
            return redirect()->to('master/customer-group')->with('error', 'ID grup tidak ditemukan');
        }

        $grup = $this->pelangganGrupModel->find($id);
        if (! $grup) {
            return redirect()->to('master/customer-group')->with('error', 'Data grup tidak ditemukan');
        }

        $search = $this->request->getVar('search') ?? '';
        $status = $this->request->getVar('status') ?? '';
        $currentPage = max(1, (int) ($this->request->getVar('page') ?? 1));
        $perPage = 20;

        $currentMembers = $this->pelangganGrupModel->getGroupMembers($id);
        $availableCustomers = $this->pelangganGrupModel->getAvailableCustomersPaginated($id, $perPage, $currentPage, $search, $status);
        $totalAvailable = $this->pelangganGrupModel->getTotalAvailableCustomers($id, $search, $status);

        $data = [
            'title' => 'Kelola Member Grup',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'grup' => $grup,
            'currentMembers' => $currentMembers,
            'availableCustomers' => $availableCustomers,
            'totalAvailable' => $totalAvailable,
            'search' => $search,
            'status' => $status,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/customer-group') . '">Grup Pelanggan</a></li>
                <li class="breadcrumb-item active">Member</li>
            ',
        ];

        return view($this->theme->getThemePath() . '/master/pelanggan_grup/members', $data);
    }

    public function add_member($id = null)
    {
        if (! $id) {
            return redirect()->to('master/customer-group')->with('error', 'ID grup tidak ditemukan');
        }

        return redirect()->to(base_url('master/customer-group/members/' . $id));
    }

    public function store_member()
    {
        $id_grup = (int) $this->request->getPost('id_grup');
        $id_pelanggan = (int) $this->request->getPost('id_pelanggan');
        if ($id_grup < 1 || $id_pelanggan < 1) {
            return redirect()->back()->with('error', 'Data tidak valid');
        }

        if ($this->pelangganGrupModel->addMemberToGroup($id_grup, $id_pelanggan)) {
            return redirect()->back()->with('success', 'Member berhasil ditambahkan');
        }

        return redirect()->back()->with('error', 'Member sudah ada di grup atau gagal menyimpan');
    }

    public function delete_member($memberRowId = null)
    {
        if (! $memberRowId) {
            return redirect()->back()->with('error', 'ID tidak valid');
        }

        $row = $this->db->table('tbl_m_pelanggan_grup_member')->where('id', $memberRowId)->get()->getRow();
        if (! $row) {
            return redirect()->back()->with('error', 'Data keanggotaan tidak ditemukan');
        }

        $this->db->table('tbl_m_pelanggan_grup_member')->where('id', $memberRowId)->delete();

        return redirect()->back()->with('success', 'Member dihapus dari grup');
    }

    public function addMember()
    {
        $id_grup = (int) $this->request->getPost('id_grup');
        $id_pelanggan = (int) $this->request->getPost('id_pelanggan');
        if ($id_grup < 1 || $id_pelanggan < 1) {
            return redirect()->back()->with('error', 'Data tidak valid');
        }

        if ($this->pelangganGrupModel->addMemberToGroup($id_grup, $id_pelanggan)) {
            return redirect()->back()->with('success', 'Pelanggan ditambahkan ke grup');
        }

        return redirect()->back()->with('error', 'Pelanggan sudah menjadi anggota grup atau gagal menyimpan');
    }

    public function removeMember()
    {
        $id_grup = (int) $this->request->getPost('id_grup');
        $id_pelanggan = (int) $this->request->getPost('id_pelanggan');
        if ($id_grup < 1 || $id_pelanggan < 1) {
            return redirect()->back()->with('error', 'Data tidak valid');
        }

        if ($this->pelangganGrupModel->removeMemberFromGroup($id_grup, $id_pelanggan)) {
            return redirect()->back()->with('success', 'Member dihapus dari grup');
        }

        return redirect()->back()->with('error', 'Gagal menghapus member dari grup');
    }

    public function addBulkMembers()
    {
        $id_grup = (int) $this->request->getPost('id_grup');
        $ids = $this->request->getPost('customer_ids');
        if ($id_grup < 1 || empty($ids) || ! is_array($ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu pelanggan');
        }

        $added = 0;
        foreach ($ids as $cid) {
            $cid = (int) $cid;
            if ($cid < 1) {
                continue;
            }
            if ($this->pelangganGrupModel->addMemberToGroup($id_grup, $cid)) {
                $added++;
            }
        }

        return redirect()->back()->with('success', "Berhasil menambahkan {$added} pelanggan ke grup");
    }

    public function searchCustomers()
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $q = trim((string) $this->request->getPost('search'));
        $id_grup = (int) $this->request->getPost('id_grup');
        if ($id_grup < 1) {
            return $this->response->setJSON(['success' => false, 'customers' => []]);
        }

        $inGroup = $this->db->table('tbl_m_pelanggan_grup_member')
            ->select('id_pelanggan')
            ->where('id_grup', $id_grup)
            ->get()
            ->getResultArray();
        $excludeIds = array_column($inGroup, 'id_pelanggan');

        $builder = $this->pelangganModel->builder();
        $builder->where('status_hps', '0')->where('status', '1');
        if ($excludeIds !== []) {
            $builder->whereNotIn('id', $excludeIds);
        }
        if ($q !== '') {
            $builder->groupStart()->like('nama', $q)->orLike('no_telp', $q)->orLike('kode', $q)->groupEnd();
        }
        $rows = $builder->limit(30)->get()->getResult();

        return $this->response->setJSON(['success' => true, 'customers' => $rows]);
    }

    public function getCurrentMembers($id = null)
    {
        if (! $id) {
            return $this->response->setJSON(['success' => false, 'members' => []]);
        }

        $members = $this->pelangganGrupModel->getGroupMembers($id);

        return $this->response->setJSON(['success' => true, 'members' => $members]);
    }

    public function importForm()
    {
        $data = [
            'title' => 'Import Grup Pelanggan',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/customer-group') . '">Grup Pelanggan</a></li>
                <li class="breadcrumb-item active">Import</li>
            ',
        ];

        return view($this->theme->getThemePath() . '/master/pelanggan_grup/import', $data);
    }

    public function downloadTemplate()
    {
        $csv = "grup,deskripsi,status\nContoh Grup,Deskripsi opsional,1\n";

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="template_grup_pelanggan.csv"')
            ->setBody($csv);
    }

    public function importCsv()
    {
        $file = $this->request->getFile('csv_file');
        if (! $file || ! $file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid');
        }

        $path = $file->getTempName();
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return redirect()->back()->with('error', 'Tidak dapat membaca file');
        }

        $skipHeader = (bool) $this->request->getPost('skip_header');
        $updateExisting = (bool) $this->request->getPost('update_existing');
        $rowNum = 0;
        $inserted = 0;
        $updated = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            if ($skipHeader && $rowNum === 1) {
                continue;
            }
            if (count($row) < 1 || trim((string) ($row[0] ?? '')) === '') {
                continue;
            }

            $grup = trim((string) $row[0]);
            $deskripsi = trim((string) ($row[1] ?? ''));
            $status = trim((string) ($row[2] ?? '1'));
            if ($status !== '0' && $status !== '1') {
                $status = '1';
            }

            $existing = $this->pelangganGrupModel->where('grup', $grup)->first();
            if ($existing) {
                if ($updateExisting) {
                    $this->pelangganGrupModel->update($existing->id, [
                        'deskripsi' => $deskripsi,
                        'status' => $status,
                    ]);
                    $updated++;
                }
                continue;
            }

            $this->pelangganGrupModel->insert([
                'grup' => $grup,
                'deskripsi' => $deskripsi,
                'status' => $status,
            ]);
            $inserted++;
        }
        fclose($handle);

        return redirect()->to('master/customer-group')->with('success', "Import selesai: {$inserted} baru, {$updated} diperbarui");
    }
}