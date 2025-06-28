<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-28
 * Github : github.com/mikhaelfelian
 * description : Controller for managing stock opname data.
 * This file represents the Opname controller.
 */

namespace App\Controllers\Gudang;

use App\Controllers\BaseController;
use App\Models\UtilSOModel;
use App\Models\GudangModel;
use App\Models\OutletModel;

class Opname extends BaseController
{
    protected $utilSOModel;
    protected $gudangModel;
    protected $outletModel;

    public function __construct()
    {
        parent::__construct();
        $this->utilSOModel = new UtilSOModel();
        $this->gudangModel = new GudangModel();
        $this->outletModel = new OutletModel();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_opname') ?? 1;
        $perPage = 10;
        $keyword = $this->request->getVar('keyword');
        $tgl = $this->request->getVar('tgl');
        $ket = $this->request->getVar('ket');

        // Build query with filters
        $builder = $this->utilSOModel;
        
        if ($tgl) {
            $builder = $builder->where('DATE(created_at)', $tgl);
        }
        
        if ($ket) {
            $builder = $builder->like('keterangan', $ket);
        }

        $opnameData = $builder->paginate($perPage, 'opname');
        
        // Get user data for each opname record
        $opnameWithUsers = [];
        foreach ($opnameData as $opname) {
            $user = $this->ionAuth->user($opname->id_user)->row();
            $opname->user_name = $user ? $user->first_name : 'Unknown User';
            $opnameWithUsers[] = $opname;
        }

        $data = [
            'title'       => 'Data Stok Opname',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'opname'      => $opnameWithUsers,
            'pager'       => $builder->pager,
            'currentPage' => $currentPage,
            'perPage'     => $perPage,
            'keyword'     => $keyword,
            'tgl'         => $tgl,
            'ket'         => $ket,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Gudang</li>
                <li class="breadcrumb-item active">Opname</li>
            '
        ];

        return view($this->theme->getThemePath() . '/gudang/opname/index', $data);
    }

    public function create()
    {
        $data = [
            'title'       => 'Form Stok Opname',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'gudang'      => $this->gudangModel->where('status', '1')->findAll(),
            'outlet'      => $this->outletModel->where('status', '1')->findAll(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('gudang/opname') . '">Opname</a></li>
                <li class="breadcrumb-item active">Tambah Opname</li>
            '
        ];

        return view($this->theme->getThemePath() . '/gudang/opname/create', $data);
    }

    public function store()
    {
        // Validate form data
        $rules = [
            'tgl_masuk' => 'required',
            'id_outlet' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get form data using explicit variable assignment pattern
        $id_user = $this->ionAuth->user()->row()->id;
        $tgl_masuk = $this->request->getPost('tgl_masuk');
        $id_outlet = $this->request->getPost('id_outlet');
        $keterangan = $this->request->getPost('keterangan');

        $data = [
            'id_user' => $id_user,
            'tgl_masuk' => $tgl_masuk,
            'id_outlet' => $id_outlet,
            'keterangan' => $keterangan,
            'status' => '0', // Draft
            'reset' => '0', // Not reset
        ];

        try {
            // Save to database
            $this->utilSOModel->insert($data);
            
            return redirect()->to(base_url('gudang/opname'))->with('success', 'Data opname berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data opname: ' . $e->getMessage());
        }
    }
} 