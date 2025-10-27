<?php
/**
 * Gudang Controller
 *
 * Controller for managing warehouses (gudang)
 * Handles CRUD operations and other related functionalities
 *
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @date      2025-01-12
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\GudangModel;
use App\Models\ItemModel;
use App\Models\ItemStokModel;

class Gudang extends BaseController
{
    protected $gudangModel;
    protected $validation;
    protected $itemModel;
    protected $itemStokModel;
    public function __construct()
    {
        $this->gudangModel      = new GudangModel();
        $this->itemModel        = new ItemModel();
        $this->itemStokModel    = new ItemStokModel();
        $this->validation       = \Config\Services::validation();
    }

    public function index()
    {
        $currentPage    = $this->request->getVar('page_gudang') ?? 1;
        $perPage        = $this->pengaturan->pagination_limit;
        $keyword        = $this->request->getVar('keyword');

        $query = $this->gudangModel->where('status_otl', '0')->where('status_hps', '0');

        if ($keyword) {
            $query->groupStart()
                ->like('nama', $keyword)
                ->orLike('kode', $keyword)
                ->orLike('deskripsi', $keyword)
                ->groupEnd();
        }

        $data = [
            'title'         => 'Data Gudang',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'gudang'        => $query->paginate($perPage, 'gudang'),
            'pager'         => $this->gudangModel->pager,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,
            'keyword'       => $keyword,
            'trashCount'    => $this->trashCount(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Gudang</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/gudang/index', $data);
    }

    public function create()
    {
        $data = [
            'title'         => 'Form Gudang',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/gudang') . '">Gudang</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/gudang/create', $data);
    }

    public function store()
    {
        // Validation rules
        $rules = [
            'gudang' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Nama gudang harus diisi',
                    'max_length' => 'Nama gudang maksimal 160 karakter'
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

        $kode      = $this->gudangModel->generateKode();
        $nama      = $this->request->getPost('gudang');
        $deskripsi = $this->request->getPost('keterangan');
        $status    = $this->request->getPost('status');
        $status_gd = $this->request->getPost('status_gd');

        $data = [
            'kode'       => $kode,
            'nama'       => $nama,
            'deskripsi'  => $deskripsi,
            'status'     => $status,
            'status_gd'  => $status_gd
        ];

        if ($this->gudangModel->insert($data)) {
            return redirect()->to(base_url('master/gudang'))
                ->with('success', 'Data gudang berhasil ditambahkan');
        }

        return redirect()->to(base_url('master/gudang/create'))
            ->with('error', 'Gagal menambahkan data gudang')
            ->withInput();
    }

    public function edit($id)
    {
        $data = [
            'title'         => 'Form Gudang',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/gudang') . '">Gudang</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        $data['gudang'] = $this->gudangModel->find($id);

        if (empty($data['gudang'])) {
            return redirect()->to(base_url('master/gudang'))
                ->with('error', 'Data gudang tidak ditemukan');
        }

        return view($this->theme->getThemePath() . '/master/gudang/edit', $data);
    }

    public function update($id)
    {
        // Validation rules
        $rules = [
            'gudang' => [
                'rules' => 'required|max_length[160]',
                'errors' => [
                    'required' => 'Nama gudang harus diisi',
                    'max_length' => 'Nama gudang maksimal 160 karakter'
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

        $kode      = $this->gudangModel->generateKode();
        $nama      = $this->request->getPost('gudang');
        $deskripsi = $this->request->getPost('keterangan');
        $status    = $this->request->getPost('status');
        $status_gd = $this->request->getPost('status_gd');

        $data = [
            'nama'       => $nama,
            'deskripsi'  => $deskripsi,
            'status'     => $status,
            'status_gd'  => $status_gd
        ];

        if ($this->gudangModel->update($id, $data)) {
            return redirect()->to(base_url('master/gudang'))
                ->with('success', 'Data gudang berhasil diubah');
        }

        return redirect()->back()
            ->with('error', 'Gagal mengupdate data gudang')
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

        // Update warehouse status to 0 (inactive)
        if ($this->gudangModel->update($id, $data)) {
            return redirect()->to(base_url('master/gudang'))
                ->with('success', 'Data gudang berhasil dihapus');
        }

        return redirect()->back()
            ->with('error', 'Gagal menghapus data gudang');
    }

    public function delete_permanent($id)
    {
        $sql_cek = $this->itemStokModel->where('id_gudang', $id)->countAllResults();

        if ($sql_cek > 0) {
            // Hapus semua item stok yang terkait dengan gudang ini sebelum menghapus gudang secara permanen
            $this->itemStokModel->where('id_gudang', $id)->delete();
        }

        if ($this->gudangModel->delete($id, true)) {
            return redirect()->to(base_url('master/gudang/trash'))
                ->with('success', 'Data gudang berhasil dihapus permanen');
        }

        return redirect()->to(base_url('master/gudang/trash'))
            ->with('error', 'Gagal menghapus permanen data gudang');
    }

    public function trash()
    {
        $currentPage    = $this->request->getVar('page_gudang') ?? 1;
        $perPage        = $this->pengaturan->pagination_limit;
        $keyword        = $this->request->getVar('keyword');

        $this->gudangModel->where('status_otl', '0')->where('status_hps', '1');

        if ($keyword) {
            $this->gudangModel->groupStart()
                ->like('nama', $keyword)
                ->orLike('kode', $keyword)
                ->orLike('deskripsi', $keyword)
                ->groupEnd();
        }

        $data = [
            'title'         => 'Data Gudang Arsip',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'gudang'        => $this->gudangModel->paginate($perPage, 'gudang'),
            'pager'         => $this->gudangModel->pager,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,
            'keyword'       => $keyword,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/gudang') . '">Gudang</a></li>
                <li class="breadcrumb-item active">Tempat Sampah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/gudang/trash', $data);
    }

    public function restore($id)
    {
        $data = [
            'status_hps' => '0',
            'deleted_at' => null
        ];

        // Set the status of all item stock records related to this warehouse to 1 (active)
        $this->itemStokModel->where('id_gudang', $id)->set(['status' => '1'])->update();

        if ($this->gudangModel->update($id, $data)) {
            return redirect()->to(base_url('master/gudang/trash'))
                ->with('success', 'Data gudang berhasil dikembalikan');
        }

        return redirect()->to(base_url('master/gudang/trash'))
            ->with('error', 'Gagal mengembalikan data gudang');
    }

    private function trashCount()
    {
        return $this->gudangModel->where('status_otl', '0')->where('status_hps', '1')->countAllResults();
    }

    /**
     * Show CSV import form
     */
    public function importForm()
    {
        $data = [
            'title'         => 'Import Data Gudang',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/gudang') . '">Gudang</a></li>
                <li class="breadcrumb-item active">Import Excel</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/gudang/import', $data);
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
                if (count($row) >= 2) { // At least nama, alamat
                    $csvData[] = [
                        'nama' => trim($row[0] ?? ''),
                        'alamat' => trim($row[1] ?? ''),
                        'telepon' => trim($row[2] ?? ''),
                        'keterangan' => trim($row[3] ?? ''),
                        'status_otl' => isset($row[4]) ? trim($row[4]) : '0',
                        'status_hps' => isset($row[5]) ? trim($row[5]) : '0'
                    ];
                }
            }
            fclose($handle);

            if (empty($csvData)) {
                return redirect()->back()
                    ->with('error', 'File Excel kosong atau format tidak sesuai');
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($csvData as $index => $row) {
                try {
                    if ($this->gudangModel->insert($row)) {
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": " . implode(', ', $this->gudangModel->errors());
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

            return redirect()->to(base_url('master/gudang'))
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
        $filename = 'template_gudang.xlsx';
        $filepath = FCPATH . 'assets/templates/' . $filename;

        // Create template if not exists
        if (!file_exists($filepath)) {
            $templateDir = dirname($filepath);
            if (!is_dir($templateDir)) {
                mkdir($templateDir, 0777, true);
            }

            $headers = ['Nama', 'Alamat', 'Telepon', 'Keterangan', 'Status Outlet', 'Status Hapus'];
            $sampleData = [
                ['Gudang Pusat', 'Jl. Sudirman No. 1', '08123456789', 'Gudang utama', '0', '0'],
                ['Gudang Cabang', 'Jl. Thamrin No. 2', '08123456788', 'Gudang cabang', '0', '0']
            ];
            $filepath = createExcelTemplate($headers, $sampleData, $filename);
        }

        return $this->response->download($filepath, null);
    }

    /**
     * Bulk delete gudang
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
                    if ($this->gudangModel->delete($id)) {
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