<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-20
 * GitHub: github.com/mikhaelfelian
 * Description: Print view for Purchase Transaction
 * This file represents the Purchase Transaction Print view.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - <?= esc($transaksi->no_nota) ?></title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            font-weight: normal;
        }
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-left, .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-item {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #f0f0f0;
            padding: 8px;
            text-align: left;
            border-bottom: 2px solid #333;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        table tfoot td {
            font-weight: bold;
            border-top: 2px solid #333;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .no-print {
            margin-bottom: 20px;
        }
        .btn {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn">Print</button>
        <a href="<?= base_url('transaksi/beli/detail/' . $transaksi->id) ?>" class="btn" style="background-color: #6c757d;">Kembali</a>
    </div>

    <div class="header">
        <h1><?= esc($pengaturan->judul ?? 'Koperasi Konsumen Syariah RSI Sultan Agung') ?></h1>
        <h2><?= esc($pengaturan->alamat ?? 'Jl. Kaligawe Raya No.Km. 4, Terboyo Kulon, Kec. Genuk, Kota Semarang, Jawa Tengah 50112') ?></h2>
        <?php if (!empty($pengaturan->kota)): ?>
            <h2><?= esc($pengaturan->kota) ?></h2>
        <?php endif; ?>
        <hr>
        <h2>FAKTUR PEMBELIAN</h2>
    </div>

    <div class="info-section">
        <div class="info-left">
            <div class="info-item">
                <span class="info-label">No. Faktur:</span>
                <?= esc($transaksi->no_nota) ?>
            </div>
            <div class="info-item">
                <span class="info-label">Tanggal:</span>
                <?= date('d/m/Y', strtotime($transaksi->tgl_masuk)) ?>
            </div>
            <div class="info-item">
                <span class="info-label">No. PO:</span>
                <?= esc($transaksi->no_po ?? '-') ?>
            </div>
        </div>
        <div class="info-right">
            <div class="info-item">
                <span class="info-label">Supplier:</span>
                <?= esc($supplier->nama ?? $transaksi->supplier ?? '-') ?>
            </div>
            <div class="info-item">
                <span class="info-label">Alamat:</span>
                <?= esc($supplier->alamat ?? '-') ?>
            </div>
            <div class="info-item">
                <span class="info-label">Telp:</span>
                <?= esc($supplier->telepon ?? '-') ?>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Satuan</th>
                <th class="text-right">Harga</th>
                <th class="text-center">Diskon %</th>
                <th class="text-right">Potongan</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $i => $item): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= esc($item->kode) ?></td>
                        <td><?= esc($item->item) ?></td>
                        <td class="text-center"><?= number_format($item->jml, 2, ',', '.') ?></td>
                        <td class="text-center"><?= esc($item->satuan) ?></td>
                        <td class="text-right"><?= number_format($item->harga, 0, ',', '.') ?></td>
                        <td class="text-center">
                            <?php 
                            $totalDisk = ($item->disk1 ?? 0) + ($item->disk2 ?? 0) + ($item->disk3 ?? 0);
                            echo $totalDisk > 0 ? number_format($totalDisk, 2, ',', '.') : '-';
                            ?>
                        </td>
                        <td class="text-right"><?= number_format($item->potongan ?? 0, 0, ',', '.') ?></td>
                        <td class="text-right"><?= number_format($item->subtotal ?? 0, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center">Tidak ada item</td>
                </tr>
            <?php endif ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" class="text-right"><strong>Subtotal</strong></td>
                <td class="text-right"><strong><?= number_format($subtotal, 0, ',', '.') ?></strong></td>
            </tr>
            <?php if ($total_diskon > 0): ?>
                <tr>
                    <td colspan="8" class="text-right"><strong>Total Diskon</strong></td>
                    <td class="text-right"><strong><?= number_format($total_diskon, 2, ',', '.') ?>%</strong></td>
                </tr>
            <?php endif; ?>
            <?php if ($total_potongan > 0): ?>
                <tr>
                    <td colspan="8" class="text-right"><strong>Total Potongan</strong></td>
                    <td class="text-right"><strong><?= number_format($total_potongan, 0, ',', '.') ?></strong></td>
                </tr>
            <?php endif; ?>
            <?php if ($ppn > 0): ?>
                <tr>
                    <td colspan="8" class="text-right"><strong>DPP</strong></td>
                    <td class="text-right"><strong><?= number_format($dpp, 0, ',', '.') ?></strong></td>
                </tr>
                <tr>
                    <td colspan="8" class="text-right"><strong>PPN (11%)</strong></td>
                    <td class="text-right"><strong><?= number_format($ppn, 0, ',', '.') ?></strong></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td colspan="8" class="text-right"><strong>GRAND TOTAL</strong></td>
                <td class="text-right"><strong><?= number_format($total, 0, ',', '.') ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <?php if (!empty($transaksi->keterangan)): ?>
        <div style="margin-top: 20px;">
            <strong>Keterangan:</strong><br>
            <?= esc($transaksi->keterangan) ?>
        </div>
    <?php endif; ?>

    <div style="margin-top: 40px;">
        <div style="display: table; width: 100%;">
            <div style="display: table-cell; width: 50%;">
                <div style="text-align: center;">
                    <p>Dibuat oleh:</p>
                    <br><br><br>
                    <p><strong><?= esc($user->first_name ?? 'Admin') ?></strong></p>
                    <p><?= esc($user->email ?? '') ?></p>
                </div>
            </div>
            <div style="display: table-cell; width: 50%;">
                <div style="text-align: center;">
                    <p>Diterima oleh:</p>
                    <br><br><br>
                    <p>__________________________</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

