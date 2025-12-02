<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-30
 * Github: github.com/mikhaelfelian
 * Description: View for displaying order report detail
 * This file represents the order report detail view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-eye mr-1"></i> Detail Pesanan
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/order/print_invoice/' . $order->id) ?>" 
                       class="btn btn-primary btn-sm rounded-0" target="_blank">
                        <i class="fas fa-print mr-1"></i> Cetak Faktur
                    </a>
                    <a href="<?= base_url('laporan/order/detail_items/' . $order->id) ?>" 
                       class="btn btn-info btn-sm rounded-0">
                        <i class="fas fa-list mr-1"></i> Detail Item
                    </a>
                    <a href="<?= base_url('laporan/order') ?>" class="btn btn-default btn-sm rounded-0">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Order Information -->
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>No. PO</strong></td>
                                <td>: <?= $order->no_nota ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal</strong></td>
                                <td>: <?= date('d/m/Y', strtotime($order->tgl_masuk)) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Supplier</strong></td>
                                <td>: <?= esc($order->supplier_nama ?? '-') ?></td>
                            </tr>
                            <?php if (!empty($order->supplier_alamat)): ?>
                            <tr>
                                <td><strong>Alamat Supplier</strong></td>
                                <td>: <?= esc($order->supplier_alamat) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($order->supplier_no_tlp)): ?>
                            <tr>
                                <td><strong>Telp Supplier</strong></td>
                                <td>: <?= esc($order->supplier_no_tlp) ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Pembuat</strong></td>
                                <td>: <?= esc($order->user_full_name ?? $order->user_username ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: 
                                    <?php 
                                    $transBeliPOModel = new \App\Models\TransBeliPOModel();
                                    $statusLabel = $transBeliPOModel->getStatusLabel($order->status ?? '0');
                                    $statusClass = 'secondary';
                                    if ($order->status == '0') $statusClass = 'warning';
                                    elseif (in_array($order->status, ['2', '4', '5'])) $statusClass = 'success';
                                    elseif ($order->status == '3') $statusClass = 'danger';
                                    elseif ($order->status == '1') $statusClass = 'info';
                                    ?>
                                    <span class="badge badge-<?= $statusClass ?>"><?= $statusLabel ?></span>
                                </td>
                            </tr>
                            <?php if (!empty($order->keterangan)): ?>
                            <tr>
                                <td><strong>Keterangan</strong></td>
                                <td>: <?= esc($order->keterangan) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($order->pengiriman)): ?>
                            <tr>
                                <td><strong>Alamat Pengiriman</strong></td>
                                <td>: <?= esc($order->pengiriman) ?></td>
                            </tr>
                            <?php endif; ?>
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
                                <th class="text-center">Qty</th>
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
                                        <td><?= $index + 1 ?></td>
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
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

