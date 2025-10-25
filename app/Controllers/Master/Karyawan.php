<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-17
 *
 * Karyawan Controller
 *
 * Controller for managing employee (karyawan) data
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;
use App\Models\PengaturanModel;

class Karyawan extends BaseController
{
    protected $karyawanModel;
    protected $validation;
    protected $pengaturan;
    protected $ionAuth;
    protected $db;

    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
        $this->pengaturan = new PengaturanModel();
        $this->validation = \Config\Services::validation();
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_karyawan') ?? 1;
        $perPage = $this->pengaturan->pagination_limit ?? 10;

        // Start with the model query
        $query = $this->karyawanModel;

        // Filter by name/code/nik
        $search = $this->request->getVar('search');
        if ($search) {
            $query->groupStart()
                ->like('nama', $search)
                ->orLike('kode', $search)
                ->orLike('nik', $search)
                ->groupEnd();
        }


        $data = [
            'title'          => 'Data Karyawan',
            'karyawans'      => $query->paginate($perPage, 'karyawan'),
            'pager'          => $this->karyawanModel->pager,
            'currentPage'    => $currentPage,
            'perPage'        => $perPage,
            'search'         => $search,
            'breadcrumbs'    => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Karyawan</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/karyawan/index', $data);
    }

    /**
     * Display create form
     */
    public function create()
    {
        $data = [
            'title'       => 'Tambah Karyawan',
            'validation'  => $this->validation,
            'kode'        => $this->karyawanModel->generateKode(),
            'jabatans'    => $this->ionAuth->groups()->result(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/karyawan') . '">Karyawan</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/karyawan/create', $data);
    }

    /**
     * Store new employee data
     */
    public function store()
    {
        // Validation rules
        $rules = [
            'nik' => [
                'rules'  => 'required|max_length[100]',
                'errors' => [
                    'required'   => 'NIK harus diisi',
                    'max_length' => 'NIK maksimal 100 karakter'
                ]
            ],
            'nama' => [
                'rules'  => 'required|max_length[100]',
                'errors' => [
                    'required'   => 'Nama lengkap harus diisi',
                    'max_length' => 'Nama lengkap maksimal 100 karakter'
                ]
            ],
            'jns_klm' => [
                'rules'  => 'required|in_list[L,P]',
                'errors' => [
                    'required'  => 'Jenis kelamin harus dipilih',
                    'in_list'   => 'Jenis kelamin tidak valid'
                ]
            ],
            'tmp_lahir' => [
                'rules'  => 'required|max_length[100]',
                'errors' => [
                    'required'   => 'Tempat lahir harus diisi',
                    'max_length' => 'Tempat lahir maksimal 100 karakter'
                ]
            ],
            'tgl_lahir' => [
                'rules'  => 'required|valid_date',
                'errors' => [
                    'required'    => 'Tanggal lahir harus diisi',
                    'valid_date'  => 'Format tanggal lahir tidak valid'
                ]
            ],
            'jabatan' => [
                'rules'  => 'required|max_length[100]',
                'errors' => [
                    'required'   => 'Jabatan harus diisi',
                    'max_length' => 'Jabatan maksimal 100 karakter'
                ]
            ],
            'no_hp' => [
                'rules'  => 'required|max_length[20]',
                'errors' => [
                    'required'   => 'Nomor HP harus diisi',
                    'max_length' => 'Nomor HP maksimal 20 karakter'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('validation', $this->validator);
        }

        try {
            // Get all input post variables
            $id_user_group      = $this->request->getPost('id_user_group');
            $kode               = $this->karyawanModel->generateKode();
            $nik                = $this->request->getPost('nik');
            $nama               = $this->request->getPost('nama');
            $jns_klm            = $this->request->getPost('jns_klm');
            $tmp_lahir          = $this->request->getPost('tmp_lahir');
            $tgl_lahir          = $this->request->getPost('tgl_lahir');
            $alamat             = $this->request->getPost('alamat');
            $alamat_domisili    = $this->request->getPost('alamat_domisili');
            $no_hp              = $this->request->getPost('no_hp');
            $rt                 = $this->request->getPost('rt');
            $rw                 = $this->request->getPost('rw');
            $kelurahan          = $this->request->getPost('kelurahan');
            $kecamatan          = $this->request->getPost('kecamatan');
            $kota               = $this->request->getPost('kota');
            $email              = $this->request->getPost('email');
            $username           = $this->request->getPost('username');
            $password           = $this->request->getPost('password');
            $password_confirm   = $this->request->getPost('password_confirm');
            $jabatan            = $this->request->getPost('jabatan');

            // Validate password confirmation if password is provided
            if (!empty($password) && $password !== $password_confirm) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Password dan konfirmasi password tidak sama');
            }

            // Prepare user data for ion_auth
            $user_email    = $email ?: strtolower(str_replace(' ', '', $nama)) . '@example.com';
            $user_username = $username ?: strtolower(str_replace(' ', '', $nama));
            $user_password = $password ?: 'password123'; // Default password, should be changed
            $additional_data = [
                'first_name' => $nama,
                'last_name'  => $nama,
                'phone'      => $no_hp,
                'tipe'       => '1'
            ];
            $group = $id_user_group;

            // Only create user if not already exists (by email or username)
            $userByEmail    = $this->ionAuth->where('email', $user_email)->users()->row();
            $userByUsername = $this->ionAuth->where('username', $user_username)->users()->row();
            $userExists     = ($userByEmail !== null) || ($userByUsername !== null);

            if ($userExists) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'User dengan email atau username tersebut sudah terdaftar.');
            }

            // Create user first
            $user_id = $this->ionAuth->register($user_username, $user_password, $user_email, $additional_data, [$group]);
            if (!$user_id) {
                log_message('error', '[Karyawan::store] Gagal membuat user ion_auth: ' . implode(', ', $this->ionAuth->errors_array()));
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal membuat user login. Silakan cek data user.');
            }

            // Get group description for jabatan
            $groups = $this->ionAuth->group($id_user_group)->row();

            // Prepare karyawan data, including id_user from ion_auth
            $data = [
                'id_user'         => $user_id,
                'id_user_group'   => $id_user_group,
                'kode'            => $kode,
                'nik'             => $nik,
                'nama'            => $nama,
                'jns_klm'         => $jns_klm,
                'tmp_lahir'       => $tmp_lahir,
                'tgl_lahir'       => $tgl_lahir,
                'alamat'          => $alamat,
                'alamat_domisili' => $alamat_domisili,
                'jabatan'         => $jabatan,
                'no_hp'           => $no_hp,
                'rt'              => $rt,
                'rw'              => $rw,
                'kelurahan'       => $kelurahan,
                'kecamatan'       => $kecamatan,
                'kota'            => $kota,
            ];

            if (!$this->karyawanModel->save($data)) {
                // Optionally, you may want to rollback user creation here
                log_message('error', '[Karyawan::store] Gagal menyimpan data karyawan');
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal menyimpan data karyawan');
            }

            return redirect()->to(base_url('master/karyawan'))
                ->with('success', 'Data karyawan dan user login berhasil ditambahkan');
        } catch (\Exception $e) {
            log_message('error', '[Karyawan::store] ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data karyawan');
        }
    }

    /**
     * Display edit form
     */
    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->to('master/karyawan')
                           ->with('error', 'ID karyawan tidak ditemukan');
        }

