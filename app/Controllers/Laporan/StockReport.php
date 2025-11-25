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
use App\Models\VItemStokModel;
use App\Models\GudangModel;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class StockReport extends BaseController
{
    protected $vItemStokModel;
    protected $gudangModel;

    public function __construct()
    {
        $this->vItemStokModel = new VItemStokModel();
        $this->gudangModel = new GudangModel();
    }

    /**
     * Display stock report index
     */
    public function index()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $gudangId = $this->request->getGet('gudang_id');
        $sortBy = $this->request->getGet('sort_by') ?: 'sisa';
        $sortOrder = $this->request->getGet('sort_order') ?: 'DESC';
        $perPage = $this->pengaturan->pagination_limit ?? 10;
        $page = max(1, (int) ($this->request->getGet('page_stock') ?? 1));

        $stockResult = $this->vItemStokModel->getStockOverview($gudangId, null, null, $perPage, $page, $sortBy, $sortOrder);
        $stock = $stockResult['data'] ?? [];
        $totalItems = $stockResult['total'] ?? count($stock);
        $totalStockQty = $stockResult['total_stock'] ?? array_sum(array_map(static fn ($item) => (float) ($item->sisa ?? 0), $stock));
        $outOfStockCount = $stockResult['out_of_stock'] ?? count(array_filter($stock, static fn ($item) => (float) ($item->sisa ?? 0) <= 0));
        $totalStockValue = $stockResult['total_stock_value'] ?? array_sum(array_map(static fn ($item) => (float) ($item->harga_beli ?? 0) * (float) ($item->sisa ?? 0), $stock));

        // Get outlet list (only with status_otl='1')
        $gudangList = $this->gudangModel->where('status', '1')->where('status_otl', '1')->findAll();

        $data = [
            'title' => 'Laporan Stok Item',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'stock' => $stock,
            'totalItems' => $totalItems,
            'totalStockQty' => $totalStockQty,
            'outOfStockCount' => $outOfStockCount,
            'totalStockValue' => $totalStockValue,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'gudangList' => $gudangList,
            'selectedGudang' => $gudangId,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'pagination' => [
                'current_page' => $stockResult['current_page'] ?? $page,
                'last_page' => $stockResult['last_page'] ?? 1,
                'total' => $totalItems,
                'per_page' => $perPage,
            ],
            'baseQuery' => http_build_query([
                'start_date' => $startDate,
                'end_date' => $endDate,
                'gudang_id' => $gudangId,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ]),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Laporan</li>
                <li class="breadcrumb-item active">Stok Item</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/stock/index', $data);
    }

    /**
     * Temporary test method to check data
     */
    public function test_data()
    {
        // Check what items exist
        $items = $this->db->table('tbl_m_item')
            ->select('id, kode, item')
            ->where('status', '1')
            ->limit(5)
            ->get()
            ->getResult();
            
        // Check what stock exists
        $stock = $this->db->table('tbl_m_item_stok')
            ->select('id_item, id_gudang, jml')
            ->where('status', '1')
            ->limit(5)
            ->get()
            ->getResult();
            
        // Check what gudang exists
        $gudang = $this->db->table('tbl_m_gudang')
            ->select('id, nama')
            ->where('status_hps', '0')
            ->limit(5)
            ->get()
            ->getResult();
            
        echo "Items: " . json_encode($items) . "\n";
        echo "Stock: " . json_encode($stock) . "\n";
        echo "Gudang: " . json_encode($gudang) . "\n";
    }

    /**
     * Display stock detail for specific item
     */
    public function detail($itemId = null)
    {
        if (!$itemId) {
            return redirect()->to('laporan/stock')->with('error', 'ID Item tidak valid');
        }

        $gudangId = $this->request->getGet('gudang_id');
        
        // Get stock data for specific item across all warehouses
        $stockData = $this->vItemStokModel->where('id_item', $itemId)->findAll();
        
        if (empty($stockData)) {
            return redirect()->to('laporan/stock')->with('error', 'Data stok tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Stok Item',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'stockData' => $stockData,
            'item' => $stockData[0], // First record for item info
            'selectedGudang' => $gudangId
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/stock/detail', $data);
    }

    /**
     * Export stock report to Excel
     */
    public function export_excel()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $gudangId = $this->request->getGet('gudang_id');
        $sortBy = $this->request->getGet('sort_by') ?: 'sisa';
        $sortOrder = $this->request->getGet('sort_order') ?: 'DESC';
        $stockResult = $this->vItemStokModel->getStockOverview($gudangId, null, null, 0, 1, $sortBy, $sortOrder);
        $stock = $stockResult['data'] ?? [];
        $gudangName = 'Semua Outlet';
        if ($gudangId) {
            $gudang = $this->gudangModel->find($gudangId);
            $gudangName = $gudang->nama ?? $gudangName;
        }

        $gudangName = 'Semua Gudang';
        if ($gudangId) {
            $gudang = $this->gudangModel->find($gudangId);
            $gudangName = $gudang->nama ?? $gudangName;
        }

        $totalSisa = array_sum(array_map(static fn ($item) => (float) ($item->sisa ?? 0), $stock));
        $totalNilai = array_sum(array_map(static fn ($item) => (float) ($item->harga_beli ?? 0) * (float) ($item->sisa ?? 0), $stock));

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LAPORAN STOK ITEM');
        $headers = ['No', 'Kode Item', 'Nama Item', 'Gudang', 'SO', 'Stok Masuk', 'Stok Keluar', 'Sisa', 'Nilai (Rp)'];
        $headerCount = count($headers);
        $lastColumnLetter = Coordinate::stringFromColumnIndex($headerCount);
        $sheet->mergeCells('A1:' . $lastColumnLetter . '1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $headerRow = 3;
        foreach ($headers as $index => $header) {
            $columnLetter = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($columnLetter . $headerRow, $header);
            $sheet->getStyle($columnLetter . $headerRow)->getFont()->setBold(true);
        }

        // Set data
        $row = 4;
        $no = 1;
        foreach ($stock as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->kode ?? '');
            $sheet->setCellValue('C' . $row, $item->item ?? '');
            $sheet->setCellValue('D' . $row, $item->gudang ?? '');
            $sheet->setCellValue('E' . $row, (float)($item->so ?? 0));
            $sheet->setCellValue('F' . $row, (float)($item->stok_masuk ?? 0));
            $sheet->setCellValue('G' . $row, (float)($item->stok_keluar ?? 0));
            $sheet->setCellValue('H' . $row, (float)($item->sisa ?? 0));
            $sheet->setCellValue('I' . $row, (float)($item->harga_beli ?? 0) * (float)($item->sisa ?? 0));
            $row++;
        }

        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->setCellValue('H' . $row, $totalSisa);
        $sheet->setCellValue('I' . $row, $totalNilai);
        $sheet->getStyle('A' . $row . ':I' . $row)->getFont()->setBold(true);

        // Auto-size columns
        for ($i = 1; $i <= $headerCount; $i++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }

        // Add borders
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A3:' . $lastColumnLetter . $row)->applyFromArray($styleArray);

        // Create response
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Laporan_Stok_' . $gudangName . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Get stock summary for dashboard
     */
    public function getStockSummary()
    {
        $gudangId = $this->request->getGet('gudang_id');
        
        $summary = $this->vItemStokModel->getStockMovementSummary($gudangId);
        $warehouseSummary = $this->vItemStokModel->getStockSummaryByWarehouse($gudangId);
        $stockAging = $this->vItemStokModel->getStockAgingAnalysis($gudangId);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'summary' => $summary,
                'warehouseSummary' => $warehouseSummary,
                'stockAging' => $stockAging
            ]
        ]);
    }

    /**
     * Get low stock alerts
     */
    public function getLowStockAlerts()
    {
        $gudangId = $this->request->getGet('gudang_id');
        $threshold = $this->request->getGet('threshold') ?: 10;
        
        $lowStock = $this->vItemStokModel->getLowStockItems($gudangId, $threshold);
        $outOfStock = $this->vItemStokModel->getOutOfStockItems($gudangId);
        $negativeStock = $this->vItemStokModel->getNegativeStockItems($gudangId);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'lowStock' => $lowStock,
                'outOfStock' => $outOfStock,
                'negativeStock' => $negativeStock,
                'totalAlerts' => count($lowStock) + count($outOfStock) + count($negativeStock)
            ]
        ]);
    }

    /**
     * Get stock comparison between warehouses
     */
    public function getStockComparison()
    {
        $gudangIds = $this->request->getGet('gudang_ids');
        
        if (!$gudangIds) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID Gudang tidak boleh kosong'
            ]);
        }
        
        $gudangIds = explode(',', $gudangIds);
        $comparison = $this->vItemStokModel->getStockComparison($gudangIds);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $comparison
        ]);
    }

    /**
     * Get top items by stock quantity
     */
    public function getTopItems()
    {
        $gudangId = $this->request->getGet('gudang_id');
        $limit = $this->request->getGet('limit') ?: 20;
        $order = $this->request->getGet('order') ?: 'DESC';
        
        $topItems = $this->vItemStokModel->getTopItemsByStock($gudangId, $limit, $order);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $topItems
        ]);
    }

    public function export_pdf()
    {
        require_once(APPPATH . '../vendor/tecnickcom/tcpdf/tcpdf.php');
        
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $gudangId = $this->request->getGet('gudang_id');
        $sortBy = $this->request->getGet('sort_by') ?: 'sisa';
        $sortOrder = $this->request->getGet('sort_order') ?: 'DESC';

        $stockResult = $this->vItemStokModel->getStockOverview($gudangId, null, null, 0, 1, $sortBy, $sortOrder);
        $stock = $stockResult['data'] ?? [];
        $gudangName = 'Semua Outlet';
        if ($gudangId) {
            $gudang = $this->gudangModel->find($gudangId);
            $gudangName = $gudang->nama ?? $gudangName;
        }
        $totalSisa = array_sum(array_map(static fn ($item) => (float) ($item->sisa ?? 0), $stock));
        $totalNilai = array_sum(array_map(static fn ($item) => (float) ($item->harga_beli ?? 0) * (float) ($item->sisa ?? 0), $stock));

        // Create PDF
        $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator($this->pengaturan->judul_app ?? 'POS System');
        $pdf->SetAuthor($this->pengaturan->judul ?? 'Company');
        $pdf->SetTitle('Laporan Stok Item');
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
        $pdf->Cell(0, 8, 'LAPORAN STOK ITEM', 0, 1, 'C');
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
        $pdf->Cell(25, 6, 'Kode', 1, 0, 'C');
        $pdf->Cell(55, 6, 'Nama Item', 1, 0, 'C');
        $pdf->Cell(35, 6, 'Gudang', 1, 0, 'C');
        $pdf->Cell(20, 6, 'SO', 1, 0, 'R');
        $pdf->Cell(25, 6, 'Masuk', 1, 0, 'R');
        $pdf->Cell(25, 6, 'Keluar', 1, 0, 'R');
        $pdf->Cell(25, 6, 'Sisa', 1, 0, 'R');
        $pdf->Cell(35, 6, 'Nilai (Rp)', 1, 1, 'R');

        // Table Data
        $pdf->SetFont('helvetica', '', 7);
        $no = 1;
        foreach ($stock as $item) {
            $pdf->Cell(10, 5, $no++, 1, 0, 'C');
            $pdf->Cell(25, 5, substr($item->kode ?? '-', 0, 15), 1, 0, 'L');
            $pdf->Cell(55, 5, substr($item->item ?? '-', 0, 30), 1, 0, 'L');
            $pdf->Cell(35, 5, substr($item->gudang ?? '-', 0, 20), 1, 0, 'L');
            $pdf->Cell(20, 5, format_angka($item->so ?? 0), 1, 0, 'R');
            $pdf->Cell(25, 5, format_angka($item->stok_masuk ?? 0), 1, 0, 'R');
            $pdf->Cell(25, 5, format_angka($item->stok_keluar ?? 0), 1, 0, 'R');
            $pdf->Cell(25, 5, format_angka($item->sisa ?? 0), 1, 0, 'R');
            $pdf->Cell(35, 5, format_angka((float) ($item->harga_beli ?? 0) * (float) ($item->sisa ?? 0)), 1, 1, 'R');
        }

        // Summary
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(0, 5, 'Total Item: ' . count($stock), 0, 1, 'L');
        $pdf->Cell(0, 5, 'Total Sisa Stok: ' . format_angka($totalSisa), 0, 1, 'L');
        $pdf->Cell(0, 5, 'Total Nilai Stok: ' . format_angka($totalNilai), 0, 1, 'L');

        // Output
        $filename = 'Laporan_Stok_Item_' . date('Y-m-d') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }
}
