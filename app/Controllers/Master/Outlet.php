<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\GudangModel;
use App\Models\ItemModel;
use App\Models\ItemStokModel;

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
    public function __construct()
    {
        $this->outletModel   = new GudangModel();
        $this->itemModel     = new ItemModel();
        $this->itemStokModel = new ItemStokModel();
        $this->validation    = \Config\Services::validation();
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
                <li class="breadcrumb-item active">Import CSV</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/outlet/import', $data);
    }

    /**
     * Process CSV import
     */
    public function importCsv()
    {
        $file = $this->request->getFile('csv_file');
        
        if (!$file || !$file->isValid()) {
            return redirect()->back()
                ->with('error', 'File CSV tidak valid');
        }

        // Validation rules
        $rules = [
            'csv_file' => [
                'rules' => 'uploaded[csv_file]|ext_in[csv_file,csv]|max_size[csv_file,2048]',
                'errors' => [
                    'uploaded' => 'File CSV harus diupload',
                    'ext_in' => 'File harus berformat CSV',
                    'max_size' => 'Ukuran file maksimal 2MB'
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
                        'nama' => trim($row[0]),
                        'alamat' => isset($row[1]) ? trim($row[1]) : '',
                        'telepon' => isset($row[2]) ? trim($row[2]) : '',
                        'keterangan' => isset($row[3]) ? trim($row[3]) : '',
                        'status_otl' => isset($row[4]) ? trim($row[4]) : '1',
                        'status_hps' => isset($row[5]) ? trim($row[5]) : '0'
                    ];
                }
            }
            fclose($handle);

            if (empty($csvData)) {
                return redirect()->back()
                    ->with('error', 'File CSV kosong atau format tidak sesuai');
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($csvData as $index => $data) {
                try {
                    if ($this->outletModel->insert($data)) {
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
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $filename = 'template_outlet.csv';
        $filepath = FCPATH . 'assets/templates/' . $filename;
        
        // Create template if not exists
        if (!file_exists($filepath)) {
            $templateDir = dirname($filepath);
            if (!is_dir($templateDir)) {
                mkdir($templateDir, 0777, true);
            }
            
            $template = "Nama,Alamat,Telepon,Keterangan,Status Outlet,Status Hapus\n";
            $template .= "Outlet Pusat,Jl. Sudirman No. 1,08123456789,Outlet utama,1,0\n";
            $template .= "Outlet Cabang,Jl. Thamrin No. 2,08123456788,Outlet cabang,1,0\n";
            
            file_put_contents($filepath, $template);
        }
        
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
                'message' => 'Tidak ada item yang dipilih'
            ]);
        }

        try {
            $deletedCount = 0;
            $failedCount = 0;

            foreach ($itemIds as $id) {
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
                }
            }

            if ($deletedCount > 0) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Berhasil menghapus {$deletedCount} outlet" . ($failedCount > 0 ? ", gagal {$failedCount} outlet" : "")
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus semua outlet yang dipilih'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
} 