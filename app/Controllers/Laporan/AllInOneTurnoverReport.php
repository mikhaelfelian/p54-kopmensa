<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-09-11
 * Github: github.com/mikhaelfelian
 * Description: Controller for all-in-one turnover report (comprehensive sales report)
 * This file represents the AllInOneTurnoverReport controller.
 */

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Models\TransJualModel;
use App\Models\TransJualDetModel;
use App\Models\TransBeliModel;
use App\Models\GudangModel;
use App\Models\KategoriModel;
use App\Models\ItemModel;

class AllInOneTurnoverReport extends BaseController
{
    protected $transJualModel;
    protected $transJualDetModel;
    protected $transBeliModel;
    protected $gudangModel;
    protected $kategoriModel;
    protected $itemModel;

    public function __construct()
    {
        parent::__construct();
        $this->transJualModel = new TransJualModel();
        $this->transJualDetModel = new TransJualDetModel();
        $this->transBeliModel = new TransBeliModel();
        $this->gudangModel = new GudangModel();
        $this->kategoriModel = new KategoriModel();
        $this->itemModel = new ItemModel();
    }

    public function index()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idGudang = $this->request->getGet('id_gudang');
        $idKategori = $this->request->getGet('id_kategori');

        // 1. Sales Summary
        $salesBuilder = $this->transJualModel->select('
                COUNT(id) as total_transactions,
                SUM(jml_total) as total_sales,
                SUM(CASE WHEN metode_bayar IN ("cash", "tunai") THEN jml_total ELSE 0 END) as cash_sales,
                SUM(CASE WHEN metode_bayar NOT IN ("cash", "tunai") THEN jml_total ELSE 0 END) as non_cash_sales,
                AVG(jml_total) as avg_transaction_value
            ')
            ->where('status_nota', '1')
            ->where('deleted_at IS NULL');

        if ($startDate && $endDate) {
            $salesBuilder->where('DATE(tgl_masuk) >=', $startDate)
                        ->where('DATE(tgl_masuk) <=', $endDate);
        }

        if ($idGudang) {
            $salesBuilder->where('id_gudang', $idGudang);
        }

        $salesSummary = $salesBuilder->first();

        // 2. Daily Sales Trend
        $dailySalesBuilder = $this->transJualModel->select('
                DATE(tgl_masuk) as sale_date,
                COUNT(id) as daily_transactions,
                SUM(jml_total) as daily_sales
            ')
            ->where('status_nota', '1')
            ->where('deleted_at IS NULL');

        if ($startDate && $endDate) {
            $dailySalesBuilder->where('DATE(tgl_masuk) >=', $startDate)
                            ->where('DATE(tgl_masuk) <=', $endDate);
        }

        if ($idGudang) {
            $dailySalesBuilder->where('id_gudang', $idGudang);
        }

        $dailySales = $dailySalesBuilder->groupBy('DATE(tgl_masuk)')
                                      ->orderBy('DATE(tgl_masuk)', 'ASC')
                                      ->findAll();

        // 3. Top Products
        $topProductsBuilder = $this->transJualDetModel->select('
                tbl_trans_jual_det.id_item,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_kategori.kategori,
                SUM(tbl_trans_jual_det.jml) as total_qty,
                SUM(tbl_trans_jual_det.subtotal) as total_revenue,
                COUNT(DISTINCT tbl_trans_jual.id) as total_transactions
            ')
            ->join('tbl_trans_jual', 'tbl_trans_jual.id = tbl_trans_jual_det.id_penjualan')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_jual_det.id_item')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->where('tbl_trans_jual.status_nota', '1')
            ->where('tbl_trans_jual.deleted_at IS NULL')
            ->groupBy('tbl_trans_jual_det.id_item');

        if ($startDate && $endDate) {
            $topProductsBuilder->where('DATE(tbl_trans_jual.tgl_masuk) >=', $startDate)
                              ->where('DATE(tbl_trans_jual.tgl_masuk) <=', $endDate);
        }

        if ($idGudang) {
            $topProductsBuilder->where('tbl_trans_jual.id_gudang', $idGudang);
        }

        if ($idKategori) {
            $topProductsBuilder->where('tbl_m_item.id_kategori', $idKategori);
        }

        $topProducts = $topProductsBuilder->orderBy('total_revenue', 'DESC')
                                         ->limit(10)
                                         ->findAll();

        // 4. Sales by Category
        $categoryBuilder = $this->transJualDetModel->select('
                tbl_m_kategori.kategori,
                SUM(tbl_trans_jual_det.jml) as total_qty,
                SUM(tbl_trans_jual_det.subtotal) as total_revenue,
                COUNT(DISTINCT tbl_trans_jual.id) as total_transactions
            ')
            ->join('tbl_trans_jual', 'tbl_trans_jual.id = tbl_trans_jual_det.id_penjualan')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_jual_det.id_item')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->where('tbl_trans_jual.status_nota', '1')
            ->where('tbl_trans_jual.deleted_at IS NULL')
            ->groupBy('tbl_m_kategori.id');

        if ($startDate && $endDate) {
            $categoryBuilder->where('DATE(tbl_trans_jual.tgl_masuk) >=', $startDate)
                           ->where('DATE(tbl_trans_jual.tgl_masuk) <=', $endDate);
        }

        if ($idGudang) {
            $categoryBuilder->where('tbl_trans_jual.id_gudang', $idGudang);
        }

        $categorySales = $categoryBuilder->orderBy('total_revenue', 'DESC')->findAll();

        // 5. Sales by Warehouse/Store
        $warehouseBuilder = $this->transJualModel->select('
                tbl_m_gudang.nama as gudang_nama,
                COUNT(tbl_trans_jual.id) as total_transactions,
                SUM(tbl_trans_jual.jml_total) as total_sales
            ')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_trans_jual.id_gudang', 'left')
            ->where('tbl_trans_jual.status_nota', '1')
            ->where('tbl_trans_jual.deleted_at IS NULL')
            ->groupBy('tbl_trans_jual.id_gudang');

        if ($startDate && $endDate) {
            $warehouseBuilder->where('DATE(tbl_trans_jual.tgl_masuk) >=', $startDate)
                            ->where('DATE(tbl_trans_jual.tgl_masuk) <=', $endDate);
        }

        $warehouseSales = $warehouseBuilder->orderBy('total_sales', 'DESC')->findAll();

        // 6. Payment Method Analysis
        $paymentBuilder = $this->transJualModel->select('
                metode_bayar,
                COUNT(id) as total_transactions,
                SUM(jml_total) as total_amount
            ')
            ->where('status_nota', '1')
            ->where('deleted_at IS NULL')
            ->groupBy('metode_bayar');

        if ($startDate && $endDate) {
            $paymentBuilder->where('DATE(tgl_masuk) >=', $startDate)
                          ->where('DATE(tgl_masuk) <=', $endDate);
        }

        if ($idGudang) {
            $paymentBuilder->where('id_gudang', $idGudang);
        }

        $paymentMethods = $paymentBuilder->orderBy('total_amount', 'DESC')->findAll();

        // Get filter options
        $gudangList = $this->gudangModel->where('status', '1')->findAll();
        $kategoriList = $this->kategoriModel->where('status', '1')->findAll();

        $data = [
            'title' => 'Laporan Omset Terpadu (All-in-One)',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'idGudang' => $idGudang,
            'idKategori' => $idKategori,
            'gudangList' => $gudangList,
            'kategoriList' => $kategoriList,
            'salesSummary' => $salesSummary,
            'dailySales' => $dailySales,
            'topProducts' => $topProducts,
            'categorySales' => $categorySales,
            'warehouseSales' => $warehouseSales,
            'paymentMethods' => $paymentMethods,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Laporan</li>
                <li class="breadcrumb-item active">Omset Terpadu</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/all_in_one_turnover/index', $data);
    }

    public function export()
    {
        // Export comprehensive Excel report
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idGudang = $this->request->getGet('id_gudang');

        // Get all data (similar to index but optimized for export)
        $salesSummary = $this->transJualModel->select('
                COUNT(id) as total_transactions,
                SUM(jml_total) as total_sales,
                SUM(CASE WHEN metode_bayar IN ("cash", "tunai") THEN jml_total ELSE 0 END) as cash_sales,
                SUM(CASE WHEN metode_bayar NOT IN ("cash", "tunai") THEN jml_total ELSE 0 END) as non_cash_sales
            ')
            ->where('status_nota', '1')
            ->where('deleted_at IS NULL')
            ->where('DATE(tgl_masuk) >=', $startDate)
            ->where('DATE(tgl_masuk) <=', $endDate)
            ->first();

        // Create Excel with multiple sheets
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        
        // Summary Sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ringkasan');
        
        $sheet->setCellValue('A1', 'LAPORAN OMSET TERPADU');
        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));
        
        $sheet->setCellValue('A4', 'RINGKASAN PENJUALAN');
        $sheet->setCellValue('A5', 'Total Transaksi');
        $sheet->setCellValue('B5', $salesSummary->total_transactions);
        $sheet->setCellValue('A6', 'Total Penjualan');
        $sheet->setCellValue('B6', $salesSummary->total_sales);
        $sheet->setCellValue('A7', 'Penjualan Cash');
        $sheet->setCellValue('B7', $salesSummary->cash_sales);
        $sheet->setCellValue('A8', 'Penjualan Non-Cash');
        $sheet->setCellValue('B8', $salesSummary->non_cash_sales);

        // Style the summary
        $sheet->getStyle('A1:B1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A4:A8')->getFont()->setBold(true);
        
        foreach (range('A', 'B') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Export
        $filename = 'Laporan_Omset_Terpadu_' . date('Y-m-d', strtotime($startDate)) . '_to_' . date('Y-m-d', strtotime($endDate)) . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
