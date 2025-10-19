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
        $curr_page  = $this->request->getVar('page_satuan') ?? 1;
        $per_page   = 10;
        $query      = $this->request->getVar('keyword') ?? '';

        // Apply search filter if keyword exists
        if ($query) {
            $this->satuanModel->groupStart()
                ->like('satuanKecil', $query)
                ->orLike('satuanBesar', $query)
                ->groupEnd();
        }

        $data = [
            'title'         => 'Data Satuan',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'satuan'        => $this->satuanModel->paginate($per_page, 'satuan'),
            'pager'         => $this->satuanModel->pager,
            'currentPage'   => $curr_page,
            'perPage'       => $per_page,
            'keyword'       => $query,
            'breadcrumbs'   => '
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
            csrf_token() => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'CSRF token tidak valid'
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

    /**
     * Show CSV import form
     */
    public function importForm()
    {
        $data = [
            'title'         => 'Import Data Satuan',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/satuan') . '">Satuan</a></li>
                <li class="breadcrumb-item active">Import CSV</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/satuan/import', $data);
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
                if (count($row) >= 3) { // At least satuanKecil, satuanBesar, jml
                    $csvData[] = [
                        'satuanKecil' => trim($row[0]),
                        'satuanBesar' => trim($row[1]),
                        'jml' => isset($row[2]) ? (int)trim($row[2]) : 1,
                        'status' => isset($row[3]) ? trim($row[3]) : '1'
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
                    if ($this->satuanModel->insert($data)) {
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": " . implode(', ', $this->satuanModel->errors());
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

            return redirect()->to(base_url('master/satuan'))
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
        $filename = 'template_satuan.csv';
        $filepath = FCPATH . 'assets/templates/' . $filename;
        
        // Create template if not exists
        if (!file_exists($filepath)) {
            $templateDir = dirname($filepath);
            if (!is_dir($templateDir)) {
                mkdir($templateDir, 0777, true);
            }
            
            $template = "Satuan Kecil,Satuan Besar,Jumlah,Status\n";
            $template .= "Pcs,Box,12,1\n";
            $template .= "Gram,Kilogram,1000,1\n";
            $template .= "Ml,Liter,1000,1\n";
            
            file_put_contents($filepath, $template);
        }
        
        return $this->response->download($filepath, null);
    }

    /**
     * Bulk delete satuan
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
                if ($this->satuanModel->delete($id)) {
                    $deletedCount++;
                } else {
                    $failedCount++;
                }
            }

            if ($deletedCount > 0) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Berhasil menghapus {$deletedCount} satuan" . ($failedCount > 0 ? ", gagal {$failedCount} satuan" : "")
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus semua satuan yang dipilih'
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