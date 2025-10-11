<?php

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github : github.com/mikhaelfelian
 * Description : Controller for managing product variants (varian)
 * This file represents the Varian controller.
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\VarianModel;

class Varian extends BaseController
{
    protected $varianModel;
    protected $validation;

    public function __construct()
    {
        $this->varianModel = new VarianModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $curr_page  = $this->request->getVar('page_varian') ?? 1;
        $per_page   = 10;
        $query      = $this->request->getVar('keyword') ?? '';

        // Apply search filter if keyword exists
        if ($query) {
            $this->varianModel->groupStart()
                ->like('nama', $query)
                ->orLike('kode', $query)
                ->orLike('keterangan', $query)
                ->groupEnd();
        }

        $data = [
            'title'         => 'Data Varian',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'varian'        => $this->varianModel->paginate($per_page, 'varian'),
            'pager'         => $this->varianModel->pager,
            'currentPage'   => $curr_page,
            'perPage'       => $per_page,
            'keyword'       => $query,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Varian</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/varian/index', $data);
    }

    public function create()
    {
        $data = [
            'title'         => 'Form Varian',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/varian') . '">Varian</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/varian/create', $data);
    }

    public function store()
    {
        $nama = $this->request->getPost('nama');
        $ket  = $this->request->getPost('keterangan');
        $status = $this->request->getPost('status');

        // Validation rules
        $rules = [
            'nama' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Nama varian harus diisi',
                    'max_length' => 'Nama varian maksimal 100 karakter'
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
        $kode = $this->varianModel->generateCode();

        // Generate data
        $data = [
            'kode'       => $kode,
            'nama'       => $nama,
            'keterangan' => $ket,
            'status'     => $status
        ];

        if ($this->varianModel->insert($data)) {
            return redirect()->to(base_url('master/varian'))
                ->with('success', 'Data varian berhasil ditambahkan');
        }

        return redirect()->back()
            ->with('error', 'Gagal menambahkan data varian')
            ->withInput();
    }

    public function edit($id)
    {
        $data = [
            'title'         => 'Form Varian',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/varian') . '">Varian</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];
        $data['varian'] = $this->varianModel->find($id);

        if (empty($data['varian'])) {
            return redirect()->to(base_url('master/varian'))
                ->with('error', 'Data varian tidak ditemukan');
        }

        return view($this->theme->getThemePath() . '/master/varian/edit', $data);
    }

    public function update($id)
    {
        // Validation rules
        $rules = [
            'nama' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Nama varian harus diisi',
                    'max_length' => 'Nama varian maksimal 100 karakter'
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
            'nama'       => $this->request->getPost('nama'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status'     => $this->request->getPost('status')
        ];

        if ($this->varianModel->update($id, $data)) {
            return redirect()->to(base_url('master/varian'))
                ->with('success', 'Data varian berhasil diubah!');
        }

        return redirect()->back()
            ->with('error', 'Gagal mengupdate data varian')
            ->withInput();
    }

    public function delete($id)
    {
        if ($this->varianModel->delete($id)) {
            return redirect()->to(base_url('master/varian'))
                ->with('success', 'Data varian berhasil dihapus');
        }

        return redirect()->back()
            ->with('error', 'Gagal menghapus data varian');
    }

    /**
     * Show CSV import form
     */
    public function importForm()
    {
        $data = [
            'title'         => 'Import Data Varian',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/varian') . '">Varian</a></li>
                <li class="breadcrumb-item active">Import CSV</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/varian/import', $data);
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
                if (count($row) >= 1) { // At least nama
                    $csvData[] = [
                        'nama' => trim($row[0]),
                        'keterangan' => isset($row[1]) ? trim($row[1]) : '',
                        'status' => isset($row[2]) ? trim($row[2]) : '1'
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
                    // Generate kode
                    $kode = $this->varianModel->generateKode();
                    
                    $insertData = [
                        'kode' => $kode,
                        'nama' => $data['nama'],
                        'keterangan' => $data['keterangan'],
                        'status' => $data['status']
                    ];

                    if ($this->varianModel->insert($insertData)) {
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": " . implode(', ', $this->varianModel->errors());
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

            return redirect()->to(base_url('master/varian'))
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
        $filename = 'template_varian.csv';
        $filepath = FCPATH . 'assets/templates/' . $filename;
        
        // Create template if not exists
        if (!file_exists($filepath)) {
            $templateDir = dirname($filepath);
            if (!is_dir($templateDir)) {
                mkdir($templateDir, 0777, true);
            }
            
            $template = "Nama,Keterangan,Status\n";
            $template .= "Warna,Variasi warna produk,1\n";
            $template .= "Ukuran,Variasi ukuran produk,1\n";
            $template .= "Model,Variasi model produk,1\n";
            
            file_put_contents($filepath, $template);
        }
        
        return $this->response->download($filepath, null);
    }
} 