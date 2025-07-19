<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<!-- Info boxes -->
<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cog"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Lalu Lintas CPU</span>
                <span class="info-box-number">
                    10
                    <small>%</small>
                </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-thumbs-up"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Suka</span>
                <span class="info-box-number">41,410</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->

    <!-- fix for small devices only -->
    <div class="clearfix hidden-md-up"></div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Transaksi Lunas</span>
                <span class="info-box-number"><?= number_format($totalPaidTransactions) ?></span>
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
                <span class="info-box-number">Rp <?= number_format($totalRevenue) ?></span>
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
                <h5 class="card-title">Laporan Bulanan</h5>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-wrench"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                            <a href="#" class="dropdown-item">Aksi</a>
                            <a href="#" class="dropdown-item">Aksi Lainnya</a>
                            <a href="#" class="dropdown-item">Lainnya</a>
                            <a class="dropdown-divider"></a>
                            <a href="#" class="dropdown-item">Tautan Terpisah</a>
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
                            <strong>Penjualan: 1 Jan, 2014 - 30 Jul, 2014</strong>
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
                            <strong>Pencapaian Tujuan</strong>
                        </p>

                        <div class="progress-group">
                            Tambah Produk ke Keranjang
                            <span class="float-right"><b>160</b>/200</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" style="width: 80%"></div>
                            </div>
                        </div>
                        <!-- /.progress-group -->

                        <div class="progress-group">
                            Selesaikan Pembelian
                            <span class="float-right"><b>310</b>/400</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-danger" style="width: 75%"></div>
                            </div>
                        </div>

                        <!-- /.progress-group -->
                        <div class="progress-group">
                            <span class="progress-text">Kunjungi Halaman Premium</span>
                            <span class="float-right"><b>480</b>/800</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success" style="width: 60%"></div>
                            </div>
                        </div>

                        <!-- /.progress-group -->
                        <div class="progress-group">
                            Kirim Pertanyaan
                            <span class="float-right"><b>250</b>/500</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning" style="width: 50%"></div>
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
                            <span class="description-percentage text-success"><i class="fas fa-caret-up"></i>
                                100%</span>
                            <h5 class="description-header">Rp <?= number_format($totalRevenue) ?></h5>
                            <span class="description-text">TOTAL PENDAPATAN</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i>
                                0%</span>
                            <h5 class="description-header">Rp 10.390.900</h5>
                            <span class="description-text">TOTAL BIAYA</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-success"><i class="fas fa-caret-up"></i>
                                20%</span>
                            <h5 class="description-header">Rp 24.813.530</h5>
                            <span class="description-text">TOTAL LABA</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-6">
                        <div class="description-block">
                            <span class="description-percentage text-danger"><i class="fas fa-caret-down"></i>
                                18%</span>
                            <h5 class="description-header">1200</h5>
                            <span class="description-text">TUJUAN TERCAPAI</span>
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
        <!-- MAP & BOX PANE -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Laporan Pengunjung</h3>

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
                        <!-- Map will be created here -->
                        <div id="world-map-markers" style="height: 325px; overflow: hidden">
                            <div class="map"></div>
                        </div>
                    </div>
                    <div class="card-pane-right bg-success pt-2 pb-2 pl-4 pr-4">
                        <div class="description-block mb-4">
                            <div class="sparkbar pad" data-color="#fff">90,70,90,70,75,80,70</div>
                            <h5 class="description-header">8390</h5>
                            <span class="description-text">Kunjungan</span>
                        </div>
                        <!-- /.description-block -->
                        <div class="description-block mb-4">
                            <div class="sparkbar pad" data-color="#fff">90,50,90,70,61,83,63</div>
                            <h5 class="description-header">30%</h5>
                            <span class="description-text">Referral</span>
                        </div>
                        <!-- /.description-block -->
                        <div class="description-block">
                            <div class="sparkbar pad" data-color="#fff">90,50,90,70,61,83,63</div>
                            <h5 class="description-header">70%</h5>
                            <span class="description-text">Organik</span>
                        </div>
                        <!-- /.description-block -->
                    </div><!-- /.card-pane-right -->
                </div><!-- /.d-md-flex -->
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
        <div class="row">
            <div class="col-md-12">
                <!-- USERS LIST -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Anggota Terbaru</h3>

                        <div class="card-tools">
                            <span class="badge badge-danger">8 Anggota Baru</span>
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
                        <ul class="users-list clearfix">
                            <li>
                                <img src="<?= base_url('public/assets/theme/admin-lte-3/dist/img/user1-128x128.jpg') ?>" alt="User Image">
                                <a class="users-list-name" href="#">Alexander Pierce</a>
                                <span class="users-list-date">Hari Ini</span>
                            </li>
                            <li>
                                <img src="<?= base_url('public/assets/theme/admin-lte-3/dist/img/user8-128x128.jpg') ?>" alt="User Image">
                                <a class="users-list-name" href="#">Norman</a>
                                <span class="users-list-date">Kemarin</span>
                            </li>
                            <li>
                                <img src="<?= base_url('public/assets/theme/admin-lte-3/dist/img/user7-128x128.jpg') ?>" alt="User Image">
                                <a class="users-list-name" href="#">Jane</a>
                                <span class="users-list-date">12 Jan</span>
                            </li>
                            <li>
                                <img src="<?= base_url('public/assets/theme/admin-lte-3/dist/img/user6-128x128.jpg') ?>" alt="User Image">
                                <a class="users-list-name" href="#">John</a>
                                <span class="users-list-date">12 Jan</span>
                            </li>
                            <li>
                                <img src="<?= base_url('public/assets/theme/admin-lte-3/dist/img/user2-160x160.jpg') ?>" alt="User Image">
                                <a class="users-list-name" href="#">Alexander</a>
                                <span class="users-list-date">13 Jan</span>
                            </li>
                            <li>
                                <img src="<?= base_url('public/assets/theme/admin-lte-3/dist/img/user5-128x128.jpg') ?>" alt="User Image">
                                <a class="users-list-name" href="#">Sarah</a>
                                <span class="users-list-date">14 Jan</span>
                            </li>
                            <li>
                                <img src="<?= base_url('public/assets/theme/admin-lte-3/dist/img/user4-128x128.jpg') ?>" alt="User Image">
                                <a class="users-list-name" href="#">Nora</a>
                                <span class="users-list-date">15 Jan</span>
                            </li>
                            <li>
                                <img src="<?= base_url('public/assets/theme/admin-lte-3/dist/img/user3-128x128.jpg') ?>" alt="User Image">
                                <a class="users-list-name" href="#">Nadia</a>
                                <span class="users-list-date">15 Jan</span>
                            </li>
                        </ul>
                        <!-- /.users-list -->
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer text-center">
                        <a href="javascript:">Lihat Semua Pengguna</a>
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
                    <span class="badge badge-success"><?= $totalPaidTransactions ?> Transaksi</span>
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
                                <th>No. Nota</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($paidTransactions)): ?>
                                <?php foreach (array_slice($paidTransactions, 0, 5) as $transaction): ?>
                                    <tr>
                                        <td><a href="<?= base_url('transaksi/jual/detail/' . $transaction->id) ?>"><?= esc($transaction->no_nota) ?></a></td>
                                        <td><?= date('d/m/Y H:i', strtotime($transaction->tgl_masuk)) ?></td>
                                        <td>Rp <?= number_format($transaction->jml_gtotal) ?></td>
                                        <td><span class="badge badge-success">Lunas</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada transaksi lunas</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                <a href="<?= base_url('transaksi/jual/cashier') ?>" class="btn btn-sm btn-info float-left">Kasir Baru</a>
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
                <span class="info-box-number">5,200</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box mb-3 bg-success">
            <span class="info-box-icon"><i class="far fa-heart"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Mention</span>
                <span class="info-box-number">92,050</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box mb-3 bg-danger">
            <span class="info-box-icon"><i class="fas fa-cloud-download-alt"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Unduhan</span>
                <span class="info-box-number">114,381</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box mb-3 bg-info">
            <span class="info-box-icon"><i class="far fa-comment"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Pesan Langsung</span>
                <span class="info-box-number">163,921</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Penggunaan Browser</h3>

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
                <div class="row">
                    <div class="col-md-8">
                        <div class="chart-responsive">
                            <canvas id="pieChart" height="150"></canvas>
                        </div>
                        <!-- ./chart-responsive -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-4">
                        <ul class="chart-legend clearfix">
                            <li><i class="far fa-circle text-danger"></i> Chrome</li>
                            <li><i class="far fa-circle text-success"></i> IE</li>
                            <li><i class="far fa-circle text-warning"></i> FireFox</li>
                            <li><i class="far fa-circle text-info"></i> Safari</li>
                            <li><i class="far fa-circle text-primary"></i> Opera</li>
                            <li><i class="far fa-circle text-secondary"></i> Navigator</li>
                        </ul>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer p-0">
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            Amerika Serikat
                            <span class="float-right text-danger">
                                <i class="fas fa-arrow-down text-sm"></i>
                                12%</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            India
                            <span class="float-right text-success">
                                <i class="fas fa-arrow-up text-sm"></i> 4%
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            China
                            <span class="float-right text-warning">
                                <i class="fas fa-arrow-left text-sm"></i> 0%
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- /.footer -->
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
                <a href="javascript:void(0)" class="uppercase">Lihat Semua Produk</a>
            </div>
            <!-- /.card-footer -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/jquery/jquery.min.js') ?>"></script>
<!-- Bootstrap -->
<script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- overlayScrollbars -->
<script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?= base_url('public/assets/theme/admin-lte-3/dist/js/adminlte.js') ?>"></script>

<!-- PAGE PLUGINS -->
<!-- jQuery Mapael -->
<script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/jquery-mousewheel/jquery.mousewheel.js') ?>"></script>
<script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/raphael/raphael.min.js') ?>"></script>
<script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/jquery-mapael/jquery.mapael.min.js') ?>"></script>
<script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/jquery-mapael/maps/usa_states.min.js') ?>"></script>

<!-- ChartJS -->
<script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/chart.js/Chart.min.js') ?>"></script>

<!-- AdminLTE for demo purposes -->
<script src="<?= base_url('public/assets/theme/admin-lte-3/dist/js/demo.js') ?>"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?= base_url('public/assets/theme/admin-lte-3/dist/js/pages/dashboard2.js') ?>"></script>

<?= $this->endSection() ?>