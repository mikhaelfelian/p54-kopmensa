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

    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
        $this->pengaturan = new PengaturanModel();
        $this->validation = \Config\Services::validation();
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

        // Filter by status
        $selectedStatus = $this->request->getVar('status');
        if ($selectedStatus !== null && $selectedStatus !== '') {
            $query->where('status', $selectedStatus);
        }

        $data = [
            'title'          => 'Data Karyawan',
            'karyawans'      => $query->paginate($perPage, 'karyawan'),
            'pager'          => $this->karyawanModel->pager,
            'currentPage'    => $currentPage,
            'perPage'        => $perPage,
            'search'         => $search,
            'selectedStatus' => $selectedStatus,
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
            $nama_pgl           = $this->request->getPost('nama_pgl');
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
            $jabatan            = $this->request->getPost('jabatan');
            $status             = $this->request->getPost('status');

            // Prepare user data for ion_auth
            $user_email    = $email ?: strtolower(str_replace(' ', '', $nama)) . '@example.com';
            $user_username = $username ?: strtolower(str_replace(' ', '', $nama_pgl));
            $user_password = $password ?: 'password123'; // Default password, should be changed
            $additional_data = [
                'first_name' => $nama,
                'last_name'  => $nama_pgl,
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
                'nama_pgl'        => $nama_pgl,
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
                'status'          => $status
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

        $data = [
            'title'       => 'Edit Karyawan',
            'validation'  => $this->validation,
            'karyawan'    => $karyawan,
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
            $nama = $this->request->getPost('nama');
            $nama_pgl = $this->request->getPost('nama_pgl');
            $groups   = $this->ionAuth->group($this->request->getPost('id_user_group'))->row();

            $data = [
                'id_user_group'   => $this->request->getPost('id_user_group'),
                'nik'             => $this->request->getPost('nik'),
                'nama'            => $this->request->getPost('nama'),
                'nama_pgl'        => $nama_pgl,
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
                'status'          => $this->request->getPost('status')
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

            if (!$this->karyawanModel->delete($id)) {
                throw new \RuntimeException('Gagal menghapus data karyawan');
            }

            return redirect()->to(base_url('master/karyawan'))
                           ->with('success', 'Data karyawan berhasil dihapus');

        } catch (\Exception $e) {
            log_message('error', '[Karyawan::delete] ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Gagal menghapus data karyawan');
        }
    }
} 