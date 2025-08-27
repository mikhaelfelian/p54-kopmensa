<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-20
 * Github : github.com/mikhaelfelian
 * description : Controller for managing customer (pelanggan) data
 * This file represents the Pelanggan Controller.
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\PelangganModel;
use App\Models\PengaturanModel;

class Pelanggan extends BaseController
{
    protected $pelangganModel;
    protected $validation;
    protected $pengaturan;

    public function __construct()
    {
        $this->pelangganModel = new PelangganModel();
        $this->pengaturan = new PengaturanModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_pelanggan') ?? 1;
        $perPage = $this->pengaturan->pagination_limit ?? 10;

        // Start with the model query
        $query = $this->pelangganModel;

        // Filter by name/code
        $search = $this->request->getVar('search');
        if ($search) {
            $query->groupStart()
                ->like('nama', $search)
                ->orLike('kode', $search)
                ->groupEnd();
        }

        // Filter by type
        $selectedTipe = $this->request->getVar('tipe');
        if ($selectedTipe !== null && $selectedTipe !== '') {
            $query->where('tipe', $selectedTipe);
        }

        // Filter by status_hps = '0' (not deleted)
        $query->where('status_hps', '0');

        // Get total records for pagination
        $total = $query->countAllResults(false);

        $data = [
            'title'          => 'Data Pelanggan / Anggota',
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'pelanggans'     => $query->paginate($perPage, 'pelanggan'),
            'pager'          => $this->pelangganModel->pager,
            'currentPage'    => $currentPage,
            'perPage'        => $perPage,
            'total'          => $total,
            'search'         => $search,
            'selectedTipe'   => $selectedTipe,
            'selectedStatus' => null,  // Set to null since we're not using status filter
            'getTipeLabel'   => function($tipe) {
                return $this->pelangganModel->getTipeLabel($tipe);
            },
            'getStatusLabel' => function($status) {
                return $this->pelangganModel->getStatusLabel($status);
            },
            'breadcrumbs'    => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Pelanggan</li>
            ',
            'trashCount'     => $this->pelangganModel->where('status_hps', '1')->countAllResults()
        ];

        return $this->view($this->theme->getThemePath() . '/master/pelanggan/index', $data);
    }

    /**
     * Display create form
     */
    public function create()
    {
        $data = [
            'title'       => 'Form Tambah Pelanggan',
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'validation'  => $this->validation,
            'kode'        => $this->pelangganModel->generateKode(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/customer') . '">Pelanggan</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/pelanggan/create', $data);
    }

    /**
     * Store new customer data
     */
    public function store()
    {
        // Ambil input dari form
        $nama      = $this->request->getPost('nama');
        $no_telp   = $this->request->getPost('no_telp');
        $alamat    = $this->request->getPost('alamat');
        $kota      = $this->request->getPost('kota');
        $provinsi  = $this->request->getPost('provinsi');
        $limit     = $this->request->getPost('limit') ?? 0;
        $email     = $this->request->getPost('email');
        $username  = $this->request->getPost('username');
        $password  = $this->request->getPost('password');
        // tipe pelanggan/anggota = 2 (anggota/pelanggan)
        $tipe      = '2';

        // Validasi input
        $rules = [
            'nama' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Nama pelanggan harus diisi',
                    'max_length' => 'Nama maksimal 100 karakter'
                ]
            ],
            'no_telp' => [
                'rules' => 'permit_empty|max_length[20]',
                'errors' => [
                    'max_length' => 'No. Telp maksimal 20 karakter'
                ]
            ],
            'alamat' => [
                'rules' => 'required|max_length[255]',
                'errors' => [
                    'required' => 'Alamat harus diisi',
                    'max_length' => 'Alamat maksimal 255 karakter'
                ]
            ],
            'kota' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Kota harus diisi',
                    'max_length' => 'Kota maksimal 100 karakter'
                ]
            ],
            'provinsi' => [
                'rules' => 'permit_empty|max_length[100]',
                'errors' => [
                    'max_length' => 'Provinsi maksimal 100 karakter'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'required' => 'Email harus diisi',
                    'valid_email' => 'Format email tidak valid',
                    'is_unique' => 'Email sudah terdaftar'
                ]
            ],
            'username' => [
                'rules' => 'required|alpha_numeric|min_length[4]|max_length[50]|is_unique[users.username]',
                'errors' => [
                    'required' => 'Username harus diisi',
                    'alpha_numeric' => 'Username hanya boleh huruf dan angka',
                    'min_length' => 'Username minimal 4 karakter',
                    'max_length' => 'Username maksimal 50 karakter',
                    'is_unique' => 'Username sudah terdaftar'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password harus diisi',
                    'min_length' => 'Password minimal 6 karakter'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validator);
        }

