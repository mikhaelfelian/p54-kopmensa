<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PengaturanModel;
use App\Models\TransJualModel;
use App\Models\TransJualPlatModel;
use App\Models\TransBeliModel;
use App\Models\TransBeliPOModel;
use TCPDF;

/**
 * Unified Print Controller
 * 
 * Handles all printing functionality with three standardized outputs:
 * 1. Receipt (Thermal/Dot Matrix) - for POS receipts
 * 2. Invoice (A4 PDF) - for formal invoices
 * 3. Report (A4 PDF) - for reports and documents
 */
class PrintController extends BaseController
{
    protected $pengaturanModel;
    protected $transJualModel;
    protected $transJualPlatModel;
    protected $transBeliModel;
    protected $transBeliPOModel;

    public function __construct()
    {
        $this->pengaturanModel = new PengaturanModel();
        $this->transJualModel = new TransJualModel();
        $this->transJualPlatModel = new TransJualPlatModel();
        $this->transBeliModel = new TransBeliModel();
        $this->transBeliPOModel = new TransBeliPOModel();
    }

    /**
     * Print Receipt (Thermal/Dot Matrix)
     * 
     * @param string $type Transaction type (jual, beli, po)
     * @param int $id Transaction ID
     * @return mixed
     */
    public function receipt($type, $id)
    {
        try {
            // Get company settings
            $settings = $this->pengaturanModel->getSettings();
            if (!$settings) {
                throw new \Exception('Company settings not found');
            }

            // Get transaction data based on type
            $transaction = $this->getTransactionData($type, $id);
            if (!$transaction) {
                throw new \Exception('Transaction not found');
            }

            // Generate receipt HTML
            $receiptHtml = $this->generateReceiptHtml($transaction, $settings, $type);

            // Return HTML for thermal printer
            return $this->response->setContentType('text/html')->setBody($receiptHtml);

        } catch (\Exception $e) {
            log_message('error', '[Print::receipt] ' . $e->getMessage());
            return $this->response->setContentType('text/html')
                                 ->setBody('<div style="text-align:center;padding:20px;">Error: ' . $e->getMessage() . '</div>');
        }
    }

    /**
     * Print Invoice (A4 PDF)
     * 
     * @param string $type Transaction type (jual, beli, po)
     * @param int $id Transaction ID
     * @return mixed
     */
    public function invoice($type, $id)
    {
        try {
            // Get company settings
            $settings = $this->pengaturanModel->getSettings();
            if (!$settings) {
                throw new \Exception('Company settings not found');
            }

            // Get transaction data based on type
            $transaction = $this->getTransactionData($type, $id);
            if (!$transaction) {
                throw new \Exception('Transaction not found');
            }

            // Generate PDF
            $pdf = $this->generateInvoicePdf($transaction, $settings, $type);

            // Output PDF
            $filename = strtoupper($type) . '_' . $transaction->no_nota . '_' . date('Ymd') . '.pdf';
            $pdf->Output($filename, 'D');

        } catch (\Exception $e) {
            log_message('error', '[Print::invoice] ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate invoice: ' . $e->getMessage());
        }
    }

