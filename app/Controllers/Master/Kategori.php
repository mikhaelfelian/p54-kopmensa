<?php
/**
 * Kategori Controller
 * 
 * Controller for managing categories (kategori)
 * Handles CRUD operations and other related functionalities
 * 
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @date      2025-01-12
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\KategoriModel;

class Kategori extends BaseController
{
    protected $kategoriModel;
    protected $validation;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $curr_page  = $this->request->getVar('page_kategori') ?? 1;
        $per_page   = 10;
        $query      = $this->request->getVar('keyword') ?? '';

        // Apply search filter if keyword exists
        if ($query) {
            $this->kategoriModel->groupStart()
                ->like('kategori', $query)
                ->orLike('kode', $query)
                ->orLike('keterangan', $query)
                ->groupEnd();
        }

        $data = [
            'title'         => 'Data Kategori',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'kategori'      => $this->kategoriModel->paginate($per_page, 'kategori'),
            'pager'         => $this->kategoriModel->pager,
            'currentPage'   => $curr_page,
            'perPage'       => $per_page,
            'keyword'       => $query,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Kategori</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/kategori/index', $data);
    }

    public function create()
    {
        $data = [
            'title'         => 'Form Kategori',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/kategori') . '">Kategori</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/kategori/create', $data);
    }

    public function store()
    {
        $kategori = $this->request->getPost('kategori');
        $ket      = $this->request->getPost('keterangan');
        $status   = $this->request->getPost('status');

        // Validation rules
        $rules = [
            'kategori' => [
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => 'Kategori harus diisi',
                    'max_length' => 'Kategori maksimal 255 karakter'
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

        // Generate kode
        $kode     = $this->kategoriModel->generateKode($kategori);

        // Generate data
        $data = [
            'kode'       => $kode,
            'kategori'   => $kategori,
            'keterangan' => $ket,
            'status'     => $status
        ];

        if ($this->kategoriModel->insert($data)) {
            return redirect()->to(base_url('master/kategori'))
                ->with('success', 'Data kategori berhasil ditambahkan');
        }

        return redirect()->back()
            ->with('error', 'Gagal menambahkan data kategori')
            ->withInput();
    }

    public function edit($id)
    {
        $data = [
            'title'         => 'Form Kategori',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/kategori') . '">Kategori</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];
        $data['kategori'] = $this->kategoriModel->find($id);

        if (empty($data['kategori'])) {
            return redirect()->to(base_url('master/kategori'))
                ->with('error', 'Data kategori tidak ditemukan');
        }

        return view($this->theme->getThemePath() . '/master/kategori/edit', $data);
    }

    public function update($id)
    {
        // Validation rules
        $rules = [
            'kategori' => [
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => 'Kategori harus diisi',
                    'max_length' => 'Kategori maksimal 255 karakter'
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
            'kategori'   => $this->request->getPost('kategori'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status'     => $this->request->getPost('status')
        ];

        if ($this->kategoriModel->update($id, $data)) {
            return redirect()->to(base_url('master/kategori'))
                ->with('success', 'Data kategori berhasil diubah!');
        }

        return redirect()->back()
            ->with('error', 'Gagal mengupdate data kategori')
            ->withInput();
    }

    public function delete($id)
    {
        if ($this->kategoriModel->delete($id)) {
            return redirect()->to(base_url('master/kategori'))
                ->with('success', 'Data kategori berhasil dihapus');
        }

        return redirect()->back()
            ->with('error', 'Gagal menghapus data kategori');
    }

    /**
     * Show CSV import form
     */
    public function importForm()
    {
        $data = [
            'title'         => 'Import Data Kategori',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/kategori') . '">Kategori</a></li>
                <li class="breadcrumb-item active">Import Excel</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/kategori/import', $data);
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
                if (count($row) >= 1) { // At least kategori
                    $csvData[] = [
                        'kategori' => trim($row[0] ?? ''),
                        'keterangan' => trim($row[1] ?? ''),
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
                    // Generate kode
                    $kode = $this->kategoriModel->generateKode($data['kategori']);
                    
                    $insertData = [
                        'kode' => $kode,
                        'kategori' => $data['kategori'],
                        'keterangan' => $data['keterangan'],
                        'status' => $data['status']
                    ];

                    if ($this->kategoriModel->insert($insertData)) {
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": " . implode(', ', $this->kategoriModel->errors());
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

            return redirect()->to(base_url('master/kategori'))
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
        $filename = 'template_kategori.xlsx';
        $filepath = FCPATH . 'assets/templates/' . $filename;
        
        // Create template if not exists
        if (!file_exists($filepath)) {
            $templateDir = dirname($filepath);
            if (!is_dir($templateDir)) {
                mkdir($templateDir, 0777, true);
            }
            
            $template = "Kategori,Keterangan,Status\n";
            $template .= "Elektronik,Produk elektronik dan gadget,1\n";
            $template .= "Pakaian,Produk fashion dan pakaian,1\n";
            $template .= "Makanan,Produk makanan dan minuman,1\n";
            
            file_put_contents($filepath, $template);
        }
        
        return $this->response->download($filepath, null);
    }

    /**
     * Bulk delete kategori
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
                if ($this->kategoriModel->delete($id)) {
                    $deletedCount++;
                } else {
                    $failedCount++;
                }
            }

            if ($deletedCount > 0) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Berhasil menghapus {$deletedCount} kategori" . ($failedCount > 0 ? ", gagal {$failedCount} kategori" : "")
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus semua kategori yang dipilih'
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