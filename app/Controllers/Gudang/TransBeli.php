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

class TransBeli extends BaseController
{
    protected $transBeliModel;
    protected $transBeliDetModel;
    protected $supplierModel;

    public function __construct()
    {
        $this->transBeliModel = new TransBeliModel();
        $this->transBeliDetModel = new TransBeliDetModel();
        $this->supplierModel = new SupplierModel();
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

        return $this->view($this->theme->getThemePath() . '/gudang/trans_beli/index', $data);
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

            $data = [
                'title'         => 'Terima Barang - ' . $transaksi->no_nota,
                'Pengaturan'    => $this->pengaturan,
                'user'          => $this->ionAuth->user()->row(),
                'transaksi'     => $transaksi,
                'items'         => $items
            ];

            return $this->view($this->theme->getThemePath() . '/gudang/trans_beli/terima', $data);

        } catch (\Exception $e) {
            return redirect()->to('gudang/penerimaan')
                            ->with('error', 'Gagal memuat data penerimaan: ' . $e->getMessage());
        }
    }
} 