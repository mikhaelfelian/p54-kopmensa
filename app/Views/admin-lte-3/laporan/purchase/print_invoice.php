<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: View for printing purchase invoice
 * This file represents the purchase invoice print view.
 */
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Pembelian - <?= $purchase->no_nota ?></title>
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
        .invoice-footer {
            margin-top: 20px;
            border-top: 2px solid #000;
            padding-top: 10px;
        }
        .invoice-footer table {
            width: 100%;
        }
        .invoice-footer td {
            padding: 5px;
        }
        .invoice-footer .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
            <i class="fas fa-print"></i> Cetak
        </button>
        <a href="<?= base_url('laporan/purchase/detail/' . $purchase->id) ?>" 
           style="padding: 10px 20px; font-size: 14px; text-decoration: none; background: #6c757d; color: white; border-radius: 4px; display: inline-block; margin-left: 10px;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="invoice-header">
        <?php 
        $logoPath = null;
        if (!empty($Pengaturan->logo_header)) {
            $logoPath = 'public/file/app/' . $Pengaturan->logo_header;
        } elseif (!empty($Pengaturan->logo)) {
            $logoPath = 'public/file/app/' . $Pengaturan->logo;
        }
        
        if ($logoPath && file_exists(FCPATH . $logoPath)): ?>
            <img src="<?= base_url($logoPath) ?>" 
                 alt="Logo" 
                 style="max-height: 60px; margin-bottom: 10px; display: block; margin-left: auto; margin-right: auto;"
                 onerror="this.style.display='none';">
        <?php endif; ?>
        <h2>FAKTUR PEMBELIAN</h2>
        <p><?= esc($Pengaturan->judul_app ?? $Pengaturan->judul ?? 'Perusahaan') ?></p>
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
                <td class="label">No. Faktur</td>
                <td>: <?= $purchase->no_nota ?></td>
                <td class="label">Tanggal</td>
                <td>: <?= date('d/m/Y', strtotime($purchase->tgl_masuk)) ?></td>
            </tr>
            <tr>
                <td class="label">Supplier</td>
                <td>: <?= $purchase->supplier_nama ?? '-' ?></td>
                <td class="label">Gudang</td>
                <td>: <?= $purchase->gudang_nama ?? '-' ?></td>
            </tr>
            <?php if (!empty($purchase->supplier_alamat)): ?>
            <tr>
                <td class="label">Alamat</td>
                <td>: <?= $purchase->supplier_alamat ?></td>
                <td class="label">Penerima</td>
                <td>: <?= $purchase->penerima_nama ?? '-' ?></td>
            </tr>
            <?php endif; ?>
            <?php if (!empty($purchase->supplier_no_tlp)): ?>
            <tr>
                <td class="label">Telepon</td>
                <td>: <?= $purchase->supplier_no_tlp ?></td>
                <td></td>
                <td></td>
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
                <th width="15%" class="text-right">Harga</th>
                <th width="15%" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr>
                    <td colspan="7" class="text-center">Tidak ada item</td>
                </tr>
            <?php else: ?>
                <?php foreach ($items as $index => $item): ?>
                    <tr>
                        <td class="text-center"><?= $index + 1 ?></td>
                        <td><?= $item->item_kode ?? '-' ?></td>
                        <td><?= $item->item_nama ?? '-' ?></td>
                        <td><?= $item->satuan_nama ?? '-' ?></td>
                        <td class="text-center"><?= number_format($item->jml ?? 0, 0, ',', '.') ?></td>
                        <td class="text-right"><?= number_format($item->harga ?? 0, 0, ',', '.') ?></td>
                        <td class="text-right"><?= number_format($item->subtotal ?? 0, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="invoice-footer">
        <table>
            <tr>
                <td width="70%"></td>
                <td width="30%">
                    <table>
                        <tr>
                            <td><strong>Subtotal</strong></td>
                            <td class="text-right"><?= number_format($purchase->jml_subtotal ?? 0, 0, ',', '.') ?></td>
                        </tr>
                        <?php if (($purchase->jml_diskon ?? 0) > 0): ?>
                        <tr>
                            <td><strong>Diskon</strong></td>
                            <td class="text-right">-<?= number_format($purchase->jml_diskon ?? 0, 0, ',', '.') ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (($purchase->jml_ppn ?? 0) > 0): ?>
                        <tr>
                            <td><strong>PPN</strong></td>
                            <td class="text-right"><?= number_format($purchase->jml_ppn ?? 0, 0, ',', '.') ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr class="total-row">
                            <td><strong>TOTAL</strong></td>
                            <td class="text-right"><?= number_format($purchase->jml_gtotal ?? 0, 0, ',', '.') ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 40px;">
        <table style="width: 100%;">
            <tr>
                <td width="50%" style="text-align: center;">
                    <p>Penerima,</p>
                    <br><br><br>
                    <p><strong><?= $purchase->penerima_nama ?? '-' ?></strong></p>
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

