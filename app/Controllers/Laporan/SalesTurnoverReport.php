<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-09-11
 * Github: github.com/mikhaelfelian
 * Description: Controller for handling sales turnover reports
 * This file represents the SalesTurnoverReport controller.
 */

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Models\TransJualModel;
use App\Models\TransJualDetModel;
use App\Models\GudangModel;
use App\Models\PelangganModel;
use App\Models\KaryawanModel;

class SalesTurnoverReport extends BaseController
{
    protected $transJualModel;
    protected $transJualDetModel;
    protected $gudangModel;
    protected $pelangganModel;
    protected $karyawanModel;

    public function __construct()
    {
        parent::__construct();
        $this->transJualModel = new TransJualModel();
        $this->transJualDetModel = new TransJualDetModel();
        $this->gudangModel = new GudangModel();
        $this->pelangganModel = new PelangganModel();
        $this->karyawanModel = new KaryawanModel();
    }

    public function index()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idGudang = $this->request->getGet('id_gudang');
        $idSales = $this->request->getGet('id_sales');
        $paymentMethod = $this->request->getGet('payment_method');

        // Build query for sales turnover
        $builder = $this->transJualModel->select('
                tbl_trans_jual.*,
                tbl_m_gudang.nama as gudang_nama,
                tbl_m_karyawan.nama as sales_nama,
                tbl_m_pelanggan.nama as pelanggan_nama,
                SUM(tbl_trans_jual_det.subtotal) as total_amount,
                COUNT(tbl_trans_jual_det.id) as total_items
            ')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_trans_jual.id_gudang', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_jual.id_sales', 'left')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
            ->join('tbl_trans_jual_det', 'tbl_trans_jual_det.id_penjualan = tbl_trans_jual.id', 'left')
            ->where('tbl_trans_jual.status_nota', '1')
            ->where('tbl_trans_jual.deleted_at IS NULL')
            ->groupBy('tbl_trans_jual.id');

        // Apply date filter
        if ($startDate && $endDate) {
            $builder->where('DATE(tbl_trans_jual.tgl_masuk) >=', $startDate)
                   ->where('DATE(tbl_trans_jual.tgl_masuk) <=', $endDate);
        }

        // Apply filters
        if ($idGudang) {
            $builder->where('tbl_trans_jual.id_gudang', $idGudang);
        }

        if ($idSales) {
            $builder->where('tbl_trans_jual.id_sales', $idSales);
        }

        if ($paymentMethod) {
            $builder->where('tbl_trans_jual.metode_bayar', $paymentMethod);
        }

        $salesData = $builder->orderBy('tbl_trans_jual.tgl_masuk', 'DESC')->findAll();

        // Calculate summary
        $totalSales = 0;
        $totalTransactions = count($salesData);
        $totalCash = 0;
        $totalNonCash = 0;

        foreach ($salesData as $sale) {
            $totalSales += (float) $sale->total_amount;
            if ($sale->metode_bayar == 'cash' || $sale->metode_bayar == 'tunai') {
                $totalCash += (float) $sale->total_amount;
            } else {
                $totalNonCash += (float) $sale->total_amount;
            }
        }

        // Get filter options
        $gudangList = $this->gudangModel->where('status', '1')->findAll();
        $salesList = $this->karyawanModel->where('status', '0')->findAll();

        $data = [
            'title' => 'Laporan Omset Penjualan',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'salesData' => $salesData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'idGudang' => $idGudang,
            'idSales' => $idSales,
            'paymentMethod' => $paymentMethod,
            'gudangList' => $gudangList,
            'salesList' => $salesList,
            'summary' => [
                'total_sales' => $totalSales,
                'total_transactions' => $totalTransactions,
                'total_cash' => $totalCash,
                'total_non_cash' => $totalNonCash,
                'average_per_transaction' => $totalTransactions > 0 ? $totalSales / $totalTransactions : 0
            ],
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Laporan</li>
                <li class="breadcrumb-item active">Omset Penjualan</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/sales_turnover/index', $data);
    }

    public function export()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idGudang = $this->request->getGet('id_gudang');
        $idSales = $this->request->getGet('id_sales');
        $paymentMethod = $this->request->getGet('payment_method');

        // Same query as index but for export
        $builder = $this->transJualModel->select('
                tbl_trans_jual.*,
                tbl_m_gudang.nama as gudang_nama,
                tbl_m_karyawan.nama as sales_nama,
                tbl_m_pelanggan.nama as pelanggan_nama,
                SUM(tbl_trans_jual_det.subtotal) as total_amount,
                COUNT(tbl_trans_jual_det.id) as total_items
            ')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_trans_jual.id_gudang', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_jual.id_sales', 'left')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
            ->join('tbl_trans_jual_det', 'tbl_trans_jual_det.id_penjualan = tbl_trans_jual.id', 'left')
            ->where('tbl_trans_jual.status_nota', '1')
            ->where('tbl_trans_jual.deleted_at IS NULL')
            ->groupBy('tbl_trans_jual.id');

        // Apply filters (same as index)
        if ($startDate && $endDate) {
            $builder->where('DATE(tbl_trans_jual.tgl_masuk) >=', $startDate)
                   ->where('DATE(tbl_trans_jual.tgl_masuk) <=', $endDate);
        }

        if ($idGudang) {
            $builder->where('tbl_trans_jual.id_gudang', $idGudang);
        }

        if ($idSales) {
            $builder->where('tbl_trans_jual.id_sales', $idSales);
        }

        if ($paymentMethod) {
            $builder->where('tbl_trans_jual.metode_bayar', $paymentMethod);
        }

        $salesData = $builder->orderBy('tbl_trans_jual.tgl_masuk', 'DESC')->findAll();

        // Create Excel export
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Laporan Omset Penjualan');
        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));

        // Column headers
        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Tanggal');
        $sheet->setCellValue('C4', 'No. Nota');
        $sheet->setCellValue('D4', 'Gudang');
        $sheet->setCellValue('E4', 'Sales');
        $sheet->setCellValue('F4', 'Pelanggan');
        $sheet->setCellValue('G4', 'Metode Bayar');
        $sheet->setCellValue('H4', 'Total Items');
        $sheet->setCellValue('I4', 'Total Amount');

        // Data rows
        $row = 5;
        $no = 1;
        $grandTotal = 0;

        foreach ($salesData as $sale) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, date('d/m/Y H:i', strtotime($sale->tgl_masuk)));
            $sheet->setCellValue('C' . $row, $sale->no_nota);
            $sheet->setCellValue('D' . $row, $sale->gudang_nama);
            $sheet->setCellValue('E' . $row, $sale->sales_nama);
            $sheet->setCellValue('F' . $row, $sale->pelanggan_nama ?: 'Umum');
            $sheet->setCellValue('G' . $row, ucfirst($sale->metode_bayar));
            $sheet->setCellValue('H' . $row, $sale->total_items);
            $sheet->setCellValue('I' . $row, (float) $sale->total_amount);
            
            $grandTotal += (float) $sale->total_amount;
            $row++;
        }

        // Grand total
        $sheet->setCellValue('H' . $row, 'GRAND TOTAL:');
        $sheet->setCellValue('I' . $row, $grandTotal);

        // Style the sheet
        $sheet->getStyle('A1:I1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A4:I4')->getFont()->setBold(true);
        $sheet->getStyle('A4:I4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Export
        $filename = 'Laporan_Omset_Penjualan_' . date('Y-m-d', strtotime($startDate)) . '_to_' . date('Y-m-d', strtotime($endDate)) . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
