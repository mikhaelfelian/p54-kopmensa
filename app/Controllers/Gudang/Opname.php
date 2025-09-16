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
use Exception;

class Opname extends BaseController
{
    protected $utilSOModel;
    protected $gudangModel;
    protected $outletModel;
    protected $itemModel;
    protected $itemStokModel;
    protected $itemHistModel;
    protected $utilSODetModel;
    protected $ionAuth;
    protected $pengaturan;
    protected $theme;

    public function __construct()
    {
        parent::__construct();
        $this->utilSOModel = new UtilSOModel();
        $this->gudangModel = new GudangModel();
        $this->outletModel = new OutletModel();
        $this->itemModel = new ItemModel();
        $this->itemStokModel = new ItemStokModel();
        $this->itemHistModel = new ItemHistModel();
        $this->utilSODetModel = new \App\Models\UtilSODetModel();
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
        $this->pengaturan = new \App\Models\PengaturanModel();
        $this->theme = new \App\Libraries\Theme();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_opname') ?? 1;
        $perPage = 10;
        $keyword = $this->request->getVar('keyword');
        $tgl = $this->request->getVar('tgl');
        $ket = $this->request->getVar('ket');
        $tipe = $this->request->getVar('tipe');
        $status = $this->request->getVar('status');

        // Build query with filters
        $builder = $this->utilSOModel;
        
        if ($tgl) {
            $builder = $builder->where('DATE(created_at)', $tgl);
        }
        
        if ($ket) {
            $builder = $builder->like('keterangan', $ket);
        }
        
        if ($tipe) {
            if ($tipe == 'Gudang') {
                $builder = $builder->where('tipe', '1');
            } elseif ($tipe == 'Outlet') {
                $builder = $builder->where('tipe', '2');
            }
        }
        
        if ($status !== null && $status !== '') {
            $builder = $builder->where('status', $status);
        }

        $opnameData = $builder->paginate($perPage, 'opname');
        
        // Get user data and location info for each opname record
        $opnameWithUsers = [];
        foreach ($opnameData as $opname) {
            // Check if id_user is not null before calling user() method
            if ($opname->id_user) {
            $user = $this->ionAuth->user($opname->id_user)->row();
            $opname->user_name = $user ? $user->first_name : 'Unknown User';
            } else {
                $opname->user_name = 'Unknown User';
            }
            
            // Determine opname type and location based on tipe field
            if ($opname->tipe == '1') {
                // Gudang opname
                $gudang = $this->gudangModel->find($opname->id_gudang);
                $opname->opname_type = 'Gudang';
                $opname->location_name = $gudang ? $gudang->nama : 'N/A';
            } elseif ($opname->tipe == '2') {
                // Outlet opname
                $outlet = $this->outletModel->find($opname->id_outlet);
                $opname->opname_type = 'Outlet';
                $opname->location_name = $outlet ? $outlet->nama : 'N/A';
            } else {
                // Fallback to legacy method for records without tipe
                if ($opname->id_gudang > 0) {
                    $gudang = $this->gudangModel->find($opname->id_gudang);
                    $opname->opname_type = 'Gudang';
                    $opname->location_name = $gudang ? $gudang->nama : 'N/A';
                } elseif ($opname->id_outlet > 0) {
                    $outlet = $this->outletModel->find($opname->id_outlet);
                    $opname->opname_type = 'Outlet';
                    $opname->location_name = $outlet ? $outlet->nama : 'N/A';
                } else {
                    $opname->opname_type = 'Unknown';
                    $opname->location_name = 'N/A';
                }
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
            'tipe'        => $tipe,
            'status'      => $status,
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
            'gudang'      => $this->gudangModel->where('status', '1')->where('status_otl', '0')->where('status_hps', '0')->findAll(),
            'outlet'      => $this->gudangModel->where('status', '1')->where('status_otl', '1')->where('status_hps', '0')->findAll(),
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
        $opnameType = $this->request->getPost('tipe');
        
        $rules = [
            'tgl_masuk' => 'required',
            'tipe'      => 'required|in_list[1,2]',
        ];
        
        // Dynamic validation based on opname type
        if ($opnameType === '1') {
            $rules['gudang'] = 'required|numeric';
        } elseif ($opnameType === '2') {
            $rules['outlet'] = 'required|numeric';
        }

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('gudang/opname/create'))->with('errors', $this->validator->getErrors());
        }

        // Get form data using explicit variable assignment pattern
        $id_user    = $this->ionAuth->user()->row()->id;
        $tgl_masuk  = $this->request->getPost('tgl_masuk');
        $id_gudang  = $this->request->getPost('gudang');
        $id_outlet  = $this->request->getPost('outlet');
        $tipe       = $this->request->getPost('tipe');
        $keterangan = $this->request->getPost('keterangan');
        $opn_tipe   = tipeOpn($tipe);

        $data = [
            'id_user'    => $id_user,
            'id_gudang'  => ($tipe === '1') ? $id_gudang : $id_outlet,
            'id_outlet'  => ($tipe === '2') ? $id_outlet : 0,
            'tgl_masuk'  => tgl_indo_sys($tgl_masuk),
            'keterangan' => $keterangan,
            'status'     => '0', // Draft
            'reset'      => '0', // Not reset
            'tipe'       => $tipe,
        ];

        try {
            // Save to database
                $this->utilSOModel->save($data);
            
                return redirect()->to(base_url('gudang/opname'))->with('success', "Data opname {$opn_tipe['label']} berhasil disimpan.");
        } catch (\Exception $e) {
                return redirect()->to(base_url('gudang/opname/create'))->withInput()->with('error', 'Gagal menyimpan data opname: ' . $e->getMessage());
        }
    }

