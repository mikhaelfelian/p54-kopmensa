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
use App\Models\TransMutasiDetModel;
use App\Models\GudangModel;
use App\Models\OutletModel;
use App\Models\ItemModel;
use App\Models\ItemStokModel;
use App\Models\ItemHistModel;

class Transfer extends BaseController
{
    protected $transMutasiModel;
    protected $transMutasiDetModel;
    protected $gudangModel;
    protected $outletModel;
    protected $itemModel;
    protected $itemStokModel;
    protected $itemHistModel;

    public function __construct()
    {
        parent::__construct();
        $this->transMutasiModel = new TransMutasiModel();
        $this->transMutasiDetModel = new TransMutasiDetModel();
        $this->gudangModel = new GudangModel();
        $this->outletModel = new OutletModel();
        $this->itemModel = new ItemModel();
        $this->itemStokModel = new ItemStokModel();
        $this->itemHistModel = new ItemHistModel();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_transfer') ?? 1;
        $perPage = 10;
        $keyword = $this->request->getVar('keyword');

        // Build query with filters
        $builder = $this->transMutasiModel;
        
        if ($keyword) {
            $builder = $builder->groupStart()
                ->like('no_nota', $keyword)
                ->orLike('keterangan', $keyword)
                ->groupEnd();
        }

        $transfers = $builder->paginate($perPage, 'transfer');
        
        // Get user data for each transfer record
        $transfersWithUsers = [];
        foreach ($transfers as $transfer) {
            $user = $this->ionAuth->user($transfer->id_user)->row();
            $transfer->user_name = $user ? $user->first_name : 'Unknown User';
            
            // Get gudang names - handle data safely
            $gudangAsal = $this->gudangModel->find($transfer->id_gd_asal);
            $gudangTujuan = $this->gudangModel->find($transfer->id_gd_tujuan);
            
            // Safely get gudang names
            $transfer->gudang_asal_name = 'N/A';
            $transfer->gudang_tujuan_name = 'N/A';
            
            if ($gudangAsal) {
                if (is_object($gudangAsal) && isset($gudangAsal->gudang)) {
                    $transfer->gudang_asal_name = $gudangAsal->gudang;
                } elseif (is_array($gudangAsal) && isset($gudangAsal['gudang'])) {
                    $transfer->gudang_asal_name = $gudangAsal['gudang'];
                }
            }
            
            if ($gudangTujuan) {
                if (is_object($gudangTujuan) && isset($gudangTujuan->gudang)) {
                    $transfer->gudang_tujuan_name = $gudangTujuan->gudang;
                } elseif (is_array($gudangTujuan) && isset($gudangTujuan['gudang'])) {
                    $transfer->gudang_tujuan_name = $gudangTujuan['gudang'];
                }
            }
            
            $transfersWithUsers[] = $transfer;
        }

        $data = [
            'title'       => 'Data Transfer/Mutasi',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'transfers'   => $transfersWithUsers,
            'pager'       => $builder->pager,
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
            'gudang'      => $this->gudangModel->where('status', '1')->where('status_otl', '0')->where('status_hps', '0')->findAll(),
            'outlet'      => $this->gudangModel->where('status', '1')->where('status_otl', '1')->where('status_hps', '0')->findAll(),
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
        // Get form data first
        $tipe = $this->request->getPost('tipe');
        
        // Validate form data based on transfer type
        $rules = [
            'tgl_masuk' => 'required',
            'tipe' => 'required',
        ];
        
        // Add conditional validation based on transfer type
        if ($tipe == '1') { // Pindah Gudang
            $rules['id_gd_asal'] = 'required';
            $rules['id_gd_tujuan'] = 'required';
        } elseif ($tipe == '2') { // Stok Masuk
            $rules['id_gd_tujuan'] = 'required';
        } elseif ($tipe == '3') { // Stok Keluar
            $rules['id_gd_asal'] = 'required';
        } elseif ($tipe == '4') { // Pindah Outlet
            $rules['id_outlet'] = 'required';
        }

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('gudang/transfer'))->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get form data using explicit variable assignment pattern
        $id_user     = $this->ionAuth->user()->row()->id;
        $tgl_masuk   = $this->request->getPost('tgl_masuk');
        $id_gd_asal  = $this->request->getPost('id_gd_asal');
        $id_gd_tujuan = $this->request->getPost('id_gd_tujuan');
        $id_outlet   = $this->request->getPost('id_outlet');
        $keterangan  = $this->request->getPost('keterangan');

        $data = [
            'id_user'      => $id_user,
            'tgl_masuk'    => tgl_indo_sys($tgl_masuk),
            'tipe'         => $tipe,
            'id_gd_asal'   => $id_gd_asal ?: 0,
            'id_gd_tujuan' => $id_gd_tujuan ?: $id_outlet,
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
            return redirect()->to(base_url('gudang/transfer'))
                ->withInput()
                ->with('error', 'Gagal menyimpan data transfer: ' . $e->getMessage());
        }
    }

