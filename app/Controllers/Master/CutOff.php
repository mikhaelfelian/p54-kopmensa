<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-09-11
 * Github: github.com/mikhaelfelian
 * Description: Controller for handling cut-off functionality
 * This file represents the CutOff controller.
 */

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\CutOffModel;
use App\Models\TransJualModel;
use App\Models\TransBeliModel;
use App\Models\InputStokModel;
use App\Models\GudangModel;
use App\Models\PettyModel;

class CutOff extends BaseController
{
    protected $cutOffModel;
    protected $transJualModel;
    protected $transBeliModel;
    protected $inputStokModel;
    protected $gudangModel;
    protected $pettyModel;
    protected $validation;

    public function __construct()
    {
        parent::__construct();
        $this->cutOffModel = new CutOffModel();
        $this->transJualModel = new TransJualModel();
        $this->transBeliModel = new TransBeliModel();
        $this->inputStokModel = new InputStokModel();
        $this->gudangModel = new GudangModel();
        $this->pettyModel = new PettyModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_cutoff') ?? 1;
        $perPage = 10;
        
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idGudang = $this->request->getGet('id_gudang');
        $status = $this->request->getGet('status');

        // Build query
        $builder = $this->cutOffModel->select('
                tbl_cut_off.*,
                tbl_m_gudang.nama as gudang_nama,
                tbl_ion_users.first_name as user_name
            ')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_cut_off.id_gudang', 'left')
            ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_cut_off.id_user', 'left');

        // Apply filters
        if ($startDate && $endDate) {
            $builder->where('DATE(tbl_cut_off.tgl_cutoff) >=', $startDate)
                   ->where('DATE(tbl_cut_off.tgl_cutoff) <=', $endDate);
        }

        if ($idGudang) {
            $builder->where('tbl_cut_off.id_gudang', $idGudang);
        }

        if ($status !== null && $status !== '') {
            $builder->where('tbl_cut_off.status', $status);
        }

        $cutOffs = $builder->orderBy('tbl_cut_off.tgl_cutoff', 'DESC')
                          ->paginate($perPage, 'cutoff');

        // Get filter options
        $gudangList = $this->gudangModel->where('status', '1')->findAll();

        $data = [
            'title' => 'Cut-Off Management',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'cutOffs' => $cutOffs,
            'pager' => $this->cutOffModel->pager,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'idGudang' => $idGudang,
            'status' => $status,
            'gudangList' => $gudangList,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Cut-Off</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/cutoff/index', $data);
    }

    public function create()
    {
        // Get available warehouses
        $gudangList = $this->gudangModel->where('status', '1')->findAll();

        $data = [
            'title' => 'Buat Cut-Off Baru',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'gudangList' => $gudangList,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/cutoff') . '">Cut-Off</a></li>
                <li class="breadcrumb-item active">Buat Baru</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/cutoff/create', $data);
    }

    public function store()
    {
        $rules = [
            'tgl_cutoff' => 'required|valid_date',
            'id_gudang' => 'required|integer',
            'keterangan' => 'max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $tglCutoff = $this->request->getPost('tgl_cutoff');
        $idGudang = $this->request->getPost('id_gudang');
        $keterangan = $this->request->getPost('keterangan');

        // Check if cut-off already exists for this date and warehouse
        $existingCutOff = $this->cutOffModel->where('DATE(tgl_cutoff)', $tglCutoff)
                                           ->where('id_gudang', $idGudang)
                                           ->first();

        if ($existingCutOff) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Cut-off sudah ada untuk tanggal dan gudang tersebut');
        }

        // Calculate cut-off data
        $cutoffData = $this->calculateCutOffData($tglCutoff, $idGudang);

        // Generate cut-off number
        $cutoffNumber = $this->generateCutOffNumber($tglCutoff);

        $data = [
            'no_cutoff' => $cutoffNumber,
            'tgl_cutoff' => $tglCutoff . ' ' . date('H:i:s'),
            'id_gudang' => $idGudang,
            'id_user' => $this->ionAuth->user()->row()->id,
            'total_penjualan' => $cutoffData['total_penjualan'],
            'total_pembelian' => $cutoffData['total_pembelian'],
            'total_kas_masuk' => $cutoffData['total_kas_masuk'],
            'total_kas_keluar' => $cutoffData['total_kas_keluar'],
            'saldo_kas' => $cutoffData['saldo_kas'],
            'keterangan' => $keterangan,
            'status' => '1', // Active
            'tgl_masuk' => date('Y-m-d H:i:s')
        ];

        if ($this->cutOffModel->insert($data)) {
            // Update related transactions status (mark as cut-off)
            $this->updateTransactionsStatus($tglCutoff, $idGudang);

            return redirect()->to('master/cutoff')
                           ->with('success', 'Cut-off berhasil dibuat');
        } else {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal membuat cut-off');
        }
    }

    public function detail($id)
    {
        $cutOff = $this->cutOffModel->select('
                tbl_cut_off.*,
                tbl_m_gudang.nama as gudang_nama,
                tbl_ion_users.first_name as user_name
            ')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_cut_off.id_gudang', 'left')
            ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_cut_off.id_user', 'left')
            ->find($id);

        if (!$cutOff) {
            return redirect()->to('master/cutoff')->with('error', 'Cut-off tidak ditemukan');
        }

        // Get detailed transactions for this cut-off period
        $tglCutoff = date('Y-m-d', strtotime($cutOff->tgl_cutoff));
        
        $penjualan = $this->transJualModel->select('*')
                                         ->where('DATE(tgl_masuk)', $tglCutoff)
                                         ->where('id_gudang', $cutOff->id_gudang)
                                         ->where('status_nota', '1')
                                         ->findAll();

        $pembelian = $this->transBeliModel->select('*')
                                         ->where('DATE(tgl_masuk)', $tglCutoff)
                                         ->where('status_nota', '1')
                                         ->findAll();

        $kasTransaksi = $this->pettyModel->select('*')
                                        ->where('DATE(tgl_masuk)', $tglCutoff)
                                        ->where('id_gudang', $cutOff->id_gudang)
                                        ->findAll();

        $data = [
            'title' => 'Detail Cut-Off - ' . $cutOff->no_cutoff,
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'cutOff' => $cutOff,
            'penjualan' => $penjualan,
            'pembelian' => $pembelian,
            'kasTransaksi' => $kasTransaksi,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item"><a href="' . base_url('master/cutoff') . '">Cut-Off</a></li>
                <li class="breadcrumb-item active">Detail</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/master/cutoff/detail', $data);
    }

    public function delete($id)
    {
        $cutOff = $this->cutOffModel->find($id);
        
        if (!$cutOff) {
            return redirect()->to('master/cutoff')->with('error', 'Cut-off tidak ditemukan');
        }

        // Check if cut-off can be deleted (only if not finalized)
        if ($cutOff->status == '2') { // Finalized
            return redirect()->to('master/cutoff')->with('error', 'Cut-off yang sudah difinalisasi tidak dapat dihapus');
        }

        if ($this->cutOffModel->delete($id)) {
            return redirect()->to('master/cutoff')->with('success', 'Cut-off berhasil dihapus');
        } else {
            return redirect()->to('master/cutoff')->with('error', 'Gagal menghapus cut-off');
        }
    }

    public function finalize($id)
    {
        $cutOff = $this->cutOffModel->find($id);
        
        if (!$cutOff) {
            return redirect()->to('master/cutoff')->with('error', 'Cut-off tidak ditemukan');
        }

        if ($cutOff->status == '2') {
            return redirect()->to('master/cutoff')->with('error', 'Cut-off sudah difinalisasi');
        }

        // Update status to finalized
        $updateData = [
            'status' => '2', // Finalized
            'tgl_finalisasi' => date('Y-m-d H:i:s'),
            'id_user_finalisasi' => $this->ionAuth->user()->row()->id
        ];

        if ($this->cutOffModel->update($id, $updateData)) {
            return redirect()->to('master/cutoff')->with('success', 'Cut-off berhasil difinalisasi');
        } else {
            return redirect()->to('master/cutoff')->with('error', 'Gagal memfinalisasi cut-off');
        }
    }

    private function calculateCutOffData($tglCutoff, $idGudang)
    {
        // Calculate sales for the day
        $penjualan = $this->transJualModel->select('SUM(jml_total) as total')
                                         ->where('DATE(tgl_masuk)', $tglCutoff)
                                         ->where('id_gudang', $idGudang)
                                         ->where('status_nota', '1')
                                         ->first();

        // Calculate purchases for the day
        $pembelian = $this->transBeliModel->select('SUM(jml_total) as total')
                                         ->where('DATE(tgl_masuk)', $tglCutoff)
                                         ->where('status_nota', '1')
                                         ->first();

        // Calculate cash in/out for the day
        $kasIn = $this->pettyModel->select('SUM(nominal) as total')
                                 ->where('DATE(tgl_masuk)', $tglCutoff)
                                 ->where('id_gudang', $idGudang)
                                 ->where('tipe', 'masuk')
                                 ->first();

        $kasOut = $this->pettyModel->select('SUM(nominal) as total')
                                  ->where('DATE(tgl_masuk)', $tglCutoff)
                                  ->where('id_gudang', $idGudang)
                                  ->where('tipe', 'keluar')
                                  ->first();

        $totalPenjualan = (float) ($penjualan->total ?? 0);
        $totalPembelian = (float) ($pembelian->total ?? 0);
        $totalKasIn = (float) ($kasIn->total ?? 0);
        $totalKasOut = (float) ($kasOut->total ?? 0);

        return [
            'total_penjualan' => $totalPenjualan,
            'total_pembelian' => $totalPembelian,
            'total_kas_masuk' => $totalKasIn,
            'total_kas_keluar' => $totalKasOut,
            'saldo_kas' => $totalPenjualan + $totalKasIn - $totalKasOut
        ];
    }

    private function generateCutOffNumber($tglCutoff)
    {
        $date = date('Ymd', strtotime($tglCutoff));
        $lastCutOff = $this->cutOffModel->like('no_cutoff', 'CO-' . $date, 'after')
                                       ->orderBy('no_cutoff', 'DESC')
                                       ->first();

        if ($lastCutOff) {
            $lastNumber = (int) substr($lastCutOff->no_cutoff, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'CO-' . $date . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    private function updateTransactionsStatus($tglCutoff, $idGudang)
    {
        // Mark sales transactions as cut-off
        $this->transJualModel->where('DATE(tgl_masuk)', $tglCutoff)
                            ->where('id_gudang', $idGudang)
                            ->where('status_nota', '1')
                            ->set(['status_cutoff' => '1'])
                            ->update();

        // Mark cash transactions as cut-off
        $this->pettyModel->where('DATE(tgl_masuk)', $tglCutoff)
                        ->where('id_gudang', $idGudang)
                        ->set(['status_cutoff' => '1'])
                        ->update();
    }
}
