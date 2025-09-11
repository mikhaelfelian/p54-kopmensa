<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-09-11
 * Github: github.com/mikhaelfelian
 * Description: Controller for profit and loss report
 * This file represents the ProfitLossReport controller.
 */

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Models\TransJualModel;
use App\Models\TransJualDetModel;
use App\Models\TransBeliModel;
use App\Models\TransBeliDetModel;
use App\Models\PettyModel;
use App\Models\GudangModel;

class ProfitLossReport extends BaseController
{
    protected $transJualModel;
    protected $transJualDetModel;
    protected $transBeliModel;
    protected $transBeliDetModel;
    protected $pettyModel;
    protected $gudangModel;

    public function __construct()
    {
        parent::__construct();
        $this->transJualModel = new TransJualModel();
        $this->transJualDetModel = new TransJualDetModel();
        $this->transBeliModel = new TransBeliModel();
        $this->transBeliDetModel = new TransBeliDetModel();
        $this->pettyModel = new PettyModel();
        $this->gudangModel = new GudangModel();
    }

    public function index()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idGudang = $this->request->getGet('id_gudang');

        // 1. REVENUE (Income from Sales)
        $revenueBuilder = $this->transJualModel->select('
                SUM(jml_total) as total_revenue,
                COUNT(id) as total_transactions
            ')
            ->where('status_nota', '1')
            ->where('deleted_at IS NULL');

        if ($startDate && $endDate) {
            $revenueBuilder->where('DATE(tgl_masuk) >=', $startDate)
                          ->where('DATE(tgl_masuk) <=', $endDate);
        }

        if ($idGudang) {
            $revenueBuilder->where('id_gudang', $idGudang);
        }

        $revenue = $revenueBuilder->first();

        // 2. COST OF GOODS SOLD (COGS)
        $cogsBuilder = $this->transJualDetModel->select('
                SUM(tbl_trans_jual_det.jml * tbl_m_item.harga_beli) as total_cogs
            ')
            ->join('tbl_trans_jual', 'tbl_trans_jual.id = tbl_trans_jual_det.id_penjualan')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_jual_det.id_item')
            ->where('tbl_trans_jual.status_nota', '1')
            ->where('tbl_trans_jual.deleted_at IS NULL');

        if ($startDate && $endDate) {
            $cogsBuilder->where('DATE(tbl_trans_jual.tgl_masuk) >=', $startDate)
                       ->where('DATE(tbl_trans_jual.tgl_masuk) <=', $endDate);
        }

        if ($idGudang) {
            $cogsBuilder->where('tbl_trans_jual.id_gudang', $idGudang);
        }

        $cogs = $cogsBuilder->first();

        // 3. OPERATING EXPENSES (from Petty Cash)
        $expensesBuilder = $this->pettyModel->select('
                SUM(CASE WHEN tipe = "keluar" THEN nominal ELSE 0 END) as total_expenses,
                SUM(CASE WHEN tipe = "masuk" THEN nominal ELSE 0 END) as other_income
            ')
            ->where('status', '1');

        if ($startDate && $endDate) {
            $expensesBuilder->where('DATE(tgl_masuk) >=', $startDate)
                           ->where('DATE(tgl_masuk) <=', $endDate);
        }

        if ($idGudang) {
            $expensesBuilder->where('id_gudang', $idGudang);
        }

        $expenses = $expensesBuilder->first();

        // 4. PURCHASES (for reference)
        $purchasesBuilder = $this->transBeliModel->select('
                SUM(jml_total) as total_purchases,
                COUNT(id) as total_purchase_transactions
            ')
            ->where('status_nota', '1');

        if ($startDate && $endDate) {
            $purchasesBuilder->where('DATE(tgl_masuk) >=', $startDate)
                            ->where('DATE(tgl_masuk) <=', $endDate);
        }

        $purchases = $purchasesBuilder->first();

        // 5. Calculate Profit/Loss
        $totalRevenue = (float) ($revenue->total_revenue ?? 0);
        $totalCogs = (float) ($cogs->total_cogs ?? 0);
        $totalExpenses = (float) ($expenses->total_expenses ?? 0);
        $otherIncome = (float) ($expenses->other_income ?? 0);

        $grossProfit = $totalRevenue - $totalCogs;
        $netProfit = $grossProfit - $totalExpenses + $otherIncome;
        $grossMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;
        $netMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        // 6. Monthly Trend
        $monthlyBuilder = $this->transJualModel->select('
                YEAR(tgl_masuk) as year,
                MONTH(tgl_masuk) as month,
                SUM(jml_total) as monthly_revenue,
                COUNT(id) as monthly_transactions
            ')
            ->where('status_nota', '1')
            ->where('deleted_at IS NULL');

        if ($startDate && $endDate) {
            $monthlyBuilder->where('DATE(tgl_masuk) >=', $startDate)
                          ->where('DATE(tgl_masuk) <=', $endDate);
        }

        if ($idGudang) {
            $monthlyBuilder->where('id_gudang', $idGudang);
        }

        $monthlyTrend = $monthlyBuilder->groupBy('YEAR(tgl_masuk), MONTH(tgl_masuk)')
                                     ->orderBy('YEAR(tgl_masuk), MONTH(tgl_masuk)', 'ASC')
                                     ->findAll();

        // Get filter options
        $gudangList = $this->gudangModel->where('status', '1')->findAll();

        $data = [
            'title' => 'Laporan Laba Rugi',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'idGudang' => $idGudang,
            'gudangList' => $gudangList,
            'profitLoss' => [
                'total_revenue' => $totalRevenue,
                'total_cogs' => $totalCogs,
                'gross_profit' => $grossProfit,
                'total_expenses' => $totalExpenses,
                'other_income' => $otherIncome,
                'net_profit' => $netProfit,
                'gross_margin' => $grossMargin,
                'net_margin' => $netMargin,
                'total_transactions' => $revenue->total_transactions ?? 0,
                'total_purchases' => $purchases->total_purchases ?? 0
            ],
            'monthlyTrend' => $monthlyTrend,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Laporan</li>
                <li class="breadcrumb-item active">Laba Rugi</li>
            '
        ];

        return $this->view($this->theme->getThemePath() . '/laporan/profit_loss/index', $data);
    }

    public function export()
    {
        // Same logic as index but for export
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $idGudang = $this->request->getGet('id_gudang');

        // Get all calculations (simplified for export)
        $revenue = $this->transJualModel->select('SUM(jml_total) as total_revenue')
                                       ->where('status_nota', '1')
                                       ->where('deleted_at IS NULL')
                                       ->where('DATE(tgl_masuk) >=', $startDate)
                                       ->where('DATE(tgl_masuk) <=', $endDate)
                                       ->first();

        $totalRevenue = (float) ($revenue->total_revenue ?? 0);

        // Create Excel export
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'LAPORAN LABA RUGI');
        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));

        $sheet->setCellValue('A4', 'PENDAPATAN');
        $sheet->setCellValue('A5', 'Penjualan');
        $sheet->setCellValue('B5', $totalRevenue);

        // Style
        $sheet->getStyle('A1:B1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A4:A5')->getFont()->setBold(true);

        foreach (range('A', 'B') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Export
        $filename = 'Laporan_Laba_Rugi_' . date('Y-m-d', strtotime($startDate)) . '_to_' . date('Y-m-d', strtotime($endDate)) . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
