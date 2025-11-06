<?php
/**
 * Print Shift Report - POS Thermal Format (58mm)
 * Created by: Mikhael Felian Waskito
 * Date: 2025-01-18
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Shift - <?= esc($shift['shift_code']) ?></title>
    <style>
        @media print {
            @page {
                size: 58mm auto;
                margin: 0;
            }
            body {
                margin: 0;
                padding: 5mm;
            }
            .no-print {
                display: none;
            }
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 58mm;
            margin: 0 auto;
            padding: 5mm;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        .company-name {
            font-weight: bold;
            font-size: 14px;
        }
        .report-title {
            font-weight: bold;
            margin: 5px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            border-bottom: 1px dotted #ccc;
        }
        .info-label {
            font-weight: bold;
        }
        .section-title {
            font-weight: bold;
            text-align: center;
            margin: 5px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 3px 0;
        }
        .payment-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }
        .total-row {
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 3px 0;
            margin: 3px 0;
        }
        .footer {
            text-align: center;
            margin-top: 10px;
            padding-top: 5px;
            border-top: 1px dashed #000;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name"><?= esc($Pengaturan->nama_perusahaan ?? 'KOPMENSA') ?></div>
        <div><?= esc($Pengaturan->alamat ?? '') ?></div>
        <div><?= esc($Pengaturan->no_telp ?? '') ?></div>
    </div>

    <div class="report-title text-center">LAPORAN SHIFT</div>
    <div class="text-center" style="font-size: 10px;"><?= esc($shift['shift_code']) ?></div>

    <div class="section-title">INFORMASI SHIFT</div>
    <div class="info-row">
        <span class="info-label">Kasir:</span>
        <span><?= esc(($shift['user_open_name'] ?? '') . ' ' . ($shift['user_open_lastname'] ?? '')) ?></span>
    </div>
    <div class="info-row">
        <span class="info-label">Outlet:</span>
        <span><?= esc($shift['outlet_name'] ?? 'N/A') ?></span>
    </div>
    <div class="info-row">
        <span class="info-label">Buka:</span>
        <span><?= date('d/m/Y H:i', strtotime($shift['start_at'])) ?></span>
    </div>
    <?php if (!empty($shift['end_at'])): ?>
    <div class="info-row">
        <span class="info-label">Tutup:</span>
        <span><?= date('d/m/Y H:i', strtotime($shift['end_at'])) ?></span>
    </div>
    <?php endif; ?>

    <div class="section-title">RINGKASAN KEUANGAN</div>
    <div class="info-row">
        <span>Uang Modal:</span>
        <span class="text-right"><?= format_angka($shift['open_float'], 0) ?></span>
    </div>
    <div class="info-row">
        <span>Total Transaksi:</span>
        <span class="text-right"><?= $transactionStats['total_transactions'] ?? 0 ?></span>
    </div>
    <div class="info-row">
        <span>Total Pendapatan:</span>
        <span class="text-right"><?= format_angka($transactionStats['total_revenue'] ?? 0, 0) ?></span>
    </div>

    <div class="section-title">METODE PEMBAYARAN</div>
    <?php if (!empty($paymentBreakdown['payment_methods'])): ?>
        <?php foreach ($paymentBreakdown['payment_methods'] as $payment): ?>
        <div class="payment-row">
            <span><?= esc($payment['payment_method_name'] ?? $payment['payment_method_type'] ?? 'Unknown') ?>:</span>
            <span class="text-right"><?= format_angka($payment['total_amount'], 0) ?></span>
        </div>
        <div style="font-size: 10px; padding-left: 10px;">
            (<?= $payment['transaction_count'] ?> transaksi)
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center">- Tidak ada transaksi -</div>
    <?php endif; ?>

    <?php if (($paymentBreakdown['total_refund'] ?? 0) > 0): ?>
    <div class="payment-row" style="color: red;">
        <span>Total Refund:</span>
        <span class="text-right">-<?= format_angka($paymentBreakdown['total_refund'], 0) ?></span>
    </div>
    <?php endif; ?>

    <div class="section-title">KAS KECIL</div>
    <div class="info-row">
        <span>Kas Kecil Masuk:</span>
        <span class="text-right">+<?= format_angka($shift['petty_in_total'], 0) ?></span>
    </div>
    <div class="info-row">
        <span>Kas Kecil Keluar:</span>
        <span class="text-right">-<?= format_angka($shift['petty_out_total'], 0) ?></span>
    </div>

    <div class="section-title">PENUTUPAN</div>
    <div class="info-row">
        <span>Uang Diharapkan:</span>
        <span class="text-right"><?= format_angka($shift['expected_cash'], 0) ?></span>
    </div>
    <div class="info-row">
        <span>Uang Dihitung:</span>
        <span class="text-right"><?= format_angka($shift['counted_cash'] ?? 0, 0) ?></span>
    </div>
    <div class="total-row">
        <span>Selisih:</span>
        <span class="text-right"><?= format_angka($shift['diff_cash'] ?? 0, 0) ?></span>
    </div>

    <?php if (!empty($shift['catatan_shift'])): ?>
    <div class="section-title">CATATAN</div>
    <div style="font-size: 11px; padding: 3px 0;">
        <?= nl2br(esc($shift['catatan_shift'])) ?>
    </div>
    <?php endif; ?>

    <div class="footer">
        <div>Dicetak: <?= date('d/m/Y H:i:s') ?></div>
        <div><?= esc($Pengaturan->footer_nota ?? 'Terima Kasih') ?></div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" class="btn btn-primary">Print</button>
        <a href="<?= base_url('transaksi/shift/print/' . $shift['id'] . '?format=pdf') ?>" class="btn btn-info">Export PDF</a>
        <a href="<?= base_url('transaksi/shift') ?>" class="btn btn-secondary">Kembali</a>
    </div>
</body>
</html>

