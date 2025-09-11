<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-09-11
 * Github: github.com/mikhaelfelian
 * Description: Controller for handling product sales reports
 * This file represents the ProductSalesReport controller.
 */

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Models\TransJualDetModel;
use App\Models\ItemModel;
use App\Models\GudangModel;
use App\Models\KategoriModel;
use App\Models\MerkModel;

class ProductSalesReport extends BaseController
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
        $sortBy = $this->request->getGet('sort_by') ?? 'total_qty';
        $sortOrder = $this->request->getGet('sort_order') ?? 'DESC';

        // Build query for product sales
        $builder = $this->transJualDetModel->select('
                tbl_trans_jual_det.id_item,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_item.barcode,
                tbl_m_kategori.kategori,
                tbl_m_merk.merk,
                tbl_m_satuan.SatuanBesar as satuan,
                SUM(tbl_trans_jual_det.jml) as total_qty,
                SUM(tbl_trans_jual_det.subtotal) as total_amount,
                AVG(tbl_trans_jual_det.harga) as avg_price,
                COUNT(DISTINCT tbl_trans_jual.id) as total_transactions,
                MIN(tbl_trans_jual.tgl_masuk) as first_sale,
                MAX(tbl_trans_jual.tgl_masuk) as last_sale
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

        // Apply sorting
        $validSortColumns = ['total_qty', 'total_amount', 'total_transactions', 'item'];
        if (in_array($sortBy, $validSortColumns)) {
            $builder->orderBy($sortBy, $sortOrder);
        } else {
            $builder->orderBy('total_qty', 'DESC');
        }

        $productSales = $builder->findAll();

        // Calculate summary
        $totalProducts = count($productSales);
        $totalQuantitySold = 0;
        $totalRevenue = 0;
        $totalTransactions = 0;

        foreach ($productSales as $product) {
            $totalQuantitySold += (float) $product->total_qty;
            $totalRevenue += (float) $product->total_amount;
            $totalTransactions += (int) $product->total_transactions;
        }

        // Get filter options
        $gudangList = $this->gudangModel->where('status', '1')->findAll();
        $kategoriList = $this->kategoriModel->where('status', '1')->findAll();
        $merkList = $this->merkModel->where('status', '1')->findAll();

        $data = [
            'title' => 'Laporan Penjualan Produk',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'productSales' => $productSales,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'idGudang' => $idGudang,
            'idKategori' => $idKategori,
            'idMerk' => $idMerk,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'gudangList' => $gudangList,
            'kategoriList' => $kategoriList,
            'merkList' => $merkList,
            'summary' => [
                'total_products' => $totalProducts,
                'total_quantity_sold' => $totalQuantitySold,
                'total_revenue' => $totalRevenue,
                'total_transactions' => $totalTransactions,
                'avg_revenue_per_product' => $totalProducts > 0 ? $totalRevenue / $totalProducts : 0
            ],
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Laporan</li>
                <li class="breadcrumb-item active">Penjualan Produk</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/product_sales/index', $data);
    }

    public function export()
    {
        // Same logic as index but for export
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idGudang = $this->request->getGet('id_gudang');
        $idKategori = $this->request->getGet('id_kategori');
        $idMerk = $this->request->getGet('id_merk');
        $sortBy = $this->request->getGet('sort_by') ?? 'total_qty';
        $sortOrder = $this->request->getGet('sort_order') ?? 'DESC';

        $builder = $this->transJualDetModel->select('
                tbl_trans_jual_det.id_item,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_item.barcode,
                tbl_m_kategori.kategori,
                tbl_m_merk.merk,
                tbl_m_satuan.SatuanBesar as satuan,
                SUM(tbl_trans_jual_det.jml) as total_qty,
                SUM(tbl_trans_jual_det.subtotal) as total_amount,
                AVG(tbl_trans_jual_det.harga) as avg_price,
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

        $validSortColumns = ['total_qty', 'total_amount', 'total_transactions', 'item'];
        if (in_array($sortBy, $validSortColumns)) {
            $builder->orderBy($sortBy, $sortOrder);
        } else {
            $builder->orderBy('total_qty', 'DESC');
        }

        $productSales = $builder->findAll();

        // Create Excel export
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Laporan Penjualan Produk');
        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));

        // Column headers
        $sheet->setCellValue('A4', 'No');
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
        $no = 1;

        foreach ($productSales as $product) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $product->kode);
            $sheet->setCellValue('C' . $row, $product->item);
            $sheet->setCellValue('D' . $row, $product->kategori);
            $sheet->setCellValue('E' . $row, $product->merk);
            $sheet->setCellValue('F' . $row, $product->satuan);
            $sheet->setCellValue('G' . $row, (float) $product->total_qty);
            $sheet->setCellValue('H' . $row, (float) $product->total_amount);
            $sheet->setCellValue('I' . $row, (float) $product->avg_price);
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
        $filename = 'Laporan_Penjualan_Produk_' . date('Y-m-d', strtotime($startDate)) . '_to_' . date('Y-m-d', strtotime($endDate)) . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
