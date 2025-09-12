<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: View for displaying best-selling products report
 * This file represents the best selling report index view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-trophy mr-1"></i> Laporan Produk Terlaris
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/best-selling/export') ?>?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&id_gudang=<?= $idGudang ?>&id_kategori=<?= $idKategori ?>&id_merk=<?= $idMerk ?>&limit=<?= $limit ?>" 
                       class="btn btn-success btn-sm rounded-0">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="get" action="<?= base_url('laporan/best-selling') ?>" class="mb-4">
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
                        <div class="col-md-2">
                            <label>Merk</label>
                            <select name="id_merk" class="form-control form-control-sm">
                                <option value="">Semua Merk</option>
                                <?php foreach ($merkList as $merk): ?>
                                    <option value="<?= $merk->id ?>" <?= $idMerk == $merk->id ? 'selected' : '' ?>>
                                        <?= $merk->merk ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Limit</label>
                            <select name="limit" class="form-control form-control-sm">
                                <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>Top 10</option>
                                <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>Top 25</option>
                                <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>Top 50</option>
                                <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>Top 100</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search mr-1"></i> Filter
                            </button>
                            <a href="<?= base_url('laporan/best-selling') ?>" class="btn btn-secondary btn-sm ml-1">
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
                                <h3><?= number_format($summary['total_products'], 0, ',', '.') ?></h3>
                                <p>Total Produk</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-box"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= number_format($summary['total_qty_sold'], 0, ',', '.') ?></h3>
                                <p>Total Qty Terjual</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= number_format($summary['total_revenue'], 0, ',', '.') ?></h3>
                                <p>Total Revenue</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?= number_format($summary['avg_profit_margin'], 2) ?>%</h3>
                                <p>Rata-rata Margin</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Best Selling Products Table -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-trophy mr-1"></i> Daftar Produk Terlaris
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th width="5%">Rank</th>
                                                <th>Produk</th>
                                                <th>Kategori</th>
                                                <th>Merk</th>
                                                <th class="text-center">Qty Terjual</th>
                                                <th class="text-right">Revenue</th>
                                                <th class="text-right">Profit</th>
                                                <th class="text-center">Margin</th>
                                                <th class="text-center">Transaksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($bestSellingProducts)): ?>
                                                <tr>
                                                    <td colspan="9" class="text-center">Tidak ada data produk</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($bestSellingProducts as $product): ?>
                                                    <tr>
                                                        <td class="text-center">
                                                            <span class="badge badge-<?= $product->rank <= 3 ? 'warning' : 'secondary' ?>">
                                                                #<?= $product->rank ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong><?= $product->item ?></strong>
                                                            <br><small class="text-muted"><?= $product->kode ?></small>
                                                            <?php if ($product->barcode): ?>
                                                                <br><small class="text-info"><?= $product->barcode ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= $product->kategori ?: '-' ?></td>
                                                        <td><?= $product->merk ?: '-' ?></td>
                                                        <td class="text-center"><?= number_format($product->total_qty_sold, 0, ',', '.') ?></td>
                                                        <td class="text-right"><?= number_format($product->total_revenue, 0, ',', '.') ?></td>
                                                        <td class="text-right"><?= number_format($product->total_profit, 0, ',', '.') ?></td>
                                                        <td class="text-center">
                                                            <span class="badge badge-<?= $product->profit_margin >= 20 ? 'success' : ($product->profit_margin >= 10 ? 'warning' : 'danger') ?>">
                                                                <?= number_format($product->profit_margin, 1) ?>%
                                                            </span>
                                                        </td>
                                                        <td class="text-center"><?= number_format($product->total_transactions, 0, ',', '.') ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Breakdown -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie mr-1"></i> Breakdown per Kategori
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Kategori</th>
                                                <th class="text-center">Produk</th>
                                                <th class="text-right">Qty</th>
                                                <th class="text-right">Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($categoryBreakdown)): ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">Tidak ada data</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($categoryBreakdown as $category => $data): ?>
                                                    <tr>
                                                        <td><?= $category ?></td>
                                                        <td class="text-center"><?= $data['product_count'] ?></td>
                                                        <td class="text-right"><?= number_format($data['total_qty'], 0, ',', '.') ?></td>
                                                        <td class="text-right"><?= number_format($data['total_revenue'], 0, ',', '.') ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Top 5 Products -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-medal mr-1"></i> Top 5 Produk
                                </h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($bestSellingProducts)): ?>
                                    <?php foreach (array_slice($bestSellingProducts, 0, 5) as $index => $product): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <strong>#<?= $index + 1 ?> <?= $product->item ?></strong>
                                                <br><small class="text-muted"><?= number_format($product->total_qty_sold, 0, ',', '.') ?> qty</small>
                                            </div>
                                            <div class="text-right">
                                                <span class="badge badge-<?= $index < 3 ? 'warning' : 'secondary' ?>">
                                                    <?= number_format($product->total_revenue, 0, ',', '.') ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-center text-muted">Tidak ada data</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
