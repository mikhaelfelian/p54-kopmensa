<?= $this->extend(theme_path('main')) ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url('public/assets/theme/admin-lte-3/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Info boxes -->
<div class="row">
    <!-- fix for small devices only -->
    <div class="clearfix hidden-md-up"></div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Penjualan Lunas</span>
                <span class="info-box-number"><?= format_angka($totalPaidSalesTransactions) ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-money-bill-wave"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Total Pendapatan</span>
                <span class="info-box-number">Rp <?= format_angka($totalRevenue) ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-shopping-bag"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Pembelian Lunas</span>
                <span class="info-box-number"><?= format_angka($totalPaidPurchaseTransactions) ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-chart-line"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Total Laba</span>
                <span class="info-box-number">Rp <?= format_angka($totalProfit) ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Laporan Penjualan 12 Bulan Terakhir</h5>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-wrench"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                            <a href="#" class="dropdown-item">Export PDF</a>
                            <a href="#" class="dropdown-item">Export Excel</a>
                            <a href="#" class="dropdown-item">Print</a>
                            <a class="dropdown-divider"></a>
                            <a href="#" class="dropdown-item">Refresh Data</a>
                        </div>
                    </div>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="text-center">
                            <strong>Grafik Penjualan: <?= date('M Y', strtotime('-11 months')) ?> - <?= date('M Y') ?></strong>
                        </p>

                        <div class="chart">
                            <!-- Sales Chart Canvas -->
                            <canvas id="salesChart" height="180" style="height: 180px;"></canvas>
                        </div>
                        <!-- /.chart-responsive -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-4">
                        <p class="text-center">
                            <strong>Target Penjualan Bulan Ini</strong>
                        </p>

                        <div class="progress-group">
                            Target Bulanan
                            <span class="float-right"><b>Rp <?= format_angka($currentMonthSales) ?></b> / Rp <?= format_angka($monthlyTarget) ?></span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" style="width: <?= min($monthlyProgress, 100) ?>%"></div>
                            </div>
                        </div>
                        <!-- /.progress-group -->

                        <div class="progress-group">
                            Target Harian Hari Ini
                            <span class="float-right"><b>Rp <?= format_angka($todaySales) ?></b> / Rp <?= format_angka($dailyTarget) ?></span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success" style="width: <?= min($dailyProgress, 100) ?>%"></div>
                            </div>
                        </div>

                        <!-- /.progress-group -->
                        <div class="progress-group">
                            <span class="progress-text">Rata-rata Order</span>
                            <span class="float-right"><b>Rp <?= format_angka($avgOrderValue) ?></b></span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-info" style="width: <?= min(($avgOrderValue / 1000000) * 10, 100) ?>%"></div>
                            </div>
                        </div>

                        <!-- /.progress-group -->
                        <div class="progress-group">
                            Pertumbuhan vs Bulan Lalu
                            <span class="float-right"><b><?= number_format($salesGrowth, 1) ?>%</b></span>
                            <div class="progress progress-sm">
                                <div class="progress-bar <?= $salesGrowth >= 0 ? 'bg-success' : 'bg-danger' ?>" style="width: <?= min(abs($salesGrowth), 100) ?>%"></div>
                            </div>
                        </div>
                        <!-- /.progress-group -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- ./card-body -->
            <div class="card-footer">
                <div class="row">
                    <div class="col-sm-3 col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage <?= $salesGrowth >= 0 ? 'text-success' : 'text-danger' ?>">
                                <i class="fas fa-caret-<?= $salesGrowth >= 0 ? 'up' : 'down' ?>"></i>
                                <?= number_format(abs($salesGrowth), 1) ?>%</span>
                            <h5 class="description-header">Rp <?= format_angka($totalRevenue) ?></h5>
                            <span class="description-text">TOTAL PENDAPATAN</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-danger"><i class="fas fa-caret-down"></i>
                                <?= $totalRevenue > 0 ? number_format(($totalExpenses / $totalRevenue) * 100, 1) : 0 ?>%</span>
                            <h5 class="description-header">Rp <?= format_angka($totalExpenses) ?></h5>
                            <span class="description-text">TOTAL BIAYA</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage <?= $totalProfit >= 0 ? 'text-success' : 'text-danger' ?>">
                                <i class="fas fa-caret-<?= $totalProfit >= 0 ? 'up' : 'down' ?>"></i>
                                <?= $totalRevenue > 0 ? number_format(($totalProfit / $totalRevenue) * 100, 1) : 0 ?>%</span>
                            <h5 class="description-header">Rp <?= format_angka($totalProfit) ?></h5>
                            <span class="description-text">TOTAL LABA</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-6">
                        <div class="description-block">
                            <span class="description-percentage text-success"><i class="fas fa-caret-up"></i>
                                <?= number_format($monthlyProgress, 1) ?>%</span>
                            <h5 class="description-header"><?= $totalPaidSalesTransactions ?></h5>
                            <span class="description-text">TRANSAKSI LUNAS</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.card-footer -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <div class="col-md-8">
        <!-- SALES BY CATEGORY -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Penjualan per Kategori</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <div class="d-md-flex">
                    <div class="p-1 flex-fill" style="overflow: hidden">
                        <!-- Category Chart will be created here -->
                        <div class="chart-responsive">
                            <canvas id="categoryChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                    <div class="card-pane-right bg-success pt-2 pb-2 pl-4 pr-4">
                        <?php foreach (array_slice($salesByCategory, 0, 3) as $index => $category): ?>
                        <div class="description-block mb-4">
                            <h5 class="description-header text-white"><?= format_angka($category->total_sales) ?></h5>
                            <span class="description-text text-white"><?= esc($category->kategori) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div><!-- /.card-pane-right -->
                </div><!-- /.d-md-flex -->
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
        
        <div class="row">
            <div class="col-md-12">
                <!-- TOP SELLING PRODUCTS -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Produk Terlaris</h3>

                        <div class="card-tools">
                            <span class="badge badge-success"><?= count($topSellingProducts) ?> Produk Teratas</span>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table m-0">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Terjual</th>
                                        <th>Transaksi</th>
                                        <th>Total Penjualan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($topSellingProducts)): ?>
                                        <?php foreach ($topSellingProducts as $product): ?>
                                            <tr>
                                                <td><?= esc($product->produk) ?></td>
                                                <td><span class="badge badge-success"><?= format_angka($product->total_qty) ?></span></td>
                                                <td><?= format_angka($product->transactions) ?></td>
                                                <td>Rp <?= format_angka($product->total_sales) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Belum ada data penjualan produk</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer text-center">
                        <a href="<?= base_url('master/item') ?>">Lihat Semua Produk</a>
                    </div>
                    <!-- /.card-footer -->
                </div>
                <!--/.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- TABLE: RECENT PAID TRANSACTIONS -->
        <div class="card">
            <div class="card-header border-transparent">
                <h3 class="card-title">Transaksi Lunas Terbaru</h3>

                <div class="card-tools">
                    <span class="badge badge-success"><?= $totalPaidSalesTransactions ?> Penjualan</span>
                    <span class="badge badge-danger ml-1"><?= $totalPaidPurchaseTransactions ?> Pembelian</span>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table m-0">
                        <thead>
                            <tr>
                                <th>Tipe</th>
                                <th>No. Nota</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Combine and sort recent transactions
                            $allRecentTransactions = [];
                            
                            // Add sales transactions
                            foreach ($recentSalesTransactions as $transaction) {
                                $allRecentTransactions[] = [
                                    'type' => 'Penjualan',
                                    'type_class' => 'success',
                                    'nota' => $transaction->no_nota,
                                    'date' => $transaction->tgl_masuk,
                                    'total' => $transaction->jml_gtotal,
                                    'id' => $transaction->id,
                                    'url' => base_url('transaksi/jual/detail/' . $transaction->id)
                                ];
                            }
                            
                            // Add purchase transactions
                            foreach ($recentPurchaseTransactions as $transaction) {
                                $allRecentTransactions[] = [
                                    'type' => 'Pembelian',
                                    'type_class' => 'danger',
                                    'nota' => $transaction->no_nota,
                                    'date' => $transaction->tgl_masuk,
                                    'total' => $transaction->jml_gtotal,
                                    'id' => $transaction->id,
                                    'url' => base_url('transaksi/beli/detail/' . $transaction->id)
                                ];
                            }
                            
                            // Sort by date (newest first)
                            usort($allRecentTransactions, function($a, $b) {
                                return strtotime($b['date']) - strtotime($a['date']);
                            });
                            
                            // Take only the first 5
                            $allRecentTransactions = array_slice($allRecentTransactions, 0, 5);
                            ?>
                            
                            <?php if (!empty($allRecentTransactions)): ?>
                                <?php foreach ($allRecentTransactions as $transaction): ?>
                                    <tr>
                                        <td><span class="badge badge-<?= $transaction['type_class'] ?>"><?= $transaction['type'] ?></span></td>
                                        <td><a href="<?= $transaction['url'] ?>"><?= esc($transaction['nota']) ?></a></td>
                                        <td><?= date('d/m/Y H:i', strtotime($transaction['date'])) ?></td>
                                        <td>Rp <?= format_angka($transaction['total']) ?></td>
                                        <td><span class="badge badge-success">Lunas</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada transaksi lunas</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                <a href="<?= base_url('transaksi/jual/cashier') ?>" class="btn btn-sm btn-success float-left">Kasir Baru</a>
                <a href="<?= base_url('transaksi/beli/create') ?>" class="btn btn-sm btn-danger float-left ml-2">Pembelian Baru</a>
                <a href="<?= base_url('transaksi/jual') ?>" class="btn btn-sm btn-secondary float-right">Lihat Semua Transaksi</a>
            </div>
            <!-- /.card-footer -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->

    <div class="col-md-4">
        <!-- Info Boxes Style 2 -->
        <div class="info-box mb-3 bg-warning">
            <span class="info-box-icon"><i class="fas fa-tag"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Persediaan</span>
                <span class="info-box-number"><?= format_angka($totalStock) ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box mb-3 bg-success">
            <span class="info-box-icon"><i class="far fa-heart"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Pelanggan Baru</span>
                <span class="info-box-number"><?= format_angka($totalLikes) ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box mb-3 bg-danger">
            <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Total Transaksi</span>
                <span class="info-box-number"><?= format_angka($totalMentions) ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box mb-3 bg-info">
            <span class="info-box-icon"><i class="far fa-user"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Total Pelanggan</span>
                <span class="info-box-number"><?= format_angka($totalDirectMessages) ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Penjualan Harian (Bulan Ini)</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="chart-responsive">
                    <canvas id="dailyChart" height="200"></canvas>
                </div>
                <!-- ./chart-responsive -->
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

        <!-- PRODUCT LIST -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Produk Terbaru</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <li class="item">
                                <div class="product-img">
                                    <?php if (!empty($item->foto) && file_exists(FCPATH . 'public/assets/images/item/' . $item->foto)): ?>
                                        <img src="<?= base_url('public/assets/images/item/' . $item->foto) ?>" alt="<?= esc($item->item) ?>" class="img-size-50">
                                    <?php else: ?>
                                        <img src="<?= base_url('public/assets/theme/admin-lte-3/dist/img/default-150x150.png') ?>" alt="<?= esc($item->item) ?>" class="img-size-50">
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <a href="javascript:void(0)" class="product-title"><?= esc($item->item) ?>
                                        <span class="badge badge-success float-right"><?= 'Rp ' . number_format($item->harga_jual, 0, ',', '.') ?></span>
                                    </a>
                                    <span class="product-description">
                                        <?= esc($item->kategori ?? 'Tidak berkategori') ?> | <?= esc($item->merk ?? 'Tidak bermerk') ?>
                                        <?php if (!empty($item->deskripsi)): ?>
                                            <br><?= esc(substr($item->deskripsi, 0, 50)) ?><?= strlen($item->deskripsi) > 50 ? '...' : '' ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="item">
                            <div class="product-info">
                                <span class="product-description text-muted">Tidak ada produk aktif</span>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- /.card-body -->
            <div class="card-footer text-center">
                <a href="<?= base_url('master/item') ?>" class="uppercase">Lihat Semua Produk</a>
            </div>
            <!-- /.card-footer -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- REQUIRED SCRIPTS -->
