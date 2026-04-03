<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\ShiftScheduleModel;
use App\Models\GudangModel;

class ShiftSchedule extends BaseController
{
    protected $shiftScheduleModel;
    protected $gudangModel;
    protected $ionAuth;
    protected $pengaturan;

    public function __construct()
    {
        $this->shiftScheduleModel = new ShiftScheduleModel();
        $this->gudangModel        = new GudangModel();
        $this->ionAuth            = new \IonAuth\Libraries\IonAuth();
        $pengaturanModel          = new \App\Models\PengaturanModel();
        $this->pengaturan         = $pengaturanModel->getSettings();
    }

    public function index()
    {
        $perPage = $this->pengaturan->pagination_limit ?? 25;
        $rows = $this->shiftScheduleModel
            ->select('tbl_m_shift_schedule.*, tbl_m_gudang.nama as outlet_nama')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_shift_schedule.outlet_id', 'left')
            ->where('tbl_m_gudang.status_hps', '0')
            ->orderBy('tbl_m_shift_schedule.outlet_id', 'ASC')
            ->orderBy('tbl_m_shift_schedule.day_of_week', 'ASC')
            ->paginate($perPage, 'shift_schedule');

        $data = [
            'title'       => 'Jadwal Shift (Master)',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'rows'        => $rows,
            'pager'       => $this->shiftScheduleModel->pager,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Jadwal Shift</li>
            ',
        ];

        return view($this->theme->getThemePath() . '/master/shift_schedule/index', $data);
    }

    public function create()
    {
        $data = [
            'title'       => 'Tambah Jadwal Shift',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'outlets'     => $this->gudangModel->getOutlets(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/shift-schedule') . '">Jadwal Shift</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            ',
        ];

        return view($this->theme->getThemePath() . '/master/shift_schedule/create', $data);
    }

    public function store()
    {
        $rules = [
            'outlet_id'    => 'required|integer',
            'day_of_week'  => 'required|integer|greater_than[0]|less_than[8]',
            'jam_buka'     => 'permit_empty',
            'jam_tutup'    => 'permit_empty',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali isian form.');
        }

        $outletId   = (int) $this->request->getPost('outlet_id');
        $dayOfWeek  = (int) $this->request->getPost('day_of_week');
        $exists = $this->shiftScheduleModel->where('outlet_id', $outletId)->where('day_of_week', $dayOfWeek)->first();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Jadwal untuk outlet dan hari ini sudah ada.');
        }

        $this->shiftScheduleModel->insert([
            'outlet_id'   => $outletId,
            'day_of_week' => $dayOfWeek,
            'jam_buka'    => $this->request->getPost('jam_buka') ?: null,
            'jam_tutup'   => $this->request->getPost('jam_tutup') ?: null,
            'keterangan'  => $this->request->getPost('keterangan'),
            'status'      => $this->request->getPost('status') === '0' ? '0' : '1',
        ]);

        return redirect()->to(base_url('master/shift-schedule'))->with('success', 'Jadwal berhasil disimpan.');
    }

    public function edit($id)
    {
        $row = $this->shiftScheduleModel->find($id);
        if (! $row) {
            return redirect()->to(base_url('master/shift-schedule'))->with('error', 'Data tidak ditemukan.');
        }

        $data = [
            'title'       => 'Ubah Jadwal Shift',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'row'         => $row,
            'outlets'     => $this->gudangModel->getOutlets(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('master/shift-schedule') . '">Jadwal Shift</a></li>
                <li class="breadcrumb-item active">Ubah</li>
            ',
        ];

        return view($this->theme->getThemePath() . '/master/shift_schedule/edit', $data);
    }

    public function update($id)
    {
        $row = $this->shiftScheduleModel->find($id);
        if (! $row) {
            return redirect()->to(base_url('master/shift-schedule'))->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'outlet_id'    => 'required|integer',
            'day_of_week'  => 'required|integer|greater_than[0]|less_than[8]',
            'jam_buka'     => 'permit_empty',
            'jam_tutup'    => 'permit_empty',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali isian form.');
        }

        $outletId  = (int) $this->request->getPost('outlet_id');
        $dayOfWeek = (int) $this->request->getPost('day_of_week');
        $dup = $this->shiftScheduleModel
            ->where('outlet_id', $outletId)
            ->where('day_of_week', $dayOfWeek)
            ->where('id !=', $id)
            ->first();
        if ($dup) {
            return redirect()->back()->withInput()->with('error', 'Jadwal untuk outlet dan hari ini sudah digunakan data lain.');
        }

        $this->shiftScheduleModel->update($id, [
            'outlet_id'   => $outletId,
            'day_of_week' => $dayOfWeek,
            'jam_buka'    => $this->request->getPost('jam_buka') ?: null,
            'jam_tutup'   => $this->request->getPost('jam_tutup') ?: null,
            'keterangan'  => $this->request->getPost('keterangan'),
            'status'      => $this->request->getPost('status') === '0' ? '0' : '1',
        ]);

        return redirect()->to(base_url('master/shift-schedule'))->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function delete($id)
    {
        if (! $this->request->is('post')) {
            return redirect()->to(base_url('master/shift-schedule'))->with('error', 'Metode tidak diizinkan.');
        }

        $row = $this->shiftScheduleModel->find($id);
        if (! $row) {
            return redirect()->to(base_url('master/shift-schedule'))->with('error', 'Data tidak ditemukan.');
        }
        $this->shiftScheduleModel->delete($id);

        return redirect()->to(base_url('master/shift-schedule'))->with('success', 'Jadwal dihapus.');
    }
}
