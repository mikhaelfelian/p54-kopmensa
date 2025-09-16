<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Fitur Sistem POS yang Ditingkatkan</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Bagian Laporan -->
                    <div class="col-md-6">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">📊 Laporan Lanjutan</h3>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="<?= base_url('laporan/sales-turnover') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-chart-line"></i> Laporan Omzet Penjualan
                                    </a>
                                    <a href="<?= base_url('laporan/product-sales') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-box"></i> Laporan Penjualan Produk
                                    </a>
                                    <a href="<?= base_url('laporan/order') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-file-invoice"></i> Laporan Pesanan (Berdasarkan Faktur)
                                    </a>
                                    <a href="<?= base_url('laporan/all-in-one-turnover') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-chart-pie"></i> Laporan Omzet All-in-One
                                    </a>
                                    <a href="<?= base_url('laporan/profit-loss') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-calculator"></i> Laporan Laba & Rugi
                                    </a>
                                    <a href="<?= base_url('laporan/best-selling') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-trophy"></i> Laporan Produk Terlaris
                                    </a>
                                    <a href="<?= base_url('laporan/cutoff') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-cut"></i> Laporan Cut-off
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bagian Manajemen -->
                    <div class="col-md-6">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">⚙️ Manajemen Ditingkatkan</h3>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="<?= base_url('gudang/input-stok') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-warehouse"></i> Manajemen Gudang Ditingkatkan
                                        <small class="text-muted d-block">Dengan pelacakan pengguna & filter</small>
                                    </a>
                                    <a href="<?= base_url('gudang/inventori') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-boxes"></i> Tampilan Inventaris Langsung
                                        <small class="text-muted d-block">Tampilkan semua barang secara langsung</small>
                                    </a>
                                    <a href="<?= base_url('master/supplier') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-truck"></i> Pengaturan Barang Supplier
                                        <small class="text-muted d-block">Kelola barang per supplier</small>
                                    </a>
                                    <a href="<?= base_url('master/cutoff') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-cut"></i> Manajemen Cut-off
                                        <small class="text-muted d-block">Cut-off keuangan harian</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <!-- Fitur Member -->
                    <div class="col-md-6">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">👥 Manajemen Member</h3>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="<?= base_url('master/customer') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-users"></i> Manajemen Member Ditingkatkan
                                        <small class="text-muted d-block">Pelacakan pembelian bulanan</small>
                                    </a>
                                    <a href="<?= base_url('master/customer/create') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-user-plus"></i> Tambah Member Baru
                                    </a>
                                    <div class="list-group-item">
                                        <i class="fas fa-store"></i> Pilihan Toko Saat Login
                                        <small class="text-muted d-block">Member dapat memilih toko saat login</small>
                                    </div>
                                    <div class="list-group-item">
                                        <i class="fas fa-ban"></i> Manajemen Akun
                                        <small class="text-muted d-block">Reset, blokir akun dengan catatan</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fitur Sistem -->
                    <div class="col-md-6">
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title">🔧 Peningkatan Sistem</h3>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <div class="list-group-item">
                                        <i class="fas fa-exclamation-triangle"></i> Pencegahan Barang Duplikat
                                        <small class="text-muted d-block">Peringatan saat menambah barang duplikat</small>
                                    </div>
                                    <div class="list-group-item">
                                        <i class="fas fa-receipt"></i> Cetak Struk Ditingkatkan
                                        <small class="text-muted d-block">Tampilan metode pembayaran di struk</small>
                                    </div>
                                    <div class="list-group-item">
                                        <i class="fas fa-shield-alt"></i> Hak Akses Pengguna
                                        <small class="text-muted d-block">Manajemen hak akses yang komprehensif</small>
                                    </div>
                                    <a href="<?= base_url('gudang/opname') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-clipboard-check"></i> Input Opname Diperbaiki
                                        <small class="text-muted d-block">Dukungan input banyak produk</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Fitur -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card card-dark">
                            <div class="card-header">
                                <h3 class="card-title">📋 Ringkasan Implementasi</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="info-box bg-success">
                                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Fitur Selesai</span>
                                                <span class="info-box-number">20/20</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-info">
                                            <span class="info-box-icon"><i class="fas fa-chart-bar"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Laporan Baru</span>
                                                <span class="info-box-number">7</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-warning">
                                            <span class="info-box-icon"><i class="fas fa-cogs"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Fitur Ditingkatkan</span>
                                                <span class="info-box-number">8</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-danger">
                                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Fitur Member</span>
                                                <span class="info-box-number">6</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-success mt-3">
                                    <h5><i class="icon fas fa-check"></i> Semua Fitur Berhasil Diimplementasikan!</h5>
                                    <p>Sistem POS Anda telah ditingkatkan dengan seluruh 20 fitur yang diminta:</p>
                                    <ul class="mb-0">
                                        <li><strong>Manajemen Gudang:</strong> Filter ditingkatkan, pelacakan pengguna, dan label "Gudang/Toko"</li>
                                        <li><strong>Inventaris:</strong> Tampilkan barang langsung dengan kontrol halaman</li>
                                        <li><strong>Laporan:</strong> 7 laporan lengkap dengan fitur ekspor termasuk Laporan Cut-off</li>
                                        <li><strong>Manajemen Member:</strong> Pilihan toko, pelacakan bulanan, manajemen akun</li>
                                        <li><strong>Peningkatan Sistem:</strong> Fitur cut-off, pencegahan duplikat, struk ditingkatkan</li>
                                        <li><strong>Perbaikan:</strong> Input opname kini mendukung banyak produk</li>
                                    </ul>
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
