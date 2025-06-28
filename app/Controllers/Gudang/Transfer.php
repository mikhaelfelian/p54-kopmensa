<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-28
 * Github : github.com/mikhaelfelian
 * description : Controller for managing transfer/mutasi data.
 * This file represents the Transfer controller.
 */

namespace App\Controllers\Gudang;

use App\Controllers\BaseController;
use App\Models\TransMutasiModel;
use App\Models\GudangModel;
use App\Models\OutletModel;

class Transfer extends BaseController
{
    protected $transMutasiModel;
    protected $gudangModel;
    protected $outletModel;

    public function __construct()
    {
        parent::__construct();
        $this->transMutasiModel = new TransMutasiModel();
        $this->gudangModel = new GudangModel();
        $this->outletModel = new OutletModel();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_transfer') ?? 1;
        $perPage = 10;
        $keyword = $this->request->getVar('keyword');

        $data = [
            'title'       => 'Data Transfer/Mutasi',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'transfers'   => $this->transMutasiModel->paginate($perPage, 'transfer'),
            'pager'       => $this->transMutasiModel->pager,
            'currentPage' => $currentPage,
            'perPage'     => $perPage,
            'keyword'     => $keyword,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Gudang</li>
                <li class="breadcrumb-item active">Transfer</li>
            '
        ];

        return view($this->theme->getThemePath() . '/gudang/transfer/index', $data);
    }

    public function create()
    {
        $data = [
            'title'       => 'Tambah Transfer/Mutasi',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'gudang'      => $this->gudangModel->findAll(),
            'outlet'      => $this->outletModel->where('status', '1')->findAll(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('gudang/transfer') . '">Transfer</a></li>
                <li class="breadcrumb-item active">Tambah Transfer</li>
            '
        ];

        return view($this->theme->getThemePath() . '/gudang/transfer/create', $data);
    }

    public function store()
    {
        // Get form data using explicit variable assignment pattern
        $id_user     = $this->ionAuth->user()->row()->id;
        $tgl_masuk   = $this->request->getPost('tgl_masuk');
        $tipe        = $this->request->getPost('tipe');
        $id_gd_asal  = $this->request->getPost('id_gd_asal');
        $id_outlet   = $this->request->getPost('id_outlet');
        $keterangan  = $this->request->getPost('keterangan');

        // Validate form data
        $rules = [
            'tipe' => 'required',
            'id_outlet' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('gudang/transfer/create'))
                ->withInput()
                ->with('error', 'Validasi gagal! Silakan periksa kembali data yang dimasukkan.');
        }

        $data = [
            'id_user'      => $id_user,
            'tgl_masuk'    => tgl_indo_sys($tgl_masuk),
            'tipe'         => $tipe,
            'id_gd_asal'   => $id_gd_asal ?: 0,
            'id_outlet'    => $id_outlet ?: 0,
            'keterangan'   => $keterangan,
            'status_nota'  => '0', // Draft
            'status_terima'=> '0', // Belum
            'no_nota'      => $this->generateNotaNumber(),
        ];

        try {
            // Save to database
            $this->transMutasiModel->insert($data);
            
            return redirect()->to(base_url('gudang/transfer'))
                ->with('success', 'Data transfer berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data transfer: ' . $e->getMessage());
        }
    }

    private function generateNotaNumber()
    {
        // Generate unique nota number
        $prefix = 'TRF';
        $date = date('Ymd');
        $lastTransfer = $this->transMutasiModel->where('DATE(created_at)', date('Y-m-d'))->orderBy('id', 'DESC')->first();
        
        if ($lastTransfer) {
            $lastNumber = (int) substr($lastTransfer->no_nota, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
} 