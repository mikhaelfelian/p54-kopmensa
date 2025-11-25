<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: Controller for handling purchase reports
 * This file represents the PurchaseReport controller.
 */

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Models\TransBeliModel;
use App\Models\TransBeliDetModel;
use App\Models\SupplierModel;
use App\Models\GudangModel;
use App\Models\KaryawanModel;

class PurchaseReport extends BaseController
{
    protected $transBeliModel;
    protected $transBeliDetModel;
    protected $supplierModel;
    protected $gudangModel;
    protected $karyawanModel;

    public function __construct()
    {
        parent::__construct();
        $this->transBeliModel = new TransBeliModel();
        $this->transBeliDetModel = new TransBeliDetModel();
        $this->supplierModel = new SupplierModel();
        $this->gudangModel = new GudangModel();
        $this->karyawanModel = new KaryawanModel();
    }

    public function index()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idSupplier = $this->request->getGet('id_supplier');

        // Build query
        $builder = $this->transBeliModel->select('
                tbl_trans_beli.*,
                tbl_m_supplier.nama as supplier_nama,
                tbl_m_karyawan.nama as penerima_nama
            ')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli.id_supplier', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_beli.id_penerima', 'left')
            ->where('tbl_trans_beli.deleted_at IS NULL');

        // Apply filters
        if ($startDate && $endDate) {
            $builder->where('tbl_trans_beli.tgl_masuk >=', $startDate . ' 00:00:00')
                   ->where('tbl_trans_beli.tgl_masuk <=', $endDate . ' 23:59:59');
        }

        if ($idSupplier) {
            $builder->where('tbl_trans_beli.id_supplier', $idSupplier);
        }

        $purchases = $builder->orderBy('tbl_trans_beli.tgl_masuk', 'DESC')->findAll();

        // Calculate summary
        $totalPurchase = 0;
        $totalTransactions = count($purchases);
        $totalPaid = 0;
        $totalUnpaid = 0;

        foreach ($purchases as $purchase) {
            $totalPurchase += $purchase->jml_gtotal ?? 0;
            if ($purchase->status_bayar == '1') {
                $totalPaid += $purchase->jml_gtotal ?? 0;
            } else {
                $totalUnpaid += $purchase->jml_gtotal ?? 0;
            }
        }

        // Get filter options
        $supplierList = $this->supplierModel->where('deleted_at IS NULL')->findAll();

        $data = [
            'title' => 'Laporan Pembelian',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'purchases' => $purchases,
            'totalPurchase' => $totalPurchase,
            'totalTransactions' => $totalTransactions,
            'totalPaid' => $totalPaid,
            'totalUnpaid' => $totalUnpaid,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'idSupplier' => $idSupplier,
            'supplierList' => $supplierList,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Laporan</li>
                <li class="breadcrumb-item active">Laporan Pembelian</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/purchase/index', $data);
    }

    public function detail($id)
    {
        $purchase = $this->transBeliModel->select('
                tbl_trans_beli.*,
                tbl_m_supplier.nama as supplier_nama,
                tbl_m_supplier.alamat as supplier_alamat,
                tbl_m_supplier.no_tlp as supplier_no_tlp,
                tbl_m_karyawan.nama as penerima_nama,
                tbl_m_gudang.nama as gudang_nama
            ')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli.id_supplier', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_beli.id_penerima', 'left')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_trans_beli.id_gudang', 'left')
            ->where('tbl_trans_beli.id', $id)
            ->first();

        if (!$purchase) {
            return redirect()->to('laporan/purchase')->with('error', 'Data pembelian tidak ditemukan');
        }

        $items = $this->transBeliDetModel->select('
                tbl_trans_beli_det.*,
                tbl_m_item.item as item_nama,
                tbl_m_item.kode as item_kode,
                tbl_m_satuan.SatuanBesar as satuan_nama
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_beli_det.id_item', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_trans_beli_det.id_satuan', 'left')
            ->where('id_pembelian', $id)
            ->findAll();

        $data = [
            'title' => 'Detail Pembelian - ' . $purchase->no_nota,
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'purchase' => $purchase,
            'items' => $items,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('laporan/purchase') . '">Laporan Pembelian</a></li>
                <li class="breadcrumb-item active">Detail</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/purchase/detail', $data);
    }

    public function export_excel()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idSupplier = $this->request->getGet('id_supplier');

        // Build query
        $builder = $this->transBeliModel->select('
                tbl_trans_beli.*,
                tbl_m_supplier.nama as supplier_nama,
                tbl_m_karyawan.nama as penerima_nama
            ')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli.id_supplier', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_beli.id_penerima', 'left')
            ->where('tbl_trans_beli.deleted_at IS NULL');

        // Apply filters
        if ($startDate && $endDate) {
            $builder->where('tbl_trans_beli.tgl_masuk >=', $startDate . ' 00:00:00')
                   ->where('tbl_trans_beli.tgl_masuk <=', $endDate . ' 23:59:59');
        }

        if ($idSupplier) {
            $builder->where('tbl_trans_beli.id_supplier', $idSupplier);
        }

        $purchases = $builder->orderBy('tbl_trans_beli.tgl_masuk', 'DESC')->findAll();

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'LAPORAN PEMBELIAN');
        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));
        
        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Tanggal');
        $sheet->setCellValue('C4', 'No. Faktur');
        $sheet->setCellValue('D4', 'Supplier');
        $sheet->setCellValue('E4', 'Penerima');
        $sheet->setCellValue('F4', 'Status');
        $sheet->setCellValue('G4', 'Total');

