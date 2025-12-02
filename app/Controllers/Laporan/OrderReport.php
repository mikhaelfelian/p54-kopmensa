<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-30
 * Github: github.com/mikhaelfelian
 * Description: Controller for handling order reports (Purchase Orders)
 * This file represents the OrderReport controller.
 */

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Models\TransBeliPOModel;
use App\Models\TransBeliPODetModel;
use App\Models\SupplierModel;
use App\Models\GudangModel;
use App\Models\KaryawanModel;

class OrderReport extends BaseController
{
    protected $transBeliPOModel;
    protected $transBeliPODetModel;
    protected $supplierModel;
    protected $gudangModel;
    protected $karyawanModel;

    public function __construct()
    {
        parent::__construct();
        $this->transBeliPOModel = new TransBeliPOModel();
        $this->transBeliPODetModel = new TransBeliPODetModel();
        $this->supplierModel = new SupplierModel();
        $this->gudangModel = new GudangModel();
        $this->karyawanModel = new KaryawanModel();
    }

    /**
     * Get report data with consistent query logic
     * 
     * @param array $filters
     * @return array
     */
    protected function getReportData($filters)
    {
        $startDate = $filters['start_date'] ?? date('Y-m-d');
        $endDate = $filters['end_date'] ?? date('Y-m-d');
        $idSupplier = $filters['id_supplier'] ?? null;
        $status = $filters['status'] ?? null;

        // Build query
        $builder = $this->transBeliPOModel->select('
                tbl_trans_beli_po.*,
                tbl_m_supplier.nama as supplier_nama,
                tbl_m_supplier.alamat as supplier_alamat,
                tbl_m_supplier.no_tlp as supplier_no_tlp,
                tbl_ion_users.username as user_username,
                tbl_ion_users.first_name as user_first_name,
                tbl_ion_users.last_name as user_last_name
            ')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli_po.id_supplier', 'left')
            ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_beli_po.id_user', 'left');

        // Apply filters
        if ($startDate && $endDate) {
            $builder->where('DATE(tbl_trans_beli_po.tgl_masuk) >=', $startDate)
                   ->where('DATE(tbl_trans_beli_po.tgl_masuk) <=', $endDate);
        }

        if ($idSupplier) {
            $builder->where('tbl_trans_beli_po.id_supplier', $idSupplier);
        }

        if ($status !== null && $status !== '') {
            $builder->where('tbl_trans_beli_po.status', $status);
        }

        $orders = $builder->orderBy('tbl_trans_beli_po.tgl_masuk', 'DESC')->findAll();

        // Calculate summary
        $totalOrders = count($orders);
        $totalDraft = 0;
        $totalApproved = 0;
        $totalCompleted = 0;

        foreach ($orders as $order) {
            if ($order->status == '0') {
                $totalDraft++;
            } elseif (in_array($order->status, ['2', '4', '5'])) {
                $totalApproved++;
            }
            if ($order->status == '5') {
                $totalCompleted++;
            }
        }

        return [
            'orders' => $orders,
            'totalOrders' => $totalOrders,
            'totalDraft' => $totalDraft,
            'totalApproved' => $totalApproved,
            'totalCompleted' => $totalCompleted,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'id_supplier' => $idSupplier,
                'status' => $status
            ]
        ];
    }

    public function index()
    {
        $filters = [
            'start_date' => $this->request->getGet('start_date') ?? date('Y-m-01'),
            'end_date' => $this->request->getGet('end_date') ?? date('Y-m-t'),
            'id_supplier' => $this->request->getGet('id_supplier'),
            'status' => $this->request->getGet('status')
        ];

        $report = $this->getReportData($filters);

        // Get filter options
        $supplierList = $this->supplierModel->where('deleted_at IS NULL')->findAll();

        $data = [
            'title' => 'Laporan Pesanan',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'orders' => $report['orders'],
            'totalOrders' => $report['totalOrders'],
            'totalDraft' => $report['totalDraft'],
            'totalApproved' => $report['totalApproved'],
            'totalCompleted' => $report['totalCompleted'],
            'startDate' => $report['filters']['start_date'],
            'endDate' => $report['filters']['end_date'],
            'idSupplier' => $report['filters']['id_supplier'],
            'status' => $report['filters']['status'],
            'supplierList' => $supplierList,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Laporan</li>
                <li class="breadcrumb-item active">Laporan Pesanan</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/order/index', $data);
    }

    public function detail($id)
    {
        $order = $this->transBeliPOModel->select('
                tbl_trans_beli_po.*,
                tbl_m_supplier.nama as supplier_nama,
                tbl_m_supplier.alamat as supplier_alamat,
                tbl_m_supplier.no_tlp as supplier_no_tlp,
                tbl_ion_users.username as user_username,
                tbl_ion_users.first_name as user_first_name,
                tbl_ion_users.last_name as user_last_name
            ')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli_po.id_supplier', 'left')
            ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_beli_po.id_user', 'left')
            ->where('tbl_trans_beli_po.id', $id)
            ->first();

        if (!$order) {
            return redirect()->to('laporan/order')->with('error', 'Data pesanan tidak ditemukan');
        }

        // Get items
        $items = $this->transBeliPODetModel->select('
                tbl_trans_beli_po_det.*,
                tbl_m_item.item as item_nama,
                tbl_m_item.kode as item_kode,
                tbl_m_satuan.SatuanBesar as satuan_nama
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_beli_po_det.id_item', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_trans_beli_po_det.id_satuan', 'left')
            ->where('id_pembelian', $id)
            ->findAll();

        // Create full name
        $fullName = trim(($order->user_first_name ?? '') . ' ' . ($order->user_last_name ?? ''));
        $order->user_full_name = $fullName ?: $order->user_username;

        $data = [
            'title' => 'Detail Pesanan - ' . $order->no_nota,
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'order' => $order,
            'items' => $items,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('laporan/order') . '">Laporan Pesanan</a></li>
                <li class="breadcrumb-item active">Detail</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/order/detail', $data);
    }

    public function detail_items($id = null)
    {
        if (!$id) {
            return redirect()->to('laporan/order')->with('error', 'ID Pesanan tidak valid');
        }

        $order = $this->transBeliPOModel->select('
                tbl_trans_beli_po.*,
                tbl_m_supplier.nama as supplier_nama,
                tbl_m_supplier.alamat as supplier_alamat,
                tbl_m_supplier.no_tlp as supplier_no_tlp,
                tbl_ion_users.username as user_username,
                tbl_ion_users.first_name as user_first_name,
                tbl_ion_users.last_name as user_last_name
            ')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli_po.id_supplier', 'left')
            ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_beli_po.id_user', 'left')
            ->where('tbl_trans_beli_po.id', $id)
            ->first();

        if (!$order) {
            return redirect()->to('laporan/order')->with('error', 'Data pesanan tidak ditemukan');
        }

        $items = $this->transBeliPODetModel->select('
                tbl_trans_beli_po_det.*,
                tbl_m_item.item as item_nama,
                tbl_m_item.kode as item_kode,
                tbl_m_satuan.SatuanBesar as satuan_nama
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_beli_po_det.id_item', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_trans_beli_po_det.id_satuan', 'left')
            ->where('id_pembelian', $id)
            ->findAll();

        // Create full name
        $fullName = trim(($order->user_first_name ?? '') . ' ' . ($order->user_last_name ?? ''));
        $order->user_full_name = $fullName ?: $order->user_username;

        $data = [
            'title' => 'Detail Item Pesanan - ' . $order->no_nota,
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'order' => $order,
            'items' => $items,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('laporan/order') . '">Laporan Pesanan</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('laporan/order/detail/' . $id) . '">Detail</a></li>
                <li class="breadcrumb-item active">Detail Item</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/order/detail_items', $data);
    }

    public function print_invoice($id = null)
    {
        if (!$id) {
            return redirect()->to('laporan/order')->with('error', 'ID Pesanan tidak valid');
        }

        $order = $this->transBeliPOModel->select('
                tbl_trans_beli_po.*,
                tbl_m_supplier.nama as supplier_nama,
                tbl_m_supplier.alamat as supplier_alamat,
                tbl_m_supplier.no_tlp as supplier_no_tlp,
                tbl_ion_users.username as user_username,
                tbl_ion_users.first_name as user_first_name,
                tbl_ion_users.last_name as user_last_name
            ')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli_po.id_supplier', 'left')
            ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_beli_po.id_user', 'left')
            ->where('tbl_trans_beli_po.id', $id)
            ->first();

        if (!$order) {
            return redirect()->to('laporan/order')->with('error', 'Data pesanan tidak ditemukan');
        }

        $items = $this->transBeliPODetModel->select('
                tbl_trans_beli_po_det.*,
                tbl_m_item.item as item_nama,
                tbl_m_item.kode as item_kode,
                tbl_m_satuan.SatuanBesar as satuan_nama
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_beli_po_det.id_item', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_trans_beli_po_det.id_satuan', 'left')
            ->where('id_pembelian', $id)
            ->findAll();

        // Create full name
        $fullName = trim(($order->user_first_name ?? '') . ' ' . ($order->user_last_name ?? ''));
        $order->user_full_name = $fullName ?: $order->user_username;

        $data = [
            'title' => 'Cetak Faktur Pesanan - ' . $order->no_nota,
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'order' => $order,
            'items' => $items
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/order/print_invoice', $data);
    }

    public function export_excel()
    {
        $filters = [
            'start_date' => $this->request->getGet('start_date') ?? date('Y-m-d'),
            'end_date' => $this->request->getGet('end_date') ?? date('Y-m-d'),
            'id_supplier' => $this->request->getGet('id_supplier'),
            'status' => $this->request->getGet('status')
        ];

        $report = $this->getReportData($filters);
        $orders = $report['orders'];
        $startDate = $report['filters']['start_date'];
        $endDate = $report['filters']['end_date'];

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'LAPORAN PESANAN (PURCHASE ORDER)');
        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));
        
        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Tanggal');
        $sheet->setCellValue('C4', 'No. PO');
        $sheet->setCellValue('D4', 'Supplier');
        $sheet->setCellValue('E4', 'Pembuat');
        $sheet->setCellValue('F4', 'Status');
        $sheet->setCellValue('G4', 'Keterangan');

        // Style header row
        $sheet->getStyle('A4:G4')->getFont()->setBold(true);
        $sheet->getStyle('A4:G4')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        $row = 5;

        foreach ($orders as $index => $order) {
            $statusLabel = $this->transBeliPOModel->getStatusLabel($order->status ?? '0');
            $fullName = trim(($order->user_first_name ?? '') . ' ' . ($order->user_last_name ?? ''));
            $userName = $fullName ?: ($order->user_username ?? '-');

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($order->tgl_masuk)));
            $sheet->setCellValueExplicit('C' . $row, (string)($order->no_nota ?? ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row, $order->supplier_nama ?? '-');
            $sheet->setCellValue('E' . $row, $userName);
            $sheet->setCellValue('F' . $row, $statusLabel);
            $sheet->setCellValue('G' . $row, $order->keterangan ?? '-');
            
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create response
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Laporan_Pesanan_' . date('Y-m-d') . '.xlsx';

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
            'start_date' => $this->request->getGet('start_date') ?? date('Y-m-d'),
            'end_date' => $this->request->getGet('end_date') ?? date('Y-m-d'),
            'id_supplier' => $this->request->getGet('id_supplier'),
            'status' => $this->request->getGet('status')
        ];

        $report = $this->getReportData($filters);
        $orders = $report['orders'];
        $startDate = $report['filters']['start_date'];
        $endDate = $report['filters']['end_date'];
        $idSupplier = $report['filters']['id_supplier'];

        $supplierList = $this->supplierModel->where('deleted_at IS NULL')->findAll();
        $supplierName = 'Semua Supplier';
        if ($idSupplier) {
            foreach ($supplierList as $s) {
                if ($s->id == $idSupplier) {
                    $supplierName = $s->nama;
                    break;
                }
            }
        }

        // Create PDF
        $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator($this->pengaturan->judul_app ?? 'POS System');
        $pdf->SetAuthor($this->pengaturan->judul ?? 'Company');
        $pdf->SetTitle('Laporan Pesanan');
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
        $pdf->Cell(0, 8, 'LAPORAN PESANAN (PURCHASE ORDER)', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 5, 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)), 0, 1, 'C');
        $pdf->Ln(2);

        // Filter Info
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(0, 4, 'Supplier: ' . $supplierName, 0, 1, 'L');
        $pdf->Ln(2);

        // Table Header
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(10, 6, 'No', 1, 0, 'C');
        $pdf->Cell(30, 6, 'Tanggal', 1, 0, 'C');
        $pdf->Cell(40, 6, 'No. PO', 1, 0, 'C');
        $pdf->Cell(60, 6, 'Supplier', 1, 0, 'C');
        $pdf->Cell(50, 6, 'Pembuat', 1, 0, 'C');
        $pdf->Cell(40, 6, 'Status', 1, 0, 'C');
        $pdf->Cell(57, 6, 'Keterangan', 1, 1, 'C');

        // Table Data
        $pdf->SetFont('helvetica', '', 7);
        $no = 1;
        foreach ($orders as $order) {
            $statusLabel = $this->transBeliPOModel->getStatusLabel($order->status ?? '0');
            $fullName = trim(($order->user_first_name ?? '') . ' ' . ($order->user_last_name ?? ''));
            $userName = $fullName ?: ($order->user_username ?? '-');

            $pdf->Cell(10, 5, $no++, 1, 0, 'C');
            $pdf->Cell(30, 5, date('d/m/Y', strtotime($order->tgl_masuk)), 1, 0, 'L');
            $pdf->Cell(40, 5, substr($order->no_nota ?? '-', 0, 20), 1, 0, 'L');
            $pdf->Cell(60, 5, substr($order->supplier_nama ?? '-', 0, 35), 1, 0, 'L');
            $pdf->Cell(50, 5, substr($userName, 0, 30), 1, 0, 'L');
            $pdf->Cell(40, 5, substr($statusLabel, 0, 20), 1, 0, 'C');
            $pdf->Cell(57, 5, substr($order->keterangan ?? '-', 0, 30), 1, 1, 'L');
        }

        // Summary
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(0, 5, 'Total Pesanan: ' . count($orders), 0, 1, 'L');

        // Output
        $filename = 'Laporan_Pesanan_' . date('Y-m-d') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }
}

