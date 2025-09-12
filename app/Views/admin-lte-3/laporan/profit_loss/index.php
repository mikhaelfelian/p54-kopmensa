<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: View for displaying profit and loss report
 * This file represents the profit loss report index view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-1"></i> Laporan Laba Rugi
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/profit-loss/export') ?>?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&id_gudang=<?= $idGudang ?>" 
                       class="btn btn-success btn-sm rounded-0">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="get" action="<?= base_url('laporan/profit-loss') ?>" class="mb-4">
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
                                <h3><?= number_format($profitLoss['total_revenue'], 0, ',', '.') ?></h3>
                                <p>Total Pendapatan</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= number_format($profitLoss['gross_profit'], 0, ',', '.') ?></h3>
                                <p>Laba Kotor</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= number_format($profitLoss['total_expenses'], 0, ',', '.') ?></h3>
                                <p>Total Biaya</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box <?= $profitLoss['net_profit'] >= 0 ? 'bg-success' : 'bg-danger' ?>">
                            <div class="inner">
                                <h3><?= number_format($profitLoss['net_profit'], 0, ',', '.') ?></h3>
                                <p><?= $profitLoss['net_profit'] >= 0 ? 'Laba Bersih' : 'Rugi Bersih' ?></p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-<?= $profitLoss['net_profit'] >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profit & Loss Statement -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-calculator mr-1"></i> Laporan Laba Rugi
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="60%">Keterangan</th>
                                                <th width="40%" class="text-right">Jumlah (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- PENDAPATAN -->
                                            <tr class="bg-info text-white">
                                                <td><strong>PENDAPATAN</strong></td>
                                                <td class="text-right"><strong><?= number_format($profitLoss['total_revenue'], 0, ',', '.') ?></strong></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;&nbsp;Penjualan</td>
                                                <td class="text-right"><?= number_format($profitLoss['total_revenue'], 0, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;&nbsp;Pendapatan Lainnya</td>
                                                <td class="text-right"><?= number_format($profitLoss['other_income'], 0, ',', '.') ?></td>
                                            </tr>
                                            
                                            <!-- HPP -->
                                            <tr class="bg-warning text-white">
                                                <td><strong>HARGA POKOK PENJUALAN (HPP)</strong></td>
                                                <td class="text-right"><strong><?= number_format($profitLoss['total_cogs'], 0, ',', '.') ?></strong></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;&nbsp;HPP</td>
                                                <td class="text-right"><?= number_format($profitLoss['total_cogs'], 0, ',', '.') ?></td>
                                            </tr>
                                            
                                            <!-- LABA KOTOR -->
                                            <tr class="bg-success text-white">
                                                <td><strong>LABA KOTOR</strong></td>
                                                <td class="text-right"><strong><?= number_format($profitLoss['gross_profit'], 0, ',', '.') ?></strong></td>
                                            </tr>
                                            
                                            <!-- BIAYA OPERASIONAL -->
                                            <tr class="bg-danger text-white">
                                                <td><strong>BIAYA OPERASIONAL</strong></td>
                                                <td class="text-right"><strong><?= number_format($profitLoss['total_expenses'], 0, ',', '.') ?></strong></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;&nbsp;Biaya Operasional</td>
                                                <td class="text-right"><?= number_format($profitLoss['total_expenses'], 0, ',', '.') ?></td>
                                            </tr>
                                            
                                            <!-- LABA/RUGI BERSIH -->
                                            <tr class="<?= $profitLoss['net_profit'] >= 0 ? 'bg-success' : 'bg-danger' ?> text-white">
                                                <td><strong><?= $profitLoss['net_profit'] >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' ?></strong></td>
                                                <td class="text-right"><strong><?= number_format($profitLoss['net_profit'], 0, ',', '.') ?></strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Key Metrics -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie mr-1"></i> Metrik Kunci
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Gross Margin</strong></td>
                                            <td class="text-right">
                                                <span class="badge badge-info"><?= number_format($profitLoss['gross_margin'], 2) ?>%</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Net Margin</strong></td>
                                            <td class="text-right">
                                                <span class="badge <?= $profitLoss['net_margin'] >= 0 ? 'badge-success' : 'badge-danger' ?>">
                                                    <?= number_format($profitLoss['net_margin'], 2) ?>%
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Transaksi</strong></td>
                                            <td class="text-right"><?= number_format($profitLoss['total_transactions'], 0, ',', '.') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Pembelian</strong></td>
                                            <td class="text-right"><?= number_format($profitLoss['total_purchases'], 0, ',', '.') ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Trend -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-bar mr-1"></i> Tren Bulanan
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Bulan</th>
                                                <th class="text-right">Pendapatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($monthlyTrend)): ?>
                                                <tr>
                                                    <td colspan="2" class="text-center">Tidak ada data</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($monthlyTrend as $month): ?>
                                                    <tr>
                                                        <td><?= date('M Y', mktime(0, 0, 0, $month->month, 1, $month->year)) ?></td>
                                                        <td class="text-right"><?= number_format($month->monthly_revenue, 0, ',', '.') ?></td>
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