        $row = 5;
        $total = 0;

        foreach ($purchases as $index => $purchase) {
            $status = 'Draft';
            if ($purchase->status_nota == '1') {
                $status = 'Proses';
            } elseif ($purchase->status_nota == '2') {
                $status = 'Selesai';
            }

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($purchase->tgl_masuk)));
            // Ensure invoice number is displayed as text (not converted to number)
            $sheet->setCellValueExplicit('C' . $row, (string)($purchase->no_nota ?? ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row, $purchase->supplier_nama ?? '-');
            $sheet->setCellValue('E' . $row, $purchase->penerima_nama ?? '-');
            $sheet->setCellValue('F' . $row, $status);
            // Use actual numeric value for Total column
            $sheet->setCellValue('G' . $row, (float)($purchase->jml_gtotal ?? 0));
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
            
            $total += $purchase->jml_gtotal ?? 0;
            $row++;
        }

        // Add total
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('G' . $row, (float)$total);
        $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');

        // Auto size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create response
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Laporan_Pembelian_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Show detailed item purchase per invoice
     */
    public function detail_items($id = null)
    {
        if (!$id) {
            return redirect()->to('laporan/purchase')->with('error', 'ID Pembelian tidak valid');
        }

        $purchase = $this->transBeliModel->select('
                tbl_trans_beli.*,
                tbl_m_supplier.nama as supplier_nama,
                tbl_m_supplier.alamat as supplier_alamat,
                tbl_m_supplier.no_tlp as supplier_no_tlp,
                tbl_m_karyawan.nama as penerima_nama,
                tbl_m_gudang.nama as gudang_nama
            ')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli.id_supplier', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_beli.id_penerima', 'left')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_trans_beli.id_gudang', 'left')
            ->where('tbl_trans_beli.id', $id)
            ->first();

        if (!$purchase) {
            return redirect()->to('laporan/purchase')->with('error', 'Data pembelian tidak ditemukan');
        }

        $items = $this->transBeliDetModel->select('
                tbl_trans_beli_det.*,
                tbl_m_item.item as item_nama,
                tbl_m_item.kode as item_kode,
                tbl_m_satuan.SatuanBesar as satuan_nama
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_beli_det.id_item', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_trans_beli_det.id_satuan', 'left')
            ->where('id_pembelian', $id)
            ->findAll();

        $data = [
            'title' => 'Detail Item Pembelian - ' . $purchase->no_nota,
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'purchase' => $purchase,
            'items' => $items,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('laporan/purchase') . '">Laporan Pembelian</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('laporan/purchase/detail/' . $id) . '">Detail</a></li>
                <li class="breadcrumb-item active">Detail Item</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/purchase/detail_items', $data);
    }

    /**
     * Print purchase invoice
     */
    public function print_invoice($id = null)
    {
        if (!$id) {
            return redirect()->to('laporan/purchase')->with('error', 'ID Pembelian tidak valid');
        }

        $purchase = $this->transBeliModel->select('
                tbl_trans_beli.*,
                tbl_m_supplier.nama as supplier_nama,
                tbl_m_supplier.alamat as supplier_alamat,
                tbl_m_supplier.no_tlp as supplier_no_tlp,
                tbl_m_supplier.npwp as supplier_npwp,
                tbl_m_karyawan.nama as penerima_nama,
                tbl_m_gudang.nama as gudang_nama
            ')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli.id_supplier', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_beli.id_penerima', 'left')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_trans_beli.id_gudang', 'left')
            ->where('tbl_trans_beli.id', $id)
            ->first();

        if (!$purchase) {
            return redirect()->to('laporan/purchase')->with('error', 'Data pembelian tidak ditemukan');
        }

        $items = $this->transBeliDetModel->select('
                tbl_trans_beli_det.*,
                tbl_m_item.item as item_nama,
                tbl_m_item.kode as item_kode,
                tbl_m_satuan.SatuanBesar as satuan_nama
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_beli_det.id_item', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_trans_beli_det.id_satuan', 'left')
            ->where('id_pembelian', $id)
            ->findAll();

        $data = [
            'title' => 'Cetak Faktur Pembelian - ' . $purchase->no_nota,
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'purchase' => $purchase,
            'items' => $items
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/purchase/print_invoice', $data);
    }

    public function export_pdf()
    {
        require_once(APPPATH . '../vendor/tecnickcom/tcpdf/tcpdf.php');
        
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idSupplier = $this->request->getGet('id_supplier');

        $builder = $this->transBeliModel->select('
                tbl_trans_beli.*,
                tbl_m_supplier.nama as supplier_nama,
                tbl_m_karyawan.nama as penerima_nama
            ')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli.id_supplier', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_beli.id_penerima', 'left')
            ->where('tbl_trans_beli.deleted_at IS NULL');

        if ($startDate && $endDate) {
            $builder->where('tbl_trans_beli.tgl_masuk >=', $startDate . ' 00:00:00')
                   ->where('tbl_trans_beli.tgl_masuk <=', $endDate . ' 23:59:59');
        }

        if ($idSupplier) {
            $builder->where('tbl_trans_beli.id_supplier', $idSupplier);
        }

        $purchases = $builder->orderBy('tbl_trans_beli.tgl_masuk', 'DESC')->findAll();

        $totalPurchase = 0;
        foreach ($purchases as $purchase) {
            $totalPurchase += $purchase->jml_gtotal ?? 0;
        }

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
        $pdf->SetTitle('Laporan Pembelian');
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
        $pdf->Cell(0, 8, 'LAPORAN PEMBELIAN', 0, 1, 'C');
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
        $pdf->Cell(40, 6, 'No. Nota', 1, 0, 'C');
        $pdf->Cell(60, 6, 'Supplier', 1, 0, 'C');
        $pdf->Cell(50, 6, 'Penerima', 1, 0, 'C');
        $pdf->Cell(40, 6, 'Total', 1, 0, 'R');
        $pdf->Cell(57, 6, 'Status', 1, 1, 'C');

        // Table Data
        $pdf->SetFont('helvetica', '', 7);
        $no = 1;
        foreach ($purchases as $purchase) {
            $pdf->Cell(10, 5, $no++, 1, 0, 'C');
            $pdf->Cell(30, 5, date('d/m/Y', strtotime($purchase->tgl_masuk)), 1, 0, 'L');
            $pdf->Cell(40, 5, substr($purchase->no_nota, 0, 20), 1, 0, 'L');
            $pdf->Cell(60, 5, substr($purchase->supplier_nama ?? '-', 0, 35), 1, 0, 'L');
            $pdf->Cell(50, 5, substr($purchase->penerima_nama ?? '-', 0, 30), 1, 0, 'L');
            $pdf->Cell(40, 5, format_angka($purchase->jml_gtotal ?? 0), 1, 0, 'R');
            $pdf->Cell(57, 5, ($purchase->status_bayar == '1' ? 'Lunas' : 'Belum Lunas'), 1, 1, 'C');
        }

        // Total
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(190, 6, 'TOTAL', 1, 0, 'R');
        $pdf->Cell(40, 6, format_angka($totalPurchase), 1, 0, 'R');
        $pdf->Cell(57, 6, '', 1, 1);

        // Summary
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(0, 5, 'Total Transaksi: ' . count($purchases), 0, 1, 'L');
        $pdf->Cell(0, 5, 'Total Pembelian: ' . format_angka($totalPurchase), 0, 1, 'L');

        // Output
        $filename = 'Laporan_Pembelian_' . date('Y-m-d') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }
}
