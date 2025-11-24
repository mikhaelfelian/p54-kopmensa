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
        $gudangId = $this->request->getGet('gudang_id');
        $keyword = $this->request->getGet('keyword');
        $stockStatus = $this->request->getGet('stock_status');
        $outletType = $this->request->getGet('outlet_type'); // 'warehouse', 'outlet', or null
        $sortBy = $this->request->getGet('sort_by') ?: 'item';
        $sortOrder = $this->request->getGet('sort_order') ?: 'ASC';

        // Build search criteria
        $criteria = [
            'gudang_id' => $gudangId,
            'keyword' => $keyword,
            'stock_status' => $stockStatus,
            'outlet_type' => $outletType,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder
        ];

        // Get stock data
        $stockResult = $this->vItemStokModel->searchItems($criteria, 20);
        $stock = $stockResult['data'] ?? [];
        
        // Debug: Log the stock data
        log_message('debug', 'Stock data count: ' . count($stock));
        if (!empty($stock)) {
            log_message('debug', 'First stock item: ' . json_encode($stock[0]));
        }
        
        // Get summary data
        $summary = $this->vItemStokModel->getStockMovementSummary($gudangId);
        $warehouseSummary = $this->vItemStokModel->getStockSummaryByWarehouse($gudangId, $outletType);
        $stockAging = $this->vItemStokModel->getStockAgingAnalysis($gudangId);
        $outletTypeSummary = $this->vItemStokModel->getStockSummaryByOutletType($gudangId);
        
        // Get low stock alerts based on outlet type
        if ($outletType === 'outlet') {
            $lowStock = $this->vItemStokModel->getLowStockItemsInOutlets($gudangId, 10);
            $outOfStock = $this->vItemStokModel->getOutOfStockItems($gudangId);
            $negativeStock = $this->vItemStokModel->getNegativeStockItems($gudangId);
        } elseif ($outletType === 'warehouse') {
            $lowStock = $this->vItemStokModel->getLowStockItemsInWarehouses($gudangId, 10);
            $outOfStock = $this->vItemStokModel->getOutOfStockItems($gudangId);
            $negativeStock = $this->vItemStokModel->getNegativeStockItems($gudangId);
        } else {
            $lowStock = $this->vItemStokModel->getLowStockItems($gudangId, 10);
            $outOfStock = $this->vItemStokModel->getOutOfStockItems($gudangId);
            $negativeStock = $this->vItemStokModel->getNegativeStockItems($gudangId);
        }

        $data = [
            'title' => 'Laporan Stok',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'stock' => $stock,
            'summary' => $summary,
            'warehouseSummary' => $warehouseSummary,
            'stockAging' => $stockAging,
            'outletTypeSummary' => $outletTypeSummary,
            'lowStock' => $lowStock,
            'outOfStock' => $outOfStock,
            'negativeStock' => $negativeStock,
            'gudang' => $this->gudangModel->findAll(),
            'selectedGudang' => $gudangId,
            'keyword' => $keyword,
            'stockStatus' => $stockStatus,
            'outletType' => $outletType,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder
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
        $gudangId = $this->request->getGet('gudang_id');
        $keyword = $this->request->getGet('keyword');
        $stockStatus = $this->request->getGet('stock_status');

        // Build search criteria
        $criteria = [
            'gudang_id' => $gudangId,
            'keyword' => $keyword,
            'stock_status' => $stockStatus
        ];

        // Get all data for export (no pagination)
        $stockResult = $this->vItemStokModel->searchItems($criteria, 10000, 1);
        $stock = $stockResult['data'] ?? (is_array($stockResult) ? $stockResult : []);
        
        // Get warehouse name
        $gudangName = 'Semua Gudang';
        if ($gudangId) {
            $gudang = $this->gudangModel->find($gudangId);
            $gudangName = $gudang ? $gudang->nama : 'Gudang Tidak Diketahui';
        }

        // Get active warehouses (status = 1)
        $activeWarehouses = $this->gudangModel
            ->where('status', '1')
            ->where('status_hps', '0')
            ->orderBy('nama', 'ASC')
            ->findAll();

        // If no active warehouses found, fallback to warehouses present in stock data
        if (empty($activeWarehouses)) {
            $warehouseMap = [];
            foreach ($stock as $item) {
                if (!isset($item->id_gudang)) {
                    continue;
                }
                $warehouseMap[$item->id_gudang] = (object) [
                    'id' => $item->id_gudang,
                    'nama' => $item->gudang ?? 'Gudang ' . $item->id_gudang,
                ];
            }
            $activeWarehouses = array_values($warehouseMap);
        }

        $warehouseIds = array_map(static fn ($gudang) => $gudang->id, $activeWarehouses);

        // Prepare data aggregated per item with columns per warehouse
        $itemsData = [];
        foreach ($stock as $item) {
            if (!isset($item->id_item)) {
                continue;
            }

            $itemId = $item->id_item;
            if (!isset($itemsData[$itemId])) {
                $itemsData[$itemId] = [
                    'kode'   => $item->kode ?? '',
                    'item'   => $item->item ?? '',
                    'stocks' => array_fill_keys($warehouseIds, 0),
                    'total'  => 0,
                ];
            }

            $quantity = (float) ($item->sisa ?? 0);
            $itemsData[$itemId]['total'] += $quantity;

            $gudangId = $item->id_gudang ?? null;
            if ($gudangId !== null && array_key_exists($gudangId, $itemsData[$itemId]['stocks'])) {
                $itemsData[$itemId]['stocks'][$gudangId] += $quantity;
            }
        }

        // Sort data by item name for readability
        $itemsData = array_values($itemsData);
        usort($itemsData, static function ($a, $b) {
            return strcasecmp($a['item'], $b['item']);
        });

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'LAPORAN STOK - ' . strtoupper($gudangName));
        $headerCount = 3 + count($activeWarehouses) + 1; // No, Kode, Nama + warehouses + Total
        $lastColumnLetter = Coordinate::stringFromColumnIndex($headerCount);
        $sheet->mergeCells('A1:' . $lastColumnLetter . '1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Set headers
        $headers = ['No', 'Kode Item', 'Nama Item'];
        foreach ($activeWarehouses as $warehouse) {
            $headers[] = $warehouse->nama;
        }
        $headers[] = 'Total';

        $headerRow = 3;
        foreach ($headers as $index => $header) {
            $columnLetter = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($columnLetter . $headerRow, $header);
            $sheet->getStyle($columnLetter . $headerRow)->getFont()->setBold(true);
        }

        // Set data
        $row = 4;
        $no = 1;
        foreach ($itemsData as $itemData) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $itemData['kode']);
            $sheet->setCellValue('C' . $row, $itemData['item']);

            $columnIndex = 4; // Column D onwards for warehouses
            foreach ($warehouseIds as $warehouseId) {
                $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
                $sheet->setCellValue($columnLetter . $row, $itemData['stocks'][$warehouseId] ?? 0);
                $columnIndex++;
            }

            // Total column
            $totalColumnLetter = Coordinate::stringFromColumnIndex($columnIndex);
            $sheet->setCellValue($totalColumnLetter . $row, $itemData['total']);
            $row++;
        }

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
        $sheet->getStyle('A3:' . $lastColumnLetter . ($row - 1))->applyFromArray($styleArray);

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
}
