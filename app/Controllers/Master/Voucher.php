<?php

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-08-01
 * Github : github.com/mikhaelfelian
 * Description : Controller for managing voucher master data
 * This file represents the Controller.
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\VoucherModel;

class Voucher extends BaseController
{
    protected $voucherModel;
    protected $validation;
    protected $ionAuth;

    public function __construct()
    {
        parent::__construct();
        $this->voucherModel = new VoucherModel();
        $this->validation = \Config\Services::validation();
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_voucher') ?? 1;
        $perPage = 10;
        $keyword = $this->request->getVar('keyword');

        $data = [
            'title'         => 'Data Voucher',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'vouchers'      => $this->voucherModel->getVouchersWithPagination($keyword, $perPage),
            'pager'         => $this->voucherModel->pager,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,
            'keyword'       => $keyword,
            'summary'       => $this->voucherModel->getVoucherSummary(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Voucher</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/voucher/index', $data);
    }

    public function create()
    {
        $data = [
            'title'         => 'Form Voucher',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'kode'          => $this->voucherModel->generateCode(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/voucher') . '">Voucher</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/voucher/create', $data);
    }

    public function store()
    {
        // Debug logging
        log_message('debug', 'Store method called');
        log_message('debug', 'POST data: ' . json_encode($this->request->getPost()));
        
        // Get all voucher form input values from POST request
        $id             = $this->request->getPost('id');
        $kode           = $this->request->getPost('kode');
        $jml            = $this->request->getPost('jml');
        $jenis_voucher  = $this->request->getPost('jenis_voucher');
        $nominal        = $this->request->getPost('nominal');
        $jml_max        = $this->request->getPost('jml_max');
        $tgl_masuk      = $this->request->getPost('tgl_masuk');
        $tgl_keluar     = $this->request->getPost('tgl_keluar');
        $status         = $this->request->getPost('status');
        $keterangan     = $this->request->getPost('keterangan');

        // Validation rules
        $rules = [
            'kode' => [
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required' => 'Kode voucher harus diisi',
                    'max_length' => 'Kode voucher maksimal 50 karakter'
                ]
            ],
            'jml' => [
                'rules' => 'required|integer|greater_than[0]',
                'errors' => [
                    'required' => 'Jumlah voucher harus diisi',
                    'integer' => 'Jumlah voucher harus berupa angka',
                    'greater_than' => 'Jumlah voucher harus lebih dari 0'
                ]
            ],
            'jenis_voucher' => [
                'rules' => 'required|in_list[nominal,persen]',
                'errors' => [
                    'required' => 'Jenis voucher harus dipilih',
                    'in_list' => 'Jenis voucher tidak valid'
                ]
            ],
            'nominal' => [
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'Nominal voucher harus diisi',
                    'numeric' => 'Nominal voucher harus berupa angka',
                    'greater_than' => 'Nominal voucher harus lebih dari 0'
                ]
            ],
            'jml_max' => [
                'rules' => 'required|integer|greater_than[0]',
                'errors' => [
                    'required' => 'Batas maksimal harus diisi',
                    'integer' => 'Batas maksimal harus berupa angka',
                    'greater_than' => 'Batas maksimal harus lebih dari 0'
                ]
            ],
            'tgl_masuk' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Tanggal mulai harus diisi',
                    'valid_date' => 'Format tanggal mulai tidak valid'
                ]
            ],
            'tgl_keluar' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Tanggal berakhir harus diisi',
                    'valid_date' => 'Format tanggal berakhir tidak valid'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('master/voucher/create'))
                ->withInput()
                ->with('error', 'Validasi gagal');
        }

        // Check if end date is after start date
        if (strtotime($tgl_keluar) <= strtotime($tgl_masuk)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tanggal berakhir harus setelah tanggal mulai');
        }