    public function detail($id)
    {
        $transfer = $this->transMutasiModel->find($id);
        if (!$transfer) {
            return redirect()->to(base_url('gudang/transfer'))->with('error', 'Data transfer tidak ditemukan.');
        }

        // Get user data
        $user = $this->ionAuth->user($transfer->id_user)->row();
        $transfer->user_name = $user ? $user->first_name : 'Unknown User';

        // Get gudang data
        if(!empty($transfer->id_gd_asal)){
            $gudangAsal   = $this->gudangModel->find($transfer->id_gd_asal);
            $gudangTujuan = $this->gudangModel->find($transfer->id_gd_tujuan);

            $gudangAsalName   = ($gudangAsal && isset($gudangAsal->gudang)) ? $gudangAsal->gudang : 'N/A';
            $gudangTujuanName = ($gudangTujuan && isset($gudangTujuan->gudang)) ? $gudangTujuan->gudang : 'N/A';
        }else{
            $gudangAsal         = $this->gudangModel->find($transfer->id_gd_asal);
            $gudangTujuan       = $this->outletModel->find($transfer->id_outlet);
            $gudangAsalName     = $gudangAsal->gudang;
            $gudangTujuanName   = $gudangTujuan->nama;
        }
        
        // Safely get gudang names
        $gudangAsalName     = 'N/A';
        $gudangTujuanName   = 'N/A';
        
        if ($gudangAsal) {
            if (is_object($gudangAsal) && isset($gudangAsal->gudang)) {
                $gudangAsalName = $gudangAsal->gudang;
            } elseif (is_array($gudangAsal) && isset($gudangAsal['gudang'])) {
                $gudangAsalName = $gudangAsal['gudang'];
            }
        }
        
        if ($gudangTujuan) {
            if (is_object($gudangTujuan) && isset($gudangTujuan->gudang)) {
                $gudangTujuanName = $gudangTujuan->gudang;
            } elseif (is_array($gudangTujuan) && isset($gudangTujuan['gudang'])) {
                $gudangTujuanName = $gudangTujuan['gudang'];
            }
        }

        // Get transfer details
        $transferDetails = $this->getTransferDetails($id);

        $data = [
            'title'       => 'Detail Transfer',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'transfer'    => $transfer,
            'gudangAsal'  => $gudangAsal,
            'gudangTujuan'=> $gudangTujuan,
            'gudangAsalName' => $gudangAsalName,
            'gudangTujuanName' => $gudangTujuanName,
            'details'     => $transferDetails,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('gudang/transfer') . '">Transfer</a></li>
                <li class="breadcrumb-item active">Detail</li>
            '
        ];

        return view($this->theme->getThemePath() . '/gudang/transfer/detail', $data);
    }

