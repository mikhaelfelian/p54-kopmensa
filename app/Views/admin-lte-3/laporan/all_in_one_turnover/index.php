<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: View for displaying all-in-one turnover report (comprehensive sales report)
 * This file represents the all-in-one turnover report index view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i> Laporan Omset Terpadu (All-in-One)
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/all-in-one-turnover/export') ?>?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&id_gudang=<?= $idGudang ?>&id_kategori=<?= $idKategori ?>" 
                       class="btn btn-success btn-sm rounded-0">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="get" action="<?= base_url('laporan/all-in-one-turnover') ?>" class="mb-4">
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
                            <select name="id_gudang" class="form-control form-control-sm">
                                <option value="">Semua Outlet</option>
                                <?php foreach ($gudangList as $gudang): ?>
                                    <option value="<?= $gudang->id ?>" <?= $idGudang == $gudang->id ? 'selected' : '' ?>>
                                        <?= $gudang->nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Kategori</label>
                            <select name="id_kategori" class="form-control form-control-sm">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($kategoriList as $kategori): ?>
                                    <option value="<?= $kategori->id ?>" <?= $idKategori == $kategori->id ? 'selected' : '' ?>>
                                        <?= $kategori->kategori ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search mr-1"></i> Filter
                            </button>
                            <a href="<?= base_url('laporan/all-in-one-turnover') ?>" class="btn btn-secondary btn-sm ml-1">
                                <i class="fas fa-refresh mr-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Sales Summary Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= number_format($salesSummary->total_transactions ?? 0, 0, ',', '.') ?></h3>
                                <p>Total Transaksi</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= number_format($salesSummary->total_sales ?? 0, 0, ',', '.') ?></h3>
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
                                <h3><?= number_format($salesSummary->cash_sales ?? 0, 0, ',', '.') ?></h3>
                                <p>Penjualan Cash</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?= number_format($salesSummary->non_cash_sales ?? 0, 0, ',', '.') ?></h3>
                                <p>Penjualan Non-Cash</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts and Tables Row -->
                <div class="row">
                    <!-- Daily Sales Chart -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-line mr-1"></i> Tren Penjualan Harian
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Transaksi</th>
                                                <th>Penjualan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($dailySales)): ?>
                                                <tr>
                                                    <td colspan="3" class="text-center">Tidak ada data</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($dailySales as $daily): ?>
                                                    <tr>
                                                        <td><?= date('d/m/Y', strtotime($daily->sale_date)) ?></td>
                                                        <td class="text-center"><?= number_format($daily->daily_transactions, 0, ',', '.') ?></td>
                                                        <td class="text-right"><?= number_format($daily->daily_sales, 0, ',', '.') ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-credit-card mr-1"></i> Metode Pembayaran
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Metode</th>
                                                <th>Transaksi</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($paymentMethods)): ?>
                                                <tr>
                                                    <td colspan="3" class="text-center">Tidak ada data</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($paymentMethods as $payment): ?>
                                                    <tr>
                                                        <td><?= ucfirst($payment->metode_bayar) ?></td>
                                                        <td class="text-center"><?= number_format($payment->total_transactions, 0, ',', '.') ?></td>
                                                        <td class="text-right"><?= number_format($payment->total_amount, 0, ',', '.') ?></td>
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

                <!-- Top Products and Categories Row -->
                <div class="row">
                    <!-- Top Products -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-trophy mr-1"></i> Produk Terlaris (Top 10)
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Produk</th>
                                                <th>Kategori</th>
                                                <th>Qty</th>
                                                <th>Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($topProducts)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">Tidak ada data</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($topProducts as $index => $product): ?>
                                                    <tr>
                                                        <td><?= $index + 1 ?></td>
                                                        <td>
                                                            <strong><?= $product->item ?></strong>
                                                            <br><small class="text-muted"><?= $product->kode ?></small>
                                                        </td>
                                                        <td><?= $product->kategori ?: '-' ?></td>
                                                        <td class="text-center"><?= number_format($product->total_qty, 0, ',', '.') ?></td>
                                                        <td class="text-right"><?= number_format($product->total_revenue, 0, ',', '.') ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales by Category -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-tags mr-1"></i> Penjualan per Kategori
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Kategori</th>
                                                <th>Qty</th>
                                                <th>Revenue</th>
                                                <th>Transaksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($categorySales)): ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">Tidak ada data</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($categorySales as $category): ?>
                                                    <tr>
                                                        <td><?= $category->kategori ?: 'Tidak Berkategori' ?></td>
                                                        <td class="text-center"><?= number_format($category->total_qty, 0, ',', '.') ?></td>
                                                        <td class="text-right"><?= number_format($category->total_revenue, 0, ',', '.') ?></td>
                                                        <td class="text-center"><?= number_format($category->total_transactions, 0, ',', '.') ?></td>
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

                <!-- Warehouse Sales -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-store mr-1"></i> Penjualan per Outlet
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Outlet</th>
                                                <th>Total Transaksi</th>
                                                <th>Total Penjualan</th>
                                                <th>Rata-rata per Transaksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($warehouseSales)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">Tidak ada data</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($warehouseSales as $index => $warehouse): ?>
                                                    <tr>
                                                        <td><?= $index + 1 ?></td>
                                                        <td><?= $warehouse->gudang_nama ?: 'Tidak Diketahui' ?></td>
                                                        <td class="text-center"><?= number_format($warehouse->total_transactions, 0, ',', '.') ?></td>
                                                        <td class="text-right"><?= number_format($warehouse->total_sales, 0, ',', '.') ?></td>
                                                        <td class="text-right">
                                                            <?= $warehouse->total_transactions > 0 ? number_format($warehouse->total_sales / $warehouse->total_transactions, 0, ',', '.') : 0 ?>
                                                        </td>
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
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
