<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-29
 * Github: github.com/mikhaelfelian
 * Description: View for displaying stock reports
 * This file represents the stock report index view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-1"></i> Laporan Stok
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/stock/export_excel') ?>?id_gudang=<?= $idGudang ?>&id_kategori=<?= $idKategori ?>&id_merk=<?= $idMerk ?>&keyword=<?= $keyword ?>&stock_type=<?= $stockType ?>" 
                       class="btn btn-success btn-sm rounded-0">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="get" action="<?= base_url('laporan/stock') ?>" class="mb-4">
                    <div class="row">
                        <div class="col-md-2">
                            <label>Gudang</label>
                            <select name="id_gudang" class="form-control form-control-sm">
                                <option value="">Semua Gudang</option>
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
                            <label>Status Stok</label>
                            <select name="stock_type" class="form-control form-control-sm">
                                <option value="all" <?= $stockType == 'all' ? 'selected' : '' ?>>Semua</option>
                                <option value="in_stock" <?= $stockType == 'in_stock' ? 'selected' : '' ?>>Ada Stok</option>
                                <option value="out_of_stock" <?= $stockType == 'out_of_stock' ? 'selected' : '' ?>>Habis</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Keyword</label>
                            <input type="text" name="keyword" class="form-control form-control-sm" value="<?= $keyword ?>" placeholder="Cari item...">
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
                                <h3><?= number_format($totalItems, 0, ',', '.') ?></h3>
                                <p>Total Item</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= number_format($totalStock, 0, ',', '.') ?></h3>
                                <p>Total Stok</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-cubes"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= number_format($inStockCount, 0, ',', '.') ?></h3>
                                <p>Item Ada Stok</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?= number_format($outOfStockCount, 0, ',', '.') ?></h3>
                                <p>Item Habis</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-exclamation-triangle"></i>
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
                                <th>Kode Item</th>
                                <th>Nama Item</th>
                                <th>Kategori</th>
                                <th>Merk</th>
                                <th>Gudang</th>
                                <th class="text-center">Stok</th>
                                <th>Satuan</th>
                                <th class="text-right">Harga Beli</th>
                                <th class="text-right">Harga Jual</th>
                                <th class="text-right">Total Nilai</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($stocks)): ?>
                                <tr>
                                    <td colspan="12" class="text-center">Tidak ada data stok</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($stocks as $index => $stock): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= $stock->item_kode ?? '-' ?></td>
                                        <td><?= $stock->item_nama ?? '-' ?></td>
                                        <td><?= $stock->kategori_nama ?? '-' ?></td>
                                        <td><?= $stock->merk_nama ?? '-' ?></td>
                                        <td><?= $stock->gudang_nama ?? '-' ?></td>
                                                                                 <td class="text-center">
                                             <?php if (($stock->jml ?? 0) > 0): ?>
                                                 <span class="badge badge-success"><?= number_format($stock->jml ?? 0, 0, ',', '.') ?></span>
                                             <?php else: ?>
                                                 <span class="badge badge-danger">0</span>
                                             <?php endif; ?>
                                         </td>
                                        <td><?= $stock->satuan_nama ?? '-' ?></td>
                                        <td class="text-right"><?= number_format($stock->harga_beli ?? 0, 0, ',', '.') ?></td>
                                        <td class="text-right"><?= number_format($stock->harga_jual ?? 0, 0, ',', '.') ?></td>
                                                                                 <td class="text-right"><?= number_format(($stock->jml ?? 0) * ($stock->harga_beli ?? 0), 0, ',', '.') ?></td>
                                        <td>
                                            <a href="<?= base_url('laporan/stock/detail/' . $stock->id) ?>" 
                                               class="btn btn-info btn-sm rounded-0">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($stocks)): ?>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="10" class="text-right">TOTAL NILAI</th>
                                    <th class="text-right"><?= number_format($totalValue, 0, ',', '.') ?></th>
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
