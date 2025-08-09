<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: Controller for handling outlet reports
 * This file represents the OutletReport controller.
 */

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Models\OutletModel;
use App\Models\TransJualModel;
use App\Models\ItemStokModel;
use App\Models\KaryawanModel;

class OutletReport extends BaseController
{
    protected $outletModel;
    protected $transJualModel;
    protected $itemStokModel;
    protected $karyawanModel;

    public function __construct()
    {
        parent::__construct();
        $this->outletModel = new OutletModel();
        $this->transJualModel = new TransJualModel();
        $this->itemStokModel = new ItemStokModel();
        $this->karyawanModel = new KaryawanModel();
    }

    public function index()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idOutlet = $this->request->getGet('id_outlet');

        // Avoid duplicate FROM clause and alias issues by not calling ->from() on the model
        $outletsQuery = $this->outletModel->select('
                tbl_m_outlet.*,
                COUNT(DISTINCT tj.id) as total_transactions,
                SUM(tj.jml_gtotal) as total_sales,
                COUNT(DISTINCT isok.id_item) as total_items
            ')
            ->join('tbl_trans_jual tj', 'tj.id_gudang = tbl_m_outlet.id', 'left')
            ->join('tbl_m_item_stok isok', 'isok.id_gudang = tbl_m_outlet.id', 'left')
            ->where('tbl_m_outlet.status', '1')
            ->where('tbl_m_outlet.status_hps', '0')
            ->groupBy('tbl_m_outlet.id');

        // Apply date filter
        if ($startDate && $endDate) {
            $start = $startDate . ' 00:00:00';
            $end = $endDate . ' 23:59:59';
            $outletsQuery->where("(tj.tgl_masuk IS NULL OR (tj.tgl_masuk >= '{$start}' AND tj.tgl_masuk <= '{$end}'))");
        }

        if ($idOutlet) {
            $outletsQuery->where('tbl_m_outlet.id', $idOutlet);
        }

        $outletData = $outletsQuery->findAll();

        // Get detailed data for each outlet
        $outletDetails = [];
        foreach ($outletData as $outlet) {
            // Get sales data for this outlet
            $salesQuery = $this->transJualModel->select('
                    COUNT(*) as total_transactions,
                    SUM(jml_gtotal) as total_sales,
                    AVG(jml_gtotal) as avg_sales,
                    COUNT(DISTINCT id_pelanggan) as unique_customers
                ')
                ->where('id_gudang', $outlet->id)
                ->where('status_nota', '1')
                ->where('status_hps', '0');

            if ($startDate && $endDate) {
                $salesQuery->where('tgl_masuk >=', $startDate . ' 00:00:00')
                          ->where('tgl_masuk <=', $endDate . ' 23:59:59');
            }

            $salesData = $salesQuery->first();

            // Get stock data for this outlet
            $stockData = $this->itemStokModel->select('
                    COUNT(*) as total_items,
                    SUM(jml) as total_stock,
                    COUNT(CASE WHEN jml > 0 THEN 1 END) as in_stock_items,
                    COUNT(CASE WHEN jml <= 0 THEN 1 END) as out_of_stock_items
                ')
                ->where('id_gudang', $outlet->id)
                ->where('status', '1')
                ->first();

            // Get top selling items
            $topItems = $this->transJualModel->select('
                    tbl_m_item.item as item_nama,
                    SUM(tbl_trans_jual_det.jml) as total_qty,
                    SUM(tbl_trans_jual_det.jml * tbl_trans_jual_det.harga) as total_value
                ')
                ->join('tbl_trans_jual_det', 'tbl_trans_jual_det.id_penjualan = tbl_trans_jual.id', 'left')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_jual_det.id_item', 'left')
                ->where('tbl_trans_jual.id_gudang', $outlet->id)
                ->where('tbl_trans_jual.status_nota', '1')
                ->where('tbl_trans_jual.status_hps', '0')
                ->groupBy('tbl_trans_jual_det.id_item')
                ->orderBy('total_qty', 'DESC')
                ->limit(5)
                ->findAll();

            $outletDetails[] = [
                'outlet' => $outlet,
                'sales' => $salesData,
                'stock' => $stockData,
                'top_items' => $topItems
            ];
        }

        // Calculate summary
        $totalOutlets = count($outletDetails);
        $totalSales = 0;
        $totalTransactions = 0;
        $totalItems = 0;

        foreach ($outletDetails as $detail) {
            $totalSales += $detail['sales']->total_sales ?? 0;
            $totalTransactions += $detail['sales']->total_transactions ?? 0;
            $totalItems += $detail['stock']->total_items ?? 0;
        }

        // Get filter options (fix: use correct table name and column names)
        $outletList = $this->outletModel
            ->where('tbl_m_outlet.status', '1')
            ->where('tbl_m_outlet.status_hps', '0')
            ->findAll();

        $data = [
            'title' => 'Laporan Outlet',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'outletDetails' => $outletDetails,
            'totalOutlets' => $totalOutlets,
            'totalSales' => $totalSales,
            'totalTransactions' => $totalTransactions,
            'totalItems' => $totalItems,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'idOutlet' => $idOutlet,
            'outletList' => $outletList,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Laporan</li>
                <li class="breadcrumb-item active">Laporan Outlet</li>
            '
        ];

        return view($this->theme->getThemePath() . '/laporan/outlet/index', $data);
    }

    public function detail($id)
    {
        $outlet = $this->outletModel->where('id', $id)->where('status_otl', '1')->first();

        if (!$outlet) {
            return redirect()->to('laporan/outlet')->with('error', 'Data outlet tidak ditemukan');
        }

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        // Get sales data
        $salesQuery = $this->transJualModel->select('
                tbl_trans_jual.*,
                tbl_m_pelanggan.nama as pelanggan_nama,
                tbl_m_karyawan.nama as sales_nama
            ')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_jual.id_sales', 'left')
            ->where('tbl_trans_jual.id_gudang', $id)
            ->where('tbl_trans_jual.status_nota', '1')
            ->where('tbl_trans_jual.status_hps', '0');

        if ($startDate && $endDate) {
            $salesQuery->where('tgl_masuk >=', $startDate . ' 00:00:00')
                      ->where('tgl_masuk <=', $endDate . ' 23:59:59');
        }

        $sales = $salesQuery->orderBy('tbl_trans_jual.tgl_masuk', 'DESC')->findAll();

        // Get stock data
        $stocks = $this->itemStokModel->select('
                tbl_m_item_stok.*,
                tbl_m_item.item as item_nama,
                tbl_m_item.kode as item_kode,
                tbl_m_kategori.kategori as kategori_nama,
                tbl_m_merk.merk as merk_nama
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'left')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->where('tbl_m_item_stok.id_gudang', $id)
            ->where('tbl_m_item_stok.status', '1')
            ->orderBy('tbl_m_item.item', 'ASC')
            ->findAll();

        // Calculate summary
        $totalSales = 0;
        $totalTransactions = count($sales);
        $totalStock = 0;
        $inStockCount = 0;
        $outOfStockCount = 0;

        foreach ($sales as $sale) {
            $totalSales += $sale->jml_gtotal ?? 0;
        }

        foreach ($stocks as $stock) {
            $totalStock += $stock->jml ?? 0;
            if (($stock->jml ?? 0) > 0) {
                $inStockCount++;
            } else {
                $outOfStockCount++;
            }
        }

        $data = [
            'title' => 'Detail Outlet - ' . $outlet->nama,
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'outlet' => $outlet,
            'sales' => $sales,
            'stocks' => $stocks,
            'totalSales' => $totalSales,
            'totalTransactions' => $totalTransactions,
            'totalStock' => $totalStock,
            'inStockCount' => $inStockCount,
            'outOfStockCount' => $outOfStockCount,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('laporan/outlet') . '">Laporan Outlet</a></li>
                <li class="breadcrumb-item active">Detail</li>
            '
        ];

        return view($this->theme->getThemePath() . '/laporan/outlet/detail', $data);
    }

    public function export_excel()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idOutlet = $this->request->getGet('id_outlet');

        // Get outlet data
        $outlets = $this->outletModel->select('
                tbl_m_gudang.*,
                COUNT(DISTINCT tbl_trans_jual.id) as total_transactions,
                SUM(tbl_trans_jual.jml_gtotal) as total_sales
            ')
            ->join('tbl_trans_jual', 'tbl_trans_jual.id_gudang = tbl_m_gudang.id', 'left')
            ->where('tbl_m_gudang.status_otl', '1')
            ->where('tbl_m_gudang.status_hps', '0')
            ->groupBy('tbl_m_gudang.id');

        if ($startDate && $endDate) {
            $outlets->where('(tbl_trans_jual.tgl_masuk IS NULL OR (tbl_trans_jual.tgl_masuk >= ? AND tbl_trans_jual.tgl_masuk <= ?))', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        if ($idOutlet) {
            $outlets->where('tbl_m_gudang.id', $idOutlet);
        }

        $outletData = $outlets->findAll();

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'LAPORAN OUTLET');
        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));
        
        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Nama Outlet');
        $sheet->setCellValue('C4', 'Alamat');
        $sheet->setCellValue('D4', 'Total Transaksi');
        $sheet->setCellValue('E4', 'Total Penjualan');
        $sheet->setCellValue('F4', 'Rata-rata Penjualan');

        $row = 5;
        $totalSales = 0;
        $totalTransactions = 0;

        foreach ($outletData as $index => $outlet) {
            $avgSales = ($outlet->total_transactions > 0) ? ($outlet->total_sales / $outlet->total_transactions) : 0;
            $totalSales += $outlet->total_sales ?? 0;
            $totalTransactions += $outlet->total_transactions ?? 0;

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $outlet->nama ?? '-');
            $sheet->setCellValue('C' . $row, $outlet->alamat ?? '-');
            $sheet->setCellValue('D' . $row, $outlet->total_transactions ?? 0);
            $sheet->setCellValue('E' . $row, number_format($outlet->total_sales ?? 0, 0, ',', '.'));
            $sheet->setCellValue('F' . $row, number_format($avgSales, 0, ',', '.'));
            
            $row++;
        }

        // Add total
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('D' . $row, $totalTransactions);
        $sheet->setCellValue('E' . $row, number_format($totalSales, 0, ',', '.'));

        // Auto size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create response
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Laporan_Outlet_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
