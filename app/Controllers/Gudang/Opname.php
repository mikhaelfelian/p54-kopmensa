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
use App\Models\ItemModel;
use App\Models\ItemStokModel;
use App\Models\ItemHistModel;

class Opname extends BaseController
{
    protected $utilSOModel;
    protected $gudangModel;
    protected $outletModel;
    protected $itemModel;
    protected $itemStokModel;
    protected $itemHistModel;

    public function __construct()
    {
        parent::__construct();
        $this->utilSOModel = new UtilSOModel();
        $this->gudangModel = new GudangModel();
        $this->outletModel = new OutletModel();
        $this->itemModel = new ItemModel();
        $this->itemStokModel = new ItemStokModel();
        $this->itemHistModel = new ItemHistModel();
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
        
        // Get user data and location info for each opname record
        $opnameWithUsers = [];
        foreach ($opnameData as $opname) {
            $user = $this->ionAuth->user($opname->id_user)->row();
            $opname->user_name = $user ? $user->first_name : 'Unknown User';
            
            // Determine opname type and location
            if ($opname->id_gudang > 0) {
                $gudang = $this->gudangModel->find($opname->id_gudang);
                $opname->opname_type = 'Gudang';
                $opname->location_name = $gudang ? $gudang->gudang : 'N/A';
            } elseif ($opname->id_outlet > 0) {
                $outlet = $this->outletModel->find($opname->id_outlet);
                $opname->opname_type = 'Outlet';
                $opname->location_name = $outlet ? $outlet->nama : 'N/A';
            } else {
                $opname->opname_type = 'Unknown';
                $opname->location_name = 'N/A';
            }
            
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
        // Get opname type and set dynamic validation rules
        $opnameType = $this->request->getPost('opname_type');
        
        $rules = [
            'tgl_masuk' => 'required',
            'opname_type' => 'required|in_list[gudang,outlet]',
        ];
        
        // Dynamic validation based on opname type
        if ($opnameType === 'gudang') {
            $rules['id_gudang'] = 'required|numeric';
        } elseif ($opnameType === 'outlet') {
            $rules['id_outlet'] = 'required|numeric';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get form data using explicit variable assignment pattern
        $id_user = $this->ionAuth->user()->row()->id;
        $tgl_masuk = $this->request->getPost('tgl_masuk');
        $id_gudang = $this->request->getPost('id_gudang');
        $id_outlet = $this->request->getPost('id_outlet');
        $keterangan = $this->request->getPost('keterangan');

        $data = [
            'id_user' => $id_user,
            'id_gudang' => $opnameType === 'gudang' ? $id_gudang : 0,
            'id_outlet' => $opnameType === 'outlet' ? $id_outlet : 0,
            'tgl_masuk' => $tgl_masuk,
            'keterangan' => $keterangan,
            'status' => '0', // Draft
            'reset' => '0', // Not reset
        ];

        try {
            // Save to database
            $this->utilSOModel->insert($data);
            
            $opnameTypeText = $opnameType === 'gudang' ? 'gudang' : 'outlet';
            return redirect()->to(base_url('gudang/opname'))->with('success', "Data opname {$opnameTypeText} berhasil disimpan.");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data opname: ' . $e->getMessage());
        }
    }

    public function detail($id)
    {
        $opname = $this->utilSOModel->find($id);
        if (!$opname) {
            return redirect()->to(base_url('gudang/opname'))->with('error', 'Data opname tidak ditemukan.');
        }

        // Get user data
        $user = $this->ionAuth->user($opname->id_user)->row();
        $opname->user_name = $user ? $user->first_name : 'Unknown User';

        // Get gudang data
        $gudang = $this->gudangModel->find($opname->id_gudang);

        // Get opname details
        $opnameDetails = $this->getOpnameDetails($id);

        $data = [
            'title'       => 'Detail Stok Opname',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'opname'      => $opname,
            'gudang'      => $gudang,
            'details'     => $opnameDetails,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('gudang/opname') . '">Opname</a></li>
                <li class="breadcrumb-item active">Detail</li>
            '
        ];

        return view($this->theme->getThemePath() . '/gudang/opname/detail', $data);
    }

    public function edit($id)
    {
        $opname = $this->utilSOModel->find($id);
        if (!$opname) {
            return redirect()->to(base_url('gudang/opname'))->with('error', 'Data opname tidak ditemukan.');
        }

        // Set default tgl_masuk if not exists
        if (!isset($opname->tgl_masuk)) {
            $opname->tgl_masuk = date('Y-m-d');
        }

        $data = [
            'title'       => 'Edit Stok Opname',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'opname'      => $opname,
            'gudang'      => $this->gudangModel->where('status', '1')->findAll(),
            'outlet'      => $this->outletModel->where('status', '1')->findAll(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('gudang/opname') . '">Opname</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        return view($this->theme->getThemePath() . '/gudang/opname/edit', $data);
    }

    public function update($id)
    {
        $opname = $this->utilSOModel->find($id);
        if (!$opname) {
            return redirect()->to(base_url('gudang/opname'))->with('error', 'Data opname tidak ditemukan.');
        }

        // Validate form data
        $rules = [
            'tgl_masuk' => 'required',
            'id_gudang' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'tgl_masuk' => $this->request->getPost('tgl_masuk'),
            'id_gudang' => $this->request->getPost('id_gudang'),
            'keterangan' => $this->request->getPost('keterangan'),
        ];

        try {
            $this->utilSOModel->update($id, $data);
            return redirect()->to(base_url('gudang/opname'))->with('success', 'Data opname berhasil diupdate.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate data opname: ' . $e->getMessage());
        }
    }

    public function input($id)
    {
        $opname = $this->utilSOModel->find($id);
        if (!$opname) {
            return redirect()->to(base_url('gudang/opname'))->with('error', 'Data opname tidak ditemukan.');
        }

        // Determine opname type and get location information
        $isGudangOpname = $opname->id_gudang > 0;
        $isOutletOpname = $opname->id_outlet > 0;
        
        if ($isGudangOpname) {
            // Get gudang information
            $gudang = $this->gudangModel->find($opname->id_gudang);
            $opname->gudang = $gudang ? $gudang->gudang : 'N/A';
            $opname->location_type = 'Gudang';
            $opname->location_name = $opname->gudang;
            
            // Get items for the selected gudang
            $items = $this->itemStokModel->select('
                    tbl_m_item_stok.*,
                    tbl_m_item.kode as item_kode,
                    tbl_m_item.item as item_name,
                    tbl_m_satuan.satuanBesar as satuan_name
                ')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'left')
                ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
                ->where('tbl_m_item_stok.id_gudang', $opname->id_gudang)
                ->where('tbl_m_item_stok.status', '1')
                ->findAll();
                
        } elseif ($isOutletOpname) {
            // Get outlet information
            $outlet = $this->outletModel->find($opname->id_outlet);
            $opname->outlet = $outlet ? $outlet->nama : 'N/A';
            $opname->location_type = 'Outlet';
            $opname->location_name = $opname->outlet;
            
            // Get items for the selected outlet
            $items = $this->itemStokModel->select('
                    tbl_m_item_stok.*,
                    tbl_m_item.kode as item_kode,
                    tbl_m_item.item as item_name,
                    tbl_m_satuan.satuanBesar as satuan_name
                ')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'left')
                ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
                ->where('tbl_m_item_stok.id_outlet', $opname->id_outlet)
                ->where('tbl_m_item_stok.status', '1')
                ->findAll();
        } else {
            return redirect()->to(base_url('gudang/opname'))->with('error', 'Data opname tidak valid - tidak ada gudang atau outlet yang dipilih.');
        }

        $data = [
            'title'       => 'Input Item Opname ' . $opname->location_type,
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'opname'      => $opname,
            'items'       => $items,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('gudang/opname') . '">Opname</a></li>
                <li class="breadcrumb-item active">Input Item</li>
            '
        ];

        return view($this->theme->getThemePath() . '/gudang/opname/input', $data);
    }

    public function process($id)
    {
        $opname = $this->utilSOModel->find($id);
        if (!$opname) {
            return redirect()->to(base_url('gudang/opname'))->with('error', 'Data opname tidak ditemukan.');
        }

        // Get form data
        $items = $this->request->getPost('items');
        $quantities = $this->request->getPost('quantities');
        $notes = $this->request->getPost('notes');

        if (!$items || !$quantities) {
            return redirect()->back()->with('error', 'Data item tidak lengkap.');
        }

        try {
            $this->db = \Config\Database::connect();
            $this->db->transStart();

            $utilSODetModel = new \App\Models\UtilSODetModel();

            foreach ($items as $index => $itemId) {
                $quantity = floatval($quantities[$index] ?? 0);
                $note = $notes[$index] ?? '';

                // Get current stock based on opname type
                if ($opname->id_gudang > 0) {
                    $currentStock = $this->itemStokModel->getStockByItemAndGudang($itemId, $opname->id_gudang);
                } else {
                    $currentStock = $this->itemStokModel->getStockByItemAndOutlet($itemId, $opname->id_outlet);
                }
                $currentQty = $currentStock ? floatval($currentStock->jml) : 0;

                // Get item details
                $item = $this->itemModel->find($itemId);
                $satuan = $this->db->table('tbl_m_satuan')->where('id', $item->id_satuan)->get()->getRow();

                // Save opname detail
                $opnameDetailData = [
                    'id_so' => $id,
                    'id_item' => $itemId,
                    'id_satuan' => $item->id_satuan,
                    'id_user' => $this->ionAuth->user()->row()->id,
                    'tgl_masuk' => isset($opname->tgl_masuk) ? $opname->tgl_masuk : date('Y-m-d'),
                    'kode' => $item->kode,
                    'item' => $item->item,
                    'satuan' => $satuan ? $satuan->satuanBesar : '',
                    'keterangan' => $note,
                    'jml_sys' => $currentQty,
                    'jml_so' => $quantity,
                    'jml_sls' => $quantity - $currentQty,
                    'jml_satuan' => 1,
                    'sp' => '0'
                ];
                $utilSODetModel->insert($opnameDetailData);

                // Calculate difference
                $difference = $quantity - $currentQty;

                if ($difference != 0) {
                    // Update stock based on opname type
                    $newQty = $currentQty + $difference;
                    if ($opname->id_gudang > 0) {
                        $this->itemStokModel->updateStock($itemId, $opname->id_gudang, $newQty, $this->ionAuth->user()->row()->id);
                        $locationId = $opname->id_gudang;
                        $locationType = 'gudang';
                    } else {
                        $this->itemStokModel->updateStockOutlet($itemId, $opname->id_outlet, $newQty, $this->ionAuth->user()->row()->id);
                        $locationId = $opname->id_outlet;
                        $locationType = 'outlet';
                    }

                    // Add to history
                    $historyData = [
                        'id_item' => $itemId,
                        'id_gudang' => $opname->id_gudang > 0 ? $opname->id_gudang : null,
                        'id_outlet' => $opname->id_outlet > 0 ? $opname->id_outlet : null,
                        'id_user' => $this->ionAuth->user()->row()->id,
                        'id_so' => $id,
                        'tgl_masuk' => date('Y-m-d H:i:s'),
                        'no_nota' => 'SO-' . $id,
                        'keterangan' => "Stock Opname {$locationType}: " . $note,
                        'jml' => abs($difference),
                        'status' => $difference > 0 ? '2' : '7', // 2=Stok Masuk, 7=Stok Keluar
                        'sp' => '0'
                    ];
                    $this->itemHistModel->addHistory($historyData);
                }
            }

            // Update opname status
            $this->utilSOModel->update($id, [
                'status' => '1', // Completed
                'reset' => '1'   // Processed
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal memproses opname');
            }

            return redirect()->to(base_url('gudang/opname'))->with('success', 'Opname berhasil diproses dan stok telah diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses opname: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $opname = $this->utilSOModel->find($id);
        if (!$opname) {
            return redirect()->to(base_url('gudang/opname'))->with('error', 'Data opname tidak ditemukan.');
        }

        try {
            $this->utilSOModel->delete($id);
            return redirect()->to(base_url('gudang/opname'))->with('success', 'Data opname berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data opname: ' . $e->getMessage());
        }
    }

    private function getOpnameDetails($opnameId)
    {
        // Get opname details from UtilSODetModel
        $utilSODetModel = new \App\Models\UtilSODetModel();
        return $utilSODetModel->where('id_so', $opnameId)->findAll();
    }
} 