    public function detail($id)
    {
        $opname = $this->utilSOModel->find($id);
        if (!$opname) {
            return redirect()->to(base_url('gudang/opname'))->with('error', 'Data opname tidak ditemukan.');
        }

        // Get user data
        if ($opname->id_user) {
        $user = $this->ionAuth->user($opname->id_user)->row();
        $opname->user_name = $user ? $user->first_name : 'Unknown User';
        } else {
            $opname->user_name = 'Unknown User';
        }

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
            'gudang'      => $this->gudangModel->where('status', '1')->where('status_otl', '0')->where('status_hps', '0')->findAll(),
            'outlet'      => $this->gudangModel->where('status', '1')->where('status_otl', '1')->where('status_hps', '0')->findAll(),
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
        $opname     = $this->utilSOModel->find($id);
        $gudang     = $this->gudangModel->where('id', $opname->id_gudang)->first();
        
        // Get opname detail items with complete information
        $opn_det = $this->utilSODetModel->select('
                tbl_util_so_det.*,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_satuan.SatuanBesar as satuan,
                tbl_m_item_stok.jml as current_stock
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_util_so_det.id_item')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->join('tbl_m_item_stok', 'tbl_m_item_stok.id_item = tbl_util_so_det.id_item AND tbl_m_item_stok.id_gudang = ' . $opname->id_gudang, 'left')
            ->where('tbl_util_so_det.id_so', $id)
            ->findAll();

        $data = [
            'title'       => 'Input Item Opname ',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'opname'      => $opname,
            'gudang'      => $gudang,
            'items'       => $opn_det,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('gudang/opname') . '">Opname</a></li>
                <li class="breadcrumb-item active">Input Item</li>
            '
        ];

        // Get all active items for dropdown
        $itemModel = new \App\Models\ItemModel();
        $dropdownItems = $itemModel->select('
                tbl_m_item.id,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_item.barcode,
                tbl_m_satuan.SatuanBesar as satuan
            ')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->where('tbl_m_item.status', '1')
            ->where('tbl_m_item.status_hps', '0')
            ->orderBy('tbl_m_item.item', 'ASC')
            ->findAll();
        
        $data['dropdownItems'] = $dropdownItems;

        return view($this->theme->getThemePath() . '/gudang/opname/input', $data);
    }

    public function addItem()
    {
        // if (!$this->request->isAJAX()) {
        //     return $this->response->setStatusCode(400)->setJSON([
        //         'status' => 'error',
        //         'message' => 'Invalid request type.'
        //     ]);
        // }

        // Accept id_so (id opname) from POST data
        $id = $this->request->getPost('id_so');
        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'ID opname tidak ditemukan.'
            ]);
        }