<!-- overlayScrollbars -->
<script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>

<!-- PAGE PLUGINS -->
<!-- jQuery Mapael -->
<script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/jquery-mousewheel/jquery.mousewheel.js') ?>"></script>
<script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/raphael/raphael.min.js') ?>"></script>
<script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/jquery-mapael/jquery.mapael.min.js') ?>"></script>
<script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/jquery-mapael/maps/usa_states.min.js') ?>"></script>

<!-- ChartJS -->
<script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/chart.js/Chart.min.js') ?>"></script>

<script>
$(document).ready(function() {
    // Monthly Sales Chart
    var salesChartData = {
        labels: [<?php foreach($monthlySalesData as $data): ?>'<?= $data['month'] ?>',<?php endforeach; ?>],
        datasets: [{
            label: 'Penjualan',
            backgroundColor: 'rgba(60,141,188,0.9)',
            borderColor: 'rgba(60,141,188,0.8)',
            pointRadius: false,
            pointColor: '#3b8bba',
            pointStrokeColor: 'rgba(60,141,188,1)',
            pointHighlightFill: '#fff',
            pointHighlightStroke: 'rgba(60,141,188,1)',
            data: [<?php foreach($monthlySalesData as $data): ?><?= $data['total'] ?>,<?php endforeach; ?>]
        }]
    };

    var salesChartCanvas = $('#salesChart').get(0).getContext('2d');
    var salesChart = new Chart(salesChartCanvas, {
        type: 'line',
        data: salesChartData,
        options: {
            maintainAspectRatio: false,
            responsive: true,
            legend: {
                display: false
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                    }
                }],
                yAxes: [{
                    gridLines: {
                        display: false,
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }]
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        return 'Penjualan: Rp ' + tooltipItem.yLabel.toLocaleString('id-ID');
                    }
                }
            }
        }
    });

    // Daily Sales Chart
    var dailyChartData = {
        labels: [<?php foreach($dailySalesData as $data): ?>'<?= $data['day'] ?>',<?php endforeach; ?>],
        datasets: [{
            label: 'Penjualan Harian',
            backgroundColor: 'rgba(210, 214, 222, 1)',
            borderColor: 'rgba(210, 214, 222, 1)',
            pointRadius: false,
            pointColor: 'rgba(210, 214, 222, 1)',
            pointStrokeColor: '#c1c7d1',
            pointHighlightFill: '#fff',
            pointHighlightStroke: 'rgba(220,220,220,1)',
            data: [<?php foreach($dailySalesData as $data): ?><?= $data['total'] ?>,<?php endforeach; ?>]
        }]
    };

    var dailyChartCanvas = $('#dailyChart').get(0).getContext('2d');
    var dailyChart = new Chart(dailyChartCanvas, {
        type: 'line',
        data: dailyChartData,
        options: {
            maintainAspectRatio: false,
            responsive: true,
            legend: {
                display: false
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                    }
                }],
                yAxes: [{
                    gridLines: {
                        display: false,
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }]
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        return 'Hari ' + tooltipItem.xLabel + ': Rp ' + tooltipItem.yLabel.toLocaleString('id-ID');
                    }
                }
            }
        }
    });

    // Category Chart (Doughnut)
    var categoryChartData = {
        labels: [<?php foreach($salesByCategory as $category): ?>'<?= esc($category->kategori) ?>',<?php endforeach; ?>],
        datasets: [{
            data: [<?php foreach($salesByCategory as $category): ?><?= $category->total_sales ?>,<?php endforeach; ?>],
            backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de']
        }]
    };

    var categoryChartCanvas = $('#categoryChart').get(0).getContext('2d');
    var categoryChart = new Chart(categoryChartCanvas, {
        type: 'doughnut',
        data: categoryChartData,
        options: {
            maintainAspectRatio: false,
            responsive: true,
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.labels[tooltipItem.index];
                        var value = data.datasets[0].data[tooltipItem.index];
                        return label + ': Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    });
});
</script>

<!-- AdminLTE for demo purposes -->
<script src="<?= base_url('public/assets/theme/admin-lte-3/dist/js/demo.js') ?>"></script>
<?= $this->endSection() ?>