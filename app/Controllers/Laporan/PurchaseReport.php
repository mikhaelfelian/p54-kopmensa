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
        $statusNota = $this->request->getGet('status_nota');

        // Build query
        $builder = $this->transBeliModel->select('
                tbl_trans_beli.*,
                tbl_m_supplier.nama as supplier_nama,
                tbl_m_karyawan.nama as penerima_nama
            ')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli.id_supplier', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_beli.id_penerima', 'left')
            ->where('tbl_trans_beli.status_hps', '0');

        // Apply filters
        if ($startDate && $endDate) {
            $builder->where('tbl_trans_beli.tgl_masuk >=', $startDate . ' 00:00:00')
                   ->where('tbl_trans_beli.tgl_masuk <=', $endDate . ' 23:59:59');
        }

        if ($idSupplier) {
            $builder->where('tbl_trans_beli.id_supplier', $idSupplier);
        }

        if ($statusNota !== null && $statusNota !== '') {
            $builder->where('tbl_trans_beli.status_nota', $statusNota);
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
        $supplierList = $this->supplierModel->where('status_hps', '0')->findAll();

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
            'statusNota' => $statusNota,
            'supplierList' => $supplierList,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Laporan</li>
                <li class="breadcrumb-item active">Laporan Pembelian</li>
            '
        ];

        return view($this->theme->getThemePath() . '/laporan/purchase/index', $data);
    }

    public function detail($id)
    {
        $purchase = $this->transBeliModel->select('
                tbl_trans_beli.*,
                tbl_m_supplier.nama as supplier_nama,
                tbl_m_supplier.alamat as supplier_alamat,
                tbl_m_supplier.telepon as supplier_telepon,
                tbl_m_karyawan.nama as penerima_nama
            ')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli.id_supplier', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_beli.id_penerima', 'left')
            ->where('tbl_trans_beli.id', $id)
            ->first();

        if (!$purchase) {
            return redirect()->to('laporan/purchase')->with('error', 'Data pembelian tidak ditemukan');
        }

        $items = $this->transBeliDetModel->select('
                tbl_trans_beli_det.*,
                tbl_m_item.item as item_nama,
                tbl_m_item.kode as item_kode,
                tbl_m_satuan.satuanBesar as satuan_nama
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

        return view($this->theme->getThemePath() . '/laporan/purchase/detail', $data);
    }

    public function export_excel()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idSupplier = $this->request->getGet('id_supplier');
        $statusNota = $this->request->getGet('status_nota');

        // Build query
        $builder = $this->transBeliModel->select('
                tbl_trans_beli.*,
                tbl_m_supplier.nama as supplier_nama,
                tbl_m_karyawan.nama as penerima_nama
            ')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli.id_supplier', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_beli.id_penerima', 'left')
            ->where('tbl_trans_beli.status_hps', '0');

        // Apply filters
        if ($startDate && $endDate) {
            $builder->where('tbl_trans_beli.tgl_masuk >=', $startDate . ' 00:00:00')
                   ->where('tbl_trans_beli.tgl_masuk <=', $endDate . ' 23:59:59');
        }

        if ($idSupplier) {
            $builder->where('tbl_trans_beli.id_supplier', $idSupplier);
        }

        if ($statusNota !== null && $statusNota !== '') {
            $builder->where('tbl_trans_beli.status_nota', $statusNota);
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
            $sheet->setCellValue('C' . $row, $purchase->no_nota);
            $sheet->setCellValue('D' . $row, $purchase->supplier_nama ?? '-');
            $sheet->setCellValue('E' . $row, $purchase->penerima_nama ?? '-');
            $sheet->setCellValue('F' . $row, $status);
            $sheet->setCellValue('G' . $row, number_format($purchase->jml_gtotal ?? 0, 0, ',', '.'));
            
            $total += $purchase->jml_gtotal ?? 0;
            $row++;
        }

        // Add total
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('G' . $row, number_format($total, 0, ',', '.'));

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
}