        $opname = $this->utilSOModel->find($id);
        if (!$opname) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Data opname tidak ditemukan.'
            ]);
        }

        // Accept both single item and array of items
        $items = $this->request->getPost('items');
        if (!$items) {
            // Try to build items from flat POST (single row)
            $singleItem = [
                'id_item'     => $this->request->getPost('id_item'),
                'stok_sistem' => $this->request->getPost('stok_sistem'),
                'stok_fisik'  => $this->request->getPost('stok_fisik'),
                'keterangan'  => $this->request->getPost('keterangan'),
                'satuan'      => $this->request->getPost('satuan'),
            ];
            // If at least id_item and stok_fisik are present, treat as single item
            if (!empty($singleItem['id_item']) && $singleItem['stok_fisik'] !== null) {
                $items = [$singleItem];
            } else {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Tidak ada item yang diinput.'
                ]);
            }
        }

        $userId = $this->ionAuth->user()->row()->id;
        $results = [];
        foreach ($items as $row) {
            // Defensive: handle both array and object
            $row = (array)$row;
            
            // Get item details for this specific item
            $item_row = $this->itemModel->find($row['id_item']);
            if (!$item_row) {
                continue; // Skip if item not found
            }

            // Get current stock from tbl_m_item_stok for this warehouse
            $currentStock = 0;
            $stockData = $this->itemStokModel
                ->where('id_item', $row['id_item'])
                ->where('id_gudang', $opname->id_gudang)
                ->first();
            
            if ($stockData) {
                $currentStock = (float) $stockData->jml;
            }
            
            $data = [
                'id_so'      => $id,
                'id_item'    => $row['id_item'],
                'id_satuan'  => $item_row->id_satuan,
                'item'       => $item_row->item,
                'jml_sys'    => $currentStock, // Use actual stock from tbl_m_item_stok
                'jml_so'     => $row['stok_fisik'],
                'keterangan' => $row['keterangan'] ?? null,
                'satuan'     => $row['satuan'] ?? null,
                'id_user'    => $userId,
            ];
            $existing = $this->utilSODetModel
                ->where('id_so', $id)
                ->where('id_item', $row['id_item'])
                ->first();

            if ($existing) {
                $success = $this->utilSODetModel->update($existing->id, $data);
                $results[] = [
                    'id_item' => $row['id_item'],
                    'action' => 'update',
                    'success' => $success
                ];
            } else {
                $insertId = $this->utilSODetModel->insert($data);
                $results[] = [
                    'id_item' => $row['id_item'],
                    'action' => 'insert',
                    'success' => $insertId ? true : false
                ];
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Data opname berhasil disimpan!',
            'results' => $results,
            'csrfHash' => csrf_hash()
        ]);
    }

    /**
     * Proses opname: update stock, create item history, and mark opname as processed
     * Route: POST gudang/opname/process/(:num)
     */
    public function proses($id)
    {
        try {
            // Handle GET requests by redirecting to input page
            if ($this->request->is('get')) {
                return redirect()->to(base_url("gudang/opname/input/{$id}"));
            }
            
            // Only allow POST for actual processing
            if (!$this->request->is('post')) {
                return $this->response->setStatusCode(405)->setJSON([
                    'status' => 'error',
                    'message' => 'Metode tidak diizinkan'
                ]);
            }

            // Test 1: Check if opname exists
            $opname = $this->utilSOModel->find($id);
            if (!$opname) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Data opname tidak ditemukan.'
                ]);
            }

            // Test 2: Check if already processed
            if ($opname->status == '1') {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Opname sudah diproses sebelumnya.'
                ]);
            }

            // Test 3: Check if items exist
            $items = $this->utilSODetModel->where('id_so', $id)->findAll();

            foreach ($items as $item) {
                $stokQuery      = $this->itemStokModel->where('id_item', $item->id_item)->where('id_gudang', $opname->id_gudang);
                $sql_stok_row   = $stokQuery->first();

                // Update stok using ->save($data)
                $stokData = [
                    'id'        => $sql_stok_row ? $sql_stok_row->id : null,
                    'id_item'   => $item->id_item,
                    'id_gudang' => $opname->id_gudang,
                    'jml'       => $item->jml_so,
                ];

                $this->itemStokModel->save($stokData);

                // After success, save to ItemHistModel
                $itemHistData = [
                    'id_item'     => $item->id_item,
                    'id_satuan'   => $item->id_satuan ?? null,
                    'id_gudang'   => !empty($opname->id_gudang) ? $opname->id_gudang : null,
                    'id_user'     => $this->ionAuth->user()->row()->id ?? null,
                    'id_so'       => $opname->id,
                    'tgl_masuk'   => $opname->tgl_masuk,
                    'no_nota'     => sprintf('%05d', $opname->id),
                    'kode'        => $item->kode ?? null,
                    'item'        => $item->item ?? null,
                    'keterangan'  => 'Stock Opname ' . $item->item,
                    'jml'         => $item->jml_so,
                    'jml_satuan'  => $item->jml_satuan ?? 1,
                    'satuan'      => $item->satuan ?? null,
                    'status'      => '6', // 6 = SO
                    'sp'          => '0'
                ];
                
                $this->itemHistModel->save($itemHistData);
            }

            $this->utilSOModel->update($opname->id, ['status' => '1']);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Stok opname berhasil diproses',
                'items' => '',
                'csrfHash' => csrf_hash()
            ]);

        } catch (Exception $e) {
            log_message('error', "Error in proses method: " . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memproses opname: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteItem()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Invalid request type.'
            ]);
        }

        $itemId = $this->request->getPost('item_id');
        if (!$itemId) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'ID item tidak ditemukan.'
            ]);
        }

        $item = $this->utilSODetModel->find($itemId);
        if (!$item) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Item tidak ditemukan.'
            ]);
        }

        if ($this->utilSODetModel->delete($itemId)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Item berhasil dihapus dari opname.',
                'csrfHash' => csrf_hash()
            ]);
        } else {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus item.'
            ]);
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

    public function getStockOutletAjax()
    {
        $itemId = $this->request->getGet('item_id');
        $outletId = $this->request->getGet('outlet_id');
        $itemStokModel = new \App\Models\ItemStokModel();
        $stock = $itemStokModel->getStockByItemAndOutlet($itemId, $outletId);
        return $this->response->setJSON([
                'id'     => $stock ? $stock->id_item : null,
                'item'   => $stock ? $stock->item_nama : null,
                'satuan' => $stock ? $stock->satuan_nama : null,
                'jml'    => $stock ? $stock->jml : 0
        ]);
    }

    public function getTableData($id)
    {
        $opname = $this->utilSOModel->find($id);
        if (!$opname) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Data opname tidak ditemukan.'
            ]);
        }

        // Get items for this opname
        $items = $this->utilSODetModel->where('id_so', $id)->findAll();
        
        // Get dropdown items for the select
        $dropdownItems = $this->itemModel->getItemStocksWithRelations();

        $html = '';
        $html .= '<tr>';
        $html .= '<td class="text-center">1</td>';
        $html .= '<td>';
        $html .= '<select name="items[0][id_item]" class="form-control form-control-sm rounded-0 select2" required>';
        $html .= '<option value="">Pilih Item</option>';
        foreach ($dropdownItems as $item) {
            $html .= '<option value="' . $item->id . '">' . esc($item->item) . ' (' . esc($item->kode) . ')</option>';
        }
        $html .= '</select>';
        $html .= '</td>';
        $html .= '<td><input type="text" name="items[0][satuan]" class="form-control form-control-sm rounded-0 satuan-outlet" placeholder="Satuan" readonly></td>';
        $html .= '<td><input type="text" class="form-control form-control-sm text-right stok-outlet" name="items[0][stok_sistem]" value="" readonly></td>';
        $html .= '<td><input type="number" min="0" step="any" name="items[0][stok_fisik]" class="form-control form-control-sm text-right rounded-0" placeholder="Stok Fisik" required></td>';
        $html .= '<td><input type="text" name="items[0][keterangan]" class="form-control form-control-sm rounded-0" placeholder="Keterangan"></td>';
        $html .= '<td><button type="button" class="btn btn-success btn-sm rounded-0 btn-add-opname-item"><i class="fas fa-plus"></i></button></td>';
        $html .= '</tr>';

        if (!empty($items)) {
            $no = 2;
            foreach ($items as $item) {
                $html .= '<tr>';
                $html .= '<td>' . $no++ . '</td>';
                $html .= '<td>' . esc($item->item) . '</td>';
                $html .= '<td>' . esc($item->satuan) . '</td>';
                $html .= '<td><input type="text" class="form-control form-control-sm text-right rounded-0" value="' . (float)$item->jml_sys . '" readonly></td>';
                $html .= '<td><input type="number" min="0" step="any" class="form-control form-control-sm text-right rounded-0" name="items[' . $item->id . '][stok_fisik]" value="' . ($item->jml_so ?? '') . '" required readonly></td>';
                $html .= '<td><input type="text" class="form-control form-control-sm rounded-0" name="items[' . $item->id . '][keterangan]" value="' . ($item->keterangan ?? '') . '" readonly></td>';
                $html .= '<td><button type="button" class="btn btn-danger btn-sm rounded-0 btn-delete-opname-item" data-id="' . $item->id . '"><i class="fas fa-trash"></i></button></td>';
                $html .= '</tr>';
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'html' => $html
        ]);
    }

    private function getOpnameDetails($opnameId)
    {
        // Get opname details from UtilSODetModel
        $utilSODetModel = new \App\Models\UtilSODetModel();
        return $utilSODetModel->where('id_so', $opnameId)->findAll();
    }

    /**
     * Get stock for outlet/warehouse
     */
    public function getStockOutlet()
    {
        // Disable CSRF for this specific endpoint
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        
        try {
            $itemId = $this->request->getGet('item_id');
            $outletId = $this->request->getGet('outlet_id');

            // Basic validation
            if (!$itemId) {
                return $this->response->setJSON([
                    'id' => null,
                    'jml' => 0,
                    'satuan' => '',
                    'debug' => 'No item_id provided'
                ]);
            }

            // Test database connection first
            $db = \Config\Database::connect();
            
            // Get item details with satuan using raw query to avoid model issues
            $itemQuery = $db->query("
                SELECT 
                    i.id,
                    i.item,
                    i.kode,
                    i.id_satuan,
                    s.SatuanBesar as satuan
                FROM tbl_m_item i
                LEFT JOIN tbl_m_satuan s ON s.id = i.id_satuan
                WHERE i.id = ? AND i.status = '1' AND i.status_hps = '0'
            ", [$itemId]);
            
            $item = $itemQuery->getRow();

            if (!$item) {
                return $this->response->setJSON([
                    'id' => null,
                    'jml' => 0,
                    'satuan' => '',
                    'debug' => 'Item not found with id: ' . $itemId
                ]);
            }

            // Get stock from item stock table using raw query
            $stock = 0;
            if ($outletId) {
                $stockQuery = $db->query("
                    SELECT jml 
                    FROM tbl_m_item_stok 
                    WHERE id_item = ? AND id_gudang = ?
                ", [$itemId, $outletId]);
                
                $stockData = $stockQuery->getRow();
                if ($stockData) {
                    $stock = (float) $stockData->jml;
                }
            }

            $result = [
                'id' => (int) $itemId,
                'jml' => $stock,
                'satuan' => $item->satuan ?? '',
                'item_name' => $item->item ?? '',
                'item_code' => $item->kode ?? '',
                'debug' => 'Success - Stock: ' . $stock . ', Satuan: ' . ($item->satuan ?? 'none'),
                'query_info' => [
                    'item_found' => !empty($item),
                    'outlet_id' => $outletId,
                    'stock_query_executed' => !empty($outletId)
                ]
            ];

            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'id' => null,
                'jml' => 0,
                'satuan' => '',
                'error' => $e->getMessage(),
                'debug' => 'Exception occurred: ' . $e->getFile() . ':' . $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 