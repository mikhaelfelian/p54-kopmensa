<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-09-11
 * Github: github.com/mikhaelfelian
 * Description: Controller for handling order reports based on invoice number
 * This file represents the OrderReport controller.
 */

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Models\TransJualModel;
use App\Models\TransJualDetModel;
use App\Models\GudangModel;
use App\Models\PelangganModel;
use App\Models\KaryawanModel;

class OrderReport extends BaseController
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
        $invoiceNumber = $this->request->getGet('invoice_number');
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idGudang = $this->request->getGet('id_gudang');
        $idPelanggan = $this->request->getGet('id_pelanggan');
        $status = $this->request->getGet('status');

        // Build query for orders
        $builder = $this->transJualModel->select('
                tbl_trans_jual.*,
                tbl_m_gudang.nama as gudang_nama,
                tbl_m_karyawan.nama as sales_nama,
                tbl_m_pelanggan.nama as pelanggan_nama,
                tbl_m_pelanggan.no_telp as pelanggan_hp
            ')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_trans_jual.id_gudang', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_jual.id_sales', 'left')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
            ->where('tbl_trans_jual.deleted_at IS NULL');

        // Apply invoice number filter (primary search)
        if ($invoiceNumber) {
            $builder->like('tbl_trans_jual.no_nota', $invoiceNumber);
        }

        // Apply date filter
        if ($startDate && $endDate) {
            $builder->where('DATE(tbl_trans_jual.tgl_masuk) >=', $startDate)
                   ->where('DATE(tbl_trans_jual.tgl_masuk) <=', $endDate);
        }

        // Apply other filters
        if ($idGudang) {
            $builder->where('tbl_trans_jual.id_gudang', $idGudang);
        }

        if ($idPelanggan) {
            $builder->where('tbl_trans_jual.id_pelanggan', $idPelanggan);
        }

        if ($status !== null && $status !== '') {
            $builder->where('tbl_trans_jual.status_nota', $status);
        }

        $orders = $builder->orderBy('tbl_trans_jual.tgl_masuk', 'DESC')->findAll();

        // Get order details for each order
        foreach ($orders as &$order) {
            $orderDetails = $this->transJualDetModel->select('
                    tbl_trans_jual_det.*,
                    tbl_m_item.kode,
                    tbl_m_item.item,
                    tbl_m_satuan.SatuanBesar as satuan
                ')
                ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_jual_det.id_item')
                ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
                ->where('tbl_trans_jual_det.id_penjualan', $order->id)
                ->findAll();

            $order->details = $orderDetails;
            $order->total_items = count($orderDetails);
            $order->total_qty = array_sum(array_column($orderDetails, 'jml'));
        }

        // Calculate summary
        $totalOrders = count($orders);
        $totalAmount = array_sum(array_column($orders, 'jml_total'));
        $completedOrders = count(array_filter($orders, function($order) { return $order->status_nota == '1'; }));
        $pendingOrders = count(array_filter($orders, function($order) { return $order->status_nota == '0'; }));

        // Get filter options
        $gudangList = $this->gudangModel->where('status', '1')->where('status_otl', '1')->findAll();
        $pelangganList = $this->pelangganModel->where('deleted_at IS NULL')->findAll();

        $data = [
            'title' => 'Laporan Pesanan (Berdasarkan No. Invoice)',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'orders' => $orders,
            'invoiceNumber' => $invoiceNumber,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'idGudang' => $idGudang,
            'idPelanggan' => $idPelanggan,
            'status' => $status,
            'gudangList' => $gudangList,
            'pelangganList' => $pelangganList,
            'summary' => [
                'total_orders' => $totalOrders,
                'total_amount' => $totalAmount,
                'completed_orders' => $completedOrders,
                'pending_orders' => $pendingOrders,
                'avg_order_value' => $totalOrders > 0 ? $totalAmount / $totalOrders : 0
            ],
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Laporan</li>
                <li class="breadcrumb-item active">Pesanan</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/order/index', $data);
    }

    public function detail($id)
    {
        $order = $this->transJualModel->select('
                tbl_trans_jual.*,
                tbl_m_gudang.nama as gudang_nama,
                tbl_m_karyawan.nama as sales_nama,
                tbl_m_pelanggan.nama as pelanggan_nama,
                tbl_m_pelanggan.no_telp as pelanggan_hp,
                tbl_m_pelanggan.alamat as pelanggan_alamat
            ')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_trans_jual.id_gudang', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_jual.id_sales', 'left')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
            ->where('tbl_trans_jual.id', $id)
            ->first();

        if (!$order) {
            return redirect()->to('laporan/order')->with('error', 'Pesanan tidak ditemukan');
        }

        // Get order details
        $orderDetails = $this->transJualDetModel->select('
                tbl_trans_jual_det.*,
                tbl_m_item.kode,
                tbl_m_item.item,
                tbl_m_item.barcode,
                tbl_m_satuan.SatuanBesar as satuan
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_jual_det.id_item')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_m_item.id_satuan', 'left')
            ->where('tbl_trans_jual_det.id_penjualan', $id)
            ->findAll();

        $data = [
            'title' => 'Detail Pesanan - ' . $order->no_nota,
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'order' => $order,
            'orderDetails' => $orderDetails,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Laporan</li>
                <li class="breadcrumb-item"><a href="' . base_url('laporan/order') . '">Pesanan</a></li>
                <li class="breadcrumb-item active">Detail</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/order/detail', $data);
    }

    public function export()
    {
        // Same logic as index but for export
        $invoiceNumber = $this->request->getGet('invoice_number');
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idGudang = $this->request->getGet('id_gudang');
        $idPelanggan = $this->request->getGet('id_pelanggan');
        $status = $this->request->getGet('status');

        $builder = $this->transJualModel->select('
                tbl_trans_jual.*,
                tbl_m_gudang.nama as gudang_nama,
                tbl_m_karyawan.nama as sales_nama,
                tbl_m_pelanggan.nama as pelanggan_nama
            ')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_trans_jual.id_gudang', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_trans_jual.id_sales', 'left')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
            ->where('tbl_trans_jual.deleted_at IS NULL');

        // Apply filters
        if ($invoiceNumber) {
            $builder->like('tbl_trans_jual.no_nota', $invoiceNumber);
        }

        if ($startDate && $endDate) {
            $builder->where('DATE(tbl_trans_jual.tgl_masuk) >=', $startDate)
                   ->where('DATE(tbl_trans_jual.tgl_masuk) <=', $endDate);
        }

        if ($idGudang) {
            $builder->where('tbl_trans_jual.id_gudang', $idGudang);
        }

        if ($idPelanggan) {
            $builder->where('tbl_trans_jual.id_pelanggan', $idPelanggan);
        }

        if ($status !== null && $status !== '') {
            $builder->where('tbl_trans_jual.status_nota', $status);
        }

        $orders = $builder->orderBy('tbl_trans_jual.tgl_masuk', 'DESC')->findAll();

        // Create Excel export
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Laporan Pesanan');
        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));
        if ($invoiceNumber) {
            $sheet->setCellValue('A3', 'No. Invoice: ' . $invoiceNumber);
        }

        // Column headers
        $headerRow = $invoiceNumber ? 5 : 4;
        $sheet->setCellValue('A' . $headerRow, 'No');
        $sheet->setCellValue('B' . $headerRow, 'Tanggal');
        $sheet->setCellValue('C' . $headerRow, 'No. Invoice');
        $sheet->setCellValue('D' . $headerRow, 'Gudang');
        $sheet->setCellValue('E' . $headerRow, 'Pelanggan');
        $sheet->setCellValue('F' . $headerRow, 'Sales');
        $sheet->setCellValue('G' . $headerRow, 'Status');
        $sheet->setCellValue('H' . $headerRow, 'Metode Bayar');
        $sheet->setCellValue('I' . $headerRow, 'Total');

        // Data rows
        $row = $headerRow + 1;
        $no = 1;
        $grandTotal = 0;

        foreach ($orders as $order) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, date('d/m/Y H:i', strtotime($order->tgl_masuk)));
            $sheet->setCellValue('C' . $row, $order->no_nota);
            $sheet->setCellValue('D' . $row, $order->gudang_nama);
            $sheet->setCellValue('E' . $row, $order->pelanggan_nama ?: 'Umum');
            $sheet->setCellValue('F' . $row, $order->sales_nama);
            $sheet->setCellValue('G' . $row, $order->status_nota == '1' ? 'Completed' : 'Pending');
            $sheet->setCellValue('H' . $row, ucfirst($order->metode_bayar));
            $sheet->setCellValue('I' . $row, (float) $order->jml_total);
            
            $grandTotal += (float) $order->jml_total;
            $row++;
        }

        // Grand total
        $sheet->setCellValue('H' . $row, 'GRAND TOTAL:');
        $sheet->setCellValue('I' . $row, $grandTotal);

        // Style the sheet
        $sheet->getStyle('A1:I1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A' . $headerRow . ':I' . $headerRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $headerRow . ':I' . $headerRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Export
        $filename = 'Laporan_Pesanan_' . ($invoiceNumber ? $invoiceNumber . '_' : '') . date('Y-m-d', strtotime($startDate)) . '_to_' . date('Y-m-d', strtotime($endDate)) . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
