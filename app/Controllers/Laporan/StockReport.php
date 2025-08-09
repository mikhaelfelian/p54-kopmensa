<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: Controller for handling stock reports
 * This file represents the StockReport controller.
 */

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Models\ItemStokModel;
use App\Models\ItemModel;
use App\Models\GudangModel;
use App\Models\KategoriModel;
use App\Models\MerkModel;
use App\Models\ItemHistModel;

class StockReport extends BaseController
{
    protected $itemStokModel;
    protected $itemHistModel;
    protected $itemModel;
    protected $gudangModel;
    protected $kategoriModel;
    protected $merkModel;

    public function __construct()
    {
        parent::__construct();
        $this->itemStokModel = new ItemStokModel();
        $this->itemHistModel = new ItemHistModel();
        $this->itemModel = new ItemModel();
        $this->gudangModel = new GudangModel();
        $this->kategoriModel = new KategoriModel();
        $this->merkModel = new MerkModel();
    }

    public function index()
    {
        $idGudang = $this->request->getGet('id_gudang');
        $idKategori = $this->request->getGet('id_kategori');
        $idMerk = $this->request->getGet('id_merk');
        $keyword = $this->request->getGet('keyword');
        $stockType = $this->request->getGet('stock_type') ?? 'all';

        // Build query
        $builder = $this->itemStokModel->select('
                tbl_m_item_stok.*,
                tbl_m_item.item as item_nama,
                tbl_m_item.kode as item_kode,
                tbl_m_item.harga_beli,
                tbl_m_item.harga_jual,
                tbl_m_kategori.kategori as kategori_nama,
                tbl_m_merk.merk as merk_nama,
                tbl_m_gudang.nama as gudang_nama,
                tbl_m_satuan.satuanBesar as satuan_nama
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'left')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_stok.id_gudang', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->where('tbl_m_item_stok.status', '1')
            ->where('tbl_m_item.status_hps', '0');

        // Apply filters
        if ($idGudang) {
            $builder->where('tbl_m_item_stok.id_gudang', $idGudang);
        }

        if ($idKategori) {
            $builder->where('tbl_m_item.id_kategori', $idKategori);
        }

        if ($idMerk) {
            $builder->where('tbl_m_item.id_merk', $idMerk);
        }

        if ($keyword) {
            $builder->groupStart()
                ->like('tbl_m_item.item', $keyword)
                ->orLike('tbl_m_item.kode', $keyword)
                ->groupEnd();
        }

        // Apply stock type filter
        if ($stockType === 'in_stock') {
            $builder->where('tbl_m_item_stok.jml >', 0);
        } elseif ($stockType === 'out_of_stock') {
            $builder->where('tbl_m_item_stok.jml <=', 0);
        }

        $stocks = $builder->orderBy('tbl_m_item.item', 'ASC')->findAll();

        // Calculate summary
        $totalItems = count($stocks);
        $totalStock = 0;
        $totalValue = 0;
        $inStockCount = 0;
        $outOfStockCount = 0;

        foreach ($stocks as $stock) {
            $stockQty = $stock->jml ?? 0;
            $totalStock += $stockQty;
            $totalValue += ($stockQty * ($stock->harga_beli ?? 0));
            
            if ($stockQty > 0) {
                $inStockCount++;
            } else {
                $outOfStockCount++;
            }
        }

        // Get filter options
        $gudangList = $this->gudangModel->where('status', '1')->findAll();
        $kategoriList = $this->kategoriModel->where('status', '1')->findAll();
        $merkList = $this->merkModel->where('status', '1')->findAll();

        $data = [
            'title' => 'Laporan Stok',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'stocks' => $stocks,
            'totalItems' => $totalItems,
            'totalStock' => $totalStock,
            'totalValue' => $totalValue,
            'inStockCount' => $inStockCount,
            'outOfStockCount' => $outOfStockCount,
            'idGudang' => $idGudang,
            'idKategori' => $idKategori,
            'idMerk' => $idMerk,
            'keyword' => $keyword,
            'stockType' => $stockType,
            'gudangList' => $gudangList,
            'kategoriList' => $kategoriList,
            'merkList' => $merkList,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Laporan</li>
                <li class="breadcrumb-item active">Laporan Stok</li>
            '
        ];

        return view($this->theme->getThemePath() . '/laporan/stock/index', $data);
    }

