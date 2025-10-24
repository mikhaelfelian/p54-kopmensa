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

        if (count($row) >= 3) { // At least nama, no_telp, alamat
                    try {
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
        if (count($row) >= 3) { // At least nama, no_telp, alamat
                    try {
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
            if (count($row) >= 3) { // At least nama, no_telp, alamat
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

        if (count($row) >= 3) { // At least nama, no_telp, alamat
                    try {
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
            if (count($row) >= 3) { // At least nama, no_telp, alamat
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
        if (count($row) >= 3) { // At least nama, no_telp, alamat
                    try {
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

    public function detail($id)
    {
        $grup = $this->pelangganGrupModel->getGroupWithMemberCount($id);
        if (!$grup) {
            return redirect()->to(base_url('master/customer-group'))
                ->with('error', 'Data grup pelanggan tidak ditemukan');
        }

        if (count($row) >= 3) { // At least nama, no_telp, alamat
                    try {
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
            '
        ];

        return view($this->theme->getThemePath() . '/master/pelanggan_grup/detail', $data);
    }

    public function trash()
    {
        $currentPage = $this->request->getVar('page_grup') ?? 1;
        $perPage = 10;
        $keyword = $this->request->getVar('keyword');

        $this->pelangganGrupModel->where('tbl_m_pelanggan_grup.status', '0');

        if ($keyword) {
            $this->pelangganGrupModel->groupStart()
                ->like('grup', $keyword)
                ->orLike('deskripsi', $keyword)
                ->groupEnd();
        }

        if (count($row) >= 3) { // At least nama, no_telp, alamat
                    try {
                        $data = [
            'title' => 'Data Grup Pelanggan Terhapus',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'grup_list' => $this->pelangganGrupModel->getGroupsWithMemberCount($perPage, $keyword, $currentPage),
            'pager' => $this->pelangganGrupModel->pager,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'keyword' => $keyword,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/customer-group') . '">Grup Pelanggan</a></li>
                <li class="breadcrumb-item active">Tempat Sampah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/pelanggan_grup/trash', $data);
    }

    public function restore($id)
    {
        if (count($row) >= 3) { // At least nama, no_telp, alamat
                    try {
                        $data = [
            'status' => '1',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->pelangganGrupModel->update($id, $data)) {
            return redirect()->to(base_url('master/customer-group/trash'))
                ->with('success', 'Data grup pelanggan berhasil dikembalikan');
        }

        return redirect()->back()
            ->with('error', 'Gagal mengembalikan data grup pelanggan');
    }

    public function delete_permanent($id)
    {
        if ($this->pelangganGrupModel->delete($id, true)) {
            return redirect()->to(base_url('master/customer-group/trash'))
                ->with('success', 'Data grup pelanggan berhasil dihapus permanen');
        }

        return redirect()->back()
            ->with('error', 'Gagal menghapus permanen data grup pelanggan');
    }

    /**
     * Manage group members
     */
    public function members($groupId)
    {
        $grup = $this->pelangganGrupModel->find($groupId);
        if (!$grup) {
            return redirect()->to(base_url('master/customer-group'))
                ->with('error', 'Data grup pelanggan tidak ditemukan');
        }

        $currentMembers = $this->pelangganGrupModel->getGroupMembers($groupId);
        
        // Pagination and search parameters
        $page = $this->request->getVar('page') ?? 1;
        $perPage = 5; // Show only 20 customers per page
        $search = $this->request->getVar('search') ?? '';
        $status = $this->request->getVar('status') ?? '';
        
        $availableCustomers = $this->pelangganGrupModel->getAvailableCustomersPaginated($groupId, $perPage, $page, $search, $status);
        $totalAvailable = $this->pelangganGrupModel->getTotalAvailableCustomers($groupId, $search, $status);

        if (count($row) >= 3) { // At least nama, no_telp, alamat
                    try {
                        $data = [
            'title' => 'Kelola Member Grup: ' . $grup->grup,
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'grup' => $grup,
            'currentMembers' => $currentMembers,
            'availableCustomers' => $availableCustomers,
            'pager' => $this->pelangganGrupModel->pager,
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalAvailable' => $totalAvailable,
            'search' => $search,
            'status' => $status,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/customer-group') . '">Grup Pelanggan</a></li>
                <li class="breadcrumb-item active">Kelola Member</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/pelanggan_grup/members', $data);
    }

    /**
     * Add member to group
     */
    public function addMember()
    {
        // Validate request method - now accepts regular POST
        if (!$this->request->getMethod() === 'post') {
            return redirect()->back()->with('error', 'Invalid request method');
        }

        $groupId = $this->request->getVar('id_grup');
        $customerId = $this->request->getVar('id_pelanggan');

        // Validate input
        if (!$groupId || !$customerId) {
            return redirect()->back()->with('error', 'Data tidak lengkap');
        }

        // Validate group exists
        $group = $this->pelangganGrupModel->find($groupId);
        if (!$group) {
            return redirect()->back()->with('error', 'Grup tidak ditemukan');
        }

        // Validate customer exists
        $customer = $this->pelangganModel->find($customerId);
        if (!$customer) {
            return redirect()->back()->with('error', 'Pelanggan tidak ditemukan');
        }

        try {
            if ($this->pelangganGrupModel->addMemberToGroup($groupId, $customerId)) {
                return redirect()->to(base_url("master/customer-group/members/{$groupId}"))
                    ->with('success', 'Member berhasil ditambahkan ke grup "' . $group->grup . '"');
            } else {
                return redirect()->back()->with('error', 'Member sudah ada dalam grup ini');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error adding member to group: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan member: ' . $e->getMessage());
        }
    }

    /**
     * Remove member from group
     */
    public function removeMember()
    {
        // Validate request method - now accepts regular POST
        if (!$this->request->getMethod() === 'post') {
            return redirect()->back()->with('error', 'Invalid request method');
        }

        $groupId = $this->request->getVar('id_grup');
        $customerId = $this->request->getVar('id_pelanggan');

        // Validate input
        if (!$groupId || !$customerId) {
            return redirect()->back()->with('error', 'Data tidak lengkap');
        }

        // Validate group exists
        $group = $this->pelangganGrupModel->find($groupId);
        if (!$group) {
            return redirect()->back()->with('error', 'Grup tidak ditemukan');
        }

        // Validate customer exists
        $customer = $this->pelangganModel->find($customerId);
        if (!$customer) {
            return redirect()->back()->with('error', 'Pelanggan tidak ditemukan');
        }

        try {
            if ($this->pelangganGrupModel->removeMemberFromGroup($groupId, $customerId)) {
                return redirect()->to(base_url("master/customer-group/members/{$groupId}"))
                    ->with('success', 'Member berhasil dihapus dari grup "' . $group->grup . '"');
            } else {
                return redirect()->back()->with('error', 'Member tidak ditemukan dalam grup ini');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error removing member from group: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus member: ' . $e->getMessage());
        }
    }

    /**
     * Add multiple members to group (bulk)
     */
    public function addBulkMembers()
    {
        // Validate request method - now accepts regular POST
        if (!$this->request->getMethod() === 'post') {
            return redirect()->to(base_url("master/customer-group/members/" . $this->request->getVar('id_grup')))
                ->with('error', 'Invalid request method');
        }

        $groupId = $this->request->getVar('id_grup');
        $customerIds = $this->request->getVar('id_pelanggan');

        // Validate input
        if (!$groupId || !$customerIds) {
            return redirect()->to(base_url("master/customer-group/members/{$groupId}"))
                ->with('error', 'Data tidak lengkap');
        }

        if (!is_array($customerIds)) {
            $customerIds = [$customerIds];
        }

        // Validate group exists
        $group = $this->pelangganGrupModel->find($groupId);
        if (!$group) {
            return redirect()->to(base_url("master/customer-group/members/{$groupId}"))
                ->with('error', 'Grup tidak ditemukan');
        }

        // Validate all customers exist
        $customers = $this->pelangganModel->whereIn('id', $customerIds)->findAll();
        if (count($customers) !== count($customerIds)) {
            return redirect()->to(base_url("master/customer-group/members/{$groupId}"))
                ->with('error', 'Beberapa pelanggan tidak ditemukan');
        }

        $successCount = 0;
        $alreadyExistsCount = 0;
        $failedCount = 0;
        $results = [];

        try {
            foreach ($customerIds as $customerId) {
                $customer = array_filter($customers, function($c) use ($customerId) {
                    return $c->id == $customerId;
                });
                $customer = reset($customer);

                if ($this->pelangganGrupModel->addMemberToGroup($groupId, $customerId)) {
                    $successCount++;
                    $results[] = [
                        'customer_id' => $customerId,
                        'customer_name' => $customer->nama,
                        'status' => 'success',
                        'message' => 'Berhasil ditambahkan'
                    ];
                } else {
                    $alreadyExistsCount++;
                    $results[] = [
                        'customer_id' => $customerId,
                        'customer_name' => $customer->nama,
                        'status' => 'exists',
                        'message' => 'Sudah ada dalam grup'
                    ];
                }
            }

            $message = "Berhasil menambahkan {$successCount} member";
            if ($alreadyExistsCount > 0) {
                $message .= ", {$alreadyExistsCount} member sudah ada dalam grup";
            }

            return redirect()->to(base_url("master/customer-group/members/{$groupId}"))
                ->with('success', $message);

        } catch (\Exception $e) {
            log_message('error', 'Error adding bulk members to group: ' . $e->getMessage());
            return redirect()->to(base_url("master/customer-group/members/{$groupId}"))
                ->with('error', 'Gagal menambahkan member secara bulk: ' . $e->getMessage());
        }
    }

    /**
     * Search customers for adding to group (AJAX)
     */
    public function searchCustomers()
    {
        // Validate request method
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        $search = $this->request->getVar('search') ?? '';
        $status = $this->request->getVar('status') ?? '';
        $page = (int)($this->request->getVar('page') ?? 1);
        $perPage = (int)($this->request->getVar('per_page') ?? 20);
        $groupId = (int)($this->request->getVar('group_id') ?? 0);

        try {
            // Build query for available customers (not in the group)
            $query = $this->pelangganModel->select('id, nama, no_telp, status, kode')
                ->where('status_hps', '0'); // Not deleted

            // Apply search filter
            if ($search) {
                $query->groupStart()
                    ->like('nama', $search)
                    ->orLike('no_telp', $search)
                    ->orLike('kode', $search)
                    ->groupEnd();
            }

            // Apply status filter
            if ($status !== '') {
                $query->where('status', $status);
            }

            // Exclude customers already in the group
            if ($groupId > 0) {
                $existingMembers = $this->db->table('tbl_m_pelanggan_grup_member')
                    ->select('id_pelanggan')
                    ->where('id_grup', $groupId)
                    ->get()
                    ->getResultArray();
                
                $existingIds = array_column($existingMembers, 'id_pelanggan');
                if (!empty($existingIds)) {
                    $query->whereNotIn('id', $existingIds);
                }
            }

            // Get total count for pagination
            $totalAvailable = $query->countAllResults(false);

            // Apply pagination
            $offset = ($page - 1) * $perPage;
            $customers = $query->limit($perPage, $offset)->findAll();

            // Get current members count for the group
            $totalCurrent = 0;
            if ($groupId > 0) {
                $totalCurrent = $this->db->table('tbl_m_pelanggan_grup_member')
                    ->where('id_grup', $groupId)
                    ->countAllResults();
            }

            return $this->response->setJSON([
                'success' => true,
                'customers' => $customers,
                'total_available' => $totalAvailable,
                'total_current' => $totalCurrent,
                'current_page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($totalAvailable / $perPage)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error searching customers: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Gagal mencari pelanggan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get current members of a group (AJAX)
     */
    public function getCurrentMembers($groupId)
    {
        // Validate request method
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $groupId = (int)$groupId;
            
            // Get current members with customer details
            $members = $this->db->table('tbl_m_pelanggan_grup_member pgm')
                ->select('pgm.id_pelanggan, p.nama, p.no_telp, p.status')
                ->join('tbl_m_pelanggan p', 'p.id = pgm.id_pelanggan')
                ->where('pgm.id_grup', $groupId)
                ->where('p.status_hps', '0') // Not deleted
                ->orderBy('p.nama', 'ASC')
                ->get()
                ->getResult();

            return $this->response->setJSON([
                'success' => true,
                'members' => $members
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error getting current members: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Gagal mengambil data member: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show CSV import form
     */
    public function importForm()
    {
        if (count($row) >= 3) { // At least nama, no_telp, alamat
                    try {
                        $data = [
            'title'         => 'Import Data Grup Pelanggan',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/customer-group') . '">Grup Pelanggan</a></li>
                <li class="breadcrumb-item active">Import Excel</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/pelanggan-grup/import', $data);
    }

    /**
     * Process Excel import
     */
    public function importCsv()
    {
        $file = $this->request->getFile('excel_file');
        
        if (!$file || !$file->isValid()) {
            return redirect()->back()
                ->with('error', 'File Excel tidak valid');
        }

        // Validation rules
        $rules = [
            'excel_file' => [
                'rules' => 'uploaded[excel_file]|ext_in[excel_file,xlsx,xls]|max_size[excel_file,5120]',
                'errors' => [
                    'uploaded' => 'File Excel harus diupload',
                    'ext_in' => 'File harus berformat Excel',
                    'max_size' => 'Ukuran file maksimal 5MB'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
        }

        try {
            $csvData = [];
            $handle = fopen($file->getTempName(), 'r');
            
            // Skip header row
            $header = fgetcsv($handle);
            
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 1) { // At least grup
                    $csvData[] = [
                        'grup' => trim($row[0] ?? ''),
                        'deskripsi' => trim($row[1] ?? ''),
                        'status' => isset($row[2]) ? trim($row[2]) : '1'
                    ];
                }
            }
            fclose($handle);

            if (empty($excelData)) {
                return redirect()->back()
                    ->with('error', 'File Excel kosong atau format tidak sesuai');
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($excelData as $index => $row) {
                try {
                    if ($this->pelangganGrupModel->insert($data)) {
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": " . implode(', ', $this->pelangganGrupModel->errors());
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Import selesai. Berhasil: {$successCount}, Gagal: {$errorCount}";
            if (!empty($errors)) {
                $message .= "<br>Error details:<br>" . implode("<br>", array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $message .= "<br>... dan " . (count($errors) - 10) . " error lainnya";
                }
            }

            return redirect()->to(base_url('master/customer-group'))
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template
     */
    public function downloadTemplate()
    {
        $filename = 'template_pelanggan_grup.xlsx';
        $filepath = FCPATH . 'assets/templates/' . $filename;
        
        // Create template if not exists
        if (!file_exists($filepath)) {
            $templateDir = dirname($filepath);
            if (!is_dir($templateDir)) {
                mkdir($templateDir, 0777, true);
            }
            
            $template = "Grup,Deskripsi,Status\n";
            $template .= "VIP,Pelanggan VIP dengan diskon khusus,1\n";
            $template .= "Reguler,Pelanggan reguler,1\n";
            $template .= "Member,Pelanggan member dengan benefit,1\n";
            
            file_put_contents($filepath, $template);
        }
        
        return $this->response->download($filepath, null);
    }
}
