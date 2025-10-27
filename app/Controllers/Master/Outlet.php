<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\GudangModel;
use App\Models\ItemModel;
use App\Models\ItemStokModel;
use App\Models\PlatformModel;
use App\Models\OutletPlatformModel;

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
    protected $itemModel;
    protected $itemStokModel;
    protected $platformModel;
    protected $outletPlatformModel;
    
    public function __construct()
    {
        $this->outletModel         = new GudangModel();
        $this->itemModel           = new ItemModel();
        $this->itemStokModel       = new ItemStokModel();
        $this->platformModel       = new PlatformModel();
        $this->outletPlatformModel = new OutletPlatformModel();
        $this->validation          = \Config\Services::validation();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_outlet') ?? 1;
        $perPage     = $this->pengaturan->pagination_limit;
        $keyword     = $this->request->getVar('keyword');

        $this->outletModel->where('status_otl', '1')->where('status_hps', '0');

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
            'outlet'        => $this->outletModel->paginate($perPage, 'gudang'),
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
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('master/outlet/create'))
                ->withInput()
                ->with('error', 'Validasi gagal');
        }

        $data = [
            'id_user'    => $id_user,
            'kode'       => $this->outletModel->generateKode('1'),
            'nama'       => $nama,
            'deskripsi'  => $deskripsi,
            'status'     => $status,
            'status_otl' => '1',
        ];

        $db = \Config\Database::connect();
        $db->transStart();
        try {
            $this->outletModel->insert($data);
            $last_id = $this->outletModel->getInsertID();

            $sql_cek = $this->itemModel->where('status_hps', '0')->where('status', '1')->findAll();
            foreach ($sql_cek as $row) {
                $this->itemStokModel->insert([
                    'id_item'   => $row->id,
                    'id_gudang' => $last_id,
                    'id_user'   => $this->ionAuth->user()->row()->id,
                    'status'    => '1',
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi gagal');
            }

            return redirect()->to(base_url('master/outlet'))
                ->with('success', 'Data outlet berhasil ditambahkan');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->to(base_url('master/outlet/create'))
                ->withInput()
                ->with('error', 'Gagal menambahkan data outlet: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $outlet = $this->outletModel->find($id);
        
        if (empty($outlet)) {
            return redirect()->to(base_url('master/outlet'))
                ->with('error', 'Data outlet tidak ditemukan');
        }

        $data = [
            'title'              => 'Form Outlet',
            'Pengaturan'         => $this->pengaturan,
            'user'               => $this->ionAuth->user()->row(),
            'validation'         => $this->validation,
            'outlet'             => $outlet,
            'assignedPlatforms'  => $this->outletPlatformModel->getPlatformsByOutlet($id),
            'availablePlatforms' => $this->outletPlatformModel->getAvailablePlatforms($id),
            'breadcrumbs'        => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/outlet') . '">Outlet</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

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

        // Set the status of all item stock records related to this warehouse to 0 (inactive)
        $this->itemStokModel->where('id_gudang', $id)->set(['status' => '0'])->update();

        if ($this->outletModel->update($id, $data)) {
            return redirect()->to(base_url('master/outlet'))
                ->with('success', 'Data outlet berhasil dihapus');
        }

        return redirect()->to(base_url('master/outlet'))
            ->with('error', 'Gagal menghapus data outlet');
    }

    public function delete_permanent($id)
    {
        $sql_cek = $this->itemStokModel->where('id_gudang', $id)->countAllResults();

        if ($sql_cek > 0) {
            // Delete all item stock records related to this outlet before permanently deleting the outlet
            $this->itemStokModel->where('id_gudang', $id)->delete();
        }

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
        $perPage     = $this->pengaturan->pagination_limit;
        $keyword     = $this->request->getVar('keyword');

        $this->outletModel->where('status_otl', '1')->where('status_hps', '1');

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

        // Set the status of all item stock records related to this warehouse to 1 (active)
        $this->itemStokModel->where('id_gudang', $id)->set(['status' => '1'])->update();

        // Update warehouse status to 1 (active)
        if ($this->outletModel->update($id, $data)) {
            return redirect()->to(base_url('master/outlet/trash'))
                ->with('success', 'Data outlet berhasil dikembalikan');
        }

        return redirect()->to(base_url('master/outlet/trash'))
            ->with('error', 'Gagal mengembalikan data outlet');
    }

    private function trashCount()
    {
        return $this->outletModel->where('status_otl', '1')->where('status_hps', '1')->countAllResults();
    }

    /**
     * Show CSV import form
     */
    public function importForm()
    {
        $data = [
            'title'         => 'Import Data Outlet',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/outlet') . '">Outlet</a></li>
                <li class="breadcrumb-item active">Import Excel</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/outlet/import', $data);
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
            // Read Excel file using PhpSpreadsheet
            $tempPath = $file->getTempName();
            $excelData = readExcelFile($tempPath);
            
            if (empty($excelData)) {
                return redirect()->back()
                    ->with('error', 'File Excel kosong atau format tidak sesuai');
            }

            $csvData = [];
            foreach ($excelData as $row) {
                if (count($row) >= 2) { // At least nama, alamat
                    $csvData[] = [
                        'nama' => trim($row[0] ?? ''),
                        'alamat' => trim($row[1] ?? ''),
                        'telepon' => trim($row[2] ?? ''),
                        'keterangan' => trim($row[3] ?? ''),
                        'status_otl' => isset($row[4]) ? trim($row[4]) : '1',
                        'status_hps' => isset($row[5]) ? trim($row[5]) : '0'
                    ];
                }
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($csvData as $index => $row) {
                try {
                    if ($this->outletModel->insert($row)) {
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": " . implode(', ', $this->outletModel->errors());
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

            return redirect()->to(base_url('master/outlet'))
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
        $headers = ['Nama', 'Alamat', 'Telepon', 'Keterangan', 'Status Outlet', 'Status Hapus'];
        $sampleData = [
            ['Outlet Pusat', 'Jl. Sudirman No. 1', '08123456789', 'Outlet utama', '1', '0'],
            ['Outlet Cabang', 'Jl. Thamrin No. 2', '08123456788', 'Outlet cabang', '1', '0']
        ];
        
        $filename = 'template_outlet.xlsx';
        $filepath = createExcelTemplate($headers, $sampleData, $filename);
        
        return $this->response->download($filepath, null);
    }

    /**
     * Bulk delete outlet
     */

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
                    // Soft delete - set status_hps = 1
                    $data = [
                        'status_hps' => '1',
                        'deleted_at' => date('Y-m-d H:i:s')
                    ];
                    
                    // Set the status of all item stock records related to this warehouse to 0 (inactive)
                    $this->itemStokModel->where('id_gudang', $id)->set(['status' => '0'])->update();
                    
                    if ($this->outletModel->update($id, $data)) {
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

    /**
     * Manage platforms for outlet
     */
    public function platforms($id)
    {
        $outlet = $this->outletModel->find($id);
        
        if (empty($outlet)) {
            return redirect()->to(base_url('master/outlet'))
                ->with('error', 'Data outlet tidak ditemukan');
        }

        $data = [
            'title'              => 'Kelola Platform Pembayaran - ' . $outlet->nama,
            'Pengaturan'         => $this->pengaturan,
            'user'               => $this->ionAuth->user()->row(),
            'outlet'             => $outlet,
            'assignedPlatforms'  => $this->outletPlatformModel->getPlatformsByOutlet($id),
            'availablePlatforms' => $this->outletPlatformModel->getAvailablePlatforms($id),
            'breadcrumbs'        => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/outlet') . '">Outlet</a></li>
                <li class="breadcrumb-item active">Platform</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/outlet/platforms', $data);
    }

    /**
     * Assign platform to outlet
     */
    public function assignPlatform($id)
    {
        $id_platform = $this->request->getPost('id_platform');

        if (empty($id_platform)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Platform harus dipilih'
            ]);
        }

        // Check if already assigned
        if ($this->outletPlatformModel->isPlatformAssigned($id, $id_platform)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Platform sudah ditambahkan ke outlet ini'
            ]);
        }

        $data = [
            'id_outlet'   => $id,
            'id_platform' => $id_platform,
            'status'      => '1'
        ];

        if ($this->outletPlatformModel->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Platform berhasil ditambahkan'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menambahkan platform'
        ]);
    }

    /**
     * Remove platform from outlet
     */
    public function removePlatform($id, $id_platform)
    {

        if ($this->outletPlatformModel->removePlatform($id, $id_platform)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Platform berhasil dihapus'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menghapus platform'
        ]);
    }
}