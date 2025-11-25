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

    /**
     * Build sales report data with filters shared by index & exports
     *
     * @param array $filters
     * @return array
     */
    private function getReportData(array $filters): array
    {
        $startDate   = $filters['start_date'] ?? date('Y-m-d');
        $endDate     = $filters['end_date'] ?? date('Y-m-d');
        $idGudang    = $filters['id_gudang'] ?? null;
        $idPelanggan = $filters['id_pelanggan'] ?? null;
        $idPlatform  = $filters['id_platform'] ?? null;

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
            ->where('tbl_trans_jual.status_bayar', '1');

        if ($startDate && $endDate) {
            $builder->where('tbl_trans_jual.tgl_masuk >=', $startDate . ' 00:00:00')
                    ->where('tbl_trans_jual.tgl_masuk <=', $endDate . ' 23:59:59');
        }

        if (!empty($idGudang)) {
            $builder->where('tbl_trans_jual.id_gudang', $idGudang);
        }

        if (!empty($idPelanggan)) {
            $builder->where('tbl_trans_jual.id_pelanggan', $idPelanggan);
        }

        if (!empty($idPlatform)) {
            $builder->join('tbl_trans_jual_plat tjp_filter', 'tjp_filter.id_penjualan = tbl_trans_jual.id', 'inner')
                    ->where('tjp_filter.id_platform', $idPlatform)
                    ->groupBy('tbl_trans_jual.id')
                    ->groupBy('tbl_trans_jual.tgl_masuk')
                    ->groupBy('tbl_trans_jual.no_nota');
        }

        $sales = $builder->orderBy('tbl_trans_jual.tgl_masuk', 'DESC')->findAll();

        $platforms = $this->platformModel->where('status', '1')->findAll();
        $vouchers  = $this->voucherModel->findAll();

        $transactionIds  = array_column($sales, 'id');
        $paymentMethods  = [];
        $paymentBreakdown = [];
        $voucherUsage    = [];

        if (!empty($transactionIds)) {
            $db = \Config\Database::connect();

            $paymentData = $db->table('tbl_trans_jual_plat')
                ->select('id_penjualan, id_platform, platform, SUM(nominal) as total_nominal')
                ->whereIn('id_penjualan', $transactionIds)
                ->groupBy('id_penjualan')
                ->groupBy('id_platform')
                ->groupBy('platform')
                ->get()
                ->getResult();

            foreach ($paymentData as $pm) {
                $platformId = $pm->id_platform;
                $existingSummary = $paymentMethods[$pm->id_penjualan] ?? '';
                $paymentMethods[$pm->id_penjualan] = $existingSummary .
                    ($existingSummary ? ', ' : '') .
                    $pm->platform . ' (' . format_angka($pm->total_nominal) . ')';

                if ($platformId === null) {
                    continue;
                }

                if (!isset($paymentBreakdown[$platformId])) {
                    $paymentBreakdown[$platformId] = [];
                }
                $paymentBreakdown[$platformId][$pm->id_penjualan] = (float)$pm->total_nominal;
            }

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

            $voucherCodeMap = [];
            foreach ($vouchers as $voucher) {
                if (!empty($voucher->kode)) {
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

        $totalSales = 0;
        $totalRetur = 0;
        $totalTransactions = count($sales);

        foreach ($sales as $sale) {
            $sale->jml_retur = (float)($sale->jml_retur ?? 0);
            $totalSales += $sale->jml_gtotal ?? 0;
            $totalRetur += $sale->jml_retur;

            if (empty($sale->sales_nama) || $sale->sales_nama === '-') {
                $sale->sales_nama = $sale->username ?? 'User ID: ' . ($sale->id_user ?? 'Unknown');
            }

            $sale->metode_pembayaran = $paymentMethods[$sale->id] ?? '-';

            $sale->platform_amounts = [];
            foreach ($platforms as $platform) {
                $platformId = $platform->id;
                $sale->platform_amounts[$platformId] = $paymentBreakdown[$platformId][$sale->id] ?? 0;
            }

            $sale->voucher_amounts = [];
            foreach ($vouchers as $voucher) {
                $voucherId = $voucher->id;
                $sale->voucher_amounts[$voucherId] = $voucherUsage[$voucherId][$sale->id] ?? 0;
            }

            if (empty($sale->pelanggan_nama) || !$sale->id_pelanggan) {
                $sale->pelanggan_nama = 'Umum';
                $sale->pelanggan_kode = '';
            }
        }

        return [
            'sales'             => $sales,
            'platforms'         => $platforms,
            'vouchers'          => $vouchers,
            'totalSales'        => $totalSales,
            'totalRetur'        => $totalRetur,
            'totalTransactions' => $totalTransactions,
            'filters'           => [
                'start_date'   => $startDate,
                'end_date'     => $endDate,
                'id_gudang'    => $idGudang,
                'id_pelanggan' => $idPelanggan,
                'id_platform'  => $idPlatform,
            ]
        ];
    }

    public function index()
    {
        $filters = [
            'start_date'   => $this->request->getGet('start_date') ?? date('Y-m-01'),
            'end_date'     => $this->request->getGet('end_date') ?? date('Y-m-t'),
            'id_gudang'    => $this->request->getGet('id_gudang'),
            'id_pelanggan' => $this->request->getGet('id_pelanggan'),
            'id_platform'  => $this->request->getGet('id_platform'),
        ];

        $reportData = $this->getReportData($filters);

        $gudangList     = $this->gudangModel->where('status', '1')->where('status_otl', '1')->findAll();
        $pelangganList  = $this->pelangganModel->where('status', '0')->findAll();
        $platformList   = $this->platformModel->where('status', '1')->findAll();

        $data = [
            'title'             => 'Laporan Penjualan',
            'Pengaturan'        => $this->pengaturan,
            'user'              => $this->ionAuth->user()->row(),
            'sales'             => $reportData['sales'],
            'totalSales'        => $reportData['totalSales'],
            'totalRetur'        => $reportData['totalRetur'],
            'totalTransactions' => $reportData['totalTransactions'],
            'startDate'         => $filters['start_date'],
            'endDate'           => $filters['end_date'],
            'idGudang'          => $filters['id_gudang'],
            'idPelanggan'       => $filters['id_pelanggan'],
            'idPlatform'        => $filters['id_platform'],
            'gudangList'        => $gudangList,
            'pelangganList'     => $pelangganList,
            'platformList'      => $platformList,
            'platforms'         => $reportData['platforms'],
            'vouchers'          => $reportData['vouchers'],
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
        $filters = [
            'start_date'   => $this->request->getGet('start_date') ?? date('Y-m-d'),
            'end_date'     => $this->request->getGet('end_date') ?? date('Y-m-d'),
            'id_gudang'    => $this->request->getGet('id_gudang'),
            'id_pelanggan' => $this->request->getGet('id_pelanggan'),
            'id_platform'  => $this->request->getGet('id_platform'),
        ];

        $startDate = $filters['start_date'];
        $endDate   = $filters['end_date'];

        $reportData = $this->getReportData($filters);
        $sales      = $reportData['sales'];
        $platforms  = $reportData['platforms'];
        $vouchers   = $reportData['vouchers'];
        $totalSales = $reportData['totalSales'];
        $totalRetur = $reportData['totalRetur'];

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'LAPORAN PENJUALAN');
        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));
        
        // Column headers: No, Tanggal, Pelanggan, [Platforms], [Vouchers], Subtotal, Retur
        $col = 'A';
        $sheet->setCellValue($col++ . '4', 'No');
        $sheet->setCellValue($col++ . '4', 'Tanggal');
        $sheet->setCellValue($col++ . '4', 'Pelanggan');
        
        // Dynamic platform columns
        $platformCols = [];
        foreach ($platforms as $platform) {
            $platformCols[$platform->id] = $col;
            $sheet->setCellValue($col++ . '4', $platform->platform);
        }
        
        // Dynamic voucher columns
        $voucherCols = [];
        foreach ($vouchers as $voucher) {
            $voucherCols[$voucher->id] = $col;
            $sheet->setCellValue($col++ . '4', $voucher->kode ?? 'Voucher ' . $voucher->id);
        }
        
        // Subtotal & retur columns
        $subtotalCol = $col;
        $sheet->setCellValue($col . '4', 'Subtotal');
        $returCol = ++$col;
        $sheet->setCellValue($col . '4', 'Retur');

        // Data rows
        $row = 5;
        foreach ($sales as $index => $sale) {
            $col = 'A';
            
            $sheet->setCellValue($col++ . $row, $index + 1);
            $sheet->setCellValue($col++ . $row, date('d/m/Y H:i', strtotime($sale->tgl_masuk)));
            
            // Pelanggan (with code if available)
            $pelangganText = $sale->pelanggan_nama ?? 'Umum';
            if (!empty($sale->pelanggan_kode)) {
                $pelangganText .= ' (' . $sale->pelanggan_kode . ')';
            }
            $sheet->setCellValue($col++ . $row, $pelangganText);
            
            // Platform columns
            foreach ($platforms as $platform) {
                $amount = $sale->platform_amounts[$platform->id] ?? 0;
                $sheet->setCellValue($platformCols[$platform->id] . $row, (float)$amount);
                $sheet->getStyle($platformCols[$platform->id] . $row)->getNumberFormat()->setFormatCode('#,##0');
            }
            
            // Voucher columns
            foreach ($vouchers as $voucher) {
                $amount = $sale->voucher_amounts[$voucher->id] ?? 0;
                $sheet->setCellValue($voucherCols[$voucher->id] . $row, (float)$amount);
                $sheet->getStyle($voucherCols[$voucher->id] . $row)->getNumberFormat()->setFormatCode('#,##0');
            }
            
            // Subtotal
            $sheet->setCellValue($subtotalCol . $row, (float)($sale->jml_gtotal ?? 0));
            $sheet->getStyle($subtotalCol . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->setCellValue($returCol . $row, (float)($sale->jml_retur ?? 0));
            $sheet->getStyle($returCol . $row)->getNumberFormat()->setFormatCode('#,##0');
            
            $row++;
        }

        // Add total row
        $col = 'A';
        $sheet->setCellValue($col++ . $row, 'TOTAL');
        $sheet->setCellValue($col++ . $row, '');
        $sheet->setCellValue($col++ . $row, '');
        
        // Platform totals
        foreach ($platforms as $platform) {
            $totalPlatform = 0;
            foreach ($sales as $sale) {
                $totalPlatform += $sale->platform_amounts[$platform->id] ?? 0;
            }
            $sheet->setCellValue($platformCols[$platform->id] . $row, (float)$totalPlatform);
            $sheet->getStyle($platformCols[$platform->id] . $row)->getNumberFormat()->setFormatCode('#,##0');
        }
        
        // Voucher totals
        foreach ($vouchers as $voucher) {
            $totalVoucher = 0;
            foreach ($sales as $sale) {
                $totalVoucher += $sale->voucher_amounts[$voucher->id] ?? 0;
            }
            $sheet->setCellValue($voucherCols[$voucher->id] . $row, (float)$totalVoucher);
            $sheet->getStyle($voucherCols[$voucher->id] . $row)->getNumberFormat()->setFormatCode('#,##0');
        }
        
        // Subtotal total
        $sheet->setCellValue($subtotalCol . $row, (float)$totalSales);
        $sheet->getStyle($subtotalCol . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->setCellValue($returCol . $row, (float)$totalRetur);
        $sheet->getStyle($returCol . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('A' . $row . ':' . $returCol . $row)->getFont()->setBold(true);

        // Auto size columns
        $lastColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($returCol);
        for ($i = 1; $i <= $lastColIndex; $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
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
        
        $filters = [
            'start_date'   => $this->request->getGet('start_date') ?? date('Y-m-d'),
            'end_date'     => $this->request->getGet('end_date') ?? date('Y-m-d'),
            'id_gudang'    => $this->request->getGet('id_gudang'),
            'id_pelanggan' => $this->request->getGet('id_pelanggan'),
            'id_platform'  => $this->request->getGet('id_platform'),
        ];

        $startDate = $filters['start_date'];
        $endDate   = $filters['end_date'];

        $reportData = $this->getReportData($filters);
        $sales      = $reportData['sales'];
        $platforms  = $reportData['platforms'];
        $vouchers   = $reportData['vouchers'];
        $totalSales = $reportData['totalSales'];
        $totalRetur = $reportData['totalRetur'];

        // Get filter labels
        $gudangList = $this->gudangModel->where('status', '1')->where('status_otl', '1')->findAll();
        $pelangganList = $this->pelangganModel->where('status', '0')->findAll();
        $platformList = $this->platformModel->where('status', '1')->findAll();
        
        $gudangName = 'Semua Outlet';
        if ($filters['id_gudang']) {
            foreach ($gudangList as $g) {
                if ($g->id == $filters['id_gudang']) {
                    $gudangName = $g->nama;
                    break;
                }
            }
        }
        
        $pelangganName = 'Semua Pelanggan';
        if ($filters['id_pelanggan']) {
            $ionAuth = new \IonAuth\Libraries\IonAuth();
            $pelangganUsers = $ionAuth->where('tipe', '2')->users()->result();
            foreach ($pelangganUsers as $p) {
                if ($p->id == $filters['id_pelanggan']) {
                    $pelangganName = isset($p->nama) ? $p->nama : (isset($p->first_name) ? $p->first_name : 'Pelanggan ' . $p->id);
                    break;
                }
            }
        }
        
        $platformName = 'Semua Platform';
        if ($filters['id_platform']) {
            foreach ($platformList as $p) {
                if ($p->id == $filters['id_platform']) {
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

        // Calculate column widths dynamically
        $numPlatforms = count($platforms);
        $numVouchers = count($vouchers);
        $totalDynamicCols = $numPlatforms + $numVouchers;
        
        $subtotalWidth = 40;
        $returWidth = 30;
        // Base columns: No (10), Tanggal (30), Pelanggan (50), Subtotal, Retur
        $baseWidth = 10 + 30 + 50 + $subtotalWidth + $returWidth;
        $availableWidth = 277 - $baseWidth; // Landscape A4 width minus margins and base columns
        $dynamicColWidth = $totalDynamicCols > 0 ? min(25, $availableWidth / max(1, $totalDynamicCols)) : 0;
        
        // Table Header
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->Cell(10, 6, 'No', 1, 0, 'C');
        $pdf->Cell(30, 6, 'Tanggal', 1, 0, 'C');
        $pdf->Cell(50, 6, 'Pelanggan', 1, 0, 'C');
        
        // Dynamic platform columns
        foreach ($platforms as $platform) {
            $pdf->Cell($dynamicColWidth, 6, substr($platform->platform, 0, 12), 1, 0, 'C');
        }
        
        // Dynamic voucher columns
        foreach ($vouchers as $voucher) {
            $voucherLabel = substr($voucher->kode ?? 'Voucher ' . $voucher->id, 0, 12);
            $pdf->Cell($dynamicColWidth, 6, $voucherLabel, 1, 0, 'C');
        }
        
        $pdf->Cell($subtotalWidth, 6, 'Subtotal', 1, 0, 'R');
        $pdf->Cell($returWidth, 6, 'Retur', 1, 1, 'R');

        // Table Data
        $pdf->SetFont('helvetica', '', 6);
        $no = 1;
        foreach ($sales as $sale) {
            $pdf->Cell(10, 5, $no++, 1, 0, 'C');
            $pdf->Cell(30, 5, date('d/m/Y H:i', strtotime($sale->tgl_masuk)), 1, 0, 'L');
            
            // Pelanggan (with code if available)
            $pelangganText = $sale->pelanggan_nama ?? 'Umum';
            if (!empty($sale->pelanggan_kode)) {
                $pelangganText .= ' (' . $sale->pelanggan_kode . ')';
            }
            $pdf->Cell(50, 5, substr($pelangganText, 0, 25), 1, 0, 'L');
            
            // Platform columns
            foreach ($platforms as $platform) {
                $amount = $sale->platform_amounts[$platform->id] ?? 0;
                $pdf->Cell($dynamicColWidth, 5, format_angka($amount), 1, 0, 'R');
            }
            
            // Voucher columns
            foreach ($vouchers as $voucher) {
                $amount = $sale->voucher_amounts[$voucher->id] ?? 0;
                $pdf->Cell($dynamicColWidth, 5, format_angka($amount), 1, 0, 'R');
            }
            
            // Subtotal
            $pdf->Cell($subtotalWidth, 5, format_angka($sale->jml_gtotal ?? 0), 1, 0, 'R');
            $pdf->Cell($returWidth, 5, format_angka($sale->jml_retur ?? 0), 1, 1, 'R');
        }

        // Total row
        $pdf->SetFont('helvetica', 'B', 7);
        $totalColWidth = 10 + 30 + 50 + ($dynamicColWidth * $totalDynamicCols);
        $pdf->Cell($totalColWidth, 6, 'TOTAL', 1, 0, 'R');
        
        // Platform totals
        foreach ($platforms as $platform) {
            $totalPlatform = 0;
            foreach ($sales as $sale) {
                $totalPlatform += $sale->platform_amounts[$platform->id] ?? 0;
            }
            $pdf->Cell($dynamicColWidth, 6, format_angka($totalPlatform), 1, 0, 'R');
        }
        
        // Voucher totals
        foreach ($vouchers as $voucher) {
            $totalVoucher = 0;
            foreach ($sales as $sale) {
                $totalVoucher += $sale->voucher_amounts[$voucher->id] ?? 0;
            }
            $pdf->Cell($dynamicColWidth, 6, format_angka($totalVoucher), 1, 0, 'R');
        }
        
        $pdf->Cell($subtotalWidth, 6, format_angka($totalSales), 1, 0, 'R');
        $pdf->Cell($returWidth, 6, format_angka($totalRetur), 1, 1, 'R');

        // Summary
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(0, 5, 'Total Transaksi: ' . count($sales), 0, 1, 'L');
        $pdf->Cell(0, 5, 'Total Penjualan: ' . format_angka($totalSales), 0, 1, 'L');
        $pdf->Cell(0, 5, 'Total Retur: ' . format_angka($totalRetur), 0, 1, 'L');

        // Output
        $filename = 'Laporan_Penjualan_' . date('Y-m-d') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }
}