        $karyawan = $this->karyawanModel->find($id);
        if (!$karyawan) {
            return redirect()->to('master/karyawan')
                           ->with('error', 'Data karyawan tidak ditemukan');
        }

        // Get IonAuth user data
        $ionAuthUser = null;
        if (!empty($karyawan->id_user)) {
            $ionAuthUser = $this->ionAuth->user($karyawan->id_user)->row();
        }

        $data = [
            'title'       => 'Edit Karyawan',
            'validation'  => $this->validation,
            'karyawan'    => $karyawan,
            'ionAuthUser' => $ionAuthUser,
            'jabatans'    => $this->ionAuth->groups()->result(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/karyawan') . '">Karyawan</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/karyawan/edit', $data);
    }

    /**
     * Update employee data
     */
    public function update($id = null)
    {
        if (!$id) {
            return redirect()->to('master/karyawan')
                           ->with('error', 'ID karyawan tidak ditemukan');
        }

        try {
            $karyawan = $this->karyawanModel->find($id);
            if (!$karyawan) {
                throw new \RuntimeException('Data karyawan tidak ditemukan');
            }

            $nama = $this->request->getPost('nama');
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
            $password_confirm = $this->request->getPost('password_confirm');
            $groups   = $this->ionAuth->group($this->request->getPost('id_user_group'))->row();

            // Update IonAuth user data if user exists
            if (!empty($karyawan->id_user)) {
                $ionAuthData = [];

                // Update username if provided
                if (!empty($username)) {
                    // Check if username is unique (excluding current user)
                    $existingUser = $this->ionAuth->where('username', $username)
                                                ->where('id !=', $karyawan->id_user)
                                                ->users()
                                                ->row();

                    if ($existingUser) {
                        return redirect()->back()
                                       ->withInput()
                                       ->with('error', 'Username sudah digunakan oleh user lain');
                    }

                    $ionAuthData['username'] = $username;
                }

                // Update first_name from nama field
                if (!empty($nama)) {
                    $ionAuthData['first_name'] = $nama;
                }

                // Update phone from no_hp field
                $no_hp = $this->request->getPost('no_hp');
                if (!empty($no_hp)) {
                    $ionAuthData['phone'] = $no_hp;
                }

                // Update password if provided
                if (!empty($password)) {
                    // Validate password confirmation
                    if ($password !== $password_confirm) {
                        return redirect()->back()
                                       ->withInput()
                                       ->with('error', 'Password dan konfirmasi password tidak sama');
                    }

                    // Hash the password
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $ionAuthData['password'] = $hashedPassword;
                }

                // Update IonAuth user data if there's data to update
                if (!empty($ionAuthData)) {
                    $this->db->table('tbl_ion_users')
                            ->where('id', $karyawan->id_user)
                            ->set($ionAuthData)
                            ->update();
                }
            }

            $data = [
                'id_user_group'   => $this->request->getPost('id_user_group'),
                'nik'             => $this->request->getPost('nik'),
                'nama'            => $this->request->getPost('nama'),
                'jns_klm'         => $this->request->getPost('jns_klm'),
                'tmp_lahir'       => $this->request->getPost('tmp_lahir'),
                'tgl_lahir'       => $this->request->getPost('tgl_lahir'),
                'alamat'          => $this->request->getPost('alamat'),
                'alamat_domisili' => $this->request->getPost('alamat_domisili'),
                'rt'              => $this->request->getPost('rt'),
                'rw'              => $this->request->getPost('rw'),
                'kelurahan'       => $this->request->getPost('kelurahan'),
                'kecamatan'       => $this->request->getPost('kecamatan'),
                'kota'            => $this->request->getPost('kota'),
                'jabatan'         => $this->request->getPost('jabatan'),
                'no_hp'           => $this->request->getPost('no_hp'),
            ];

            if (!$this->karyawanModel->update($id, $data)) {
                throw new \RuntimeException('Gagal mengupdate data karyawan');
            }

            return redirect()->to(base_url('master/karyawan'))
                           ->with('success', 'Data karyawan berhasil diupdate');

        } catch (\Exception $e) {
            log_message('error', '[Karyawan::update] ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal mengupdate data karyawan');
        }
    }

    /**
     * Display employee details
     */
    public function detail($id = null)
    {
        if (!$id) {
            return redirect()->to('master/karyawan')
                           ->with('error', 'ID karyawan tidak ditemukan');
        }

        $karyawan = $this->karyawanModel->find($id);
        if (!$karyawan) {
            return redirect()->to('master/karyawan')
                           ->with('error', 'Data karyawan tidak ditemukan');
        }

        $data = [
            'title'       => 'Detail Karyawan',
            'karyawan'    => $karyawan,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/karyawan') . '">Karyawan</a></li>
                <li class="breadcrumb-item active">Detail</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/karyawan/detail', $data);
    }

    /**
     * Delete employee data
     */
    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->to('master/karyawan')
                           ->with('error', 'ID karyawan tidak ditemukan');
        }

        try {
            $karyawan = $this->karyawanModel->find($id);
            if (!$karyawan) {
                throw new \RuntimeException('Data karyawan tidak ditemukan');
            }

            // Delete IonAuth user if exists
            if (!empty($karyawan->id_user)) {
                $this->db->table('tbl_ion_users')
                        ->where('id', $karyawan->id_user)
                        ->delete();
            }

            if (!$this->karyawanModel->delete($id)) {
                throw new \RuntimeException('Gagal menghapus data karyawan');
            }

            return redirect()->to(base_url('master/karyawan'))
                           ->with('success', 'Data karyawan dan user login berhasil dihapus');

        } catch (\Exception $e) {
            log_message('error', '[Karyawan::delete] ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Gagal menghapus data karyawan');
        }
    }

