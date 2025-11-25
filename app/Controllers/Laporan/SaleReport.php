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
use App\Models\PlatformModel;
use App\Models\VoucherModel;

class SaleReport extends BaseController
{
    protected $transJualModel;
    protected $transJualDetModel;
    protected $pelangganModel;
    protected $gudangModel;
    protected $karyawanModel;
    protected $platformModel;
    protected $voucherModel;
    protected $ionAuth;

    public function __construct()
    {
        parent::__construct();
        $this->transJualModel = new TransJualModel();
        $this->transJualDetModel = new TransJualDetModel();
        $this->pelangganModel = new PelangganModel();
        $this->gudangModel = new GudangModel();
        $this->karyawanModel = new KaryawanModel();
        $this->platformModel = new PlatformModel();
        $this->voucherModel = new VoucherModel();
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
    }

    public function index()
    {
        $startDate    = $this->request->getGet('start_date')    ?? date('Y-m-01');
        $endDate      = $this->request->getGet('end_date')      ?? date('Y-m-t');
        $idGudang     = $this->request->getGet('id_gudang');
        $idPelanggan  = $this->request->getGet('id_pelanggan');
        $idPlatform   = $this->request->getGet('id_platform');

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

        if ($idPlatform) {
            $builder->join('tbl_trans_jual_plat tjp_filter', 'tjp_filter.id_penjualan = tbl_trans_jual.id', 'inner')
                    ->where('tjp_filter.id_platform', $idPlatform)
                    ->groupBy('tbl_trans_jual.id, tbl_trans_jual.tgl_masuk, tbl_trans_jual.no_nota');
        }

        $sales = $builder->orderBy('tbl_trans_jual.tgl_masuk', 'DESC')->findAll();

        // Get platforms with status='1' for dynamic columns
        $platforms = $this->platformModel->where('status', '1')->findAll();
        
        // Get vouchers for dynamic columns
        $vouchers = $this->voucherModel->findAll();
        
        // Get payment methods for each transaction
        $transactionIds = array_column($sales, 'id');
        $paymentMethods = [];
        $paymentBreakdown = []; // Platform ID => Transaction ID => Amount
        $voucherUsage = []; // Voucher ID => Transaction ID => Amount
        
        if (!empty($transactionIds)) {
            $db = \Config\Database::connect();
            
            // Get payment breakdown by platform
            $paymentData = $db->table('tbl_trans_jual_plat')
                ->select('id_penjualan, id_platform, platform, SUM(nominal) as total_nominal')
                ->whereIn('id_penjualan', $transactionIds)
                ->groupBy('id_penjualan', 'id_platform', 'platform')
                ->get()
                ->getResult();
            
            // Build payment methods string and breakdown
            foreach ($paymentData as $pm) {
                $platformId = $pm->id_platform;
                $existingSummary = $paymentMethods[$pm->id_penjualan] ?? '';

                $paymentMethods[$pm->id_penjualan] = $existingSummary . 
                    ($existingSummary ? ', ' : '') . 
                    $pm->platform . ' (' . format_angka($pm->total_nominal) . ')';
                
                if ($platformId === null) {
                    continue;
                }

                // Store breakdown by platform ID
                if (!isset($paymentBreakdown[$platformId])) {
                    $paymentBreakdown[$platformId] = [];
                }
                $paymentBreakdown[$platformId][$pm->id_penjualan] = (float)$pm->total_nominal;
            }
            
            // Get voucher usage per transaction
            $voucherData = $db->table('tbl_trans_jual')
                ->select('id, voucher_code, voucher_discount_amount, voucher_id')
                ->whereIn('id', $transactionIds)
                ->groupStart()
                    ->where('voucher_code IS NOT NULL')
                    ->where('voucher_code !=', '')
                ->groupEnd()
                ->orGroupStart()
                    ->where('voucher_id IS NOT NULL')
                ->groupEnd()
                ->get()
                ->getResult();
            
            // Create a map of voucher_code to voucher_id for lookup
            $voucherCodeMap = [];
            if (!empty($vouchers)) {
                foreach ($vouchers as $voucher) {
                    $voucherCodeMap[$voucher->kode] = $voucher->id;
                }
            }
            
            foreach ($voucherData as $v) {
                $voucherId = null;
                if ($v->voucher_id) {
                    $voucherId = $v->voucher_id;
                } elseif ($v->voucher_code && isset($voucherCodeMap[$v->voucher_code])) {
                    $voucherId = $voucherCodeMap[$v->voucher_code];
                }
                
                if ($voucherId) {
                    if (!isset($voucherUsage[$voucherId])) {
                        $voucherUsage[$voucherId] = [];
                    }
                    $voucherUsage[$voucherId][$v->id] = (float)($v->voucher_discount_amount ?? 0);
                }
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
            
            // Attach platform breakdown
            $sale->platform_amounts = [];
            foreach ($platforms as $platform) {
                $platformId = $platform->id;
                if (isset($paymentBreakdown[$platformId]) && isset($paymentBreakdown[$platformId][$sale->id])) {
                    $sale->platform_amounts[$platformId] = $paymentBreakdown[$platformId][$sale->id];
                } else {
                    $sale->platform_amounts[$platformId] = 0;
                }
            }
            
            // Attach voucher usage
            $sale->voucher_amounts = [];
            foreach ($vouchers as $voucher) {
                $voucherId = $voucher->id;
                if (isset($voucherUsage[$voucherId]) && isset($voucherUsage[$voucherId][$sale->id])) {
                    $sale->voucher_amounts[$voucherId] = $voucherUsage[$voucherId][$sale->id];
                } else {
                    $sale->voucher_amounts[$voucherId] = 0;
                }
            }
            
            // Set member name (use "Umum" for general customers)
            if (empty($sale->pelanggan_nama) || !$sale->id_pelanggan) {
                $sale->pelanggan_nama = 'Umum';
                $sale->pelanggan_kode = '';
            }
        }

        // Get filter options
        $gudangList     = $this->gudangModel->where('status', '1')->where('status_otl', '1')->findAll();
        $pelangganList  = $this->pelangganModel->where('status', '0')->findAll();
        $platformList   = $this->platformModel->where('status', '1')->findAll();

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
            'idPlatform'        => $idPlatform,
            'gudangList'        => $gudangList,
            'pelangganList'     => $pelangganList,
            'platformList'      => $platformList,
            'platforms'         => $platforms,
            'vouchers'          => $vouchers,
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
        $idPlatform = $this->request->getGet('id_platform');

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

        if ($idPlatform) {
            $builder->join('tbl_trans_jual_plat tjp_filter', 'tjp_filter.id_penjualan = tbl_trans_jual.id', 'inner')
                    ->where('tjp_filter.id_platform', $idPlatform)
                    ->groupBy('tbl_trans_jual.id, tbl_trans_jual.tgl_masuk, tbl_trans_jual.no_nota');
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

    public function export_pdf()
    {
        require_once(APPPATH . '../vendor/tecnickcom/tcpdf/tcpdf.php');
        
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idGudang = $this->request->getGet('id_gudang');
        $idPelanggan = $this->request->getGet('id_pelanggan');
        $idPlatform = $this->request->getGet('id_platform');

        // Build query (same as index)
        $builder = $this->transJualModel->select('
                tbl_trans_jual.*,
                tbl_m_pelanggan.nama as pelanggan_nama,
                tbl_m_pelanggan.kode as pelanggan_kode,
                tbl_m_gudang.nama as gudang_nama,
                tbl_m_karyawan.nama as sales_nama,
                tbl_m_shift.shift_code as shift_nama,
                tbl_ion_users.username as username
            ')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_trans_jual.id_gudang', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_jual.id_sales', 'left')
            ->join('tbl_m_shift', 'tbl_m_shift.id = tbl_trans_jual.id_shift', 'left')
            ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_jual.id_user', 'left')
            ->where('tbl_trans_jual.status_nota', '1')
            ->where('tbl_trans_jual.status', '1');

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

        if ($idPlatform) {
            $builder->join('tbl_trans_jual_plat tjp_filter', 'tjp_filter.id_penjualan = tbl_trans_jual.id', 'inner')
                    ->where('tjp_filter.id_platform', $idPlatform)
                    ->groupBy('tbl_trans_jual.id');
        }

        $sales = $builder->orderBy('tbl_trans_jual.tgl_masuk', 'DESC')->findAll();

        // Get payment methods
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

        $totalSales = 0;
        foreach ($sales as $sale) {
            $sale->metode_pembayaran = $paymentMethods[$sale->id] ?? '-';
            if (empty($sale->pelanggan_nama) || !$sale->id_pelanggan) {
                $sale->pelanggan_nama = 'Umum';
                $sale->pelanggan_kode = '';
            }
            $totalSales += $sale->jml_gtotal ?? 0;
        }

        // Get filter labels
        $gudangList = $this->gudangModel->where('status', '1')->where('status_otl', '1')->findAll();
        $pelangganList = $this->pelangganModel->where('status', '0')->findAll();
        $platformList = $this->platformModel->where('status', '1')->findAll();
        
        $gudangName = 'Semua Outlet';
        if ($idGudang) {
            foreach ($gudangList as $g) {
                if ($g->id == $idGudang) {
                    $gudangName = $g->nama;
                    break;
                }
            }
        }
        
        $pelangganName = 'Semua Pelanggan';
        if ($idPelanggan) {
            $ionAuth = new \IonAuth\Libraries\IonAuth();
            $pelangganUsers = $ionAuth->where('tipe', '2')->users()->result();
            foreach ($pelangganUsers as $p) {
                if ($p->id == $idPelanggan) {
                    $pelangganName = isset($p->nama) ? $p->nama : (isset($p->first_name) ? $p->first_name : 'Pelanggan ' . $p->id);
                    break;
                }
            }
        }
        
        $platformName = 'Semua Platform';
        if ($idPlatform) {
            foreach ($platformList as $p) {
                if ($p->id == $idPlatform) {
                    $platformName = $p->platform;
                    break;
                }
            }
        }

        // Create PDF
        $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator($this->pengaturan->judul_app ?? 'POS System');
        $pdf->SetAuthor($this->pengaturan->judul ?? 'Company');
        $pdf->SetTitle('Laporan Penjualan');
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
        $pdf->Cell(0, 8, 'LAPORAN PENJUALAN', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 5, 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)), 0, 1, 'C');
        $pdf->Ln(2);

        // Filter Info
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(0, 4, 'Outlet: ' . $gudangName . ' | Pelanggan: ' . $pelangganName . ' | Platform: ' . $platformName, 0, 1, 'L');
        $pdf->Ln(2);

        // Table Header
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(10, 6, 'No', 1, 0, 'C');
        $pdf->Cell(30, 6, 'Tanggal', 1, 0, 'C');
        $pdf->Cell(35, 6, 'No. Nota', 1, 0, 'C');
        $pdf->Cell(50, 6, 'Pelanggan', 1, 0, 'C');
        $pdf->Cell(50, 6, 'Metode Pembayaran', 1, 0, 'C');
        $pdf->Cell(30, 6, 'Gudang', 1, 0, 'C');
        $pdf->Cell(40, 6, 'Total', 1, 1, 'R');

        // Table Data
        $pdf->SetFont('helvetica', '', 7);
        $no = 1;
        foreach ($sales as $sale) {
            $pdf->Cell(10, 5, $no++, 1, 0, 'C');
            $pdf->Cell(30, 5, date('d/m/Y H:i', strtotime($sale->tgl_masuk)), 1, 0, 'L');
            $pdf->Cell(35, 5, substr($sale->no_nota, 0, 15), 1, 0, 'L');
            $pdf->Cell(50, 5, substr($sale->pelanggan_nama . ($sale->pelanggan_kode ? ' (' . $sale->pelanggan_kode . ')' : ''), 0, 30), 1, 0, 'L');
            $pdf->Cell(50, 5, substr($sale->metode_pembayaran, 0, 30), 1, 0, 'L');
            $pdf->Cell(30, 5, substr($sale->gudang_nama ?? '-', 0, 20), 1, 0, 'L');
            $pdf->Cell(40, 5, format_angka($sale->jml_gtotal ?? 0), 1, 1, 'R');
        }

        // Total
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(205, 6, 'TOTAL', 1, 0, 'R');
        $pdf->Cell(40, 6, format_angka($totalSales), 1, 1, 'R');

        // Summary
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(0, 5, 'Total Transaksi: ' . count($sales), 0, 1, 'L');
        $pdf->Cell(0, 5, 'Total Penjualan: ' . format_angka($totalSales), 0, 1, 'L');

        // Output
        $filename = 'Laporan_Penjualan_' . date('Y-m-d') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }
}
