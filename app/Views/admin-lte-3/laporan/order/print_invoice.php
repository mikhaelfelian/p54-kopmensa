<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-30
 * Github: github.com/mikhaelfelian
 * Description: View for printing order invoice
 * This file represents the order invoice print view.
 */
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Pesanan - <?= $order->no_nota ?></title>
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .invoice-header h2 {
            margin: 0;
            font-size: 18px;
        }
        .invoice-info {
            margin-bottom: 20px;
        }
        .invoice-info table {
            width: 100%;
        }
        .invoice-info td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-info .label {
            font-weight: bold;
            width: 150px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
            <i class="fas fa-print"></i> Cetak
        </button>
        <a href="<?= base_url('laporan/order/detail/' . $order->id) ?>" 
           style="padding: 10px 20px; font-size: 14px; text-decoration: none; background: #6c757d; color: white; border-radius: 4px; display: inline-block; margin-left: 10px;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="invoice-header">
        <?php 
        // Use logo_invoice if available, fallback to logo_header, then logo
        $logoPath = null;
        if (!empty($Pengaturan->logo_invoice)) {
            $logoPath = $Pengaturan->logo_invoice;
        } elseif (!empty($Pengaturan->logo_header)) {
            $logoPath = $Pengaturan->logo_header;
        } elseif (!empty($Pengaturan->logo)) {
            $logoPath = $Pengaturan->logo;
        }
        
        if ($logoPath && file_exists(FCPATH . $logoPath)): ?>
            <img src="<?= base_url($logoPath) ?>" 
                 alt="Logo" 
                 style="max-height: 60px; margin-bottom: 10px; display: block; margin-left: auto; margin-right: auto;"
                 onerror="this.style.display='none';">
        <?php endif; ?>
        <h2>PURCHASE ORDER</h2>
        <p><strong><?= esc($Pengaturan->judul ?? 'Perusahaan') ?></strong></p>
        <?php if (!empty($Pengaturan->alamat)): ?>
            <p><?= esc($Pengaturan->alamat) ?></p>
        <?php endif; ?>
        <?php if (!empty($Pengaturan->kota)): ?>
            <p><?= esc($Pengaturan->kota) ?></p>
        <?php endif; ?>
    </div>

    <div class="invoice-info">
        <table>
            <tr>
                <td class="label">No. PO</td>
                <td>: <?= $order->no_nota ?></td>
                <td class="label">Tanggal</td>
                <td>: <?= date('d/m/Y', strtotime($order->tgl_masuk)) ?></td>
            </tr>
            <tr>
                <td class="label">Supplier</td>
                <td>: <?= $order->supplier_nama ?? '-' ?></td>
                <td class="label">Pembuat</td>
                <td>: <?= esc($order->user_full_name ?? $order->user_username ?? '-') ?></td>
            </tr>
            <?php if (!empty($order->supplier_alamat)): ?>
            <tr>
                <td class="label">Alamat Supplier</td>
                <td>: <?= esc($order->supplier_alamat) ?></td>
                <td></td>
                <td></td>
            </tr>
            <?php endif; ?>
            <?php if (!empty($order->supplier_no_tlp)): ?>
            <tr>
                <td class="label">Telepon Supplier</td>
                <td>: <?= esc($order->supplier_no_tlp) ?></td>
                <td></td>
                <td></td>
            </tr>
            <?php endif; ?>
            <?php if (!empty($order->pengiriman)): ?>
            <tr>
                <td class="label">Alamat Pengiriman</td>
                <td colspan="3">: <?= esc($order->pengiriman) ?></td>
            </tr>
            <?php endif; ?>
            <?php if (!empty($order->keterangan)): ?>
            <tr>
                <td class="label">Keterangan</td>
                <td colspan="3">: <?= esc($order->keterangan) ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Kode Item</th>
                <th>Nama Item</th>
                <th>Satuan</th>
                <th width="10%" class="text-center">Qty</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr>
                    <td colspan="6" class="text-center">Tidak ada item</td>
                </tr>
            <?php else: ?>
                <?php foreach ($items as $index => $item): ?>
                    <tr>
                        <td class="text-center"><?= $index + 1 ?></td>
                        <td><?= $item->item_kode ?? '-' ?></td>
                        <td><?= $item->item_nama ?? '-' ?></td>
                        <td><?= $item->satuan_nama ?? '-' ?></td>
                        <td class="text-center"><?= number_format($item->jml ?? 0, 0, ',', '.') ?></td>
                        <td><?= esc($item->keterangan ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top: 40px;">
        <table style="width: 100%;">
            <tr>
                <td width="50%" style="text-align: center;">
                    <p>Supplier,</p>
                    <br><br><br>
                    <p><strong><?= $order->supplier_nama ?? '-' ?></strong></p>
                </td>
                <td width="50%" style="text-align: center;">
                    <p>Hormat Kami,</p>
                    <br><br><br>
                    <p><strong><?= $Pengaturan->judul_app ?? $Pengaturan->judul ?? 'Perusahaan' ?></strong></p>
                </td>
            </tr>
        </table>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            // Optional: Uncomment to auto-print
            // window.print();
        };
    </script>
</body>
</html>