        // Check if jml_max is greater than or equal to jml
        if ($jml_max < $jml) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Batas maksimal tidak boleh kurang dari jumlah voucher');
        }
        
        // Check if percentage voucher doesn't exceed 100%
        if ($jenis_voucher === 'persen' && $nominal > 100) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Persentase voucher tidak boleh lebih dari 100%');
        }

        // Check if nominal voucher has reasonable minimum amount
        if ($jenis_voucher === 'nominal' && $nominal < 1000) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Nominal voucher minimal Rp 1.000');
        }

        // Check if nominal voucher doesn't exceed reasonable maximum (e.g., 10 million)
        if ($jenis_voucher === 'nominal' && $nominal > 10000000) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Nominal voucher tidak boleh lebih dari Rp 10.000.000');
        }

        // Generate data
        $data = [
            'id_user'       => $this->ionAuth->user()->row()->id,
            'jenis_voucher' => $jenis_voucher,
            'nominal'       => $nominal,
            'tgl_masuk'     => $tgl_masuk,
            'tgl_keluar'    => $tgl_keluar,
            'status'        => $status,
            'keterangan'    => $keterangan
        ];

        if ($id) {
            $data['id'] = $id;
        }else{
            $data['kode']       = $kode;
            $data['jml_keluar'] = 0;
            $data['jml_max']    = $jml_max;
            $data['jml']        = $jml;
        }

        try {
            $this->voucherModel->save($data);
            return redirect()->to(base_url('master/voucher/'.($id ? 'edit/'.$id : 'create')))
                ->with('success', 'Data voucher berhasil disimpan');
        } catch (\Throwable $e) {
            return redirect()->to(base_url('master/voucher/create'))
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data voucher: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $voucher = $this->voucherModel->find($id);
        
        if (!$voucher) {
            return redirect()->to(base_url('master/voucher'))
                ->with('error', 'Data voucher tidak ditemukan');
        }

        $data = [
            'title'         => 'Edit Voucher',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'voucher'       => $voucher,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/voucher') . '">Voucher</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/voucher/edit', $data);
    }

    public function update($id)
    {
        // Debug logging
        log_message('debug', 'Update method called with ID: ' . $id);
        log_message('debug', 'POST data: ' . json_encode($this->request->getPost()));
        
        $voucher = $this->voucherModel->find($id);
        
        if (!$voucher) {
            return redirect()->to(base_url('master/voucher'))
                ->with('error', 'Data voucher tidak ditemukan');
        }

        $kode = $this->request->getPost('kode');
        $jml = $this->request->getPost('jml');
        $jenis_voucher = $this->request->getPost('jenis_voucher');
        $nominal = $this->request->getPost('nominal');
        $jml_max = $this->request->getPost('jml_max');
        $tgl_masuk = $this->request->getPost('tgl_masuk');
        $tgl_keluar = $this->request->getPost('tgl_keluar');
        $status = $this->request->getPost('status');
        $keterangan = $this->request->getPost('keterangan');

        // Validation rules
        $rules = [
            'id' => [
                'rules' => 'required|integer|greater_than[0]',
                'errors' => [
                    'required' => 'ID voucher harus ada',
                    'integer' => 'ID voucher harus berupa angka',
                    'greater_than' => 'ID voucher tidak valid'
                ]
            ],
            'kode' => [
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required' => 'Kode voucher harus diisi',
                    'max_length' => 'Kode voucher maksimal 50 karakter'
                ]
            ],
            'jml' => [
                'rules' => 'required|integer|greater_than[0]',
                'errors' => [
                    'required' => 'Jumlah voucher harus diisi',
                    'integer' => 'Jumlah voucher harus berupa angka',
                    'greater_than' => 'Jumlah voucher harus lebih dari 0'
                ]
            ],
            'jenis_voucher' => [
                'rules' => 'required|in_list[nominal,persen]',
                'errors' => [
                    'required' => 'Jenis voucher harus dipilih',
                    'in_list' => 'Jenis voucher tidak valid'
                ]
            ],
            'nominal' => [
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'Nominal voucher harus diisi',
                    'numeric' => 'Nominal voucher harus berupa angka',
                    'greater_than' => 'Nominal voucher harus lebih dari 0'
                ]
            ],
            'jml_max' => [
                'rules' => 'required|integer|greater_than[0]',
                'errors' => [
                    'required' => 'Batas maksimal harus diisi',
                    'integer' => 'Batas maksimal harus berupa angka',
                    'greater_than' => 'Batas maksimal harus lebih dari 0'
                ]
            ],
            'tgl_masuk' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Tanggal mulai harus diisi',
                    'valid_date' => 'Format tanggal mulai tidak valid'
                ]
            ],
            'tgl_keluar' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Tanggal berakhir harus diisi',
                    'valid_date' => 'Format tanggal berakhir tidak valid'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validasi gagal');
        }

        // Check if kode is unique (excluding current voucher)
        $existingVoucher = $this->voucherModel->where('kode', $kode)->where('id !=', $id)->first();
        if ($existingVoucher) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Kode voucher sudah digunakan');
        }

        // Check if end date is after start date
        if (strtotime($tgl_keluar) <= strtotime($tgl_masuk)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tanggal berakhir harus setelah tanggal mulai');
        }

        // Check if jml_max is greater than or equal to jml_keluar
        if ($jml_max < $voucher->jml_keluar) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Batas maksimal tidak boleh kurang dari jumlah yang sudah digunakan (' . $voucher->jml_keluar . ')');
        }
        
        // Check if percentage voucher doesn't exceed 100%
        if ($jenis_voucher === 'persen' && $nominal > 100) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Persentase voucher tidak boleh lebih dari 100%');
        }

        // Check if nominal voucher has reasonable minimum amount
        if ($jenis_voucher === 'nominal' && $nominal < 1000) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Nominal voucher minimal Rp 1.000');
        }

        // Check if nominal voucher doesn't exceed reasonable maximum (e.g., 10 million)
        if ($jenis_voucher === 'nominal' && $nominal > 10000000) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Nominal voucher tidak boleh lebih dari Rp 10.000.000');
        }

        // Generate data
        $data = [
            'id'            => $id, // Include ID for update operation
            'kode'          => $kode,
            'jml'           => $jml,
            'jenis_voucher' => $jenis_voucher,
            'nominal'       => $nominal,
            'jml_max'       => $jml_max,
            'tgl_masuk'     => $tgl_masuk,
            'tgl_keluar'    => $tgl_keluar,
            'status'        => $status,
            'keterangan'    => $keterangan
        ];

        if ($this->voucherModel->save($data)) {
            return redirect()->to(base_url('master/voucher'))
                ->with('success', 'Data voucher berhasil diperbarui');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data voucher');
        }
    }

    public function delete($id)
    {
        $voucher = $this->voucherModel->find($id);
        
        if (!$voucher) {
            return redirect()->to(base_url('master/voucher'))
                ->with('error', 'Data voucher tidak ditemukan');
        }

        // Check if voucher has been used
        if ($voucher->jml_keluar > 0) {
            return redirect()->to(base_url('master/voucher'))
                ->with('error', 'Voucher tidak dapat dihapus karena sudah digunakan');
        }

        if ($this->voucherModel->delete($id)) {
            return redirect()->to(base_url('master/voucher'))
                ->with('success', 'Data voucher berhasil dihapus');
        } else {
            return redirect()->to(base_url('master/voucher'))
                ->with('error', 'Gagal menghapus data voucher');
        }
    }

    public function detail($id)
    {
        $voucher = $this->voucherModel->find($id);
        
        if (!$voucher) {
            return redirect()->to(base_url('master/voucher'))
                ->with('error', 'Data voucher tidak ditemukan');
        }

        $stats = $this->voucherModel->getVoucherStats($id);

        $data = [
            'title'         => 'Detail Voucher',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'voucher'       => $voucher,
            'stats'         => $stats,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/voucher') . '">Voucher</a></li>
                <li class="breadcrumb-item active">Detail</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/voucher/detail', $data);
    }

    /**
     * Bulk delete vouchers
     */
    public function bulk_delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        // Get item_ids - handle both item_ids[] and item_ids formats
        $itemIds = $this->request->getPost('item_ids');
        if (empty($itemIds)) {
            // Try PHP array format with brackets
            $allPost = $this->request->getPost();
            $itemIds = $allPost['item_ids'] ?? [];
        }

        // If itemIds is a comma-separated string, convert to array
        if (is_string($itemIds) && !empty($itemIds)) {
            $itemIds = explode(',', $itemIds);
        }

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
                    $voucher = $this->voucherModel->find($id);
                    
                    // Check if voucher has been used
                    if ($voucher && $voucher->jml_keluar > 0) {
                        $failedCount++;
                        $errors[] = "Voucher ID {$id} tidak dapat dihapus (sudah digunakan)";
                        continue;
                    }
                    
                    if ($this->voucherModel->delete($id)) {
                        $deletedCount++;
                    } else {
                        $failedCount++;
                        $errors[] = "Gagal menghapus voucher ID: {$id}";
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = "Error menghapus voucher ID {$id}: " . $e->getMessage();
                }
            }

            $message = "Berhasil menghapus {$deletedCount} voucher";
            if ($failedCount > 0) {
                $message .= ", {$failedCount} voucher gagal dihapus";
            }

            return $this->response->setJSON([
                'success' => $deletedCount > 0,
                'message' => $message,
                'deleted_count' => $deletedCount,
                'failed_count' => $failedCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            log_message('error', '[Bulk Delete Voucher] ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus voucher: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show Excel import form
     */
    public function importForm()
    {
        $data = [
            'title'         => 'Import Data Voucher',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/voucher') . '">Voucher</a></li>
                <li class="breadcrumb-item active">Import Excel</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/voucher/import', $data);
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
                if (count($row) >= 4) { // At least kode, jml, jenis_voucher, nominal
                    // Map jenis_voucher: 0 = nominal, 1 = persen
                    $jenisInput = trim($row[2] ?? '0');
                    $jenisVoucher = ($jenisInput == '1' || $jenisInput == 'persen') ? 'persen' : 'nominal';
                    
                    // Calculate tgl_masuk and tgl_keluar if not provided
                    $tglMasuk = isset($row[5]) && !empty($row[5]) ? trim($row[5]) : date('Y-m-d');
                    $tglKeluar = isset($row[6]) && !empty($row[6]) ? trim($row[6]) : date('Y-m-d', strtotime('+1 year'));
                    
                    // Validate date format
                    if (!empty($row[5]) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tglMasuk)) {
                        $tglMasuk = date('Y-m-d', strtotime($tglMasuk));
                    }
                    if (!empty($row[6]) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tglKeluar)) {
                        $tglKeluar = date('Y-m-d', strtotime($tglKeluar));
                    }

                    $csvData[] = [
                        'id_user'        => $this->ionAuth->user()->row()->id ?? 0,
                        'kode'           => trim($row[0] ?? ''),
                        'jml'            => (int)trim($row[1] ?? 0),
                        'jenis_voucher'  => $jenisVoucher, // Use 'nominal' or 'persen'
                        'nominal'        => (float)format_angka_db(trim($row[3] ?? 0)),
                        'jml_max'        => isset($row[4]) && !empty($row[4]) ? (int)trim($row[4]) : 1,
                        'tgl_masuk'      => $tglMasuk,
                        'tgl_keluar'     => $tglKeluar,
                        'status'         => isset($row[7]) ? trim($row[7]) : '1',
                        'keterangan'     => isset($row[8]) ? trim($row[8]) : '',
                        'jml_keluar'     => 0 // Initialize to 0 for new vouchers
                    ];
                }
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            $db = \Config\Database::connect();

            // Start transaction
            $db->transStart();

            foreach ($csvData as $index => $row) {
                try {
                    // Validate required fields
                    if (empty($row['kode'])) {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": Kode voucher wajib diisi";
                        continue;
                    }

                    if (empty($row['jml']) || $row['jml'] <= 0) {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": Jumlah voucher harus lebih dari 0";
                        continue;
                    }

                    if (empty($row['nominal']) || $row['nominal'] <= 0) {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": Nominal voucher harus lebih dari 0";
                        continue;
                    }

                    if (!in_array($row['jenis_voucher'], ['nominal', 'persen'])) {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": Jenis voucher harus 'nominal' atau 'persen'";
                        continue;
                    }

                    // Validate date format
                    if (!empty($row['tgl_masuk']) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $row['tgl_masuk'])) {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": Format tanggal masuk tidak valid (harus YYYY-MM-DD)";
                        continue;
                    }

                    if (!empty($row['tgl_keluar']) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $row['tgl_keluar'])) {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": Format tanggal keluar tidak valid (harus YYYY-MM-DD)";
                        continue;
                    }

                    // Check if voucher code already exists
                    $existing = $this->voucherModel->where('kode', $row['kode'])->first();
                    if ($existing) {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": Kode voucher '{$row['kode']}' sudah ada";
                        continue;
                    }

                    // Insert data
                    if ($this->voucherModel->insert($row)) {
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": " . implode(', ', $this->voucherModel->errors());
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            // Complete transaction
            $db->transComplete();

            // Build message
            $messageType = $successCount > 0 ? 'success' : 'error';
            
            if ($successCount > 0) {
                $message = "Import berhasil! Data yang berhasil disimpan: <strong>{$successCount}</strong>";
                if ($errorCount > 0) {
                    $message .= "<br>Data yang gagal: <strong>{$errorCount}</strong>";
                }
            } else {
                $message = "Import gagal! Tidak ada data yang berhasil disimpan. Gagal: <strong>{$errorCount}</strong>";
            }

            // Add error details if any
            if (!empty($errors)) {
                $message .= "<br><br><strong>Detail Error:</strong><br>" . implode("<br>", array_slice($errors, 0, 20));
                if (count($errors) > 20) {
                    $message .= "<br>... dan " . (count($errors) - 20) . " error lainnya";
                }
            }

            if ($messageType === 'success') {
                return redirect()->to(base_url('master/voucher'))
                    ->with('success', $message);
            } else {
                return redirect()->to(base_url('master/voucher'))
                    ->with('error', $message);
            }

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
        $headers = ['Kode', 'Jumlah', 'Jenis (0=nominal/1=persen)', 'Nominal', 'Jml Max', 'Tgl Masuk (YYYY-MM-DD)', 'Tgl Keluar (YYYY-MM-DD)', 'Status (0/1)', 'Keterangan'];
        $sampleData = [
            ['VOUCHER001', '100', '0', '10000', '100', '2025-01-01', '2025-12-31', '1', 'Voucher diskon 10rb'],
            ['VOUCHER002', '50', '1', '5000', '50', '2025-01-01', '2025-12-31', '1', 'Voucher diskon 5rb']
        ];
        
        $filename = 'template_voucher.xlsx';
        $filepath = createExcelTemplate($headers, $sampleData, $filename);
        
        return $this->response->download($filepath, null);
    }

    /**
     * Export data to Excel
     */
    public function exportExcel()
    {
        $keyword = $this->request->getVar('keyword');
        $vouchers = $this->voucherModel->getVouchersWithPagination($keyword, 999999); // Get all

        $headers = ['No', 'Kode', 'Jml', 'Jml Keluar', 'Jenis', 'Nominal', 'Jml Max', 'Tgl Masuk', 'Tgl Keluar', 'Status', 'Keterangan'];
        
        $data = [];
        foreach ($vouchers as $index => $voucher) {
            $jenis = $voucher->jenis_voucher == '0' ? 'Diskon' : 'Cashback';
            $status = $voucher->status == '1' ? 'Aktif' : 'Tidak Aktif';
            
            $data[] = [
                $index + 1,
                $voucher->kode,
                $voucher->jml,
                $voucher->jml_keluar ?? 0,
                $jenis,
                number_format($voucher->nominal, 0, ',', '.'),
                $voucher->jml_max,
                $voucher->tgl_masuk,
                $voucher->tgl_keluar,
                $status,
                $voucher->keterangan ?? ''
            ];
        }

        $filename = 'export_voucher_' . date('Y-m-d_His') . '.xlsx';
        $filepath = createExcelTemplate($headers, $data, $filename);
        
        return $this->response->download($filepath, null);
    }
}