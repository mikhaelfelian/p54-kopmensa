<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: View for displaying purchase reports
 * This file represents the purchase report index view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-1"></i> Laporan Pembelian
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/purchase/export_excel') ?>?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&id_supplier=<?= $idSupplier ?>&status_nota=<?= $statusNota ?>" 
                       class="btn btn-success btn-sm rounded-0">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="get" action="<?= base_url('laporan/purchase') ?>" class="mb-4">
                    <div class="row">
                        <div class="col-md-2">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $startDate ?>">
                        </div>
                        <div class="col-md-2">
                            <label>Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $endDate ?>">
                        </div>
                        <div class="col-md-2">
                            <label>Supplier</label>
                            <select name="id_supplier" class="form-control form-control-sm">
                                <option value="">Semua Supplier</option>
                                <?php foreach ($supplierList as $supplier): ?>
                                    <option value="<?= $supplier->id ?>" <?= $idSupplier == $supplier->id ? 'selected' : '' ?>>
                                        <?= $supplier->nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Status</label>
                            <select name="status_nota" class="form-control form-control-sm">
                                <option value="">Semua Status</option>
                                <option value="0" <?= $statusNota === '0' ? 'selected' : '' ?>>Draft</option>
                                <option value="1" <?= $statusNota === '1' ? 'selected' : '' ?>>Proses</option>
                                <option value="2" <?= $statusNota === '2' ? 'selected' : '' ?>>Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-sm btn-block">
                                <i class="fas fa-search mr-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= number_format($totalTransactions, 0, ',', '.') ?></h3>
                                <p>Total Transaksi</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= number_format($totalPurchase, 0, ',', '.') ?></h3>
                                <p>Total Pembelian</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= number_format($totalPaid, 0, ',', '.') ?></h3>
                                <p>Total Lunas</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?= number_format($totalUnpaid, 0, ',', '.') ?></h3>
                                <p>Total Belum Lunas</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Tanggal</th>
                                <th>No. Faktur</th>
                                <th>Supplier</th>
                                <th>Penerima</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($purchases)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data pembelian</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($purchases as $index => $purchase): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= date('d/m/Y', strtotime($purchase->tgl_masuk)) ?></td>
                                        <td>
                                            <a href="<?= base_url('laporan/purchase/detail/' . $purchase->id) ?>" class="text-primary">
                                                <?= $purchase->no_nota ?>
                                            </a>
                                        </td>
                                        <td><?= $purchase->supplier_nama ?? '-' ?></td>
                                        <td><?= $purchase->penerima_nama ?? '-' ?></td>
                                        <td>
                                            <?php if ($purchase->status_nota == '0'): ?>
                                                <span class="badge badge-warning">Draft</span>
                                            <?php elseif ($purchase->status_nota == '1'): ?>
                                                <span class="badge badge-info">Proses</span>
                                            <?php elseif ($purchase->status_nota == '2'): ?>
                                                <span class="badge badge-success">Selesai</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right"><?= number_format($purchase->jml_gtotal ?? 0, 0, ',', '.') ?></td>
                                        <td>
                                            <a href="<?= base_url('laporan/purchase/detail/' . $purchase->id) ?>" 
                                               class="btn btn-info btn-sm rounded-0" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('laporan/purchase/detail_items/' . $purchase->id) ?>" 
                                               class="btn btn-secondary btn-sm rounded-0" title="Detail Item">
                                                <i class="fas fa-list"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($purchases)): ?>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="5" class="text-right">TOTAL</th>
                                    <th class="text-right"><?= number_format($totalPurchase, 0, ',', '.') ?></th>
                                    <th></th>
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
