<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: View for displaying outlet report detail
 * This file represents the outlet report detail view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-eye mr-1"></i> Detail Outlet - <?= $outlet->gudang ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/outlet') ?>" class="btn btn-default btn-sm rounded-0">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Outlet Information -->
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Nama Outlet</strong></td>
                                <td>: <?= $outlet->gudang ?></td>
                            </tr>
                            <tr>
                                <td><strong>Alamat</strong></td>
                                <td>: <?= $outlet->alamat ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Telepon</strong></td>
                                <td>: <?= $outlet->telepon ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Email</strong></td>
                                <td>: <?= $outlet->email ?? '-' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Status</strong></td>
                                <td>: 
                                    <?php if ($outlet->status == '1'): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Periode Laporan</strong></td>
                                <td>: <?= date('d/m/Y', strtotime($startDate)) ?> - <?= date('d/m/Y', strtotime($endDate)) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
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
                                <h3><?= number_format($totalStock, 0, ',', '.') ?></h3>
                                <p>Total Stok</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?= number_format($inStockCount, 0, ',', '.') ?></h3>
                                <p>Item Ada Stok</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Transactions -->
                <h5><i class="fas fa-shopping-cart mr-1"></i> Transaksi Penjualan</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Tanggal</th>
                                <th>No. Nota</th>
                                <th>Pelanggan</th>
                                <th>Sales</th>
                                <th class="text-right">Total</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sales)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada transaksi penjualan</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($sales as $index => $sale): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= date('d/m/Y', strtotime($sale->tgl_masuk)) ?></td>
                                        <td>
                                            <a href="<?= base_url('laporan/sale/detail/' . $sale->id) ?>" class="text-primary">
                                                <?= $sale->no_nota ?>
                                            </a>
                                        </td>
                                        <td><?= $sale->pelanggan_nama ?? '-' ?></td>
                                        <td><?= $sale->sales_nama ?? '-' ?></td>
                                        <td class="text-right"><?= number_format($sale->jml_gtotal ?? 0, 0, ',', '.') ?></td>
                                        <td>
                                            <a href="<?= base_url('laporan/sale/detail/' . $sale->id) ?>" 
                                               class="btn btn-info btn-sm rounded-0">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($sales)): ?>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="5" class="text-right">TOTAL</th>
                                    <th class="text-right"><?= number_format($totalSales, 0, ',', '.') ?></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>

                <hr>

                <!-- Stock Information -->
                <h5><i class="fas fa-boxes mr-1"></i> Informasi Stok</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Kode Item</th>
                                <th>Nama Item</th>
                                <th>Kategori</th>
                                <th>Merk</th>
                                <th class="text-center">Stok</th>
                                <th class="text-right">Harga Beli</th>
                                <th class="text-right">Harga Jual</th>
                                <th class="text-right">Total Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($stocks)): ?>
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data stok</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($stocks as $index => $stock): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= $stock->item_kode ?? '-' ?></td>
                                        <td><?= $stock->item_nama ?? '-' ?></td>
                                        <td><?= $stock->kategori_nama ?? '-' ?></td>
                                        <td><?= $stock->merk_nama ?? '-' ?></td>
                                        <td class="text-center">
                                            <?php if (($stock->stok ?? 0) > 0): ?>
                                                <span class="badge badge-success"><?= number_format($stock->stok ?? 0, 0, ',', '.') ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right"><?= number_format($stock->harga_beli ?? 0, 0, ',', '.') ?></td>
                                        <td class="text-right"><?= number_format($stock->harga_jual ?? 0, 0, ',', '.') ?></td>
                                        <td class="text-right"><?= number_format(($stock->stok ?? 0) * ($stock->harga_beli ?? 0), 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($stocks)): ?>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="8" class="text-right">TOTAL NILAI</th>
                                    <th class="text-right"><?= number_format(array_sum(array_map(function($stock) { return ($stock->stok ?? 0) * ($stock->harga_beli ?? 0); }, $stocks)), 0, ',', '.') ?></th>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>

                <!-- Stock Summary -->
                <hr>
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-boxes"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Item</span>
                                <span class="info-box-number"><?= count($stocks) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success">
                                <i class="fas fa-check-circle"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Item Ada Stok</span>
                                <span class="info-box-number"><?= $inStockCount ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Item Habis</span>
                                <span class="info-box-number"><?= $outOfStockCount ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning">
                                <i class="fas fa-percentage"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Persentase Stok</span>
                                <span class="info-box-number"><?= count($stocks) > 0 ? round(($inStockCount / count($stocks)) * 100, 1) : 0 ?>%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
