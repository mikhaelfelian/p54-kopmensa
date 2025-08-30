<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: View for displaying sales reports
 * This file represents the sales report index view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-1"></i> Laporan Penjualan
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/sale/export_excel') ?>?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&id_gudang=<?= $idGudang ?>&id_pelanggan=<?= $idPelanggan ?>&id_sales=<?= $idSales ?>" 
                       class="btn btn-success btn-sm rounded-0">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="get" action="<?= base_url('laporan/sale') ?>" class="mb-4">
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
                            <label>Sales</label>
                            <select name="id_sales" class="form-control form-control-sm">
                                <option value="">Semua Sales</option>
                                <?php foreach ($salesList as $salesItem): ?>
                                    <option value="<?= $salesItem->id ?>" <?= $idSales == $salesItem->id ? 'selected' : '' ?>>
                                        <?= $salesItem->nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
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
                                <h3><?= $totalTransactions > 0 ? number_format($totalSales / $totalTransactions, 0, ',', '.') : 0 ?></h3>
                                <p>Rata-rata per Transaksi</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?= count($sales) ?></h3>
                                <p>Data Ditemukan</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-list"></i>
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
                                <th>No. Nota</th>
                                <th>Pelanggan</th>
                                <th>Gudang</th>
                                <th>Sales</th>
                                <th>Total</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sales)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data penjualan</td>
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
                                        <td><?= $sale->gudang_nama ?? '-' ?></td>
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
                                    <th colspan="6" class="text-right">TOTAL</th>
                                    <th class="text-right"><?= number_format($totalSales, 0, ',', '.') ?></th>
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
