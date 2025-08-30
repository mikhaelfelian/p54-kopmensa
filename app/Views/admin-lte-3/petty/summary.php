<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-28
 * Github: github.com/mikhaelfelian
 * Description: Summary view for Petty Cash Management
 * This file represents the View.
 */

helper('form');
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie"></i> <?= $title ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('transaksi/petty') ?>" class="btn btn-secondary btn-sm rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Section -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" action="<?= base_url('transaksi/petty/summary') ?>" class="form-inline">
                            <div class="form-group mr-2">
                                <label class="mr-2">Dari Tanggal:</label>
                                <input type="date" name="date_from" value="<?= $filters['date_from'] ?>" class="form-control form-control-sm">
                            </div>
                            <div class="form-group mr-2">
                                <label class="mr-2">Sampai Tanggal:</label>
                                <input type="date" name="date_to" value="<?= $filters['date_to'] ?>" class="form-control form-control-sm">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= number_format($summary['total_in'] ?? 0, 0, ',', '.') ?></h3>
                                <p>Total Kas Masuk</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?= number_format($summary['total_out'] ?? 0, 0, ',', '.') ?></h3>
                                <p>Total Kas Keluar</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= number_format(($summary['total_in'] ?? 0) - ($summary['total_out'] ?? 0), 0, ',', '.') ?></h3>
                                <p>Saldo Netto</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-balance-scale"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= $summary['total_transactions'] ?? 0 ?></h3>
                                <p>Total Transaksi</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-list"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Summary -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-tags"></i> Ringkasan per Kategori
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($categorySummary)) : ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Kategori</th>
                                                    <th>Kas Masuk</th>
                                                    <th>Kas Keluar</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($categorySummary as $cat) : ?>
                                                    <tr>
                                                        <td><?= $cat['category_name'] ?? 'Tanpa Kategori' ?></td>
                                                        <td class="text-success"><?= number_format($cat['total_in'] ?? 0, 0, ',', '.') ?></td>
                                                        <td class="text-danger"><?= number_format($cat['total_out'] ?? 0, 0, ',', '.') ?></td>
                                                        <td class="font-weight-bold"><?= number_format(($cat['total_in'] ?? 0) - ($cat['total_out'] ?? 0), 0, ',', '.') ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else : ?>
                                    <div class="text-center text-muted py-3">
                                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                                        <p>Tidak ada data kategori untuk periode yang dipilih</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-chart-bar"></i> Grafik Transaksi
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-chart-bar fa-3x mb-3"></i>
                                    <p>Grafik transaksi akan ditampilkan di sini</p>
                                    <small>Fitur grafik dapat dikembangkan lebih lanjut</small>
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

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize any JavaScript functionality here
    console.log('Petty Cash Summary page loaded');
});
</script>
<?= $this->endSection() ?>
