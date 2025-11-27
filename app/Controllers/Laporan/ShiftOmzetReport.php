<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: Controller for handling shift omzet (revenue) reports
 * This file represents the ShiftOmzetReport controller.
 */

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Models\TransJualModel;
use App\Models\ShiftModel;

class ShiftOmzetReport extends BaseController
{
    protected $transJualModel;
    protected $shiftModel;
    protected $ionAuth;

    public function __construct()
    {
        parent::__construct();
        $this->transJualModel = new TransJualModel();
        $this->shiftModel = new ShiftModel();
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
    }

    public function index()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idUser = $this->request->getGet('id_user');

        $db = \Config\Database::connect();
        
        // Build query: Join tbl_trans_jual with tbl_m_shift, group by shift, sum jml_gtotal
        // Move transaction status conditions to JOIN clause to preserve LEFT JOIN behavior
        $builder = $db->table('tbl_m_shift s')
            ->select('
                s.id,
                s.shift_code,
                s.nama_shift,
                s.start_at,
                s.end_at,
                s.outlet_id,
                s.user_open_id,
                s.user_close_id,
                tbl_m_gudang.nama as outlet_nama,
                tbl_ion_users_open.username as user_open_username,
                tbl_ion_users_open.first_name as user_open_first_name,
                tbl_ion_users_open.last_name as user_open_last_name,
                COALESCE(SUM(tj.jml_gtotal), 0) as total_omzet,
                COALESCE(COUNT(DISTINCT tj.id), 0) as total_transactions
            ')
            ->join('tbl_trans_jual tj', "tj.id_shift = s.id AND tj.status_nota = '1' AND tj.status = '1' AND tj.deleted_at IS NULL", 'left')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = s.outlet_id', 'left')
            ->join('tbl_ion_users tbl_ion_users_open', 'tbl_ion_users_open.id = s.user_open_id', 'left')
            ->groupBy('s.id');

        // Apply date filter
        if ($startDate && $endDate) {
            $builder->where('DATE(s.start_at) >=', $startDate)
                   ->where('DATE(s.start_at) <=', $endDate);
        }

        // Apply user filter
        if ($idUser) {
            $builder->where('s.user_open_id', $idUser);
        }

        $shifts = $builder->orderBy('s.start_at', 'DESC')->get()->getResult();

        // Calculate totals
        $totalOmzet = 0;
        $totalTransactions = 0;
        foreach ($shifts as $shift) {
            $totalOmzet += (float)($shift->total_omzet ?? 0);
            $totalTransactions += (int)($shift->total_transactions ?? 0);
        }

        // Get user list for filter - only employees (tipe='1')
        $userList = $this->ionAuth->where('tipe', '1')->users()->result();

        $data = [
            'title' => 'Laporan Omzet Shift',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'shifts' => $shifts,
            'totalOmzet' => $totalOmzet,
            'totalTransactions' => $totalTransactions,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'idUser' => $idUser,
            'userList' => $userList,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Laporan</li>
                <li class="breadcrumb-item active">Omzet Shift</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/shift_omzet/index', $data);
    }

    public function export_excel()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idUser = $this->request->getGet('id_user');

        $db = \Config\Database::connect();
        
        $builder = $db->table('tbl_m_shift s')
            ->select('
                s.id,
                s.shift_code,
                s.nama_shift,
                s.start_at,
                s.end_at,
                tbl_m_gudang.nama as outlet_nama,
                tbl_ion_users_open.username as user_open_username,
                tbl_ion_users_open.first_name as user_open_first_name,
                tbl_ion_users_open.last_name as user_open_last_name,
                COALESCE(SUM(tj.jml_gtotal), 0) as total_omzet,
                COALESCE(COUNT(DISTINCT tj.id), 0) as total_transactions
            ')
            ->join('tbl_trans_jual tj', "tj.id_shift = s.id AND tj.status_nota = '1' AND tj.status = '1' AND tj.deleted_at IS NULL", 'left')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = s.outlet_id', 'left')
            ->join('tbl_ion_users tbl_ion_users_open', 'tbl_ion_users_open.id = s.user_open_id', 'left')
            ->groupBy('s.id');

        if ($startDate && $endDate) {
            $builder->where('DATE(s.start_at) >=', $startDate)
                   ->where('DATE(s.start_at) <=', $endDate);
        }

        if ($idUser) {
            $builder->where('s.user_open_id', $idUser);
        }

        $shifts = $builder->orderBy('s.start_at', 'DESC')->get()->getResult();

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'LAPORAN OMZET SHIFT');
        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));
        
        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Shift Code');
        $sheet->setCellValue('C4', 'Nama Shift');
        $sheet->setCellValue('D4', 'Tanggal Mulai');
        $sheet->setCellValue('E4', 'Tanggal Selesai');
        $sheet->setCellValue('F4', 'Outlet');
        $sheet->setCellValue('G4', 'User');
        $sheet->setCellValue('H4', 'Total Transaksi');
        $sheet->setCellValue('I4', 'Total Omzet');

        $row = 5;
        $totalOmzet = 0;
        $totalTransactions = 0;

        foreach ($shifts as $index => $shift) {
            $userName = trim(($shift->user_open_first_name ?? '') . ' ' . ($shift->user_open_last_name ?? ''));
            $userName = $userName ?: $shift->user_open_username ?? '-';

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $shift->shift_code);
            $sheet->setCellValue('C' . $row, $shift->nama_shift ?? '-');
            $sheet->setCellValue('D' . $row, $shift->start_at ? date('d/m/Y H:i', strtotime($shift->start_at)) : '-');
            $sheet->setCellValue('E' . $row, $shift->end_at ? date('d/m/Y H:i', strtotime($shift->end_at)) : '-');
            $sheet->setCellValue('F' . $row, $shift->outlet_nama ?? '-');
            $sheet->setCellValue('G' . $row, $userName);
            $sheet->setCellValue('H' . $row, (int)($shift->total_transactions ?? 0));
            $sheet->setCellValue('I' . $row, (float)($shift->total_omzet ?? 0));
            $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('#,##0');
            
            $totalOmzet += (float)($shift->total_omzet ?? 0);
            $totalTransactions += (int)($shift->total_transactions ?? 0);
            $row++;
        }

        // Add total
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('H' . $row, (int)$totalTransactions);
        $sheet->setCellValue('I' . $row, (float)$totalOmzet);
        $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('A' . $row . ':I' . $row)->getFont()->setBold(true);

        // Auto size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create response
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Laporan_Omzet_Shift_' . date('Y-m-d') . '.xlsx';

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
        $idUser = $this->request->getGet('id_user');

        $db = \Config\Database::connect();
        
        $builder = $db->table('tbl_m_shift s')
            ->select('
                s.id,
                s.shift_code,
                s.nama_shift,
                s.start_at,
                s.end_at,
                tbl_m_gudang.nama as outlet_nama,
                tbl_ion_users_open.username as user_open_username,
                tbl_ion_users_open.first_name as user_open_first_name,
                tbl_ion_users_open.last_name as user_open_last_name,
                COALESCE(SUM(tj.jml_gtotal), 0) as total_omzet,
                COALESCE(COUNT(DISTINCT tj.id), 0) as total_transactions
            ')
            ->join('tbl_trans_jual tj', "tj.id_shift = s.id AND tj.status_nota = '1' AND tj.status = '1' AND tj.deleted_at IS NULL", 'left')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = s.outlet_id', 'left')
            ->join('tbl_ion_users tbl_ion_users_open', 'tbl_ion_users_open.id = s.user_open_id', 'left')
            ->groupBy('s.id');

        if ($startDate && $endDate) {
            $builder->where('DATE(s.start_at) >=', $startDate)
                   ->where('DATE(s.start_at) <=', $endDate);
        }

        if ($idUser) {
            $builder->where('s.user_open_id', $idUser);
        }

        $shifts = $builder->orderBy('s.start_at', 'DESC')->get()->getResult();

        $totalOmzet = 0;
        $totalTransactions = 0;
        foreach ($shifts as $shift) {
            $totalOmzet += (float)($shift->total_omzet ?? 0);
            $totalTransactions += (int)($shift->total_transactions ?? 0);
        }

        $userList = $this->ionAuth->users()->result();
        $userName = 'Semua User';
        if ($idUser) {
            foreach ($userList as $u) {
                if ($u->id == $idUser) {
                    $fullName = trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? ''));
                    $userName = $fullName ?: $u->username ?? 'User ' . $u->id;
                    break;
                }
            }
        }

        // Create PDF
        $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator($this->pengaturan->judul_app ?? 'POS System');
        $pdf->SetAuthor($this->pengaturan->judul ?? 'Company');
        $pdf->SetTitle('Laporan Omzet Shift');
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
        $pdf->Cell(0, 8, 'LAPORAN OMZET SHIFT', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 5, 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)), 0, 1, 'C');
        $pdf->Ln(2);

        // Filter Info
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(0, 4, 'User: ' . $userName, 0, 1, 'L');
        $pdf->Ln(2);

        // Table Header
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(10, 6, 'No', 1, 0, 'C');
        $pdf->Cell(35, 6, 'Shift Code', 1, 0, 'C');
        $pdf->Cell(30, 6, 'Nama Shift', 1, 0, 'C');
        $pdf->Cell(40, 6, 'Tanggal Mulai', 1, 0, 'C');
        $pdf->Cell(40, 6, 'Tanggal Selesai', 1, 0, 'C');
        $pdf->Cell(40, 6, 'Outlet', 1, 0, 'C');
        $pdf->Cell(40, 6, 'User', 1, 0, 'C');
        $pdf->Cell(25, 6, 'Transaksi', 1, 0, 'R');
        $pdf->Cell(27, 6, 'Total Omzet', 1, 1, 'R');

        // Table Data
        $pdf->SetFont('helvetica', '', 7);
        $no = 1;
        foreach ($shifts as $shift) {
            $userName = trim(($shift->user_open_first_name ?? '') . ' ' . ($shift->user_open_last_name ?? ''));
            $userName = $userName ?: $shift->user_open_username ?? '-';

            $pdf->Cell(10, 5, $no++, 1, 0, 'C');
            $pdf->Cell(35, 5, substr($shift->shift_code, 0, 20), 1, 0, 'L');
            $pdf->Cell(30, 5, substr($shift->nama_shift ?? '-', 0, 20), 1, 0, 'L');
            $pdf->Cell(40, 5, $shift->start_at ? date('d/m/Y H:i', strtotime($shift->start_at)) : '-', 1, 0, 'L');
            $pdf->Cell(40, 5, $shift->end_at ? date('d/m/Y H:i', strtotime($shift->end_at)) : '-', 1, 0, 'L');
            $pdf->Cell(40, 5, substr($shift->outlet_nama ?? '-', 0, 25), 1, 0, 'L');
            $pdf->Cell(40, 5, substr($userName, 0, 25), 1, 0, 'L');
            $pdf->Cell(25, 5, (int)($shift->total_transactions ?? 0), 1, 0, 'R');
            $pdf->Cell(27, 5, format_angka($shift->total_omzet ?? 0), 1, 1, 'R');
        }

        // Total
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(225, 6, 'TOTAL', 1, 0, 'R');
        $pdf->Cell(25, 6, (int)$totalTransactions, 1, 0, 'R');
        $pdf->Cell(27, 6, format_angka($totalOmzet), 1, 1, 'R');

        // Summary
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(0, 5, 'Total Shift: ' . count($shifts), 0, 1, 'L');
        $pdf->Cell(0, 5, 'Total Transaksi: ' . $totalTransactions, 0, 1, 'L');
        $pdf->Cell(0, 5, 'Total Omzet: ' . format_angka($totalOmzet), 0, 1, 'L');

        // Output
        $filename = 'Laporan_Omzet_Shift_' . date('Y-m-d') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }
}

