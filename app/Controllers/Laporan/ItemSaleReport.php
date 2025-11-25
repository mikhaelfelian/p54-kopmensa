<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: Controller for handling item sales reports
 * This file represents the ItemSaleReport controller.
 */

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Models\TransJualDetModel;
use App\Models\ItemModel;
use App\Models\GudangModel;

class ItemSaleReport extends BaseController
{
    protected $transJualDetModel;
    protected $itemModel;
    protected $gudangModel;

    public function __construct()
    {
        parent::__construct();
        $this->transJualDetModel = new TransJualDetModel();
        $this->itemModel = new ItemModel();
        $this->gudangModel = new GudangModel();
    }

    public function index()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idGudang = $this->request->getGet('id_gudang');
        $sortBy = $this->request->getGet('sort_by') ?? 'total_qty';
        $sortOrder = $this->request->getGet('sort_order') ?? 'DESC';

        // Build query for item sales
        $builder = $this->transJualDetModel->select('
                tbl_trans_jual_det.id_item,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_item.barcode,
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
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->where('tbl_trans_jual.status_nota', '1')
            ->where('tbl_trans_jual.deleted_at IS NULL')
            ->groupBy('tbl_trans_jual_det.id_item');

        // Apply date filter
        if ($startDate && $endDate) {
            $builder->where('DATE(tbl_trans_jual.tgl_masuk) >=', $startDate)
                   ->where('DATE(tbl_trans_jual.tgl_masuk) <=', $endDate);
        }

        // Apply outlet filter
        if ($idGudang) {
            $builder->where('tbl_trans_jual.id_gudang', $idGudang);
        }

        // Apply sorting (Highest/Lowest)
        $validSortColumns = ['total_qty', 'total_amount', 'total_transactions', 'item'];
        if (in_array($sortBy, $validSortColumns)) {
            $builder->orderBy($sortBy, $sortOrder);
        } else {
            $builder->orderBy('total_qty', 'DESC');
        }

        $itemSales = $builder->findAll();

        // Calculate summary
        $totalItems = count($itemSales);
        $totalQuantitySold = 0;
        $totalRevenue = 0;
        $totalTransactions = 0;

        foreach ($itemSales as $item) {
            $totalQuantitySold += (float) $item->total_qty;
            $totalRevenue += (float) $item->total_amount;
            $totalTransactions += (int) $item->total_transactions;
        }

        // Get filter options
        $gudangList = $this->gudangModel->where('status', '1')->where('status_otl', '1')->findAll();

        $data = [
            'title' => 'Laporan Penjualan Item',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'itemSales' => $itemSales,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'idGudang' => $idGudang,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'gudangList' => $gudangList,
            'summary' => [
                'total_items' => $totalItems,
                'total_quantity_sold' => $totalQuantitySold,
                'total_revenue' => $totalRevenue,
                'total_transactions' => $totalTransactions,
                'avg_revenue_per_item' => $totalItems > 0 ? $totalRevenue / $totalItems : 0
            ],
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Laporan</li>
                <li class="breadcrumb-item active">Penjualan Item</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/item_sale/index', $data);
    }

    public function export_excel()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idGudang = $this->request->getGet('id_gudang');
        $sortBy = $this->request->getGet('sort_by') ?? 'total_qty';
        $sortOrder = $this->request->getGet('sort_order') ?? 'DESC';

        $builder = $this->transJualDetModel->select('
                tbl_trans_jual_det.id_item,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_item.barcode,
                tbl_m_satuan.SatuanBesar as satuan,
                SUM(tbl_trans_jual_det.jml) as total_qty,
                SUM(tbl_trans_jual_det.subtotal) as total_amount,
                AVG(tbl_trans_jual_det.harga) as avg_price,
                COUNT(DISTINCT tbl_trans_jual.id) as total_transactions
            ')
            ->join('tbl_trans_jual', 'tbl_trans_jual.id = tbl_trans_jual_det.id_penjualan')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_jual_det.id_item')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->where('tbl_trans_jual.status_nota', '1')
            ->where('tbl_trans_jual.deleted_at IS NULL')
            ->groupBy('tbl_trans_jual_det.id_item');

        if ($startDate && $endDate) {
            $builder->where('DATE(tbl_trans_jual.tgl_masuk) >=', $startDate)
                   ->where('DATE(tbl_trans_jual.tgl_masuk) <=', $endDate);
        }

        if ($idGudang) {
            $builder->where('tbl_trans_jual.id_gudang', $idGudang);
        }

        $validSortColumns = ['total_qty', 'total_amount', 'total_transactions', 'item'];
        if (in_array($sortBy, $validSortColumns)) {
            $builder->orderBy($sortBy, $sortOrder);
        } else {
            $builder->orderBy('total_qty', 'DESC');
        }

        $itemSales = $builder->findAll();

        // Create Excel export
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Laporan Penjualan Item');
        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));

