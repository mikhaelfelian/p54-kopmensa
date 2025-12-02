<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: View for displaying sales report detail
 * This file represents the sales report detail view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-eye mr-1"></i> Detail Penjualan
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/sale') ?>" class="btn btn-default btn-sm rounded-0">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Transaction Information -->
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>No. Nota</strong></td>
                                <td>: <?= $sale->no_nota ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal</strong></td>
                                <td>: <?= date('d/m/Y', strtotime($sale->tgl_masuk)) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Pelanggan</strong></td>
                                <td>: <?= esc($sale->pelanggan_nama ?? 'Umum') ?></td>
                            </tr>
                            <?php if (!empty($sale->pelanggan_kode)): ?>
                            <tr>
                                <td><strong>No. Anggota</strong></td>
                                <td>: <?= esc($sale->pelanggan_kode) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td><strong>Alamat</strong></td>
                                <td>: <?= $sale->pelanggan_alamat ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Telepon</strong></td>
                                <td>: <?= $sale->pelanggan_telepon ?? '-' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Gudang/Outlet</strong></td>
                                <td>: <?= $sale->gudang_nama ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Shift</strong></td>
                                <td>: <?= $sale->shift_nama ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Kasir</strong></td>
                                <td>: <?= $sale->user_full_name ?? $sale->username ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Sales</strong></td>
                                <td>: <?= $sale->sales_nama ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Metode Bayar</strong></td>
                                <td>: <?= $sale->metode_bayar_formatted ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Total Item</strong></td>
                                <td>: <?= (int)($sale->total_items ?? 0) ?> item</td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: 
                                    <?php if ($sale->status_nota == '1'): ?>
                                        <span class="badge badge-success">Selesai</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Draft</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status Bayar</strong></td>
                                <td>: 
                                    <?php if ($sale->status_bayar == '1'): ?>
                                        <span class="badge badge-success">Lunas</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Belum Lunas</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <!-- Items Table -->
                <h5><i class="fas fa-list mr-1"></i> Detail Item</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Kode Item</th>
                                <th>Nama Item</th>
                                <th>Satuan</th>
                                <th>Keterangan Pajak</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($items)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada item</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($items as $index => $item): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= $item->item_kode ?? '-' ?></td>
                                        <td><?= $item->item_nama ?? '-' ?></td>
                                        <td><?= $item->satuan_nama ?? '-' ?></td>
                                        <td>
                                            <?php if (($item->status_ppn ?? '0') == '1'): ?>
                                                <span class="badge badge-success">PPN</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Non-PPN</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?= number_format($item->jml ?? 0, 0, ',', '.') ?></td>
                                        <td class="text-right"><?= number_format($item->harga ?? 0, 0, ',', '.') ?></td>
                                        <td class="text-right"><?= number_format($item->subtotal ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($items)): ?>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="7" class="text-right">Subtotal</th>
                                    <th class="text-right"><?= number_format($sale->jml_subtotal ?? 0, 0, ',', '.') ?></th>
                                </tr>
                                <?php if (($sale->jml_diskon ?? 0) > 0): ?>
                                    <tr>
                                        <th colspan="7" class="text-right">Diskon</th>
                                        <th class="text-right">-<?= number_format($sale->jml_diskon ?? 0, 0, ',', '.') ?></th>
                                    </tr>
                                <?php endif; ?>
                                <?php if (($sale->jml_ppn ?? 0) > 0): ?>
                                    <tr>
                                        <th colspan="7" class="text-right">PPN</th>
                                        <th class="text-right"><?= number_format($sale->jml_ppn ?? 0, 0, ',', '.') ?></th>
                                    </tr>
                                <?php endif; ?>
                                <tr class="bg-primary text-white">
                                    <th colspan="7" class="text-right">TOTAL</th>
                                    <th class="text-right"><?= number_format($sale->jml_gtotal ?? 0, 0, ',', '.') ?></th>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>

                <!-- Payment Information -->
                <?php if (($sale->jml_bayar ?? 0) > 0): ?>
                    <hr>
                    <h5><i class="fas fa-money-bill-wave mr-1"></i> Informasi Pembayaran</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Total Bayar</strong></td>
                                    <td>: <?= number_format($sale->jml_bayar ?? 0, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Kembalian</strong></td>
                                    <td>: <?= number_format($sale->jml_kembali ?? 0, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Kurang</strong></td>
                                    <td>: <?= number_format($sale->jml_kurang ?? 0, 0, ',', '.') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