        try {
            // Cek user by email/username
            $userByEmail = $this->ionAuth->where('email', $email)->users()->row();
            $userByUsername = $this->ionAuth->where('username', $username)->users()->row();
            if ($userByEmail || $userByUsername) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'User dengan email atau username tersebut sudah terdaftar.');
            }

            // Buat user baru (ion_auth)
            $additional_data = [
                'first_name' => $nama,
                'phone'      => $no_telp,
                'tipe'       => $tipe // tipe 2 = pelanggan/anggota
            ];
            // Group pelanggan/anggota, misal group id 3 (ubah sesuai kebutuhan)
            $group = 3;
            $user_id = $this->ionAuth->register($username, $password, $email, $additional_data, [$group]);
            if (!$user_id) {
                log_message('error', '[Pelanggan::store] Gagal membuat user ion_auth: ' . implode(', ', $this->ionAuth->errors_array()));
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal membuat user login. Silakan cek data user.');
            }

            // Buat data pelanggan/anggota
            $data = [
                'id_user'    => $user_id,
                'kode'       => $this->pelangganModel->generateKode(),
                'nama'       => $nama,
                'no_telp'    => $no_telp,
                'alamat'     => $alamat,
                'kota'       => $kota,
                'provinsi'   => $provinsi,
                'tipe'       => $tipe,
                'status'     => '1',
                'limit'      => $limit
            ];

            if (!$this->pelangganModel->save($data)) {
                // Rollback user jika perlu
                log_message('error', '[Pelanggan::store] Gagal menyimpan data pelanggan');
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal menyimpan data pelanggan');
            }

            return redirect()->to(base_url('master/customer'))
                ->with('success', 'Data pelanggan dan user login berhasil ditambahkan');
        } catch (\Exception $e) {
            log_message('error', '[Pelanggan::store] ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data pelanggan');
        }
    }

    /**
     * Display edit form
     */
    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->to('master/customer')
                           ->with('error', 'ID pelanggan tidak ditemukan');
        }

        $pelanggan = $this->pelangganModel->find($id);
        if (!$pelanggan) {
            return redirect()->to('master/customer')
                           ->with('error', 'Data pelanggan tidak ditemukan');
        }

        $data = [
            'title'       => 'Form Ubah Pelanggan',
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'validation'  => $this->validation,
            'pelanggan'   => $pelanggan,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/customer') . '">Pelanggan</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/pelanggan/edit', $data);
    }

    /**
     * Update customer data
     */
    public function update($id = null)
    {
        if (!$id) {
            return redirect()->to('master/customer')
                ->with('error', 'ID pelanggan tidak ditemukan');
        }

        try {
            // Use variables for all input post
            $nama      = $this->request->getPost('nama');
            $no_telp   = $this->request->getPost('no_telp');
            $alamat    = $this->request->getPost('alamat');
            $kota      = $this->request->getPost('kota');
            $provinsi  = $this->request->getPost('provinsi');
            $tipe      = $this->request->getPost('tipe');
            $status    = $this->request->getPost('status');
            $limit     = $this->request->getPost('limit') ?? 0;
            $email     = $this->request->getPost('email');
            $username  = $this->request->getPost('username');
            $password  = $this->request->getPost('password');

            // Get pelanggan data
            $pelanggan = $this->pelangganModel->find($id);
            if (!$pelanggan) {
                return redirect()->to('master/customer')
                    ->with('error', 'Data pelanggan tidak ditemukan');
            }

            // Generate username if not provided
            if (!empty($nama)) {
                $firstName      = preg_replace('/[^a-zA-Z0-9]/', '', trim($nama));
                $safeUsername   = $username ?: generateUsername($firstName);
            } else {
                $safeUsername = $username ?: null;
            }
            $safeEmail = $email ?: ($safeUsername ? $safeUsername . '@' . env('app.domain') : null);

            // Prepare additional data for ion_auth
            $additional_data = [
                'first_name' => $nama,
                'phone'      => $no_telp,
                'tipe'       => '2'
            ];

            // Handle user login update/creation
            $user_id = $pelanggan->id_user;

            if ($user_id) {
                // Update user
                $update_data = [
                    'email'      => $safeEmail,
                    'username'   => $safeUsername,
                    'first_name' => $nama,
                    'phone'      => $no_telp,
                    'tipe'       => '2'
                ];
                if (!empty($password)) {
                    $update_data['password'] = $password;
                }
                if (!$this->ionAuth->update($user_id, $update_data)) {
                    throw new \RuntimeException('Gagal mengupdate user login: ' . implode(', ', $this->ionAuth->errors_array()));
                }
            } else {
                // Only register if username and email are not null
                if (!$safeUsername || !$safeEmail) {
                    throw new \RuntimeException('Username dan Email tidak boleh kosong untuk membuat user login.');
                }

                $user_id = $this->ionAuth->register(
                    $safeUsername,
                    $password ?: $safeUsername,
                    $safeEmail,
                    $additional_data,
                    [3] // group 3 = pelanggan/anggota, adjust as needed
                );
                if (!$user_id) {
                    throw new \RuntimeException('Gagal membuat user login: ' . implode(', ', $this->ionAuth->errors_array()));
                }
            }

            // Update pelanggan data
            $data = [
                'id_user'   => $user_id,
                'nama'      => $nama,
                'no_telp'   => $no_telp,
                'alamat'    => $alamat,
                'kota'      => $kota,
                'provinsi'  => $provinsi,
                'tipe'      => $tipe,
                'status'    => $status,
                'limit'     => format_angka_db($limit),
                'email'     => $safeEmail,
                'username'  => $safeUsername
            ];

            if (!$this->pelangganModel->update($id, $data)) {
                throw new \RuntimeException('Gagal mengupdate data pelanggan');
            }

            return redirect()->to(base_url('master/customer'))
                ->with('success', 'Data pelanggan berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate data pelanggan: ' . $e->getMessage());
        }
    }

    /**
     * Display customer detail
     */
    public function detail($id = null)
    {
        if (!$id) {
            return redirect()->to('master/customer')
                           ->with('error', 'ID pelanggan tidak ditemukan');
        }

        $pelanggan = $this->pelangganModel->find($id);
        if (!$pelanggan) {
            return redirect()->to('master/customer')
                           ->with('error', 'Data pelanggan tidak ditemukan');
        }

        $data = [
            'title'       => 'Detail Pelanggan',
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'pelanggan'   => $pelanggan,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/customer') . '">Pelanggan</a></li>
                <li class="breadcrumb-item active">Detail</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/pelanggan/detail', $data);
    }

    /**
     * Delete customer (soft delete)
     */
    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->to('master/customer')
                           ->with('error', 'ID pelanggan tidak ditemukan');
        }

        try {
            $pelanggan = $this->pelangganModel->find($id);
            if (!$pelanggan) {
                throw new \RuntimeException('Data pelanggan tidak ditemukan');
            }

            if (!$this->pelangganModel->delete($id)) {
                throw new \RuntimeException('Gagal menghapus data pelanggan');
            }

            return redirect()->to(base_url('master/customer'))
                           ->with('success', 'Data pelanggan berhasil dihapus');

        } catch (\Exception $e) {
            log_message('error', '[Pelanggan::delete] ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Gagal menghapus data pelanggan');
        }
    }

    /**
     * Display trash (deleted customers)
     */
    public function trash()
    {
        $currentPage = $this->request->getVar('page_pelanggan') ?? 1;
        $perPage = $this->pengaturan->pagination_limit ?? 10;

        // Start with the model query
        $query = $this->pelangganModel;

        // Filter by name/code
        $search = $this->request->getVar('search');
        if ($search) {
            $query->groupStart()
                ->like('nama', $search)
                ->orLike('kode', $search)
                ->groupEnd();
        }

        // Filter by status_hps = '1' (deleted)
        $query->where('status_hps', '1');

        // Get total records for pagination
        $total = $query->countAllResults(false);

        $data = [
            'title'          => 'Trash Pelanggan',
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'pelanggans'     => $query->paginate($perPage, 'pelanggan'),
            'pager'          => $this->pelangganModel->pager,
            'currentPage'    => $currentPage,
            'perPage'        => $perPage,
            'total'          => $total,
            'search'         => $search,
            'breadcrumbs'    => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/customer') . '">Pelanggan</a></li>
                <li class="breadcrumb-item active">Trash</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/pelanggan/trash', $data);
    }

    /**
     * Restore deleted customer
     */
    public function restore($id = null)
    {
        if (!$id) {
            return redirect()->to('master/customer/trash')
                           ->with('error', 'ID pelanggan tidak ditemukan');
        }

        try {
            if (!$this->pelangganModel->restore($id)) {
                throw new \RuntimeException('Gagal mengembalikan data pelanggan');
            }

            return redirect()->to(base_url('master/customer/trash'))
                           ->with('success', 'Data pelanggan berhasil dikembalikan');

        } catch (\Exception $e) {
            log_message('error', '[Pelanggan::restore] ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Gagal mengembalikan data pelanggan');
        }
    }

    /**
     * Permanently delete customer
     */
    public function delete_permanent($id = null)
    {
        if (!$id) {
            return redirect()->to('master/customer/trash')
                           ->with('error', 'ID pelanggan tidak ditemukan');
        }

        try {
            if (!$this->pelangganModel->delete($id, true)) {
                throw new \RuntimeException('Gagal menghapus permanen data pelanggan');
            }

            return redirect()->to(base_url('master/customer/trash'))
                           ->with('success', 'Data pelanggan berhasil dihapus permanen');

        } catch (\Exception $e) {
            log_message('error', '[Pelanggan::delete_permanent] ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Gagal menghapus permanen data pelanggan');
        }
    }
} 