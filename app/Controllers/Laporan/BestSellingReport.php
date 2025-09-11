<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-09-11
 * Github: github.com/mikhaelfelian
 * Description: Controller for best-selling product report
 * This file represents the BestSellingReport controller.
 */

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Models\TransJualDetModel;
use App\Models\ItemModel;
use App\Models\GudangModel;
use App\Models\KategoriModel;
use App\Models\MerkModel;

class BestSellingReport extends BaseController
{
    protected $transJualDetModel;
    protected $itemModel;
    protected $gudangModel;
    protected $kategoriModel;
    protected $merkModel;

    public function __construct()
    {
        parent::__construct();
        $this->transJualDetModel = new TransJualDetModel();
        $this->itemModel = new ItemModel();
        $this->gudangModel = new GudangModel();
        $this->kategoriModel = new KategoriModel();
        $this->merkModel = new MerkModel();
    }

    public function index()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idGudang = $this->request->getGet('id_gudang');
        $idKategori = $this->request->getGet('id_kategori');
        $idMerk = $this->request->getGet('id_merk');
        $limit = $this->request->getGet('limit') ?? 50;

        // Build query for best-selling products
        $builder = $this->transJualDetModel->select('
                tbl_trans_jual_det.id_item,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_item.barcode,
                tbl_m_item.harga_jual,
                tbl_m_item.harga_beli,
                tbl_m_kategori.kategori,
                tbl_m_merk.merk,
                tbl_m_satuan.SatuanBesar as satuan,
                SUM(tbl_trans_jual_det.jml) as total_qty_sold,
                SUM(tbl_trans_jual_det.subtotal) as total_revenue,
                AVG(tbl_trans_jual_det.harga) as avg_selling_price,
                COUNT(DISTINCT tbl_trans_jual.id) as total_transactions,
                MIN(tbl_trans_jual.tgl_masuk) as first_sale_date,
                MAX(tbl_trans_jual.tgl_masuk) as last_sale_date,
                SUM(tbl_trans_jual_det.jml * tbl_m_item.harga_beli) as total_cost,
                (SUM(tbl_trans_jual_det.subtotal) - SUM(tbl_trans_jual_det.jml * tbl_m_item.harga_beli)) as total_profit
            ')
            ->join('tbl_trans_jual', 'tbl_trans_jual.id = tbl_trans_jual_det.id_penjualan')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_jual_det.id_item')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->where('tbl_trans_jual.status_nota', '1')
            ->where('tbl_trans_jual.deleted_at IS NULL')
            ->groupBy('tbl_trans_jual_det.id_item');

        // Apply date filter
        if ($startDate && $endDate) {
            $builder->where('DATE(tbl_trans_jual.tgl_masuk) >=', $startDate)
                   ->where('DATE(tbl_trans_jual.tgl_masuk) <=', $endDate);
        }

        // Apply filters
        if ($idGudang) {
            $builder->where('tbl_trans_jual.id_gudang', $idGudang);
        }

        if ($idKategori) {
            $builder->where('tbl_m_item.id_kategori', $idKategori);
        }

        if ($idMerk) {
            $builder->where('tbl_m_item.id_merk', $idMerk);
        }

        // Order by quantity sold (best-selling)
        $bestSellingProducts = $builder->orderBy('total_qty_sold', 'DESC')
                                     ->limit($limit)
                                     ->findAll();

        // Calculate rankings and add profit margins
        foreach ($bestSellingProducts as $index => &$product) {
            $product->rank = $index + 1;
            $product->profit_margin = $product->total_revenue > 0 
                ? (($product->total_profit / $product->total_revenue) * 100) 
                : 0;
            $product->days_selling = $product->first_sale_date && $product->last_sale_date
                ? ceil((strtotime($product->last_sale_date) - strtotime($product->first_sale_date)) / (60 * 60 * 24)) + 1
                : 1;
            $product->avg_qty_per_day = $product->days_selling > 0 
                ? $product->total_qty_sold / $product->days_selling 
                : 0;
        }

        // Calculate summary statistics
        $totalProducts = count($bestSellingProducts);
        $totalQtySold = array_sum(array_column($bestSellingProducts, 'total_qty_sold'));
        $totalRevenue = array_sum(array_column($bestSellingProducts, 'total_revenue'));
        $totalProfit = array_sum(array_column($bestSellingProducts, 'total_profit'));

        // Get category breakdown
        $categoryBreakdown = [];
        foreach ($bestSellingProducts as $product) {
            $category = $product->kategori ?: 'Uncategorized';
            if (!isset($categoryBreakdown[$category])) {
                $categoryBreakdown[$category] = [
                    'total_qty' => 0,
                    'total_revenue' => 0,
                    'product_count' => 0
                ];
            }
            $categoryBreakdown[$category]['total_qty'] += (float) $product->total_qty_sold;
            $categoryBreakdown[$category]['total_revenue'] += (float) $product->total_revenue;
            $categoryBreakdown[$category]['product_count']++;
        }

        // Sort category breakdown by revenue
        uasort($categoryBreakdown, function($a, $b) {
            return $b['total_revenue'] <=> $a['total_revenue'];
        });

        // Get filter options
        $gudangList = $this->gudangModel->where('status', '1')->findAll();
        $kategoriList = $this->kategoriModel->where('status', '1')->findAll();
        $merkList = $this->merkModel->where('status', '1')->findAll();

        $data = [
            'title' => 'Laporan Produk Terlaris',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'bestSellingProducts' => $bestSellingProducts,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'idGudang' => $idGudang,
            'idKategori' => $idKategori,
            'idMerk' => $idMerk,
            'limit' => $limit,
            'gudangList' => $gudangList,
            'kategoriList' => $kategoriList,
            'merkList' => $merkList,
            'summary' => [
                'total_products' => $totalProducts,
                'total_qty_sold' => $totalQtySold,
                'total_revenue' => $totalRevenue,
                'total_profit' => $totalProfit,
                'avg_profit_margin' => $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0
            ],
            'categoryBreakdown' => $categoryBreakdown,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Laporan</li>
                <li class="breadcrumb-item active">Produk Terlaris</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/best_selling/index', $data);
    }

    public function export()
    {
        // Same logic as index but for export
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idGudang = $this->request->getGet('id_gudang');
        $idKategori = $this->request->getGet('id_kategori');
        $idMerk = $this->request->getGet('id_merk');
        $limit = $this->request->getGet('limit') ?? 50;

        $builder = $this->transJualDetModel->select('
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_kategori.kategori,
                tbl_m_merk.merk,
                tbl_m_satuan.SatuanBesar as satuan,
                SUM(tbl_trans_jual_det.jml) as total_qty_sold,
                SUM(tbl_trans_jual_det.subtotal) as total_revenue,
                AVG(tbl_trans_jual_det.harga) as avg_selling_price,
                COUNT(DISTINCT tbl_trans_jual.id) as total_transactions
            ')
            ->join('tbl_trans_jual', 'tbl_trans_jual.id = tbl_trans_jual_det.id_penjualan')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_jual_det.id_item')
            ->join('tbl_m_kategori', 'tbl_m_kategori.id = tbl_m_item.id_kategori', 'left')
            ->join('tbl_m_merk', 'tbl_m_merk.id = tbl_m_item.id_merk', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->where('tbl_trans_jual.status_nota', '1')
            ->where('tbl_trans_jual.deleted_at IS NULL')
            ->groupBy('tbl_trans_jual_det.id_item');

        // Apply filters
        if ($startDate && $endDate) {
            $builder->where('DATE(tbl_trans_jual.tgl_masuk) >=', $startDate)
                   ->where('DATE(tbl_trans_jual.tgl_masuk) <=', $endDate);
        }

        if ($idGudang) {
            $builder->where('tbl_trans_jual.id_gudang', $idGudang);
        }

        if ($idKategori) {
            $builder->where('tbl_m_item.id_kategori', $idKategori);
        }

        if ($idMerk) {
            $builder->where('tbl_m_item.id_merk', $idMerk);
        }

        $bestSellingProducts = $builder->orderBy('total_qty_sold', 'DESC')
                                     ->limit($limit)
                                     ->findAll();

        // Create Excel export
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Laporan Produk Terlaris');
        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));

        // Column headers
        $sheet->setCellValue('A4', 'Rank');
        $sheet->setCellValue('B4', 'Kode');
        $sheet->setCellValue('C4', 'Nama Produk');
        $sheet->setCellValue('D4', 'Kategori');
        $sheet->setCellValue('E4', 'Merk');
        $sheet->setCellValue('F4', 'Satuan');
        $sheet->setCellValue('G4', 'Qty Terjual');
        $sheet->setCellValue('H4', 'Total Revenue');
        $sheet->setCellValue('I4', 'Rata-rata Harga');
        $sheet->setCellValue('J4', 'Total Transaksi');

        // Data rows
        $row = 5;
        $rank = 1;

        foreach ($bestSellingProducts as $product) {
            $sheet->setCellValue('A' . $row, $rank++);
            $sheet->setCellValue('B' . $row, $product->kode);
            $sheet->setCellValue('C' . $row, $product->item);
            $sheet->setCellValue('D' . $row, $product->kategori);
            $sheet->setCellValue('E' . $row, $product->merk);
            $sheet->setCellValue('F' . $row, $product->satuan);
            $sheet->setCellValue('G' . $row, (float) $product->total_qty_sold);
            $sheet->setCellValue('H' . $row, (float) $product->total_revenue);
            $sheet->setCellValue('I' . $row, (float) $product->avg_selling_price);
            $sheet->setCellValue('J' . $row, $product->total_transactions);
            $row++;
        }

        // Style the sheet
        $sheet->getStyle('A1:J1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A4:J4')->getFont()->setBold(true);
        $sheet->getStyle('A4:J4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');

        // Auto-size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Export
        $filename = 'Laporan_Produk_Terlaris_' . date('Y-m-d', strtotime($startDate)) . '_to_' . date('Y-m-d', strtotime($endDate)) . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
