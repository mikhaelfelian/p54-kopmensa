<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: View for displaying detailed item purchase per invoice
 * This file represents the purchase detail items view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list mr-1"></i> Detail Item Pembelian - <?= $purchase->no_nota ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/purchase/detail/' . $purchase->id) ?>" class="btn btn-default btn-sm rounded-0">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Purchase Information -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>No. Faktur</strong></td>
                                <td>: <?= $purchase->no_nota ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal</strong></td>
                                <td>: <?= date('d/m/Y', strtotime($purchase->tgl_masuk)) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Supplier</strong></td>
                                <td>: <?= $purchase->supplier_nama ?? '-' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Gudang</strong></td>
                                <td>: <?= $purchase->gudang_nama ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Penerima</strong></td>
                                <td>: <?= $purchase->penerima_nama ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Total</strong></td>
                                <td>: <strong><?= number_format($purchase->jml_gtotal ?? 0, 0, ',', '.') ?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <!-- Items Table -->
                <h5><i class="fas fa-list mr-1"></i> Detail Item Pembelian</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Kode Item</th>
                                <th>Nama Item</th>
                                <th>Satuan</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Subtotal</th>
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
                                        <td><?= $index + 1 ?></td>
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
                        <?php if (!empty($items)): ?>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="6" class="text-right">Subtotal</th>
                                    <th class="text-right"><?= number_format($purchase->jml_subtotal ?? 0, 0, ',', '.') ?></th>
                                </tr>
                                <?php if (($purchase->jml_diskon ?? 0) > 0): ?>
                                    <tr>
                                        <th colspan="6" class="text-right">Diskon</th>
                                        <th class="text-right">-<?= number_format($purchase->jml_diskon ?? 0, 0, ',', '.') ?></th>
                                    </tr>
                                <?php endif; ?>
                                <?php if (($purchase->jml_ppn ?? 0) > 0): ?>
                                    <tr>
                                        <th colspan="6" class="text-right">PPN</th>
                                        <th class="text-right"><?= number_format($purchase->jml_ppn ?? 0, 0, ',', '.') ?></th>
                                    </tr>
                                <?php endif; ?>
                                <tr class="bg-primary text-white">
                                    <th colspan="6" class="text-right">TOTAL</th>
                                    <th class="text-right"><?= number_format($purchase->jml_gtotal ?? 0, 0, ',', '.') ?></th>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