    /**
     * Show CSV import form
     */
    public function importForm()
    {
        $data = [
            'title'         => 'Import Data Karyawan',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/karyawan') . '">Karyawan</a></li>
                <li class="breadcrumb-item active">Import Excel</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/karyawan/import', $data);
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
                if (count($row) >= 3) { // At least nama, nik, alamat
                    $csvData[] = [
                        'nama' => trim($row[0] ?? ''),
                        'nik' => trim($row[1] ?? ''),
                        'alamat' => trim($row[2] ?? ''),
                        'no_telp' => trim($row[3] ?? ''),
                        'email' => trim($row[4] ?? ''),
                        'tanggal_lahir' => isset($row[5]) ? trim($row[5]) : null,
                        'jenis_kelamin' => trim($row[6] ?? ''),
                        'jabatan' => trim($row[7] ?? ''),
                        'tanggal_masuk' => isset($row[8]) ? trim($row[8]) : date('Y-m-d'),
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
                    if ($this->karyawanModel->insert($data)) {
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": " . implode(', ', $this->karyawanModel->errors());
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

            return redirect()->to(base_url('master/karyawan'))
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
        $filename = 'template_karyawan.xlsx';
        $filepath = FCPATH . 'assets/templates/' . $filename;

        // Create template if not exists
        if (!file_exists($filepath)) {
            $templateDir = dirname($filepath);
            if (!is_dir($templateDir)) {
                mkdir($templateDir, 0777, true);
            }

            $headers = ['Nama,NIK,Alamat,No Telp,Email,Tanggal Lahir,Jenis Kelamin,Jabatan,Tanggal Masuk,Status\n'];
        $sampleData = [
            ['John Doe,1234567890123456,Jl. Sudirman No. 1,08123456789,john@email.com,1990-01-01,L,Kasir,2024-01-01,1\n'],
            ['Jane Smith,1234567890123457,Jl. Thamrin No. 2,08123456788,jane@email.com,1992-05-15,P,Manager,2024-01-01,1\n']
        ];
        $filepath = createExcelTemplate($headers, $sampleData, $filename);
        }

        return $this->response->download($filepath, null);
    }

    /**
     * Bulk delete karyawan
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
                    if ($this->karyawanModel->delete($id)) {
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