    public function detail($id)
    {
        $item = $this->itemStokModel->select('
                tbl_m_item_stok.*,
                tbl_m_item.item as item_nama,
                tbl_m_item.kode as item_kode,
                tbl_m_item.harga_beli,
                tbl_m_item.harga_jual,
                tbl_m_kategori.kategori as kategori_nama,
                tbl_m_merk.merk as merk_nama,
                tbl_m_gudang.nama as gudang_nama,
                tbl_m_satuan.satuanBesar as satuan_nama
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'left')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_stok.id_gudang', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->where('tbl_m_item_stok.id', $id)
            ->first();

        if (!$item) {
            return redirect()->to('laporan/stock')->with('error', 'Data stok tidak ditemukan');
        }

        // Get stock history
        $stockHistory = $this->itemHistModel->select('
                tbl_m_item_hist.*,
                tbl_m_item.item as item_nama,
                tbl_m_gudang.nama as gudang_nama
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_hist.id_item', 'left')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_hist.id_gudang', 'left')
            ->where('tbl_m_item_hist.id_item', $item->id_item)
            ->where('tbl_m_item_hist.id_gudang', $item->id_gudang)
            ->orderBy('tbl_m_item_hist.created_at', 'DESC')
            ->limit(20)
            ->findAll();

        $data = [
            'title' => 'Detail Stok - ' . $item->item_nama,
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'item' => $item,
            'stockHistory' => $stockHistory,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('laporan/stock') . '">Laporan Stok</a></li>
                <li class="breadcrumb-item active">Detail</li>
            '
        ];

        return view($this->theme->getThemePath() . '/laporan/stock/detail', $data);
    }

    public function export_excel()
    {
        $idGudang = $this->request->getGet('id_gudang');
        $idKategori = $this->request->getGet('id_kategori');
        $idMerk = $this->request->getGet('id_merk');
        $keyword = $this->request->getGet('keyword');
        $stockType = $this->request->getGet('stock_type') ?? 'all';

        // Build query
        $builder = $this->itemStokModel->select('
                tbl_m_item_stok.*,
                tbl_m_item.item as item_nama,
                tbl_m_item.kode as item_kode,
                tbl_m_item.harga_beli,
                tbl_m_item.harga_jual,
                tbl_m_kategori.kategori as kategori_nama,
                tbl_m_merk.merk as merk_nama,
                tbl_m_gudang.nama as gudang_nama,
                tbl_m_satuan.satuanBesar as satuan_nama
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'left')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_stok.id_gudang', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->where('tbl_m_item_stok.status', '1')
            ->where('tbl_m_item.status_hps', '0');

        // Apply filters
        if ($idGudang) {
            $builder->where('tbl_m_item_stok.id_gudang', $idGudang);
        }

        if ($idKategori) {
            $builder->where('tbl_m_item.id_kategori', $idKategori);
        }

        if ($idMerk) {
            $builder->where('tbl_m_item.id_merk', $idMerk);
        }

        if ($keyword) {
            $builder->groupStart()
                ->like('tbl_m_item.item', $keyword)
                ->orLike('tbl_m_item.kode', $keyword)
                ->groupEnd();
        }

        // Apply stock type filter
        if ($stockType === 'in_stock') {
            $builder->where('tbl_m_item_stok.jml >', 0);
        } elseif ($stockType === 'out_of_stock') {
            $builder->where('tbl_m_item_stok.jml <=', 0);
        }

        $stocks = $builder->orderBy('tbl_m_item.item', 'ASC')->findAll();

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'LAPORAN STOK');
        $sheet->setCellValue('A2', 'Tanggal: ' . date('d/m/Y H:i:s'));
        
        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Kode Item');
        $sheet->setCellValue('C4', 'Nama Item');
        $sheet->setCellValue('D4', 'Kategori');
        $sheet->setCellValue('E4', 'Merk');
        $sheet->setCellValue('F4', 'Gudang');
        $sheet->setCellValue('G4', 'Stok');
        $sheet->setCellValue('H4', 'Satuan');
        $sheet->setCellValue('I4', 'Harga Beli');
        $sheet->setCellValue('J4', 'Harga Jual');
        $sheet->setCellValue('K4', 'Total Nilai');

        $row = 5;
        $totalValue = 0;

        foreach ($stocks as $index => $stock) {
            $stockQty = $stock->jml ?? 0;
            $itemValue = $stockQty * ($stock->harga_beli ?? 0);
            $totalValue += $itemValue;

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $stock->item_kode ?? '-');
            $sheet->setCellValue('C' . $row, $stock->item_nama ?? '-');
            $sheet->setCellValue('D' . $row, $stock->kategori_nama ?? '-');
            $sheet->setCellValue('E' . $row, $stock->merk_nama ?? '-');
            $sheet->setCellValue('F' . $row, $stock->gudang_nama ?? '-');
            $sheet->setCellValue('G' . $row, number_format($stockQty, 0, ',', '.'));
            $sheet->setCellValue('H' . $row, $stock->satuan_nama ?? '-');
            $sheet->setCellValue('I' . $row, number_format($stock->harga_beli ?? 0, 0, ',', '.'));
            $sheet->setCellValue('J' . $row, number_format($stock->harga_jual ?? 0, 0, ',', '.'));
            $sheet->setCellValue('K' . $row, number_format($itemValue, 0, ',', '.'));
            
            $row++;
        }

        // Add total
        $sheet->setCellValue('A' . $row, 'TOTAL NILAI');
        $sheet->setCellValue('K' . $row, number_format($totalValue, 0, ',', '.'));

        // Auto size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create response
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Laporan_Stok_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
