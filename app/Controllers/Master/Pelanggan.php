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

        // Get total records for pagination
        $total = $query->countAllResults(false);

        $data = [
            'title'          => 'Data Pelanggan',
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
            'trashCount'     => $this->pelangganModel->onlyDeleted()->countAllResults()
        ];

        return $this->view($this->theme->getThemePath() . '/master/pelanggan/index', $data);
    }

    /**
     * Display create form
     */
    public function create()
    {
        $data = [
            'title'       => 'Tambah Pelanggan',
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
        try {
            $data = [
                'kode'       => $this->pelangganModel->generateKode(),
                'nama'       => $this->request->getPost('nama'),
                'no_telp'    => $this->request->getPost('no_telp'),
                'alamat'     => $this->request->getPost('alamat'),
                'kota'       => $this->request->getPost('kota'),
                'provinsi'   => $this->request->getPost('provinsi'),
                'tipe'       => $this->request->getPost('tipe'),
                'status'     => '1'
            ];

            if (!$this->pelangganModel->insert($data)) {
                throw new \RuntimeException('Gagal menyimpan data pelanggan');
            }

            return redirect()->to(base_url('master/customer'))
                           ->with('success', 'Data pelanggan berhasil ditambahkan');

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
            'title'       => 'Edit Pelanggan',
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
            $data = [
                'nama'       => $this->request->getPost('nama'),
                'no_telp'    => $this->request->getPost('no_telp'),
                'alamat'     => $this->request->getPost('alamat'),
                'kota'       => $this->request->getPost('kota'),
                'provinsi'   => $this->request->getPost('provinsi'),
                'tipe'       => $this->request->getPost('tipe')
            ];

            if (!$this->pelangganModel->update($id, $data)) {
                throw new \RuntimeException('Gagal mengupdate data pelanggan');
            }

            return redirect()->to(base_url('master/customer'))
                           ->with('success', 'Data pelanggan berhasil diupdate');

        } catch (\Exception $e) {
            log_message('error', '[Pelanggan::update] ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal mengupdate data pelanggan');
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

        $query = $this->pelangganModel->onlyDeleted();

        // Filter by name/code
        $search = $this->request->getVar('search');
        if ($search) {
            $query->groupStart()
                ->like('nama', $search)
                ->orLike('kode', $search)
                ->groupEnd();
        }

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