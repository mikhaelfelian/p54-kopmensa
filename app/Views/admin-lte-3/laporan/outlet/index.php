<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: View for displaying outlet reports
 * This file represents the outlet report index view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-1"></i> Laporan Outlet
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/outlet/export_excel') ?>?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&id_outlet=<?= $idOutlet ?>" 
                       class="btn btn-success btn-sm rounded-0">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="get" action="<?= base_url('laporan/outlet') ?>" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $startDate ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $endDate ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Outlet</label>
                            <select name="id_outlet" class="form-control form-control-sm">
                                <option value="">Semua Outlet</option>
                                <?php foreach ($outletList as $outlet): ?>
                                    <option value="<?= $outlet->id ?>" <?= $idOutlet == $outlet->id ? 'selected' : '' ?>>
                                        <?= $outlet->gudang ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
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
                                <h3><?= number_format($totalOutlets, 0, ',', '.') ?></h3>
                                <p>Total Outlet</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-store"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= number_format($totalSales, 0, ',', '.') ?></h3>
                                <p>Total Penjualan</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= number_format($totalTransactions, 0, ',', '.') ?></h3>
                                <p>Total Transaksi</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?= number_format($totalItems, 0, ',', '.') ?></h3>
                                <p>Total Item</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Outlet Cards -->
                <div class="row">
                    <?php foreach ($outletDetails as $detail): ?>
                        <div class="col-lg-6 col-md-12 mb-4">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-store mr-1"></i> <?= $detail['outlet']->gudang ?>
                                    </h3>
                                    <div class="card-tools">
                                        <a href="<?= base_url('laporan/outlet/detail/' . $detail['outlet']->id) ?>" 
                                           class="btn btn-info btn-sm rounded-0">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6><i class="fas fa-chart-line mr-1"></i> Penjualan</h6>
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td>Total Transaksi</td>
                                                    <td>: <?= number_format($detail['sales']->total_transactions ?? 0, 0, ',', '.') ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Total Penjualan</td>
                                                    <td>: <?= number_format($detail['sales']->total_sales ?? 0, 0, ',', '.') ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Rata-rata Penjualan</td>
                                                    <td>: <?= number_format($detail['sales']->avg_sales ?? 0, 0, ',', '.') ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Pelanggan Unik</td>
                                                    <td>: <?= number_format($detail['sales']->unique_customers ?? 0, 0, ',', '.') ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6><i class="fas fa-boxes mr-1"></i> Stok</h6>
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td>Total Item</td>
                                                    <td>: <?= number_format($detail['stock']->total_items ?? 0, 0, ',', '.') ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Total Stok</td>
                                                    <td>: <?= number_format($detail['stock']->total_stock ?? 0, 0, ',', '.') ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Item Ada Stok</td>
                                                    <td>: <?= number_format($detail['stock']->in_stock_items ?? 0, 0, ',', '.') ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Item Habis</td>
                                                    <td>: <?= number_format($detail['stock']->out_of_stock_items ?? 0, 0, ',', '.') ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Top Selling Items -->
                                    <?php if (!empty($detail['top_items'])): ?>
                                        <hr>
                                        <h6><i class="fas fa-trophy mr-1"></i> Top 5 Item Terjual</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Item</th>
                                                        <th class="text-center">Qty</th>
                                                        <th class="text-right">Nilai</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($detail['top_items'] as $item): ?>
                                                        <tr>
                                                            <td><?= $item->item_nama ?? '-' ?></td>
                                                            <td class="text-center"><?= number_format($item->total_qty ?? 0, 0, ',', '.') ?></td>
                                                            <td class="text-right"><?= number_format($item->total_value ?? 0, 0, ',', '.') ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
