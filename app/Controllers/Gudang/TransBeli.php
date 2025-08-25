<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github : github.com/mikhaelfelian
 * description : Controller for handling receiving completed purchases
 * This file represents the TransBeli controller in Gudang namespace.
 */

namespace App\Controllers\Gudang;

use App\Controllers\BaseController;
use App\Models\TransBeliModel;
use App\Models\TransBeliDetModel;
use App\Models\SupplierModel;
use App\Models\GudangModel;
use App\Models\ItemModel;
use App\Models\ItemStokModel;
use App\Models\ItemHistModel;


class TransBeli extends BaseController
{
    protected $transBeliModel;
    protected $transBeliDetModel;
    protected $supplierModel;
    protected $gudangModel;
    protected $itemModel;
    protected $itemStokModel;
    protected $itemHistModel;

    public function __construct()
    {
        $this->transBeliModel     = new TransBeliModel();
        $this->transBeliDetModel  = new TransBeliDetModel();
        $this->supplierModel      = new SupplierModel();
        $this->gudangModel        = new GudangModel();
        $this->itemModel          = new ItemModel();
        $this->itemStokModel      = new ItemStokModel();
        $this->itemHistModel      = new ItemHistModel();
    }

    /**
     * Display list of completed purchases for receiving
     */
    public function index()
    {
        $currentPage = $this->request->getVar('page_transbeli') ?? 1;
        $perPage = $this->pengaturan->pagination_limit;

        // Get completed purchases (status = 1)
        $transactions = $this->transBeliModel->select('
                tbl_trans_beli.*,
                tbl_m_supplier.nama as supplier_nama
            ')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli.id_supplier', 'left')
            ->where('tbl_trans_beli.status_nota', '1')
            ->paginate($perPage, 'transbeli');

        $data = [
            'title'         => 'Penerimaan Barang',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'transactions'  => $transactions,
            'pager'         => $this->transBeliModel->pager,
            'currentPage'   => $currentPage,
            'perPage'       => $perPage,
        ];

        return $this->view($this->theme->getThemePath() . '/gudang/penerimaan/index', $data);
    }