        // Column headers
        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Kode');
        $sheet->setCellValue('C4', 'Nama Item');
        $sheet->setCellValue('D4', 'Satuan');
        $sheet->setCellValue('E4', 'Qty Terjual');
        $sheet->setCellValue('F4', 'Total Revenue');
        $sheet->setCellValue('G4', 'Rata-rata Harga');
        $sheet->setCellValue('H4', 'Total Transaksi');

        // Data rows
        $row = 5;
        $no = 1;

        foreach ($itemSales as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->kode);
            $sheet->setCellValue('C' . $row, $item->item);
            $sheet->setCellValue('D' . $row, $item->satuan);
            $sheet->setCellValue('E' . $row, (float) $item->total_qty);
            $sheet->setCellValue('F' . $row, (float) $item->total_amount);
            $sheet->setCellValue('G' . $row, (float) $item->avg_price);
            $sheet->setCellValue('H' . $row, $item->total_transactions);
            $row++;
        }

        // Style the sheet
        $sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A4:H4')->getFont()->setBold(true);
        $sheet->getStyle('A4:H4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Export
        $filename = 'Laporan_Penjualan_Item_' . date('Y-m-d', strtotime($startDate)) . '_to_' . date('Y-m-d', strtotime($endDate)) . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        require_once(APPPATH . '../vendor/tecnickcom/tcpdf/tcpdf.php');
        
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idGudang = $this->request->getGet('id_gudang');
        $sortBy = $this->request->getGet('sort_by') ?? 'total_qty';
        $sortOrder = $this->request->getGet('sort_order') ?? 'DESC';

        $builder = $this->transJualDetModel->select('
                tbl_trans_jual_det.id_item,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_satuan.SatuanBesar as satuan,
                SUM(tbl_trans_jual_det.jml) as total_qty,
                SUM(tbl_trans_jual_det.subtotal) as total_amount,
                AVG(tbl_trans_jual_det.harga) as avg_price,
                COUNT(DISTINCT tbl_trans_jual.id) as total_transactions
            ')
            ->join('tbl_trans_jual', 'tbl_trans_jual.id = tbl_trans_jual_det.id_penjualan')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_jual_det.id_item')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->where('tbl_trans_jual.status_nota', '1')
            ->where('tbl_trans_jual.deleted_at IS NULL')
            ->groupBy('tbl_trans_jual_det.id_item');

        if ($startDate && $endDate) {
            $builder->where('DATE(tbl_trans_jual.tgl_masuk) >=', $startDate)
                   ->where('DATE(tbl_trans_jual.tgl_masuk) <=', $endDate);
        }

        if ($idGudang) {
            $builder->where('tbl_trans_jual.id_gudang', $idGudang);
        }

        $validSortColumns = ['total_qty', 'total_amount', 'total_transactions', 'item'];
        if (in_array($sortBy, $validSortColumns)) {
            $builder->orderBy($sortBy, $sortOrder);
        } else {
            $builder->orderBy('total_qty', 'DESC');
        }

        $itemSales = $builder->findAll();

        $totalQuantitySold = 0;
        $totalRevenue = 0;
        foreach ($itemSales as $item) {
            $totalQuantitySold += (float) $item->total_qty;
            $totalRevenue += (float) $item->total_amount;
        }

        // Get filter labels
        $gudangList = $this->gudangModel->where('status', '1')->where('status_otl', '1')->findAll();
        $gudangName = 'Semua Outlet';
        if ($idGudang) {
            foreach ($gudangList as $g) {
                if ($g->id == $idGudang) {
                    $gudangName = $g->nama;
                    break;
                }
            }
        }

        // Create PDF
        $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator($this->pengaturan->judul_app ?? 'POS System');
        $pdf->SetAuthor($this->pengaturan->judul ?? 'Company');
        $pdf->SetTitle('Laporan Penjualan Item');
        $pdf->SetMargins(10, 15, 10);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();

        // Header
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 8, strtoupper($this->pengaturan->judul ?? 'COMPANY NAME'), 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 5, $this->pengaturan->alamat ?? '', 0, 1, 'C');
        if (!empty($this->pengaturan->no_telp)) {
            $pdf->Cell(0, 5, 'Telp: ' . $this->pengaturan->no_telp, 0, 1, 'C');
        }
        $pdf->Ln(3);
        $pdf->Line(10, $pdf->GetY(), 287, $pdf->GetY());
        $pdf->Ln(5);

        // Report Title
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 8, 'LAPORAN PENJUALAN ITEM', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 5, 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)), 0, 1, 'C');
        $pdf->Ln(2);

        // Filter Info
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(0, 4, 'Outlet: ' . $gudangName . ' | Sort: ' . ucfirst($sortBy) . ' (' . $sortOrder . ')', 0, 1, 'L');
        $pdf->Ln(2);

        // Table Header
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(10, 6, 'No', 1, 0, 'C');
        $pdf->Cell(30, 6, 'Kode', 1, 0, 'C');
        $pdf->Cell(80, 6, 'Nama Item', 1, 0, 'C');
        $pdf->Cell(25, 6, 'Satuan', 1, 0, 'C');
        $pdf->Cell(30, 6, 'Qty Terjual', 1, 0, 'R');
        $pdf->Cell(40, 6, 'Total Revenue', 1, 0, 'R');
        $pdf->Cell(35, 6, 'Rata-rata Harga', 1, 0, 'R');
        $pdf->Cell(27, 6, 'Transaksi', 1, 1, 'R');

        // Table Data
        $pdf->SetFont('helvetica', '', 7);
        $no = 1;
        foreach ($itemSales as $item) {
            $pdf->Cell(10, 5, $no++, 1, 0, 'C');
            $pdf->Cell(30, 5, substr($item->kode, 0, 20), 1, 0, 'L');
            $pdf->Cell(80, 5, substr($item->item, 0, 40), 1, 0, 'L');
            $pdf->Cell(25, 5, substr($item->satuan ?? '-', 0, 15), 1, 0, 'C');
            $pdf->Cell(30, 5, format_angka($item->total_qty), 1, 0, 'R');
            $pdf->Cell(40, 5, format_angka($item->total_amount), 1, 0, 'R');
            $pdf->Cell(35, 5, format_angka($item->avg_price), 1, 0, 'R');
            $pdf->Cell(27, 5, $item->total_transactions, 1, 1, 'R');
        }

        // Total
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(145, 6, 'TOTAL', 1, 0, 'R');
        $pdf->Cell(30, 6, format_angka($totalQuantitySold), 1, 0, 'R');
        $pdf->Cell(40, 6, format_angka($totalRevenue), 1, 0, 'R');
        $pdf->Cell(62, 6, '', 1, 1);

        // Summary
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(0, 5, 'Total Item: ' . count($itemSales), 0, 1, 'L');
        $pdf->Cell(0, 5, 'Total Qty Terjual: ' . format_angka($totalQuantitySold), 0, 1, 'L');
        $pdf->Cell(0, 5, 'Total Revenue: ' . format_angka($totalRevenue), 0, 1, 'L');

        // Output
        $filename = 'Laporan_Penjualan_Item_' . date('Y-m-d') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }
}