    public function edit($id)
    {
        $transfer = $this->transMutasiModel->find($id);
        if (!$transfer) {
            return redirect()->to(base_url('gudang/transfer'))->with('error', 'Data transfer tidak ditemukan.');
        }

        $data = [
            'title'       => 'Edit Transfer/Mutasi',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'transfer'    => $transfer,
            'gudang'      => $this->gudangModel->where('status', '1')->where('status_otl', '0')->where('status_hps', '0')->findAll(),
            'outlet'      => $this->gudangModel->where('status', '1')->where('status_otl', '1')->where('status_hps', '0')->findAll(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('gudang/transfer') . '">Transfer</a></li>
                <li class="breadcrumb-item active">Edit</li>
            '
        ];

        return view($this->theme->getThemePath() . '/gudang/transfer/edit', $data);
    }

    public function update($id)
    {
        $transfer = $this->transMutasiModel->find($id);
        if (!$transfer) {
            return redirect()->to(base_url('gudang/transfer'))->with('error', 'Data transfer tidak ditemukan.');
        }

        // Get form data first
        $tipe = $this->request->getPost('tipe');
        
        // Validate form data based on transfer type
        $rules = [
            'tgl_masuk' => 'required',
            'tipe' => 'required',
        ];
        
        // Add conditional validation based on transfer type
        if ($tipe == '1') { // Pindah Gudang
            $rules['id_gd_asal'] = 'required';
            $rules['id_gd_tujuan'] = 'required';
        } elseif ($tipe == '2') { // Stok Masuk
            $rules['id_gd_tujuan'] = 'required';
        } elseif ($tipe == '3') { // Stok Keluar
            $rules['id_gd_asal'] = 'required';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'tgl_masuk' => tgl_indo_sys($this->request->getPost('tgl_masuk')),
            'tipe' => $tipe,
            'id_gd_asal' => $this->request->getPost('id_gd_asal') ?: 0,
            'id_gd_tujuan' => $this->request->getPost('id_gd_tujuan') ?: 0,
            'id_outlet' => $this->request->getPost('id_outlet') ?: 0,
            'keterangan' => $this->request->getPost('keterangan'),
        ];

        try {
            $this->transMutasiModel->update($id, $data);
            return redirect()->to(base_url('gudang/transfer'))->with('success', 'Data transfer berhasil diupdate.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate data transfer: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $transfer = $this->transMutasiModel->find($id);
        if (!$transfer) {
            return redirect()->to(base_url('gudang/transfer'))->with('error', 'Data transfer tidak ditemukan.');
        }

        try {
            $this->transMutasiModel->delete($id);
            return redirect()->to(base_url('gudang/transfer'))->with('success', 'Data transfer berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data transfer: ' . $e->getMessage());
        }
    }

    public function inputItem($id = null)
    {
        if (!$id) {
            return redirect()->to(base_url('gudang/transfer'))->with('error', 'ID transfer tidak ditemukan.');
        }

        // Get transfer data
        $transfer = $this->transMutasiModel->find($id);
        if (!$transfer) {
            return redirect()->to(base_url('gudang/transfer'))->with('error', 'Data transfer tidak ditemukan.');
        }

        // Get items for the source gudang
        $items = $this->itemStokModel->select('
                tbl_m_item_stok.*,
                tbl_m_item.kode as item_kode,
                tbl_m_item.item as item_name,
                tbl_m_satuan.satuanBesar as satuan_name
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->where('tbl_m_item_stok.id_gudang', $transfer->id_gd_asal)
            ->where('tbl_m_item_stok.status', '1')
            ->where('tbl_m_item_stok.jml >', 0)
            ->findAll();

        // Get gudang names
        $gudangAsal = $this->gudangModel->find($transfer->id_gd_asal);
        $gudangTujuan = $this->gudangModel->find($transfer->id_gd_tujuan);
        
        // Safely get gudang names
        $transfer->gudang_asal_name = 'N/A';
        $transfer->gudang_tujuan_name = 'N/A';
        
        if ($gudangAsal) {
            if (is_object($gudangAsal) && isset($gudangAsal->gudang)) {
                $transfer->gudang_asal_name = $gudangAsal->gudang;
            } elseif (is_array($gudangAsal) && isset($gudangAsal['gudang'])) {
                $transfer->gudang_asal_name = $gudangAsal['gudang'];
            }
        }
        
        if ($gudangTujuan) {
            if (is_object($gudangTujuan) && isset($gudangTujuan->gudang)) {
                $transfer->gudang_tujuan_name = $gudangTujuan->gudang;
            } elseif (is_array($gudangTujuan) && isset($gudangTujuan['gudang'])) {
                $transfer->gudang_tujuan_name = $gudangTujuan['gudang'];
            }
        }

        $data = [
            'title'       => 'Input Item Transfer',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'transfer'    => $transfer,
            'items'       => $items,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('gudang/transfer') . '">Transfer</a></li>
                <li class="breadcrumb-item active">Input Item</li>
            '
        ];

        return view($this->theme->getThemePath() . '/gudang/transfer/input', $data);
    }

    public function process($id)
    {
        $transfer = $this->transMutasiModel->find($id);
        if (!$transfer) {
            return redirect()->to(base_url('gudang/transfer'))->with('error', 'Data transfer tidak ditemukan.');
        }

        // Get form data
        $items      = $this->request->getPost('items');
        $quantities = $this->request->getPost('quantities');
        $notes      = $this->request->getPost('notes');

        if (!$items || !$quantities) {
            return redirect()->back()->with('error', 'Data item tidak lengkap.');
        }

        try {
            $this->db = \Config\Database::connect();
            $this->db->transStart();

            log_message('debug', 'Starting transfer process for ID: ' . $id);

            foreach ($items as $index => $itemId) {
                $sql_item = $this->db->table('tbl_m_item')->where('id', $itemId)->get()->getRow();
                $quantity = isset($quantities[$index]) ? floatval($quantities[$index]) : 0;
                $note     = isset($notes[$index]) ? $notes[$index] : '';

                if ($quantity > 0) {
                    log_message('debug', "Processing item ID: $itemId, quantity: $quantity");

                    // Get current stock in source warehouse
                    if (!empty($transfer->id_gd_asal)) {
                        $sourceStock = $this->itemStokModel->getStockByItemAndGudang($itemId, $transfer->id_gd_asal);
                        $sourceQty = $sourceStock ? floatval($sourceStock->jml) : 0;

                        // Check if enough stock
                        if ($sourceQty < $quantity) {
                            throw new \Exception("Stok tidak mencukupi untuk item ID: $itemId. Stok tersedia: $sourceQty, dibutuhkan: $quantity");
                        }
                    }

                    // Get item details
                    $item = $this->itemModel->find($itemId);
                    if (!$item) {
                        throw new \Exception("Item dengan ID $itemId tidak ditemukan");
                    }

                    $satuan = $this->db->table('tbl_m_satuan')->where('id', $item->id_satuan)->get()->getRow();

                    // Save transfer detail
                    $transferDetailData = [
                        'id_mutasi'    => $id,
                        'id_item'      => $sql_item->id,
                        'id_satuan'    => $sql_item->id_satuan,
                        'id_user'      => $this->ionAuth->user()->row()->id,
                        'tgl_masuk'    => $transfer->tgl_masuk,
                        'kode'         => $sql_item->kode,
                        'item'         => $sql_item->item,
                        'satuan'       => $satuan ? $satuan->satuanBesar : '',
                        'keterangan'   => $note,
                        'jml'          => $quantity,
                        'jml_satuan'   => 1,
                        'sp'           => '0'
                    ];

                    $insertResult = $this->transMutasiDetModel->insert($transferDetailData);
                    if (!$insertResult) {
                        $errors = $this->transMutasiDetModel->errors();
                        throw new \Exception("Gagal menyimpan detail transfer untuk item $itemId. Errors: " . json_encode($errors));
                    }

                    log_message('debug', "Transfer detail saved for item ID: $itemId");

                    // Handle stock updates based on transfer type
                    if ($transfer->tipe == '1') { // Pindah Gudang
                        // Update stock in source warehouse (decrease)
                        $sourceStock = $this->itemStokModel->getStockByItemAndGudang($itemId, $transfer->id_gd_asal);
                        $sourceQty = $sourceStock ? floatval($sourceStock->jml) : 0;
                        $newSourceQty = $sourceQty - $quantity;
                        
                        $updateResult = $this->itemStokModel->updateStock($itemId, $transfer->id_gd_asal, $newSourceQty, $this->ionAuth->user()->row()->id);
                        if (!$updateResult) {
                            throw new \Exception("Gagal mengupdate stok gudang asal untuk item ID: $itemId");
                        }

                        // Update stock in destination warehouse (increase)
                        $destStock = $this->itemStokModel->getStockByItemAndGudang($itemId, $transfer->id_gd_tujuan);
                        $destQty = $destStock ? floatval($destStock->jml) : 0;
                        $newDestQty = $destQty + $quantity;
                        
                        $updateResult2 = $this->itemStokModel->updateStock($itemId, $transfer->id_gd_tujuan, $newDestQty, $this->ionAuth->user()->row()->id);
                        if (!$updateResult2) {
                            throw new \Exception("Gagal mengupdate stok gudang tujuan untuk item ID: $itemId");
                        }

                        // Add history records
                        $historyDataOut = [
                            'id_item' => $itemId,
                            'id_gudang' => $transfer->id_gd_asal,
                            'id_user' => $this->ionAuth->user()->row()->id,
                            'id_mutasi' => $id,
                            'tgl_masuk' => date('Y-m-d H:i:s'),
                            'no_nota' => $transfer->no_nota,
                            'keterangan' => 'Transfer Keluar: ' . $note,
                            'jml' => $quantity,
                            'status' => '8', // Mutasi Antar Gd
                            'sp' => '0'
                        ];
                        
                        $historyResult1 = $this->itemHistModel->addHistory($historyDataOut);
                        if (!$historyResult1) {
                            throw new \Exception("Gagal menyimpan history keluar untuk item ID: $itemId");
                        }

                        $historyDataIn = [
                            'id_item' => $itemId,
                            'id_gudang' => $transfer->id_gd_tujuan,
                            'id_user' => $this->ionAuth->user()->row()->id,
                            'id_mutasi' => $id,
                            'tgl_masuk' => date('Y-m-d H:i:s'),
                            'no_nota' => $transfer->no_nota,
                            'keterangan' => 'Transfer Masuk: ' . $note,
                            'jml' => $quantity,
                            'status' => '8', // Mutasi Antar Gd
                            'sp' => '0'
                        ];
                        
                        $historyResult2 = $this->itemHistModel->addHistory($historyDataIn);
                        if (!$historyResult2) {
                            throw new \Exception("Gagal menyimpan history masuk untuk item ID: $itemId");
                        }

                    } elseif ($transfer->tipe == '2') { // Stok Masuk
                        // Only update destination warehouse (increase)
                        $destStock = $this->itemStokModel->getStockByItemAndGudang($itemId, $transfer->id_gd_tujuan);
                        $destQty = $destStock ? floatval($destStock->jml) : 0;
                        $newDestQty = $destQty + $quantity;
                        
                        $updateResult = $this->itemStokModel->updateStock($itemId, $transfer->id_gd_tujuan, $newDestQty, $this->ionAuth->user()->row()->id);
                        if (!$updateResult) {
                            throw new \Exception("Gagal mengupdate stok untuk item ID: $itemId");
                        }

                        // Add history record
                        $historyData = [
                            'id_item' => $itemId,
                            'id_gudang' => $transfer->id_gd_tujuan,
                            'id_user' => $this->ionAuth->user()->row()->id,
                            'id_mutasi' => $id,
                            'tgl_masuk' => date('Y-m-d H:i:s'),
                            'no_nota' => $transfer->no_nota,
                            'keterangan' => 'Stok Masuk: ' . $note,
                            'jml' => $quantity,
                            'status' => '2', // Stok Masuk
                            'sp' => '0'
                        ];
                        
                        $historyResult = $this->itemHistModel->addHistory($historyData);
                        if (!$historyResult) {
                            throw new \Exception("Gagal menyimpan history untuk item ID: $itemId");
                        }

                    } elseif ($transfer->tipe == '3') { // Stok Keluar
                        // Only update source warehouse (decrease)
                        $sourceStock = $this->itemStokModel->getStockByItemAndGudang($itemId, $transfer->id_gd_asal);
                        $sourceQty = $sourceStock ? floatval($sourceStock->jml) : 0;
                        $newSourceQty = $sourceQty - $quantity;
                        
                        $updateResult = $this->itemStokModel->updateStock($itemId, $transfer->id_gd_asal, $newSourceQty, $this->ionAuth->user()->row()->id);
                        if (!$updateResult) {
                            throw new \Exception("Gagal mengupdate stok untuk item ID: $itemId");
                        }

                        // Add history record
                        $historyData = [
                            'id_item' => $itemId,
                            'id_gudang' => $transfer->id_gd_asal,
                            'id_user' => $this->ionAuth->user()->row()->id,
                            'id_mutasi' => $id,
                            'tgl_masuk' => date('Y-m-d H:i:s'),
                            'no_nota' => $transfer->no_nota,
                            'keterangan' => 'Stok Keluar: ' . $note,
                            'jml' => $quantity,
                            'status' => '7', // Stok Keluar
                            'sp' => '0'
                        ];
                        
                        $historyResult = $this->itemHistModel->addHistory($historyData);
                        if (!$historyResult) {
                            throw new \Exception("Gagal menyimpan history untuk item ID: $itemId");
                        }

                    } elseif ($transfer->tipe == '4') { // Pindah Outlet
                        // Update stock in source warehouse (decrease)
                        $sourceStock = $this->itemStokModel->getStockByItemAndGudang($itemId, $transfer->id_gd_asal);
                        $sourceQty = $sourceStock ? floatval($sourceStock->jml) : 0;
                        $newSourceQty = $sourceQty - $quantity;
                        
                        $updateResult = $this->itemStokModel->updateStock($itemId, $transfer->id_gd_asal, $newSourceQty, $this->ionAuth->user()->row()->id);
                        if (!$updateResult) {
                            throw new \Exception("Gagal mengupdate stok gudang untuk item ID: $itemId");
                        }

                        // Add history record for warehouse
                        $historyData = [
                            'id_item' => $itemId,
                            'id_gudang' => $transfer->id_gd_asal,
                            'id_user' => $this->ionAuth->user()->row()->id,
                            'id_mutasi' => $id,
                            'tgl_masuk' => date('Y-m-d H:i:s'),
                            'no_nota' => $transfer->no_nota,
                            'keterangan' => 'Pindah ke Outlet: ' . $note,
                            'jml' => $quantity,
                            'status' => '7', // Stok Keluar
                            'sp' => '0'
                        ];
                        
                        $historyResult = $this->itemHistModel->addHistory($historyData);
                        if (!$historyResult) {
                            throw new \Exception("Gagal menyimpan history untuk item ID: $itemId");
                        }
                    }

                    log_message('debug', "Stock updated for item ID: $itemId");
                }
            }

            // Update transfer status
            $updateTransferResult = $this->transMutasiModel->update($id, [
                'status_nota' => '3', // Completed
                'status_terima' => '1' // Received
            ]);

            if (!$updateTransferResult) {
                throw new \Exception("Gagal mengupdate status transfer");
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            log_message('debug', 'Transfer process completed successfully for ID: ' . $id);

            return redirect()->to(base_url('gudang/transfer'))->with('success', 'Transfer berhasil diproses dan stok telah diperbarui.');

        } catch (\Exception $e) {
            if ($this->db->transStatus() !== false) {
                $this->db->transRollback();
            }
            
            log_message('error', 'Transfer process failed: ' . $e->getMessage());
            log_message('error', 'Transfer ID: ' . $id);
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()->with('error', 'Gagal memproses transfer: ' . $e->getMessage());
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

    private function getTransferDetails($transferId)
    {
        // Get transfer details from TransMutasiDetModel
        return $this->transMutasiDetModel
            ->select('tbl_trans_mutasi_det.*, tbl_m_item.foto')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_mutasi_det.id_item', 'left')
            ->where('id_mutasi', $transferId)
            ->findAll();
    }
} 