    /**
     * Print Report (Daily Summary)
     * 
     * @param string $type Transaction type (jual, beli, po)
     * @param int $id Transaction ID (optional - if provided, shows single transaction detail)
     * @return mixed
     */
    public function report($type, $id = null)
    {
        try {
            // Get company settings
            $settings = $this->pengaturanModel->getSettings();
            if (!$settings) {
                throw new \Exception('Company settings not found');
            }

            // If ID is provided, get single transaction, otherwise get daily summary
            if ($id && $id !== 'daily') {
                $transaction = $this->getTransactionData($type, $id);
                if (!$transaction) {
                    throw new \Exception('Transaction not found');
                }
                
                // Generate single transaction report
                $html = $this->generateSingleTransactionReport($transaction, $settings, $type);
                return $this->response->setContentType('text/html')->setBody($html);
            } else {
                // Generate daily summary report
                $html = $this->generateDailySummaryReport($type, $settings);
                return $this->response->setContentType('text/html')->setBody($html);
            }

        } catch (\Exception $e) {
            log_message('error', '[Print::report] ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }

    /**
     * Get transaction data based on type and ID
     * 
     * @param string $type Transaction type
     * @param int $id Transaction ID
     * @return object|null
     */
    private function getTransactionData($type, $id)
    {
        switch ($type) {
            case 'jual':
                // Get sales transaction with customer info
                $transaction = $this->transJualModel->select('
                        tbl_trans_jual.*,
                        tbl_m_pelanggan.nama as customer_nama,
                        tbl_ion_users.first_name as user_name
                    ')
                    ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
                    ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_jual.id_user', 'left')
                    ->where('tbl_trans_jual.id', $id)
                    ->first();
                
                if ($transaction) {
                    // Get transaction details
                    $transJualDetModel = new \App\Models\TransJualDetModel();
                    $transaction->details = $transJualDetModel->getDetailsWithItem($id);
                    $transaction->payments = $this->transJualPlatModel->getPlatformsWithInfo($id);
                }
                return $transaction;
                
            case 'beli':
                // Get purchase transaction with supplier info using existing method
                $transaction = $this->transBeliModel->getWithSupplier($id);
                
                if ($transaction) {
                    // Get transaction details
                    $transBeliDetModel = new \App\Models\TransBeliDetModel();
                    $transaction->details = $transBeliDetModel->getWithItem($id);
                    $transaction->payments = [];
                }
                return $transaction;
                
            case 'po':
                // Use the existing getWithRelations method for PO
                $transaction = $this->transBeliPOModel->getWithRelations(['tbl_trans_beli_po.id' => $id]);
                
                if ($transaction) {
                    // Get PO details
                    $transBeliPODetModel = new \App\Models\TransBeliPODetModel();
                    $transaction->details = $transBeliPODetModel->select('
                            tbl_trans_beli_po_det.*,
                            tbl_m_item.kode,
                            tbl_m_item.item
                        ')
                        ->join('tbl_m_item', 'tbl_m_item.id = tbl_trans_beli_po_det.id_item', 'left')
                        ->where('id_pembelian', $id)
                        ->findAll();
                    $transaction->payments = [];
                }
                return $transaction;
                
            default:
                return null;
        }
    }

    /**
     * Generate receipt HTML for thermal printer
     * 
     * @param object $transaction Transaction data
     * @param object $settings Company settings
     * @param string $type Transaction type
     * @return string
     */
    private function generateReceiptHtml($transaction, $settings, $type)
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: "Courier New", monospace; 
            font-size: 11px; 
            width: 80mm; 
            margin: 0 auto; 
            padding: 5px;
            line-height: 1.4;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .large { font-size: 14px; }
        .medium { font-size: 12px; }
        .small { font-size: 10px; }
        .line { border-top: 1px dashed #000; margin: 8px 0; }
        .line-solid { border-top: 2px solid #000; margin: 8px 0; }
        .spacer { margin: 4px 0; }
        
        table { width: 100%; border-collapse: collapse; }
        .item-row td { padding: 3px 0; vertical-align: top; }
        .item-name { font-weight: 500; }
        .item-detail { font-size: 10px; color: #333; }
        
        .summary-table { margin-top: 5px; }
        .summary-table td { padding: 2px 0; }
        .summary-table .label { text-align: left; }
        .summary-table .value { text-align: right; font-weight: 500; }
        
        .total-row { 
            font-weight: bold; 
            font-size: 13px;
            border-top: 1px solid #000;
            padding-top: 5px !important;
        }
        
        .info-row { 
            display: flex; 
            justify-content: space-between; 
            margin: 2px 0;
        }
        .info-label { font-weight: 500; }
        
        .footer { 
            margin-top: 10px; 
            padding-top: 5px;
            border-top: 1px dashed #000;
        }
    </style>
</head>
<body>';

        // Company header
        $html .= '<div class="center bold large">' . strtoupper($settings->judul ?? 'COMPANY NAME') . '</div>';
        $html .= '<div class="center small">' . ($settings->alamat ?? '') . '</div>';
        if (isset($settings->no_tlp) && $settings->no_tlp) {
            $html .= '<div class="center small">Telp: ' . $settings->no_tlp . '</div>';
        }
        
        $html .= '<div class="line-solid"></div>';

        // Transaction info
        $html .= '<div class="info-row">';
        $html .= '<span class="info-label">No:</span>';
        $html .= '<span>' . $transaction->no_nota . '</span>';
        $html .= '</div>';
        
        $html .= '<div class="info-row">';
        $html .= '<span class="info-label">Date:</span>';
        $html .= '<span>' . date('d/m/Y H:i', strtotime($transaction->tgl_masuk)) . '</span>';
        $html .= '</div>';
        
        // Cashier/User
        if (isset($transaction->user_name) && $transaction->user_name) {
            $html .= '<div class="info-row">';
            $html .= '<span class="info-label">Cashier:</span>';
            $html .= '<span>' . $transaction->user_name . '</span>';
            $html .= '</div>';
        }
        
        // Customer/Supplier
        if ($type === 'jual' && isset($transaction->customer_nama)) {
            $html .= '<div class="info-row">';
            $html .= '<span class="info-label">Customer:</span>';
            $html .= '<span>' . $transaction->customer_nama . '</span>';
            $html .= '</div>';
        } elseif ($type === 'beli' && isset($transaction->supplier_name)) {
            $html .= '<div class="info-row">';
            $html .= '<span class="info-label">Supplier:</span>';
            $html .= '<span>' . $transaction->supplier_name . '</span>';
            $html .= '</div>';
        } elseif ($type === 'po' && isset($transaction->supplier)) {
            $html .= '<div class="info-row">';
            $html .= '<span class="info-label">Supplier:</span>';
            $html .= '<span>' . $transaction->supplier . '</span>';
            $html .= '</div>';
        }

        // Payment method
        $paymentSummaryText = $this->formatPaymentSummaryText($transaction->payments ?? [], $transaction->metode_bayar ?? null, false);
        if ($paymentSummaryText) {
            $html .= '<div class="info-row">';
            $html .= '<span class="info-label">Payment:</span>';
            $html .= '<span>' . $paymentSummaryText . '</span>';
            $html .= '</div>';
        }
        if (!empty($transaction->payments)) {
            $html .= '<div class="info-row" style="flex-direction: column; align-items: flex-start; padding-top:4px;">';
            foreach ($transaction->payments as $payment) {
                $label = $payment->nama_platform ?? $payment->platform ?? 'Payment';
                $amount = number_format((float)($payment->nominal ?? 0), 0, ',', '.');
                $html .= '<span style="width:100%; display:flex; justify-content:space-between;">';
                $html .= '<span>' . $label . '</span>';
                $html .= '<span>Rp ' . $amount . '</span>';
                $html .= '</span>';
            }
            $html .= '</div>';
        }

        $html .= '<div class="line"></div>';

        // Items
        $html .= '<table>';
        
        if (isset($transaction->details) && is_array($transaction->details)) {
            foreach ($transaction->details as $index => $detail) {
                $itemName = $detail->produk ?? $detail->item ?? $detail->nama_item ?? 'Unknown Item';
                
                $html .= '<tr class="item-row">';
                $html .= '<td colspan="3" class="item-name">' . $itemName . '</td>';
                $html .= '</tr>';
                
                $html .= '<tr class="item-row">';
                $html .= '<td style="width: 40%;" class="item-detail">' . number_format($detail->jml, 0) . ' x ' . number_format($detail->harga, 0) . '</td>';
                $html .= '<td style="width: 30%;"></td>';
                $html .= '<td style="width: 30%; text-align: right; font-weight: 500;">' . number_format($detail->subtotal, 0) . '</td>';
                $html .= '</tr>';
                
                // Add spacing between items
                if ($index < count($transaction->details) - 1) {
                    $html .= '<tr><td colspan="3" style="height: 5px;"></td></tr>';
                }
            }
        }
        
        $html .= '</table>';

        $html .= '<div class="line"></div>';

        // Summary
        $html .= '<table class="summary-table">';
        
        $html .= '<tr><td class="label">Subtotal</td><td class="value">' . number_format($transaction->jml_subtotal ?? 0, 0) . '</td></tr>';
        
        if (isset($transaction->jml_diskon) && $transaction->jml_diskon > 0) {
            $html .= '<tr><td class="label">Discount</td><td class="value">(' . number_format($transaction->jml_diskon, 0) . ')</td></tr>';
        }
        
        if (isset($transaction->jml_ppn) && $transaction->jml_ppn > 0) {
            $html .= '<tr><td class="label">Tax (PPN ' . ($transaction->ppn ?? 0) . '%)</td><td class="value">' . number_format($transaction->jml_ppn, 0) . '</td></tr>';
        }
        
        $html .= '<tr class="total-row"><td class="label">TOTAL</td><td class="value">Rp ' . number_format($transaction->jml_gtotal ?? 0, 0) . '</td></tr>';
        
        if (isset($transaction->jml_bayar) && $transaction->jml_bayar > 0) {
            $html .= '<tr><td colspan="2" style="height: 5px;"></td></tr>';
            $html .= '<tr><td class="label">Payment</td><td class="value">' . number_format($transaction->jml_bayar, 0) . '</td></tr>';
            $html .= '<tr><td class="label">Change</td><td class="value">' . number_format($transaction->jml_kembali ?? 0, 0) . '</td></tr>';
        }
        
        $html .= '</table>';

        // Notes
        if ($type === 'jual' && isset($transaction->keterangan) && $transaction->keterangan) {
            $html .= '<div class="spacer"></div>';
            $html .= '<div class="small">Note: ' . $transaction->keterangan . '</div>';
        } elseif ($type === 'beli' && isset($transaction->catatan_terima) && $transaction->catatan_terima) {
            $html .= '<div class="spacer"></div>';
            $html .= '<div class="small">Note: ' . $transaction->catatan_terima . '</div>';
        } elseif ($type === 'po' && isset($transaction->keterangan) && $transaction->keterangan) {
            $html .= '<div class="spacer"></div>';
            $html .= '<div class="small">Note: ' . $transaction->keterangan . '</div>';
        }

        // Footer
        $html .= '<div class="footer">';
        $html .= '<div class="center bold">Thank you for your business!</div>';
        $html .= '<div class="center small" style="margin-top: 5px;">' . date('d-m-Y H:i:s') . '</div>';
        
        if (isset($settings->footer_struk) && $settings->footer_struk) {
            $html .= '<div class="center small" style="margin-top: 3px;">' . $settings->footer_struk . '</div>';
        }
        $html .= '</div>';

        $html .= '</body></html>';

        return $html;
    }

    /**
     * Generate invoice PDF
     * 
     * @param object $transaction Transaction data
     * @param object $settings Company settings
     * @param string $type Transaction type
     * @return TCPDF
     */
    private function generateInvoicePdf($transaction, $settings, $type)
    {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator($settings->judul_app ?? 'POS System');
        $pdf->SetAuthor($settings->judul ?? 'Company');
        $pdf->SetTitle('Invoice - ' . $transaction->no_nota);
        
        // Set margins - dot matrix style (narrower margins)
        $pdf->SetMargins(15, 10, 15);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Add page
        $pdf->AddPage();
        
        // Company header - dot matrix style
        $pdf->SetFont('courier', 'B', 14);
        $pdf->Cell(0, 6, strtoupper($settings->judul ?? 'COMPANY NAME'), 0, 1, 'L');
        
        $pdf->SetFont('courier', '', 9);
        $pdf->Cell(0, 4, $settings->alamat ?? '', 0, 1, 'L');
        
        $companyInfo = '';
        if (isset($settings->kota) && $settings->kota) {
            $companyInfo .= $settings->kota;
        }
        if (isset($settings->no_tlp) && $settings->no_tlp) {
            $companyInfo .= ($companyInfo ? ' - ' : '') . 'Telp: ' . $settings->no_tlp;
        }
        if ($companyInfo) {
            $pdf->Cell(0, 4, $companyInfo, 0, 1, 'L');
        }
        
        // Separator line
        $pdf->SetLineWidth(0.5);
        $pdf->Line(15, $pdf->GetY() + 2, 195, $pdf->GetY() + 2);
        $pdf->Ln(4);
        
        // Invoice title
        $pdf->SetFont('courier', 'B', 12);
        $pdf->Cell(0, 6, strtoupper($type) . ' INVOICE', 0, 1, 'C');
        $pdf->Ln(3);
        
        // Transaction info - two columns
        $pdf->SetFont('courier', '', 9);
        $leftX = 15;
        $rightX = 110;
        $currentY = $pdf->GetY();
        
        // Left column
        $pdf->SetXY($leftX, $currentY);
        $pdf->Cell(35, 5, 'Invoice No', 0, 0);
        $pdf->Cell(5, 5, ':', 0, 0);
        $pdf->Cell(0, 5, $transaction->no_nota, 0, 1);
        
        $pdf->SetX($leftX);
        $pdf->Cell(35, 5, 'Date', 0, 0);
        $pdf->Cell(5, 5, ':', 0, 0);
        $pdf->Cell(0, 5, date('d/m/Y H:i', strtotime($transaction->tgl_masuk)), 0, 1);
        
        // Customer/Supplier
        if ($type === 'jual' && isset($transaction->customer_nama)) {
            $pdf->SetX($leftX);
            $pdf->Cell(35, 5, 'Customer', 0, 0);
            $pdf->Cell(5, 5, ':', 0, 0);
            $pdf->Cell(0, 5, $transaction->customer_nama, 0, 1);
        } elseif ($type === 'beli' && isset($transaction->supplier_name)) {
            $pdf->SetX($leftX);
            $pdf->Cell(35, 5, 'Supplier', 0, 0);
            $pdf->Cell(5, 5, ':', 0, 0);
            $pdf->Cell(0, 5, $transaction->supplier_name, 0, 1);
        } elseif ($type === 'po' && isset($transaction->supplier)) {
            $pdf->SetX($leftX);
            $pdf->Cell(35, 5, 'Supplier', 0, 0);
            $pdf->Cell(5, 5, ':', 0, 0);
            $pdf->Cell(0, 5, $transaction->supplier, 0, 1);
        }
        
        // Right column - Payment info
        $invoicePaymentSummary = $this->formatPaymentSummaryText($transaction->payments ?? [], $transaction->metode_bayar ?? null);
        if ($invoicePaymentSummary) {
            $pdf->SetXY($rightX, $currentY);
            $pdf->Cell(30, 5, 'Payment', 0, 0);
            $pdf->Cell(5, 5, ':', 0, 0);
            $pdf->Cell(0, 5, $invoicePaymentSummary, 0, 1);
        }
        
        // Cashier
        if (isset($transaction->user_name) && $transaction->user_name) {
            $pdf->SetX($rightX);
            $pdf->Cell(30, 5, 'Cashier', 0, 0);
            $pdf->Cell(5, 5, ':', 0, 0);
            $pdf->Cell(0, 5, $transaction->user_name, 0, 1);
        }
        
        $pdf->Ln(3);
        
        // Separator line
        $pdf->SetLineWidth(0.3);
        $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
        $pdf->Ln(3);
        
        // Items table header - dot matrix style
        $pdf->SetFont('courier', 'B', 9);
        $pdf->Cell(10, 5, 'No', 0, 0, 'L');
        $pdf->Cell(85, 5, 'Description', 0, 0, 'L');
        $pdf->Cell(20, 5, 'Qty', 0, 0, 'R');
        $pdf->Cell(30, 5, 'Price', 0, 0, 'R');
        $pdf->Cell(35, 5, 'Amount', 0, 1, 'R');
        
        // Header line
        $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
        $pdf->Ln(2);
        
        // Items
        $pdf->SetFont('courier', '', 8);
        $itemCount = 1;
        
        if (isset($transaction->details) && is_array($transaction->details)) {
            foreach ($transaction->details as $item) {
                $itemName = $item->produk ?? $item->item ?? $item->nama_item ?? 'Unknown';
                
                $pdf->Cell(10, 5, $itemCount, 0, 0, 'L');
                $pdf->Cell(85, 5, substr($itemName, 0, 45), 0, 0, 'L');
                $pdf->Cell(20, 5, number_format($item->jml ?? 0, 0), 0, 0, 'R');
                $pdf->Cell(30, 5, number_format($item->harga ?? 0, 0), 0, 0, 'R');
                $pdf->Cell(35, 5, number_format($item->subtotal ?? 0, 0), 0, 1, 'R');
                
                $itemCount++;
            }
        }
        
        $pdf->Ln(2);
        
        // Separator line before totals
        $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
        $pdf->Ln(3);
        
        // Summary section
        $pdf->SetFont('courier', '', 9);
        $labelX = 125;
        $valueX = 160;
        
        $pdf->SetX($labelX);
        $pdf->Cell(35, 5, 'Subtotal', 0, 0, 'L');
        $pdf->Cell(35, 5, number_format($transaction->jml_subtotal ?? 0, 0), 0, 1, 'R');
        
        if (isset($transaction->jml_diskon) && $transaction->jml_diskon > 0) {
            $pdf->SetX($labelX);
            $pdf->Cell(35, 5, 'Discount', 0, 0, 'L');
            $pdf->Cell(35, 5, '(' . number_format($transaction->jml_diskon, 0) . ')', 0, 1, 'R');
        }
        
        if (isset($transaction->jml_ppn) && $transaction->jml_ppn > 0) {
            $pdf->SetX($labelX);
            $pdf->Cell(35, 5, 'Tax (PPN ' . ($transaction->ppn ?? 0) . '%)', 0, 0, 'L');
            $pdf->Cell(35, 5, number_format($transaction->jml_ppn, 0), 0, 1, 'R');
        }
        
        // Total line
        $pdf->SetLineWidth(0.5);
        $pdf->Line($labelX, $pdf->GetY() + 1, 195, $pdf->GetY() + 1);
        $pdf->Ln(3);
        
        $pdf->SetFont('courier', 'B', 10);
        $pdf->SetX($labelX);
        $pdf->Cell(35, 6, 'TOTAL', 0, 0, 'L');
        $pdf->Cell(35, 6, 'Rp ' . number_format($transaction->jml_gtotal ?? 0, 0), 0, 1, 'R');
        
        if (!empty($transaction->payments)) {
            $pdf->Ln(2);
            $pdf->SetFont('courier', '', 9);
            foreach ($transaction->payments as $payment) {
                $label = $payment->nama_platform ?? $payment->platform ?? 'Payment';
                $amount = number_format((float)($payment->nominal ?? 0), 0, ',', '.');
                $pdf->SetX($labelX);
                $pdf->Cell(35, 5, $label, 0, 0, 'L');
                $pdf->Cell(35, 5, 'Rp ' . $amount, 0, 1, 'R');
            }
        }
        
        // Payment details if available
        if (isset($transaction->jml_bayar) && $transaction->jml_bayar > 0) {
            $pdf->SetLineWidth(0.3);
            $pdf->Line($labelX, $pdf->GetY() + 1, 195, $pdf->GetY() + 1);
            $pdf->Ln(3);
            
            $pdf->SetFont('courier', '', 9);
            $pdf->SetX($labelX);
            $pdf->Cell(35, 5, 'Payment', 0, 0, 'L');
            $pdf->Cell(35, 5, number_format($transaction->jml_bayar, 0), 0, 1, 'R');
            
            $pdf->SetX($labelX);
            $pdf->Cell(35, 5, 'Change', 0, 0, 'L');
            $pdf->Cell(35, 5, number_format($transaction->jml_kembali ?? 0, 0), 0, 1, 'R');
        }
        
        // Notes
        if ($type === 'jual' && isset($transaction->keterangan) && $transaction->keterangan) {
            $pdf->Ln(5);
            $pdf->SetFont('courier', '', 8);
            $pdf->Cell(0, 4, 'Note: ' . $transaction->keterangan, 0, 1);
        } elseif ($type === 'beli' && isset($transaction->catatan_terima) && $transaction->catatan_terima) {
            $pdf->Ln(5);
            $pdf->SetFont('courier', '', 8);
            $pdf->Cell(0, 4, 'Note: ' . $transaction->catatan_terima, 0, 1);
        } elseif ($type === 'po' && isset($transaction->keterangan) && $transaction->keterangan) {
            $pdf->Ln(5);
            $pdf->SetFont('courier', '', 8);
            $pdf->Cell(0, 4, 'Note: ' . $transaction->keterangan, 0, 1);
        }
        
        // Footer
        $pdf->SetY(-30);
        $pdf->SetFont('courier', '', 8);
        $pdf->Cell(0, 4, 'Printed: ' . date('d-m-Y H:i:s'), 0, 1, 'L');
        
        if (isset($settings->footer_struk) && $settings->footer_struk) {
            $pdf->Cell(0, 4, $settings->footer_struk, 0, 1, 'C');
        }
        
        return $pdf;
    }

    /**
     * Generate daily summary report (for dot matrix or POS 58mm)
     * 
     * @param string $type Transaction type
     * @param object $settings Company settings
     * @return string HTML content
     */
    private function generateDailySummaryReport($type, $settings)
    {
        $today = date('Y-m-d');
        
        // Get all transactions for today
        $transactions = [];
        $totalAmount = 0;
        $totalQty = 0;
        $totalTransactions = 0;
        
        if ($type === 'jual') {
            $transactions = $this->transJualModel
                ->select('tbl_trans_jual.*, tbl_ion_users.first_name as user_name, tbl_m_pelanggan.nama as customer_nama')
                ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_jual.id_user', 'left')
                ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
                ->where('DATE(tbl_trans_jual.tgl_masuk)', $today)
                ->where('tbl_trans_jual.status', '1')
                ->orderBy('tbl_trans_jual.tgl_masuk', 'ASC')
                ->findAll();
            $this->attachPaymentsToTransactions($transactions);
        } elseif ($type === 'beli') {
            $transactions = $this->transBeliModel
                ->select('tbl_trans_beli.*, tbl_ion_users.first_name as user_name, tbl_m_supplier.nama as supplier_name')
                ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_beli.id_user', 'left')
                ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli.id_supplier', 'left')
                ->where('DATE(tbl_trans_beli.tgl_masuk)', $today)
                ->orderBy('tbl_trans_beli.tgl_masuk', 'ASC')
                ->findAll();
        }
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Daily Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: "Courier New", monospace; 
            font-size: 10px; 
            width: 80mm; 
            margin: 0 auto; 
            padding: 5px;
            line-height: 1.3;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .large { font-size: 13px; }
        .medium { font-size: 11px; }
        .small { font-size: 9px; }
        .line { border-top: 1px dashed #000; margin: 5px 0; }
        .line-solid { border-top: 2px solid #000; margin: 5px 0; }
        .line-double { border-top: 3px double #000; margin: 5px 0; }
        
        table { width: 100%; border-collapse: collapse; margin: 3px 0; }
        table td { padding: 2px 0; vertical-align: top; }
        
        .summary-box { 
            border: 1px solid #000; 
            padding: 5px; 
            margin: 5px 0;
            background: #f5f5f5;
        }
        
        .info-row { 
            display: flex; 
            justify-content: space-between; 
            margin: 1px 0;
        }
        
        .transaction-item {
            margin: 3px 0;
            padding: 3px 0;
            border-bottom: 1px dotted #ccc;
        }
        
        .payment-method {
            display: inline-block;
            padding: 1px 3px;
            border: 1px solid #000;
            font-size: 8px;
        }
        
        @media print {
            body { width: 58mm; }
        }
    </style>
</head>
<body>';

        // Header
        $html .= '<div class="center bold large">' . strtoupper($settings->judul ?? 'COMPANY NAME') . '</div>';
        $html .= '<div class="center small">' . ($settings->alamat ?? '') . '</div>';
        if (isset($settings->no_tlp) && $settings->no_tlp) {
            $html .= '<div class="center small">Telp: ' . $settings->no_tlp . '</div>';
        }
        
        $html .= '<div class="line-solid"></div>';
        
        // Report title
        $html .= '<div class="center bold medium">DAILY ' . strtoupper($type) . ' REPORT</div>';
        $html .= '<div class="center small">Date: ' . date('d F Y', strtotime($today)) . '</div>';
        
        $html .= '<div class="line"></div>';
        
        // Report info
        $html .= '<div class="info-row">';
        $html .= '<span class="small">Printed:</span>';
        $html .= '<span class="small bold">' . date('d/m/Y H:i:s') . '</span>';
        $html .= '</div>';
        
        $html .= '<div class="info-row">';
        $html .= '<span class="small">User:</span>';
        $html .= '<span class="small bold">' . (session()->get('username') ?? 'SYSTEM') . '</span>';
        $html .= '</div>';
        
        $html .= '<div class="line-double"></div>';
        
        // Transactions list
        if (!empty($transactions)) {
            $html .= '<div class="bold medium">TRANSACTION LIST</div>';
            $html .= '<div class="line"></div>';
            
            $paymentSummary = [];
            $userSummary = [];
            
            foreach ($transactions as $index => $trans) {
                $totalAmount += $trans->jml_gtotal ?? 0;
                $totalTransactions++;
                
                // Count by payment method
                    if (!empty($trans->payments)) {
                        foreach ($trans->payments as $paymentItem) {
                            $label = $paymentItem->nama_platform ?? $paymentItem->platform ?? 'Pembayaran';
                            if (!isset($paymentSummary[$label])) {
                                $paymentSummary[$label] = ['count' => 0, 'amount' => 0];
                            }
                            $paymentSummary[$label]['count']++;
                            $paymentSummary[$label]['amount'] += $paymentItem->nominal ?? 0;
                        }
                    } else {
                        $payment = strtoupper($trans->metode_bayar ?? 'CASH');
                        if (!isset($paymentSummary[$payment])) {
                            $paymentSummary[$payment] = ['count' => 0, 'amount' => 0];
                        }
                        $paymentSummary[$payment]['count']++;
                        $paymentSummary[$payment]['amount'] += $trans->jml_gtotal ?? 0;
                    }
                
                // Count by user
                $user = $trans->user_name ?? 'Unknown';
                if (!isset($userSummary[$user])) {
                    $userSummary[$user] = ['count' => 0, 'amount' => 0];
                }
                $userSummary[$user]['count']++;
                $userSummary[$user]['amount'] += $trans->jml_gtotal ?? 0;
                
                $html .= '<div class="transaction-item">';
                
                // Transaction header
                $html .= '<div class="info-row">';
                $html .= '<span class="bold">' . ($index + 1) . '. ' . $trans->no_nota . '</span>';
                $html .= '<span class="bold">' . number_format($trans->jml_gtotal ?? 0, 0) . '</span>';
                $html .= '</div>';
                
                // Transaction details
                $html .= '<div class="small">';
                $html .= '<table>';
                $html .= '<tr><td style="width: 30%;">Time</td><td>: ' . date('H:i:s', strtotime($trans->tgl_masuk)) . '</td></tr>';
                $html .= '<tr><td>Cashier</td><td>: ' . ($trans->user_name ?? '-') . '</td></tr>';
                
                if ($type === 'jual' && isset($trans->customer_nama)) {
                    $html .= '<tr><td>Customer</td><td>: ' . $trans->customer_nama . '</td></tr>';
                } elseif ($type === 'beli' && isset($trans->supplier_name)) {
                    $html .= '<tr><td>Supplier</td><td>: ' . $trans->supplier_name . '</td></tr>';
                }
                
                $paymentDetail = $this->formatPaymentSummaryText($trans->payments ?? [], $trans->metode_bayar ?? null);
                $html .= '<tr><td>Payment</td><td>: <span class="payment-method">' . ($paymentDetail ?: '-') . '</span></td></tr>';
                $html .= '</table>';
                $html .= '</div>';
                
                $html .= '</div>';
            }
            
            $html .= '<div class="line-double"></div>';
            
            // Summary by payment method
            $html .= '<div class="bold medium">PAYMENT SUMMARY</div>';
            $html .= '<div class="line"></div>';
            
            foreach ($paymentSummary as $method => $data) {
                $html .= '<div class="info-row">';
                $html .= '<span>' . $method . ' (' . $data['count'] . 'x)</span>';
                $html .= '<span class="bold">' . number_format($data['amount'], 0) . '</span>';
                $html .= '</div>';
            }
            
            $html .= '<div class="line"></div>';
            
            // Summary by user
            $html .= '<div class="bold medium">CASHIER SUMMARY</div>';
            $html .= '<div class="line"></div>';
            
            foreach ($userSummary as $user => $data) {
                $html .= '<div class="info-row">';
                $html .= '<span>' . $user . ' (' . $data['count'] . 'x)</span>';
                $html .= '<span class="bold">' . number_format($data['amount'], 0) . '</span>';
                $html .= '</div>';
            }
            
            $html .= '<div class="line-double"></div>';
            
            // Grand total
            $html .= '<div class="summary-box">';
            $html .= '<div class="bold medium center">DAILY SUMMARY</div>';
            $html .= '<div class="line"></div>';
            
            $html .= '<div class="info-row">';
            $html .= '<span>Total Transactions</span>';
            $html .= '<span class="bold">' . $totalTransactions . '</span>';
            $html .= '</div>';
            
            $html .= '<div class="line"></div>';
            
            $html .= '<div class="info-row">';
            $html .= '<span class="bold">GRAND TOTAL</span>';
            $html .= '<span class="bold large">Rp ' . number_format($totalAmount, 0) . '</span>';
            $html .= '</div>';
            
            $html .= '</div>';
            
        } else {
            $html .= '<div class="center bold">NO TRANSACTIONS TODAY</div>';
        }
        
        $html .= '<div class="line-double"></div>';
        
        // Footer
        $html .= '<div class="center small">*** END OF REPORT ***</div>';
        $html .= '<div class="center small" style="margin-top: 3px;">' . ($settings->footer_struk ?? 'Thank you') . '</div>';
        
        $html .= '</body></html>';
        
        return $html;
    }

    /**
     * Generate single transaction report
     * 
     * @param object $transaction Transaction data
     * @param object $settings Company settings
     * @param string $type Transaction type
     * @return string HTML content
     */
    private function generateSingleTransactionReport($transaction, $settings, $type)
    {
        // This is for detailed single transaction report (optional)
        // For now, redirect to receipt
        return $this->generateReceiptHtml($transaction, $settings, $type);
    }

    /**
     * Generate report PDF (Legacy - kept for compatibility)
     * 
     * @param object $transaction Transaction data
     * @param object $settings Company settings
     * @param string $type Transaction type
     * @return TCPDF
     */
    private function generateReportPdf($transaction, $settings, $type)
    {
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false); // Landscape for more space
        
        // Set document information
        $pdf->SetCreator($settings->judul_app ?? 'POS System');
        $pdf->SetAuthor($settings->judul ?? 'Company');
        $pdf->SetTitle('Internal Report - ' . $transaction->no_nota);
        
        // Set margins
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Add page
        $pdf->AddPage();
        
        // Header section with border
        $pdf->SetLineWidth(0.5);
        $pdf->Rect(10, 10, 277, 25); // Border around header
        
        // Company info - left side
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetXY(12, 12);
        $pdf->Cell(0, 5, strtoupper($settings->judul ?? 'COMPANY NAME'), 0, 1, 'L');
        
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetX(12);
        $pdf->Cell(0, 4, $settings->alamat ?? '', 0, 1, 'L');
        
        $companyInfo = '';
        if (isset($settings->kota) && $settings->kota) {
            $companyInfo .= $settings->kota;
        }
        if (isset($settings->no_tlp) && $settings->no_tlp) {
            $companyInfo .= ($companyInfo ? ' | Tel: ' : 'Tel: ') . $settings->no_tlp;
        }
        if ($companyInfo) {
            $pdf->SetX(12);
            $pdf->Cell(0, 4, $companyInfo, 0, 1, 'L');
        }
        
        // Report title - right side
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetXY(180, 15);
        $pdf->Cell(105, 6, 'INTERNAL TRANSACTION REPORT', 0, 1, 'R');
        
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetX(180);
        $pdf->Cell(105, 5, 'Document Type: ' . strtoupper($type), 0, 1, 'R');
        
        $pdf->Ln(3);
        
        // Transaction Information Box
        $pdf->SetLineWidth(0.3);
        $pdf->Rect(10, 40, 277, 20);
        
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY(12, 42);
        $pdf->Cell(0, 5, 'TRANSACTION INFORMATION', 0, 1, 'L');
        
        $pdf->SetFont('helvetica', '', 8);
        $infoY = 48;
        
        // Left column
        $pdf->SetXY(12, $infoY);
        $pdf->Cell(35, 4, 'Transaction No', 0, 0);
        $pdf->Cell(3, 4, ':', 0, 0);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(60, 4, $transaction->no_nota, 0, 0);
        
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetXY(110, $infoY);
        $pdf->Cell(30, 4, 'Payment Method', 0, 0);
        $pdf->Cell(3, 4, ':', 0, 0);
        $pdf->SetFont('helvetica', 'B', 8);
        $reportPaymentSummary = $this->formatPaymentSummaryText($transaction->payments ?? [], $transaction->metode_bayar ?? null);
        $pdf->Cell(0, 4, $reportPaymentSummary ?: '-', 0, 1);
        
        // Second row
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetX(12);
        $pdf->Cell(35, 4, 'Transaction Date', 0, 0);
        $pdf->Cell(3, 4, ':', 0, 0);
        $pdf->Cell(60, 4, date('d/m/Y H:i:s', strtotime($transaction->tgl_masuk)), 0, 0);
        
        $pdf->SetX(110);
        $pdf->Cell(30, 4, 'Cashier/User', 0, 0);
        $pdf->Cell(3, 4, ':', 0, 0);
        $pdf->Cell(0, 4, $transaction->user_name ?? '-', 0, 1);
        
        // Third row
        $pdf->SetX(12);
        $pdf->Cell(35, 4, 'Status', 0, 0);
        $pdf->Cell(3, 4, ':', 0, 0);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(60, 4, strtoupper($transaction->status ?? 'COMPLETED'), 0, 0);
        
        // Customer/Supplier
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetX(110);
        if ($type === 'jual' && isset($transaction->customer_nama)) {
            $pdf->Cell(30, 4, 'Customer', 0, 0);
            $pdf->Cell(3, 4, ':', 0, 0);
            $pdf->Cell(0, 4, $transaction->customer_nama, 0, 1);
        } elseif ($type === 'beli' && isset($transaction->supplier_name)) {
            $pdf->Cell(30, 4, 'Supplier', 0, 0);
            $pdf->Cell(3, 4, ':', 0, 0);
            $pdf->Cell(0, 4, $transaction->supplier_name, 0, 1);
        } elseif ($type === 'po' && isset($transaction->supplier)) {
            $pdf->Cell(30, 4, 'Supplier', 0, 0);
            $pdf->Cell(3, 4, ':', 0, 0);
            $pdf->Cell(0, 4, $transaction->supplier, 0, 1);
        }
        
        $pdf->Ln(5);
        
        // Items Table Header
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(230, 230, 230);
        
        $pdf->Cell(10, 6, 'No', 1, 0, 'C', true);
        $pdf->Cell(35, 6, 'Item Code', 1, 0, 'C', true);
        $pdf->Cell(90, 6, 'Description', 1, 0, 'C', true);
        $pdf->Cell(20, 6, 'Qty', 1, 0, 'C', true);
        $pdf->Cell(20, 6, 'Unit', 1, 0, 'C', true);
        $pdf->Cell(35, 6, 'Unit Price', 1, 0, 'C', true);
        $pdf->Cell(30, 6, 'Discount', 1, 0, 'C', true);
        $pdf->Cell(37, 6, 'Subtotal', 1, 1, 'C', true);
        
        // Items
        $pdf->SetFont('helvetica', '', 7);
        $itemCount = 1;
        $totalQty = 0;
        
        if (isset($transaction->details) && is_array($transaction->details)) {
            foreach ($transaction->details as $item) {
                $itemName = $item->produk ?? $item->item ?? $item->nama_item ?? 'Unknown';
                $itemCode = $item->kode ?? '-';
                $itemUnit = $item->satuan ?? $item->nama_satuan ?? 'PCS';
                $itemDiscount = ($item->diskon ?? 0) > 0 ? number_format($item->diskon, 0) . '%' : '-';
                
                $pdf->Cell(10, 5, $itemCount, 1, 0, 'C');
                $pdf->Cell(35, 5, substr($itemCode, 0, 20), 1, 0, 'L');
                $pdf->Cell(90, 5, substr($itemName, 0, 50), 1, 0, 'L');
                $pdf->Cell(20, 5, number_format($item->jml ?? 0, 0), 1, 0, 'R');
                $pdf->Cell(20, 5, substr($itemUnit, 0, 10), 1, 0, 'C');
                $pdf->Cell(35, 5, number_format($item->harga ?? 0, 0), 1, 0, 'R');
                $pdf->Cell(30, 5, $itemDiscount, 1, 0, 'C');
                $pdf->Cell(37, 5, number_format($item->subtotal ?? 0, 0), 1, 1, 'R');
                
                $totalQty += $item->jml ?? 0;
                $itemCount++;
            }
        }
        
        // Summary row
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(155, 5, 'TOTAL ITEMS: ' . ($itemCount - 1), 1, 0, 'R');
        $pdf->Cell(20, 5, number_format($totalQty, 0), 1, 0, 'R');
        $pdf->Cell(75, 5, '', 1, 0);
        $pdf->Cell(37, 5, '', 1, 1);
        
        $pdf->Ln(3);
        
        // Financial Summary Box
        $pdf->SetLineWidth(0.3);
        $pdf->Rect(200, $pdf->GetY(), 87, 45);
        
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY(202, $pdf->GetY() + 2);
        $pdf->Cell(0, 5, 'FINANCIAL SUMMARY', 0, 1, 'L');
        
        $pdf->SetFont('helvetica', '', 8);
        $summaryY = $pdf->GetY() + 2;
        
        $pdf->SetXY(202, $summaryY);
        $pdf->Cell(50, 5, 'Subtotal', 0, 0);
        $pdf->Cell(33, 5, number_format($transaction->jml_subtotal ?? 0, 0), 0, 1, 'R');
        
        if (isset($transaction->jml_diskon) && $transaction->jml_diskon > 0) {
            $pdf->SetX(202);
            $pdf->Cell(50, 5, 'Total Discount', 0, 0);
            $pdf->Cell(33, 5, '(' . number_format($transaction->jml_diskon, 0) . ')', 0, 1, 'R');
        }
        
        if (isset($transaction->jml_ppn) && $transaction->jml_ppn > 0) {
            $pdf->SetX(202);
            $pdf->Cell(50, 5, 'Tax (PPN ' . ($transaction->ppn ?? 0) . '%)', 0, 0);
            $pdf->Cell(33, 5, number_format($transaction->jml_ppn, 0), 0, 1, 'R');
        }
        
        $pdf->SetLineWidth(0.5);
        $pdf->Line(202, $pdf->GetY() + 1, 285, $pdf->GetY() + 1);
        $pdf->Ln(3);
        
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetX(202);
        $pdf->Cell(50, 6, 'GRAND TOTAL', 0, 0);
        $pdf->Cell(33, 6, 'Rp ' . number_format($transaction->jml_gtotal ?? 0, 0), 0, 1, 'R');
        
        if (!empty($transaction->payments)) {
            $pdf->SetFont('helvetica', '', 8);
            foreach ($transaction->payments as $payment) {
                $label = $payment->nama_platform ?? $payment->platform ?? 'Payment';
                $amount = number_format((float)($payment->nominal ?? 0), 0, ',', '.');
                $pdf->SetX(202);
                $pdf->Cell(50, 5, $label, 0, 0, 'L');
                $pdf->Cell(33, 5, 'Rp ' . $amount, 0, 1, 'R');
            }
        }
        
        if (isset($transaction->jml_bayar) && $transaction->jml_bayar > 0) {
            $pdf->SetLineWidth(0.2);
            $pdf->Line(202, $pdf->GetY() + 1, 285, $pdf->GetY() + 1);
            $pdf->Ln(2);
            
            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetX(202);
            $pdf->Cell(50, 5, 'Payment Received', 0, 0);
            $pdf->Cell(33, 5, number_format($transaction->jml_bayar, 0), 0, 1, 'R');
            
            $pdf->SetX(202);
            $pdf->Cell(50, 5, 'Change', 0, 0);
            $pdf->Cell(33, 5, number_format($transaction->jml_kembali ?? 0, 0), 0, 1, 'R');
        }
        
        // Notes section if available
        if (($type === 'jual' && isset($transaction->keterangan) && $transaction->keterangan) ||
            ($type === 'beli' && isset($transaction->catatan_terima) && $transaction->catatan_terima) ||
            ($type === 'po' && isset($transaction->keterangan) && $transaction->keterangan)) {
            
            $noteText = '';
            if ($type === 'jual' && isset($transaction->keterangan)) {
                $noteText = $transaction->keterangan;
            } elseif ($type === 'beli' && isset($transaction->catatan_terima)) {
                $noteText = $transaction->catatan_terima;
            } elseif ($type === 'po' && isset($transaction->keterangan)) {
                $noteText = $transaction->keterangan;
            }
            
            if ($noteText) {
                $pdf->SetXY(10, $pdf->GetY() + 50);
                $pdf->SetFont('helvetica', 'B', 8);
                $pdf->Cell(0, 4, 'NOTES:', 0, 1);
                $pdf->SetFont('helvetica', '', 8);
                $pdf->MultiCell(0, 4, $noteText, 0, 'L');
            }
        }
        
        // Footer
        $pdf->SetY(-15);
        $pdf->SetLineWidth(0.3);
        $pdf->Line(10, $pdf->GetY(), 287, $pdf->GetY());
        $pdf->Ln(2);
        
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(90, 3, 'Generated: ' . date('d-m-Y H:i:s'), 0, 0, 'L');
        $pdf->Cell(90, 3, 'System: ' . ($settings->judul_app ?? 'POS System'), 0, 0, 'C');
        $pdf->Cell(90, 3, 'Page 1 of 1', 0, 1, 'R');
        
        $pdf->SetFont('helvetica', 'I', 7);
        $pdf->Cell(0, 3, 'This is a computer-generated document. For internal use only.', 0, 1, 'C');
        
        return $pdf;
    }

    /**
     * Build readable payment summary text.
     *
     * @param array|null $payments
     * @param string|null $fallback
     * @param bool $includeAmount
     * @return string
     */
    private function formatPaymentSummaryText(?array $payments, ?string $fallback = null, bool $includeAmount = true): string
    {
        if (empty($payments)) {
            return $fallback ? strtoupper($fallback) : '';
        }

        $parts = [];
        foreach ($payments as $payment) {
            $label = $payment->nama_platform ?? $payment->platform ?? $payment->platform_name ?? 'Payment';
            if ($includeAmount) {
                $amount = number_format((float)($payment->nominal ?? 0), 0, ',', '.');
                $parts[] = $label . ' (Rp ' . $amount . ')';
            } else {
                $parts[] = $label;
            }
        }

        return implode(', ', $parts);
    }

    /**
     * Attach payment collections to each transaction and return summary map.
     *
     * @param array $transactions
     * @return array
     */
    private function attachPaymentsToTransactions(array &$transactions): array
    {
        if (empty($transactions)) {
            return [];
        }

        $transactionIds = array_column($transactions, 'id');
        if (empty($transactionIds)) {
            return [];
        }

        $db = \Config\Database::connect();
        $builder = $db->table('tbl_trans_jual_plat tjp');
        $payments = $builder
            ->select('tjp.id_penjualan, tjp.nominal, COALESCE(mp.platform, tjp.platform) as platform_name')
            ->join('tbl_m_platform mp', 'mp.id = tjp.id_platform', 'left')
            ->whereIn('tjp.id_penjualan', $transactionIds)
            ->get()
            ->getResult();

        $map = [];
        foreach ($payments as $payment) {
            $payment->nama_platform = $payment->platform_name;
            $map[$payment->id_penjualan][] = $payment;
        }

        foreach ($transactions as $transaction) {
            $transaction->payments = $map[$transaction->id] ?? [];
        }

        return $map;
    }
}
