<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: Controller for handling sales reports
 * This file represents the SaleReport controller.
 */

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Models\TransJualModel;
use App\Models\TransJualDetModel;
use App\Models\PelangganModel;
use App\Models\GudangModel;
use App\Models\KaryawanModel;

class SaleReport extends BaseController
{
    protected $transJualModel;
    protected $transJualDetModel;
    protected $pelangganModel;
    protected $gudangModel;
    protected $karyawanModel;
    protected $ionAuth;

    public function __construct()
    {
        parent::__construct();
        $this->transJualModel = new TransJualModel();
        $this->transJualDetModel = new TransJualDetModel();
        $this->pelangganModel = new PelangganModel();
        $this->gudangModel = new GudangModel();
        $this->karyawanModel = new KaryawanModel();
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
    }

    public function index()
    {
        $startDate    = $this->request->getGet('start_date')    ?? date('Y-m-01');
        $endDate      = $this->request->getGet('end_date')      ?? date('Y-m-t');
        $idGudang     = $this->request->getGet('id_gudang');
        $idPelanggan  = $this->request->getGet('id_pelanggan');
        $idSales      = $this->request->getGet('id_sales');

        // Build query
        $builder = $this->transJualModel->select('
                tbl_trans_jual.*,
                tbl_m_pelanggan.nama as pelanggan_nama,
                tbl_m_pelanggan.kode as pelanggan_kode,
                tbl_m_gudang.nama as gudang_nama,
                tbl_m_karyawan.nama as sales_nama,
                tbl_m_shift.shift_code as shift_nama,
                tbl_ion_users.username as username,
                tbl_ion_users.first_name as user_first_name,
                tbl_ion_users.last_name as user_last_name
            ')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_trans_jual.id_gudang', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_jual.id_sales', 'left')
            ->join('tbl_m_shift', 'tbl_m_shift.id = tbl_trans_jual.id_shift', 'left')
            ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_jual.id_user', 'left')
            ->where('tbl_trans_jual.status_nota', '1')
            ->where('tbl_trans_jual.status', '1'); // Synchronize with cashier data

        // Apply filters
        if ($startDate && $endDate) {
            $builder->where('tbl_trans_jual.tgl_masuk >=', $startDate . ' 00:00:00')
                   ->where('tbl_trans_jual.tgl_masuk <=', $endDate . ' 23:59:59');
        }

        if ($idGudang) {
            $builder->where('tbl_trans_jual.id_gudang', $idGudang);
        }

        if ($idPelanggan) {
            $builder->where('tbl_trans_jual.id_pelanggan', $idPelanggan);
        }

        if ($idSales) {
            $builder->where('tbl_trans_jual.id_sales', $idSales);
        }

        $sales = $builder->orderBy('tbl_trans_jual.tgl_masuk', 'DESC')->findAll();

        // Get payment methods for each transaction
        $transactionIds = array_column($sales, 'id');
        $paymentMethods = [];
        if (!empty($transactionIds)) {
            $db = \Config\Database::connect();
            $paymentData = $db->table('tbl_trans_jual_plat')
                ->select('id_penjualan, GROUP_CONCAT(CONCAT(platform, " (", FORMAT(nominal, 0), ")") SEPARATOR ", ") as metode_pembayaran')
                ->whereIn('id_penjualan', $transactionIds)
                ->groupBy('id_penjualan')
                ->get()
                ->getResult();
            
            foreach ($paymentData as $pm) {
                $paymentMethods[$pm->id_penjualan] = $pm->metode_pembayaran;
            }
        }

        // Calculate summary and attach payment methods
        $totalSales = 0;
        $totalItems = 0;
        $totalTransactions = count($sales);

        foreach ($sales as $sale) {
            $totalSales += $sale->jml_gtotal ?? 0;
            
            // For sales, show username if sales_nama is not available
            if (empty($sale->sales_nama) || $sale->sales_nama === '-') {
                $sale->sales_nama = $sale->username ?? 'User ID: ' . ($sale->id_user ?? 'Unknown');
            }
            
            // Attach payment methods
            $sale->metode_pembayaran = $paymentMethods[$sale->id] ?? '-';
            
            // Set member name (use "Umum" for general customers)
            if (empty($sale->pelanggan_nama) || !$sale->id_pelanggan) {
                $sale->pelanggan_nama = 'Umum';
                $sale->pelanggan_kode = '';
            }
        }

        // Get filter options
        $gudangList     = $this->gudangModel->where('status', '1')->where('status_otl', '1')->findAll();
        $pelangganList  = $this->pelangganModel->where('status', '0')->findAll();
        $salesList      = $this->karyawanModel->where('status', '0')->findAll();

        $data = [
            'title'             => 'Laporan Penjualan',
            'Pengaturan'        => $this->pengaturan,
            'user'              => $this->ionAuth->user()->row(),
            'sales'             => $sales,
            'totalSales'        => $totalSales,
            'totalTransactions' => $totalTransactions,
            'startDate'         => $startDate,
            'endDate'           => $endDate,
            'idGudang'          => $idGudang,
            'idPelanggan'       => $idPelanggan,
            'idSales'           => $idSales,
            'gudangList'        => $gudangList,
            'pelangganList'     => $pelangganList,
            'salesList'         => $salesList,
            'breadcrumbs'       => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Laporan</li>
                <li class="breadcrumb-item active">Laporan Penjualan</li>
            ',
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/sale/index', $data);
    }

    public function detail($id)
    {
        $sale = $this->transJualModel->select('
                tbl_trans_jual.*,
                tbl_m_pelanggan.nama as pelanggan_nama,
                tbl_m_pelanggan.kode as pelanggan_kode,
                tbl_m_pelanggan.alamat as pelanggan_alamat,
                tbl_m_pelanggan.no_telp as pelanggan_telepon,
                tbl_m_gudang.nama as gudang_nama,
                tbl_m_karyawan.nama as sales_nama,
                tbl_m_shift.shift_code as shift_nama,
                tbl_ion_users.username as username,
                tbl_ion_users.first_name as user_first_name,
                tbl_ion_users.last_name as user_last_name
            ')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_trans_jual.id_gudang', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_jual.id_sales', 'left')
            ->join('tbl_m_shift', 'tbl_m_shift.id = tbl_trans_jual.id_shift', 'left')
            ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_jual.id_user', 'left')
            ->where('tbl_trans_jual.id', $id)
            ->first();

        if (!$sale) {
            return redirect()->to('laporan/sale')->with('error', 'Data penjualan tidak ditemukan');
        }

        $items = $this->transJualDetModel->select('
                tbl_trans_jual_det.*,
                tbl_m_item.item as item_nama,
                tbl_m_item.kode as item_kode,
                tbl_m_item.status_ppn,
                tbl_m_satuan.SatuanBesar as satuan_nama
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_jual_det.id_item', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_trans_jual_det.id_satuan', 'left')
            ->where('id_penjualan', $id)
            ->findAll();
            
        // Ensure proper fallbacks for item data
        foreach ($items as $item) {
            $item->satuan_nama = $item->satuan_nama ?? '-';
            $item->item_nama = $item->item_nama ?? '-';
            $item->item_kode = $item->item_kode ?? '-';
            // Set PPN status label
            $item->keterangan_pajak = ($item->status_ppn == '1') ? 'PPN' : 'Non-PPN';
        }

        // Get payment methods from tbl_trans_jual_plat
        $db = \Config\Database::connect();
        $paymentData = $db->table('tbl_trans_jual_plat')
            ->select('platform, nominal, keterangan')
            ->where('id_penjualan', $id)
            ->get()
            ->getResult();
        
        $paymentMethods = [];
        foreach ($paymentData as $pm) {
            $paymentMethods[] = $pm->platform . ' (' . number_format($pm->nominal, 0, ',', '.') . ')';
        }
        $sale->metode_pembayaran = !empty($paymentMethods) ? implode(', ', $paymentMethods) : '-';
        
        // Format payment method for display (fallback to old method if no platform data)
        $paymentMethodMap = [
            '1' => 'Cash',
            '2' => 'Transfer',
            '3' => 'Kartu Kredit',
            '4' => 'Kartu Debit',
            'cash' => 'Cash',
            'transfer' => 'Transfer',
            'credit' => 'Kartu Kredit',
            'debit' => 'Kartu Debit'
        ];
        
        $sale->metode_bayar_formatted = $sale->metode_pembayaran !== '-' ? $sale->metode_pembayaran : ($paymentMethodMap[$sale->metode_bayar] ?? $sale->metode_bayar ?? '-');
        
        // Ensure proper fallbacks for missing data
        // Set member name (use "Umum" for general customers)
        if (empty($sale->pelanggan_nama) || !$sale->id_pelanggan) {
            $sale->pelanggan_nama = 'Umum';
            $sale->pelanggan_kode = '';
        }
        $sale->pelanggan_alamat = $sale->pelanggan_alamat ?? '-';
        $sale->pelanggan_telepon = $sale->pelanggan_telepon ?? '-';
        
        // For sales, show username if sales_nama is not available
        if (empty($sale->sales_nama) || $sale->sales_nama === '-') {
            $sale->sales_nama = $sale->username ?? 'User ID: ' . ($sale->id_user ?? 'Unknown');
        }
        
        $sale->gudang_nama = $sale->gudang_nama ?? '-';
        $sale->shift_nama = $sale->shift_nama ?? '-';
        $sale->username = $sale->username ?? '-';
        
        // Create full name from first_name and last_name
        $fullName = trim(($sale->user_first_name ?? '') . ' ' . ($sale->user_last_name ?? ''));
        $sale->user_full_name = $fullName ?: $sale->username;

        $data = [
            'title' => 'Detail Penjualan - ' . $sale->no_nota,
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'sale' => $sale,
            'items' => $items,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('laporan/sale') . '">Laporan Penjualan</a></li>
                <li class="breadcrumb-item active">Detail</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/sale/detail', $data);
    }

    public function export_excel()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idGudang = $this->request->getGet('id_gudang');
        $idPelanggan = $this->request->getGet('id_pelanggan');
        $idSales = $this->request->getGet('id_sales');

        // Build query
        $builder = $this->transJualModel->select('
                tbl_trans_jual.*,
                tbl_m_pelanggan.nama as pelanggan_nama,
                tbl_m_pelanggan.kode as pelanggan_kode,
                tbl_m_gudang.nama as gudang_nama,
                tbl_m_karyawan.nama as sales_nama,
                tbl_m_shift.shift_code as shift_nama,
                tbl_ion_users.username as username,
                tbl_ion_users.first_name as user_first_name,
                tbl_ion_users.last_name as user_last_name
            ')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_trans_jual.id_gudang', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_jual.id_sales', 'left')
            ->join('tbl_m_shift', 'tbl_m_shift.id = tbl_trans_jual.id_shift', 'left')
            ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_jual.id_user', 'left')
            ->where('tbl_trans_jual.status_nota', '1')
            ->where('tbl_trans_jual.status', '1'); // Synchronize with cashier data

        // Apply filters
        if ($startDate && $endDate) {
            $builder->where('tbl_trans_jual.tgl_masuk >=', $startDate . ' 00:00:00')
                   ->where('tbl_trans_jual.tgl_masuk <=', $endDate . ' 23:59:59');
        }

        if ($idGudang) {
            $builder->where('tbl_trans_jual.id_gudang', $idGudang);
        }

        if ($idPelanggan) {
            $builder->where('tbl_trans_jual.id_pelanggan', $idPelanggan);
        }

        if ($idSales) {
            $builder->where('tbl_trans_jual.id_sales', $idSales);
        }

        $sales = $builder->orderBy('tbl_trans_jual.tgl_masuk', 'DESC')->findAll();

        // Get payment methods for each transaction
        $transactionIds = array_column($sales, 'id');
        $paymentMethods = [];
        if (!empty($transactionIds)) {
            $db = \Config\Database::connect();
            $paymentData = $db->table('tbl_trans_jual_plat')
                ->select('id_penjualan, GROUP_CONCAT(CONCAT(platform, " (", FORMAT(nominal, 0), ")") SEPARATOR ", ") as metode_pembayaran')
                ->whereIn('id_penjualan', $transactionIds)
                ->groupBy('id_penjualan')
                ->get()
                ->getResult();
            
            foreach ($paymentData as $pm) {
                $paymentMethods[$pm->id_penjualan] = $pm->metode_pembayaran;
            }
        }

        // Attach payment methods and set member info
        foreach ($sales as $sale) {
            $sale->metode_pembayaran = $paymentMethods[$sale->id] ?? '-';
            
            // Set member name (use "Umum" for general customers)
            if (empty($sale->pelanggan_nama) || !$sale->id_pelanggan) {
                $sale->pelanggan_nama = 'Umum';
                $sale->pelanggan_kode = '';
            }
        }

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'LAPORAN PENJUALAN');
        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));
        
        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Tanggal');
        $sheet->setCellValue('C4', 'No. Nota');
        $sheet->setCellValue('D4', 'Nama Anggota');
        $sheet->setCellValue('E4', 'No. Anggota');
        $sheet->setCellValue('F4', 'Metode Pembayaran');
        $sheet->setCellValue('G4', 'Gudang');
        $sheet->setCellValue('H4', 'Sales');
        $sheet->setCellValue('I4', 'Shift');
        $sheet->setCellValue('J4', 'Username');
        $sheet->setCellValue('K4', 'Total');

        $row = 5;
        $total = 0;

        foreach ($sales as $index => $sale) {
            // Create full name from first_name and last_name
            $fullName = trim(($sale->user_first_name ?? '') . ' ' . ($sale->user_last_name ?? ''));
            $userDisplayName = $fullName ?: $sale->username ?? '-';
            
            // For sales, show username if sales_nama is not available
            $salesDisplayName = $sale->sales_nama ?? '-';
            if (empty($salesDisplayName) || $salesDisplayName === '-') {
                $salesDisplayName = $sale->username ?? 'User ID: ' . ($sale->id_user ?? 'Unknown');
            }
            
            // Set member name and ID (use "Umum" for general customers)
            $memberName = $sale->pelanggan_nama ?? 'Umum';
            if (empty($sale->pelanggan_nama) || !$sale->id_pelanggan) {
                $memberName = 'Umum';
            }
            $memberId = $sale->pelanggan_kode ?? '';
            if (empty($sale->pelanggan_nama) || !$sale->id_pelanggan) {
                $memberId = '';
            }
            
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($sale->tgl_masuk)));
            $sheet->setCellValue('C' . $row, $sale->no_nota);
            $sheet->setCellValue('D' . $row, $memberName);
            $sheet->setCellValue('E' . $row, $memberId);
            $sheet->setCellValue('F' . $row, $sale->metode_pembayaran ?? '-');
            $sheet->setCellValue('G' . $row, $sale->gudang_nama ?? '-');
            $sheet->setCellValue('H' . $row, $salesDisplayName);
            $sheet->setCellValue('I' . $row, $sale->shift_nama ?? '-');
            $sheet->setCellValue('J' . $row, $userDisplayName);
            // Use actual numeric value for Total column
            $sheet->setCellValue('K' . $row, (float)($sale->jml_gtotal ?? 0));
            $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode('#,##0');
            
            $total += $sale->jml_gtotal ?? 0;
            $row++;
        }

        // Add total
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('K' . $row, (float)$total);
        $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode('#,##0');

        // Auto size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create response
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Laporan_Penjualan_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
