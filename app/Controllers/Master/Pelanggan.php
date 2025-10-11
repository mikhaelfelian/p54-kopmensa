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
use App\Models\TransJualModel;

class Pelanggan extends BaseController
{
    protected $pelangganModel;
    protected $validation;
    protected $pengaturan;
    protected $transJualModel;

    public function __construct()
    {
        $this->pelangganModel = new PelangganModel();
        $this->pengaturan = new PengaturanModel();
        $this->transJualModel = new TransJualModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $curr_page  = $this->request->getVar('page_pelanggan') ?? 1;
        $per_page   = 10;
        $query      = $this->request->getVar('keyword') ?? '';

        // Apply search filter if keyword exists
        if ($query) {
            $this->pelangganModel->groupStart()
                ->like('nama', $query)
                ->orLike('kode', $query)
                ->orLike('no_telp', $query)
                ->orLike('alamat', $query)
                ->groupEnd();
        }

        $data = [
            'title'         => 'Data Pelanggan',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'pelanggan'     => $this->pelangganModel->paginate($per_page, 'pelanggan'),
            'pager'         => $this->pelangganModel->pager,
            'currentPage'   => $curr_page,
            'perPage'       => $per_page,
            'keyword'       => $query,
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Pelanggan</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/pelanggan/index', $data);
    }

    /**
     * Reset member account
     */
    public function resetAccount($id)
    {
        $pelanggan = $this->pelangganModel->find($id);
        
        if (!$pelanggan) {
            return redirect()->to('master/pelanggan')->with('error', 'Member tidak ditemukan');
        }

        // Reset account by clearing blocked status and notes
        $data = [
            'status' => '1', // Active
            'blocked_reason' => null,
            'blocked_date' => null,
            'blocked_by' => null,
            'tgl_ubah' => date('Y-m-d H:i:s')
        ];

        if ($this->pelangganModel->update($id, $data)) {
            // Also reset user account if exists
            if ($pelanggan->id_user) {
                $ionAuth = new \IonAuth\Libraries\IonAuth();
                $ionAuth->activate($pelanggan->id_user);
            }

            return redirect()->to('master/pelanggan')->with('success', 'Account berhasil direset');
        } else {
            return redirect()->to('master/pelanggan')->with('error', 'Gagal mereset account');
        }
    }

    /**
     * Block member account
     */
    public function blockAccount($id)
    {
        $pelanggan = $this->pelangganModel->find($id);
        
        if (!$pelanggan) {
            return redirect()->to('master/pelanggan')->with('error', 'Member tidak ditemukan');
        }

        $reason = $this->request->getPost('reason');
        
        if (empty($reason)) {
            return redirect()->back()->with('error', 'Alasan pemblokiran harus diisi');
        }

        // Block account
        $data = [
            'status' => '0', // Blocked
            'blocked_reason' => $reason,
            'blocked_date' => date('Y-m-d H:i:s'),
            'blocked_by' => $this->ionAuth->user()->row()->id,
            'tgl_ubah' => date('Y-m-d H:i:s')
        ];

        if ($this->pelangganModel->update($id, $data)) {
            // Also deactivate user account if exists
            if ($pelanggan->id_user) {
                $ionAuth = new \IonAuth\Libraries\IonAuth();
                $ionAuth->deactivate($pelanggan->id_user);
            }

            return redirect()->to('master/pelanggan')->with('success', 'Account berhasil diblokir');
        } else {
            return redirect()->to('master/pelanggan')->with('error', 'Gagal memblokir account');
        }
    }

    /**
     * Show add member form
     */
    public function addMember()
    {
        $data = [
            'title' => 'Tambah Member Baru',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'kode' => $this->pelangganModel->generateKode(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/pelanggan') . '">Pelanggan</a></li>
                <li class="breadcrumb-item active">Tambah Member</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/pelanggan/add_member', $data);
    }

    /**
     * Store new member
     */
    public function storeMember()
    {
        $rules = [
            'nama' => 'required|min_length[3]|max_length[100]',
            'no_telp' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[100]',
            'alamat' => 'permit_empty|max_length[255]',
            'username' => 'required|min_length[3]|max_length[50]|is_unique[tbl_ion_users.username]',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $nama     = $this->request->getPost('nama');
        $noTelp   = $this->request->getPost('no_telp');
        $email    = $this->request->getPost('email');
        $alamat   = $this->request->getPost('alamat');
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Create user account first
        $ionAuth = new \IonAuth\Libraries\IonAuth();
        $groupId = 7; // Member group ID

        $userId = $ionAuth->register($username, $password, $email, [
            'first_name' => $nama,
            'last_name' => ''
        ], [$groupId]);

        if (!$userId) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal membuat user account: ' . implode(', ', $ionAuth->errors()));
        }

        // Create member record
        $memberData = [
            'kode'        => $this->pelangganModel->generateKode(),
            'nama'        => $nama,
            'no_telp'     => $noTelp,
            'email'       => $email,
            'alamat'      => $alamat,
            'tipe'        => '1', // Member type
            'status'      => '1', // Active
            'id_user'     => $userId,
            'tgl_masuk'   => date('Y-m-d H:i:s'),
            'status_hps'  => '0'
        ];

        if ($this->pelangganModel->insert($memberData)) {
            return redirect()->to('master/pelanggan')
                           ->with('success', 'Member baru berhasil ditambahkan');
        } else {
            // Rollback user creation if member creation fails
            $ionAuth->deleteUser($userId);
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menambahkan member');
        }
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
            $group = 7;
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

    /**
     * Get user information for user management modal
     */
    public function get_user_info($user_id = null)
    {
        if (!$user_id) {
            return $this->response->setJSON(['success' => false, 'message' => 'User ID required']);
        }

        try {
            $user = $this->ionAuth->user($user_id)->row();
            if (!$user) {
                return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
            }

            $data = [
                'success' => true,
                'data' => [
                    'username' => $user->username,
                    'email' => $user->email,
                    'active' => $user->active == 1,
                    'last_login' => $user->last_login ? date('Y-m-d H:i:s', $user->last_login) : 'Never'
                ]
            ];

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', '[Pelanggan::get_user_info] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error retrieving user info']);
        }
    }

    /**
     * Reset user password
     */
    public function reset_password()
    {
        $user_id = $this->request->getPost('user_id');
        
        if (!$user_id) {
            return $this->response->setJSON(['success' => false, 'message' => 'User ID required']);
        }

        try {
            // Generate new password
            $new_password = random_string('alnum', 8);
            
            // Update password
            if ($this->ionAuth->update($user_id, ['password' => $new_password])) {
                return $this->response->setJSON([
                    'success' => true, 
                    'new_password' => $new_password,
                    'message' => 'Password reset successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Failed to reset password: ' . implode(', ', $this->ionAuth->errors_array())
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', '[Pelanggan::reset_password] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error resetting password']);
        }
    }

    /**
     * Generate new username
     */
    public function generate_username()
    {
        $user_id = $this->request->getPost('user_id');
        
        if (!$user_id) {
            return $this->response->setJSON(['success' => false, 'message' => 'User ID required']);
        }

        try {
            // Generate new username based on first name + random number
            $user = $this->ionAuth->user($user_id)->row();
            $first_name = $user->first_name;
            $new_username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $first_name)) . rand(100, 999);
            
            // Ensure username is unique
            $counter = 1;
            $original_username = $new_username;
            while ($this->ionAuth->where('username', $new_username)->users()->row()) {
                $new_username = $original_username . $counter;
                $counter++;
            }
            
            // Update username
            if ($this->ionAuth->update($user_id, ['username' => $new_username])) {
                return $this->response->setJSON([
                    'success' => true, 
                    'new_username' => $new_username,
                    'message' => 'Username generated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Failed to generate username: ' . implode(', ', $this->ionAuth->errors_array())
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', '[Pelanggan::generate_username] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error generating username']);
        }
    }

    /**
     * Toggle user block status
     */
    public function toggle_block()
    {
        $user_id = $this->request->getPost('user_id');
        $action = $this->request->getPost('action');
        
        if (!$user_id || !in_array($action, ['block', 'unblock'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid parameters']);
        }

        try {
            $active = ($action === 'unblock') ? 1 : 0;
            
            if ($this->ionAuth->update($user_id, ['active' => $active])) {
                return $this->response->setJSON([
                    'success' => true, 
                    'message' => 'Account ' . $action . 'ed successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Failed to ' . $action . ' account: ' . implode(', ', $this->ionAuth->errors_array())
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', '[Pelanggan::toggle_block] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error updating account status']);
        }
    }

    /**
     * Get user activity logs
     */
    public function get_user_logs($user_id = null)
    {
        if (!$user_id) {
            return $this->response->setJSON(['success' => false, 'message' => 'User ID required']);
        }

        try {
            // This would require a user_logs table or similar
            // For now, return empty array - you can implement this based on your logging system
            $data = [
                'success' => true,
                'data' => []
            ];

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', '[Pelanggan::get_user_logs] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error retrieving logs']);
        }
    }

    /**
     * Get customer purchase history
     */
    public function get_purchase_history($customer_id = null)
    {
        if (!$customer_id) {
            return $this->response->setJSON(['success' => false, 'message' => 'Customer ID required']);
        }

        try {
            // Get purchase history from tbl_trans_jual
            $purchases = $this->transJualModel->where('id_pelanggan', $customer_id)
                                      ->orderBy('created_at', 'DESC')
                                      ->limit(20)
                                      ->findAll();

            $data = [];
            foreach ($purchases as $purchase) {
                $data[] = [
                    'tanggal' => date('Y-m-d H:i', strtotime($purchase->created_at)),
                    'no_invoice' => $purchase->no_invoice ?? 'N/A',
                    'total' => number_format($purchase->total ?? 0, 0, ',', '.'),
                    'status' => 'Completed'
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Pelanggan::get_purchase_history] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error retrieving purchase history']);
        }
    }

    /**
     * Show CSV import form
     */
    public function importForm()
    {
        $data = [
            'title'         => 'Import Data Pelanggan',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'breadcrumbs'   => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/customer') . '">Pelanggan</a></li>
                <li class="breadcrumb-item active">Import CSV</li>
            '
        ];

        return view($this->theme->getThemePath() . '/master/pelanggan/import', $data);
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
                if (count($row) >= 3) { // At least nama, no_telp, alamat
                    $csvData[] = [
                        'nama' => trim($row[0]),
                        'no_telp' => isset($row[1]) ? trim($row[1]) : '',
                        'alamat' => isset($row[2]) ? trim($row[2]) : '',
                        'email' => isset($row[3]) ? trim($row[3]) : '',
                        'tanggal_lahir' => isset($row[4]) ? trim($row[4]) : null,
                        'jenis_kelamin' => isset($row[5]) ? trim($row[5]) : '',
                        'keterangan' => isset($row[6]) ? trim($row[6]) : '',
                        'status' => isset($row[7]) ? trim($row[7]) : '1'
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
                    if ($this->pelangganModel->insert($data)) {
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 2) . ": " . implode(', ', $this->pelangganModel->errors());
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

            return redirect()->to(base_url('master/customer'))
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
        $filename = 'template_pelanggan.csv';
        $filepath = FCPATH . 'assets/templates/' . $filename;
        
        // Create template if not exists
        if (!file_exists($filepath)) {
            $templateDir = dirname($filepath);
            if (!is_dir($templateDir)) {
                mkdir($templateDir, 0777, true);
            }
            
            $template = "Nama,No Telp,Alamat,Email,Tanggal Lahir,Jenis Kelamin,Keterangan,Status\n";
            $template .= "John Doe,08123456789,Jl. Sudirman No. 1,john@email.com,1990-01-01,L,Pelanggan VIP,1\n";
            $template .= "Jane Smith,08123456788,Jl. Thamrin No. 2,jane@email.com,1992-05-15,P,Pelanggan reguler,1\n";
            
            file_put_contents($filepath, $template);
        }
        
        return $this->response->download($filepath, null);
    }
} 