    /**
     * Handle receiving form for a specific purchase transaction
     * 
     * @param int $id Transaction ID
     * @return mixed
     */
    public function terima($id)
    {
        try {
            // Get transaction data
            $transaksi = $this->transBeliModel->select('
                    tbl_trans_beli.*,
                    tbl_m_supplier.nama as supplier_nama,
                    tbl_m_supplier.alamat as supplier_alamat
                ')
                ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli.id_supplier', 'left')
                ->where('tbl_trans_beli.id', $id)
                ->where('tbl_trans_beli.status_nota', '1')
                ->first();

            if (!$transaksi) {
                throw new \Exception('Transaksi tidak ditemukan atau belum diproses');
            }

            // Get transaction items
            $items = $this->transBeliDetModel->select('
                    tbl_trans_beli_det.*,
                    tbl_m_item.kode as item_kode,
                    tbl_m_item.item as item_name,
                    tbl_m_satuan.satuanBesar as satuan_name
                ')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_beli_det.id_item', 'left')
                ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_trans_beli_det.id_satuan', 'left')
                ->where('id_pembelian', $id)
                ->findAll();

            // Get active warehouses
            $gudang = $this->gudangModel->where('status', '1')->where('status_hps', '0')->where('status', '1')->findAll();

            $data = [
                'title'         => 'Terima Barang - ' . $transaksi->no_nota,
                'Pengaturan'    => $this->pengaturan,
                'user'          => $this->ionAuth->user()->row(),
                'transaksi'     => $transaksi,
                'items'         => $items,
                'gudang'        => $gudang
            ];

            return $this->view($this->theme->getThemePath() . '/gudang/penerimaan/terima', $data);

        } catch (\Exception $e) {
            return redirect()->to('gudang/penerimaan')
                            ->with('error', 'Gagal memuat data penerimaan: ' . $e->getMessage());
        }
    }

    /**
     * Save receiving data and update stock
     * 
     * @param int $id Transaction ID
     * @return mixed
     */
    public function save($id)
    {
        try {
            // Validate transaction exists and is ready for receiving
            $transaksi = $this->transBeliModel->where('id', $id)
                                            ->where('status_nota', '1')
                                            ->first();

            if (!$transaksi) {
                throw new \Exception('Transaksi tidak ditemukan atau tidak siap untuk diterima');
            }

            // Get form data
            $jmlDiterima = $this->request->getPost('jml_diterima');
            $idGudang = $this->request->getPost('id_gudang');
            $statusItem = $this->request->getPost('status_item');
            $keterangan = $this->request->getPost('keterangan');
            $catatanUmum = $this->request->getPost('catatan_umum');

            if (!$jmlDiterima || !$idGudang) {
                throw new \Exception('Data penerimaan tidak lengkap');
            }

            // Start database transaction
            $this->db = \Config\Database::connect();
            $this->db->transStart();

            // Get transaction items
            $items = $this->transBeliDetModel->where('id_pembelian', $id)->findAll();
            $itemStokModel = new \App\Models\ItemStokModel();

            foreach ($items as $item) {
                $itemId = $item->id;
                
                if (isset($jmlDiterima[$itemId]) && isset($idGudang[$itemId])) {
                    $receivedQty     = floatval($jmlDiterima[$itemId]);
                    $gudangId        = intval($idGudang[$itemId]);
                    $itemStatus      = isset($statusItem[$itemId]) ? $statusItem[$itemId] : '1';
                    $itemKeterangan  = isset($keterangan[$itemId]) ? $keterangan[$itemId] : '';

                    // Validate warehouse exists
                    $gudang = $this->gudangModel->find($gudangId);
                    if (!$gudang) {
                        throw new \Exception('Gudang tidak ditemukan');
                    }

                    // Update stock if item is received (status = 1 or 3)
                    if (in_array($itemStatus, ['1', '3']) && $receivedQty > 0) {
                        // Get current stock
                        $currentStock = $itemStokModel->getStockByItemAndGudang($item->id_item, $gudangId);
                        $newStock = $currentStock ? (floatval($currentStock->jml) + $receivedQty) : $receivedQty;

                        // Update or create stock record
                        $itemStokModel->updateStock($item->id_item, $gudangId, $newStock, $this->ionAuth->user()->row()->id);
                    }

                    // Update transaction detail with receiving info
                    $this->transBeliDetModel->update($itemId, [
                        'jml_diterima'      => $receivedQty,
                        'id_gudang'         => $gudangId,
                        'status_terima'     => $itemStatus,
                        'keterangan_terima' => $itemKeterangan,
                        'tgl_terima'        => date('Y-m-d H:i:s'),
                        'id_user_terima'    => $this->ionAuth->user()->row()->id
                    ]);

                    // Add stock history using ItemHistModel
                    $itemHistModel = $this->itemHistModel;
                    $itemHistModel->addHistory([
                        'id_item'         => $item->id_item,
                        'id_satuan'       => $item->id_satuan,
                        'id_gudang'       => $gudangId,
                        'id_supplier'     => $transaksi->id_supplier,
                        'id_pembelian'    => $id,
                        'id_pembelian_det'=> $itemId,
                        'tgl_masuk'       => date('Y-m-d H:i:s'),
                        'no_nota'         => $transaksi->no_nota,
                        'kode'            => $item->kode,
                        'item'            => $item->item,
                        'keterangan'      => 'Pembelian - ' . $transaksi->no_nota,
                        'jml'             => $receivedQty,
                        'jml_satuan'      => $receivedQty,
                        'satuan'          => $item->satuan,
                        'status'          => '1', // 1 = Stok Masuk Pembelian
                        'id_user'         => $this->ionAuth->user()->row()->id,
                        'created_at'      => date('Y-m-d H:i:s'),
                        'updated_at'      => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            // Update transaction status to received
            $this->transBeliModel->update($id, [
                    'status_terima'    => '1',
                    'tgl_terima'       => date('Y-m-d H:i:s'),
                    'catatan_terima'   => $catatanUmum,
                    'id_user_terima'   => $this->ionAuth->user()->row()->id
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan data penerimaan');
            }

            return redirect()->to('gudang/penerimaan')
                            ->with('success', 'Barang berhasil diterima dan stok telah diperbarui');

        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal menyimpan penerimaan: ' . $e->getMessage())
                            ->withInput();
        }
    }
} 