<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: View for displaying order detail report
 * This file represents the order detail view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shopping-bag mr-1"></i> Detail Pesanan - <?= $order->no_nota ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/order') ?>" class="btn btn-secondary btn-sm rounded-0">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Order Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Informasi Pesanan</h5>
                        <table class="table table-sm">
                            <tr>
                                <td width="30%"><strong>No. Invoice:</strong></td>
                                <td><?= $order->no_nota ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal:</strong></td>
                                <td><?= date('d/m/Y H:i', strtotime($order->tgl_masuk)) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <?php if ($order->status_nota == '1'): ?>
                                        <span class="badge badge-success">Completed</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Metode Bayar:</strong></td>
                                <td><?= ucfirst($order->metode_bayar ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Total:</strong></td>
                                <td><strong><?= number_format($order->jml_total ?? 0, 0, ',', '.') ?></strong></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Informasi Pelanggan & Outlet</h5>
                        <table class="table table-sm">
                            <tr>
                                <td width="30%"><strong>Pelanggan:</strong></td>
                                <td><?= $order->pelanggan_nama ?: 'Umum' ?></td>
                            </tr>
                            <?php if ($order->pelanggan_hp): ?>
                            <tr>
                                <td><strong>No. HP:</strong></td>
                                <td><?= $order->pelanggan_hp ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($order->pelanggan_alamat): ?>
                            <tr>
                                <td><strong>Alamat:</strong></td>
                                <td><?= $order->pelanggan_alamat ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td><strong>Outlet:</strong></td>
                                <td><?= $order->gudang_nama ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Sales:</strong></td>
                                <td><?= $order->sales_nama ?? '-' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Order Details -->
                <h5>Detail Item</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Kode Item</th>
                                <th>Nama Item</th>
                                <th>Satuan</th>
                                <th width="10%">Qty</th>
                                <th width="15%">Harga</th>
                                <th width="15%">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orderDetails)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada detail item</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orderDetails as $index => $detail): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= $detail->kode ?? '-' ?></td>
                                        <td><?= $detail->item ?? '-' ?></td>
                                        <td><?= $detail->satuan ?? '-' ?></td>
                                        <td class="text-center"><?= number_format($detail->jml ?? 0, 0, ',', '.') ?></td>
                                        <td class="text-right"><?= number_format($detail->harga ?? 0, 0, ',', '.') ?></td>
                                        <td class="text-right"><?= number_format($detail->subtotal ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($orderDetails)): ?>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="6" class="text-right">TOTAL</th>
                                    <th class="text-right"><?= number_format($order->jml_total ?? 0, 0, ',', '.') ?></th>
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
