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
            'kode' => [
                'rules' => 'required|max_length[50]|is_unique[tbl_m_voucher.kode]',
                'errors' => [
                    'required' => 'Kode voucher harus diisi',
                    'max_length' => 'Kode voucher maksimal 50 karakter',
                    'is_unique' => 'Kode voucher sudah digunakan'
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
            'kode'          => $kode,
            'jml'           => $jml,
            'jenis_voucher' => $jenis_voucher,
            'nominal'       => $nominal,
            'jml_keluar'    => 0,
            'jml_max'       => $jml_max,
            'tgl_masuk'     => $tgl_masuk,
            'tgl_keluar'    => $tgl_keluar,
            'status'        => $status,
            'keterangan'    => $keterangan
        ];

        if ($this->voucherModel->insert($data)) {
            return redirect()->to(base_url('master/voucher'))
                ->with('success', 'Data voucher berhasil ditambahkan');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data voucher');
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
            'kode' => [
                'rules' => 'required|max_length[50]|is_unique[tbl_m_voucher.kode,id,' . $id . ']',
                'errors' => [
                    'required' => 'Kode voucher harus diisi',
                    'max_length' => 'Kode voucher maksimal 50 karakter',
                    'is_unique' => 'Kode voucher sudah digunakan'
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

        if ($this->voucherModel->update($id, $data)) {
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
}