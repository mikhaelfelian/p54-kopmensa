<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: View for displaying order reports based on invoice number
 * This file represents the order report index view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shopping-bag mr-1"></i> Laporan Pesanan
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/order/export') ?>?invoice_number=<?= $invoiceNumber ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&id_gudang=<?= $idGudang ?>&id_pelanggan=<?= $idPelanggan ?>&status=<?= $status ?>" 
                       class="btn btn-success btn-sm rounded-0">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="get" action="<?= base_url('laporan/order') ?>" class="mb-4">
                    <div class="row">
                        <div class="col-md-2">
                            <label>No. Invoice</label>
                            <input type="text" name="invoice_number" class="form-control form-control-sm" 
                                   value="<?= $invoiceNumber ?>" placeholder="Cari invoice...">
                        </div>
                        <div class="col-md-2">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $startDate ?>">
                        </div>
                        <div class="col-md-2">
                            <label>Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $endDate ?>">
                        </div>
                        <div class="col-md-2">
                            <label>Outlet</label>
                            <select name="id_gudang" class="form-control form-control-sm">
                                <option value="">Semua Outlet</option>
                                <?php foreach ($gudangList as $gudang): ?>
                                    <option value="<?= $gudang->id ?>" <?= $idGudang == $gudang->id ? 'selected' : '' ?>>
                                        <?= $gudang->nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Pelanggan</label>
                            <select name="id_pelanggan" class="form-control form-control-sm">
                                <option value="">Semua Pelanggan</option>
                                <?php foreach ($pelangganList as $pelanggan): ?>
                                    <option value="<?= $pelanggan->id ?>" <?= $idPelanggan == $pelanggan->id ? 'selected' : '' ?>>
                                        <?= $pelanggan->nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Status</label>
                            <select name="status" class="form-control form-control-sm">
                                <option value="">Semua Status</option>
                                <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Completed</option>
                                <option value="0" <?= $status === '0' ? 'selected' : '' ?>>Pending</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search mr-1"></i> Filter
                            </button>
                            <a href="<?= base_url('laporan/order') ?>" class="btn btn-secondary btn-sm ml-1">
                                <i class="fas fa-refresh mr-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= number_format($summary['total_orders'], 0, ',', '.') ?></h3>
                                <p>Total Pesanan</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= number_format($summary['total_amount'], 0, ',', '.') ?></h3>
                                <p>Total Nilai</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= number_format($summary['completed_orders'], 0, ',', '.') ?></h3>
                                <p>Completed</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?= number_format($summary['pending_orders'], 0, ',', '.') ?></h3>
                                <p>Pending</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clock"></i>
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
                                <th>No. Invoice</th>
                                <th>Pelanggan</th>
                                <th>Outlet</th>
                                <th>Sales</th>
                                <th>Status</th>
                                <th>Metode Bayar</th>
                                <th>Total</th>
                                <th>Items</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="11" class="text-center">Tidak ada data pesanan</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $index => $order): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($order->tgl_masuk)) ?></td>
                                        <td>
                                            <a href="<?= base_url('laporan/order/detail/' . $order->id) ?>" class="text-primary">
                                                <?= $order->no_nota ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?= $order->pelanggan_nama ?: 'Umum' ?>
                                            <?php if ($order->pelanggan_hp): ?>
                                                <br><small class="text-muted"><?= $order->pelanggan_hp ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $order->gudang_nama ?? '-' ?></td>
                                        <td><?= $order->sales_nama ?? '-' ?></td>
                                        <td>
                                            <?php if ($order->status_nota == '1'): ?>
                                                <span class="badge badge-success">Completed</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= ucfirst($order->metode_bayar ?? '-') ?></td>
                                        <td class="text-right"><?= number_format($order->jml_total ?? 0, 0, ',', '.') ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-info"><?= $order->total_items ?? 0 ?></span>
                                            <br><small class="text-muted"><?= $order->total_qty ?? 0 ?> pcs</small>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('laporan/order/detail/' . $order->id) ?>" 
                                               class="btn btn-info btn-sm rounded-0" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($orders)): ?>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="8" class="text-right">TOTAL</th>
                                    <th class="text-right"><?= number_format($summary['total_amount'], 0, ',', '.') ?></th>
                                    <th class="text-center"><?= array_sum(array_column($orders, 'total_items')) ?></th>
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
