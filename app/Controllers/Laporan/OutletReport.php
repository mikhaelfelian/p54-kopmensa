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
use App\Models\GudangModel;
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
        $this->outletModel = new GudangModel();
        $this->transJualModel = new TransJualModel();
        $this->itemStokModel = new ItemStokModel();
        $this->karyawanModel = new KaryawanModel();
    }

    public function index()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idOutlet = $this->request->getGet('id_outlet');

        // Get filter options: only outlets (status_otl = 1)
        $outletList = $this->outletModel
            ->where('status_otl', '1')
            ->findAll();

        // Use VItemStokModel for stock summary per outlet, but with fallback
        $vItemStokModel = new \App\Models\VItemStokModel();


        if ($idOutlet) {
            $stokFilter['id_gudang'] = $idOutlet;
        }

        // Get outlet stock details - try to filter by status_otl first, fallback to manual filtering
        $rawOutletDetails = [];
        
        try {
            // First, try to check if the view exists and has status_otl column
            $testQuery = $vItemStokModel->select('id_gudang, gudang, status_otl')->limit(1);
            $testResult = $testQuery->findAll();
            
            if (!empty($testResult) && isset($testResult[0]->status_otl)) {
                // View exists with status_otl column, use it
                $stockQuery = $vItemStokModel->where('status_otl', '1');
                
                if ($idOutlet) {
                    $stockQuery->where('id_gudang', $idOutlet);
                }
                
                $rawOutletDetails = $stockQuery->findAll();
            } else {
                // View exists but no status_otl column, fallback to manual filtering
                $rawOutletDetails = $vItemStokModel->findAll();
                
                // Filter outlets manually using outletList
                $outletIds = array_column($outletList, 'id');
                $rawOutletDetails = array_filter($rawOutletDetails, function($item) use ($outletIds) {
                    return in_array($item->id_gudang, $outletIds);
                });
                
                // If specific outlet selected, filter further
                if ($idOutlet) {
                    $rawOutletDetails = array_filter($rawOutletDetails, function($item) use ($idOutlet) {
                        return $item->id_gudang == $idOutlet;
                    });
                }
            }
        } catch (\Exception $e) {
            // View doesn't exist or other error, use fallback approach
            // Get stock data from ItemStokModel instead
            $stockQuery = $this->itemStokModel->select('
                    tbl_m_item_stok.id_item,
                    tbl_m_item_stok.id_gudang,
                    tbl_m_item_stok.jml as sisa,
                    tbl_m_item.kode,
                    tbl_m_item.item,
                    tbl_m_gudang.nama as gudang
                ')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_m_item_stok.id_item', 'inner')
                ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_m_item_stok.id_gudang', 'inner')
                ->where('tbl_m_gudang.status_otl', '1')
                ->where('tbl_m_item_stok.status', '1');
            
            if ($idOutlet) {
                $stockQuery->where('tbl_m_item_stok.id_gudang', $idOutlet);
            }
            
            $rawOutletDetails = $stockQuery->findAll();
        }
        
        // Restructure data to match view expectations
        $outletDetails = [];
        $outletGroups = [];
        
        // Group by outlet
        foreach ($rawOutletDetails as $row) {
            $outletId = $row->id_gudang;
            if (!isset($outletGroups[$outletId])) {
                $outletGroups[$outletId] = [
                    'outlet' => [
                        'id' => $outletId,
                        'nama' => $row->gudang
                    ],
                    'sales' => [
                        'total_transactions' => 0,
                        'total_sales' => 0,
                        'avg_sales' => 0,
                        'unique_customers' => 0
                    ],
                    'stock' => [
                        'total_items' => 0,
                        'total_stock' => 0,
                        'in_stock_items' => 0,
                        'out_of_stock_items' => 0
                    ],
                    'top_items' => []
                ];
            }
            
            // Aggregate stock data
            $outletGroups[$outletId]['stock']['total_items']++;
            $outletGroups[$outletId]['stock']['total_stock'] += (float) $row->sisa;
            if ($row->sisa > 0) {
                $outletGroups[$outletId]['stock']['in_stock_items']++;
            } else {
                $outletGroups[$outletId]['stock']['out_of_stock_items']++;
            }
        }
        
        // Get actual sales data for each outlet
        foreach ($outletGroups as $outletId => &$outlet) {
            // Get sales transactions for this outlet
            $salesQuery = $this->transJualModel->select('
                    COUNT(DISTINCT id) as total_transactions,
                    SUM(jml_gtotal) as total_sales,
                    AVG(jml_gtotal) as avg_sales,
                    COUNT(DISTINCT id_pelanggan) as unique_customers
                ')
                ->where('id_gudang', $outletId)
                ->where('status_nota', '1');
            
            // Apply date filter if provided
            if ($startDate && $endDate) {
                $salesQuery->where('tgl_masuk >=', $startDate . ' 00:00:00')
                          ->where('tgl_masuk <=', $endDate . ' 23:59:59');
            }
            
            $salesData = $salesQuery->first();
            
            if ($salesData) {
                $outlet['sales']['total_transactions'] = (int) $salesData->total_transactions;
                $outlet['sales']['total_sales'] = (float) $salesData->total_sales;
                $outlet['sales']['avg_sales'] = (float) $salesData->avg_sales;
                $outlet['sales']['unique_customers'] = (int) $salesData->unique_customers;
            }
            
            // Get top selling items for this outlet
            $topItemsQuery = $this->transJualModel->select('
                    tbl_m_item.item as item_nama,
                    SUM(tbl_trans_jual_det.jml) as total_qty,
                    SUM(tbl_trans_jual_det.jml * tbl_trans_jual_det.harga) as total_value
                ')
                ->join('tbl_trans_jual_det', 'tbl_trans_jual_det.id_penjualan = tbl_trans_jual.id', 'inner')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_jual_det.id_item', 'inner')
                ->where('tbl_trans_jual.id_gudang', $outletId)
                ->where('tbl_trans_jual.status_nota', '1');
            
            // Apply date filter if provided
            if ($startDate && $endDate) {
                $topItemsQuery->where('tbl_trans_jual.tgl_masuk >=', $startDate . ' 00:00:00')
                             ->where('tbl_trans_jual.tgl_masuk <=', $endDate . ' 23:59:59');
            }
            
            $topItems = $topItemsQuery->groupBy('tbl_trans_jual_det.id_item, tbl_m_item.item')
                                     ->orderBy('total_qty', 'DESC')
                                     ->limit(5)
            ->findAll();
            
                         $outlet['top_items'] = $topItems;
             
             // Get transaction details for this outlet
             $transactionQuery = $this->transJualModel->select('
                     tbl_trans_jual.no_nota,
                     tbl_trans_jual.tgl_masuk,
                     tbl_trans_jual.jml_gtotal,
                     tbl_trans_jual.status_nota,
                     tbl_m_pelanggan.nama as pelanggan_nama,
                     tbl_m_karyawan.nama as sales_nama
                 ')
                 ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
                 ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_jual.id_sales', 'left')
                 ->where('tbl_trans_jual.id_gudang', $outletId)
                 ->where('tbl_trans_jual.status_nota', '1');
             
             // Apply date filter if provided
             if ($startDate && $endDate) {
                 $transactionQuery->where('tbl_trans_jual.tgl_masuk >=', $startDate . ' 00:00:00')
                               ->where('tbl_trans_jual.tgl_masuk <=', $endDate . ' 23:59:59');
             }
             
             $transactions = $transactionQuery->orderBy('tbl_trans_jual.tgl_masuk', 'DESC')->findAll();
             
             // Format transaction details
             $transactionDetails = [];
             foreach ($transactions as $trans) {
                 $transactionDetails[] = [
                     'no_nota' => $trans->no_nota,
                     'tanggal' => date('d/m/Y', strtotime($trans->tgl_masuk)),
                     'pelanggan_nama' => $trans->pelanggan_nama,
                     'sales_nama' => $trans->sales_nama,
                     'total_transaksi' => $trans->jml_gtotal,
                     'status_transaksi' => $this->getStatusNotaText($trans->status_nota)
                 ];
             }
             
             $outlet['transaction_details'] = $transactionDetails;
         }
         
         // Convert to array
         $outletDetails = array_values($outletGroups);

        // Calculate summary values
        $totalOutlets = count($outletList);
        $totalItems = count($rawOutletDetails);
        $totalSales = 0;
        $totalTransactions = 0;

        // Calculate totals from restructured data
        foreach ($outletDetails as $outlet) {
            $totalSales += $outlet['sales']['total_sales'];
            $totalTransactions += $outlet['sales']['total_transactions'];
        }

        $data = [
            'title' => 'Laporan Outlet',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'outletDetails' => $outletDetails,
            'totalOutlets' => $totalOutlets,
            'totalSales' => $totalSales,
            'totalTransactions' => $totalItems, // or adjust as needed
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
            ->where('tbl_trans_jual.status_nota', '1');

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
     
     /**
      * Get readable text for status_nota
      */
     private function getStatusNotaText($status)
     {
         $statusMap = [
             '1' => 'Anamnesa',
             '2' => 'Pemeriksaan',
             '3' => 'Tindakan',
             '4' => 'Obat',
             '5' => 'Dokter',
             '6' => 'Pembayaran',
             '7' => 'Finish'
         ];
         
         return $statusMap[$status] ?? 'Unknown';
     }
}
