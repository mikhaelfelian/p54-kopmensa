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
        $filters = [
            'start_date' => $this->request->getGet('start_date') ?? date('Y-m-01'),
            'end_date' => $this->request->getGet('end_date') ?? date('Y-m-t'),
            'id_gudang' => $this->request->getGet('id_gudang'),
            'sort_by' => $this->request->getGet('sort_by') ?? 'total_qty',
            'sort_order' => $this->request->getGet('sort_order') ?? 'DESC',
        ];

        $report = $this->getReportData($filters);
        $gudangList = $this->gudangModel->where('status', '1')->where('status_otl', '1')->findAll();

        $data = [
            'title'       => 'Laporan Penjualan Item',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'itemSales'   => $report['items'],
            'startDate'   => $report['filters']['start_date'],
            'endDate'     => $report['filters']['end_date'],
            'idGudang'    => $report['filters']['id_gudang'],
            'sortBy'      => $report['filters']['sort_by'],
            'sortOrder'   => $report['filters']['sort_order'],
            'gudangList'  => $gudangList,
            'summary'     => $report['summary'],
            'ppnRate'     => $report['summary']['ppn_rate'] ?? ($this->pengaturan->ppn ?? 11),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Laporan</li>
                <li class="breadcrumb-item active">Penjualan Item</li>
            ',
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/item_sale/index', $data);
    }

    public function export_excel()
    {
        $filters = [
            'start_date' => $this->request->getGet('start_date') ?? date('Y-m-01'),
            'end_date' => $this->request->getGet('end_date') ?? date('Y-m-t'),
            'id_gudang' => $this->request->getGet('id_gudang'),
            'sort_by' => $this->request->getGet('sort_by') ?? 'total_qty',
            'sort_order' => $this->request->getGet('sort_order') ?? 'DESC',
        ];

        $report = $this->getReportData($filters);
        $itemSales = $report['items'];
        $summary = $report['summary'];
        $startDate = $report['filters']['start_date'];
        $endDate = $report['filters']['end_date'];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Laporan Penjualan Item');
        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));
        $sheet->setCellValue('A3', 'PPN Rate: ' . $summary['ppn_rate'] . '%');

        $headers = [
            'No', 'Kode', 'Nama Item', 'Satuan',
            'Qty Terjual', 'Total Revenue', 'Total PPN', 'Status PPN',
            'Rata-rata Harga', 'Total Transaksi'
        ];

        $headerRow = 5;
        foreach ($headers as $colIndex => $header) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($column . $headerRow, $header);
        }
        $sheet->getStyle('A' . $headerRow . ':J' . $headerRow)->getFont()->setBold(true);

        $row = $headerRow + 1;
        $no = 1;

        foreach ($itemSales as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->kode);
            $sheet->setCellValue('C' . $row, $item->item);
            $sheet->setCellValue('D' . $row, $item->satuan);
            $sheet->setCellValue('E' . $row, (float) $item->total_qty);
            $sheet->setCellValue('F' . $row, (float) $item->total_amount);
            $sheet->setCellValue('G' . $row, (float) ($item->ppn_value ?? 0));
            $sheet->setCellValue('H' . $row, ($item->status_ppn ?? '0') === '1' ? 'Include PPN' : 'Non PPN');
            $sheet->setCellValue('I' . $row, (float) $item->avg_price);
            $sheet->setCellValue('J' . $row, $item->total_transactions);
            $row++;
        }

        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->setCellValue('E' . $row, (float) $summary['total_quantity_sold']);
        $sheet->setCellValue('F' . $row, (float) $summary['total_revenue']);
        $sheet->setCellValue('G' . $row, (float) $summary['total_ppn']);
        $sheet->getStyle('A' . $row . ':J' . $row)->getFont()->setBold(true);

        $sheet->getStyle('A1:J1')->getFont()->setBold(true)->setSize(16);
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $lastDataRow = $row;
        $sheet->getStyle('A' . $headerRow . ':J' . $lastDataRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

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
        
        $filters = [
            'start_date' => $this->request->getGet('start_date') ?? date('Y-m-01'),
            'end_date' => $this->request->getGet('end_date') ?? date('Y-m-t'),
            'id_gudang' => $this->request->getGet('id_gudang'),
            'sort_by' => $this->request->getGet('sort_by') ?? 'total_qty',
            'sort_order' => $this->request->getGet('sort_order') ?? 'DESC',
        ];

        $report = $this->getReportData($filters);
        $itemSales = $report['items'];
        $summary = $report['summary'];
        $startDate = $report['filters']['start_date'];
        $endDate = $report['filters']['end_date'];
        $sortBy = $report['filters']['sort_by'];
        $sortOrder = $report['filters']['sort_order'];

        $gudangName = 'Semua Outlet';
        if ($filters['id_gudang']) {
            $gudang = $this->gudangModel->find($filters['id_gudang']);
            $gudangName = $gudang->nama ?? $gudangName;
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
        $pdf->Cell(0, 5, 'PPN Rate: ' . $summary['ppn_rate'] . '%', 0, 1, 'C');
        $pdf->Ln(2);

        // Filter Info
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(0, 4, 'Outlet: ' . $gudangName . ' | Sort: ' . ucfirst($sortBy) . ' (' . $sortOrder . ')', 0, 1, 'L');
        $pdf->Ln(2);

        // Table Header
        $pdf->SetFont('helvetica', 'B', 8);
        // Table Header
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(10, 6, 'No', 1, 0, 'C');
        $pdf->Cell(25, 6, 'Kode', 1, 0, 'C');
        $pdf->Cell(55, 6, 'Nama Item', 1, 0, 'C');
        $pdf->Cell(25, 6, 'Satuan', 1, 0, 'C');
        $pdf->Cell(25, 6, 'Qty', 1, 0, 'R');
        $pdf->Cell(30, 6, 'Revenue', 1, 0, 'R');
        $pdf->Cell(30, 6, 'PPN', 1, 0, 'R');
        $pdf->Cell(25, 6, 'Status PPN', 1, 0, 'C');
        $pdf->Cell(30, 6, 'Rata2 Harga', 1, 0, 'R');
        $pdf->Cell(25, 6, 'Transaksi', 1, 1, 'R');

        $pdf->SetFont('helvetica', '', 7);
        $no = 1;
        foreach ($itemSales as $item) {
            $pdf->Cell(10, 5, $no++, 1, 0, 'C');
            $pdf->Cell(25, 5, substr($item->kode, 0, 15), 1, 0, 'L');
            $pdf->Cell(55, 5, substr($item->item, 0, 30), 1, 0, 'L');
            $pdf->Cell(25, 5, substr($item->satuan ?? '-', 0, 12), 1, 0, 'C');
            $pdf->Cell(25, 5, format_angka($item->total_qty), 1, 0, 'R');
            $pdf->Cell(30, 5, format_angka($item->total_amount), 1, 0, 'R');
            $pdf->Cell(30, 5, format_angka($item->ppn_value ?? 0), 1, 0, 'R');
            $pdf->Cell(25, 5, ($item->status_ppn ?? '0') === '1' ? 'Include' : 'Non PPN', 1, 0, 'C');
            $pdf->Cell(30, 5, format_angka($item->avg_price), 1, 0, 'R');
            $pdf->Cell(25, 5, $item->total_transactions, 1, 1, 'R');
        }

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(115, 6, 'TOTAL', 1, 0, 'R');
        $pdf->Cell(25, 6, format_angka($summary['total_quantity_sold']), 1, 0, 'R');
        $pdf->Cell(30, 6, format_angka($summary['total_revenue']), 1, 0, 'R');
        $pdf->Cell(30, 6, format_angka($summary['total_ppn']), 1, 0, 'R');
        $pdf->Cell(55, 6, '', 1, 1);

        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(0, 5, 'Total Item: ' . count($itemSales), 0, 1, 'L');
        $pdf->Cell(0, 5, 'Total Qty Terjual: ' . format_angka($summary['total_quantity_sold']), 0, 1, 'L');
        $pdf->Cell(0, 5, 'Total Revenue: ' . format_angka($summary['total_revenue']), 0, 1, 'L');
        $pdf->Cell(0, 5, 'Total PPN: ' . format_angka($summary['total_ppn']), 0, 1, 'L');

        $filename = 'Laporan_Penjualan_Item_' . date('Y-m-d') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }

    private function getReportData(array $filters): array
    {
        $startDate = $filters['start_date'];
        $endDate = $filters['end_date'];
        $idGudang = $filters['id_gudang'];
        $sortBy = $filters['sort_by'];
        $sortOrder = $filters['sort_order'];
        $ppnRate = $this->pengaturan->ppn ?? 11;

        $builder = $this->transJualDetModel->select('
                tbl_trans_jual_det.id_item,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_item.barcode,
                tbl_m_item.status_ppn,
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

        $totalItems = count($itemSales);
        $totalQuantitySold = 0;
        $totalRevenue = 0;
        $totalTransactions = 0;
        $totalPpn = 0;

        foreach ($itemSales as $item) {
            $item->status_ppn = (string)($item->status_ppn ?? '0');
            $item->ppn_rate = $ppnRate;
            $item->ppn_value = $item->status_ppn === '1'
                ? (float) $item->total_amount * $ppnRate / 100
                : 0;

            $totalQuantitySold += (float) $item->total_qty;
            $totalRevenue += (float) $item->total_amount;
            $totalTransactions += (int) $item->total_transactions;
            $totalPpn += $item->ppn_value;
        }

        return [
            'items' => $itemSales,
            'summary' => [
                'total_items' => $totalItems,
                'total_quantity_sold' => $totalQuantitySold,
                'total_revenue' => $totalRevenue,
                'total_ppn' => $totalPpn,
                'total_transactions' => $totalTransactions,
                'avg_revenue_per_item' => $totalItems > 0 ? $totalRevenue / $totalItems : 0,
                'ppn_rate' => $ppnRate,
            ],
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'id_gudang' => $idGudang,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
        ];
    }
}

