<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h3 class="card-title">Laporan Penjualan Produk</h3>
            </div>
            <div class="col-md-6 text-right">
                <a href="<?= base_url('laporan/product-sales/export') ?>?<?= http_build_query($_GET) ?>" 
                   class="btn btn-sm btn-success rounded-0">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Filter Form -->
        <div class="card card-outline card-primary mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter"></i> Filter Laporan</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Dari Tanggal:</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" 
                                   value="<?= $startDate ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Sampai Tanggal:</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" 
                                   value="<?= $endDate ?>">
                        </div>
                        <div class="col-md-2">
                            <label>Warehouse / Store:</label>
                            <select name="id_gudang" class="form-control form-control-sm">
                                <option value="">Semua</option>
                                <?php foreach ($gudangList as $gudang): ?>
                                    <option value="<?= $gudang->id ?>" <?= $idGudang == $gudang->id ? 'selected' : '' ?>>
                                        <?= esc($gudang->nama) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Kategori:</label>
                            <select name="id_kategori" class="form-control form-control-sm">
                                <option value="">Semua</option>
                                <?php foreach ($kategoriList as $kategori): ?>
                                    <option value="<?= $kategori->id ?>" <?= $idKategori == $kategori->id ? 'selected' : '' ?>>
                                        <?= esc($kategori->kategori) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Merk:</label>
                            <select name="id_merk" class="form-control form-control-sm">
                                <option value="">Semua</option>
                                <?php foreach ($merkList as $merk): ?>
                                    <option value="<?= $merk->id ?>" <?= $idMerk == $merk->id ? 'selected' : '' ?>>
                                        <?= esc($merk->merk) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-2">
                            <label>Urutkan:</label>
                            <select name="sort_by" class="form-control form-control-sm">
                                <option value="total_qty" <?= $sortBy == 'total_qty' ? 'selected' : '' ?>>Qty Terjual</option>
                                <option value="total_amount" <?= $sortBy == 'total_amount' ? 'selected' : '' ?>>Total Revenue</option>
                                <option value="total_transactions" <?= $sortBy == 'total_transactions' ? 'selected' : '' ?>>Jumlah Transaksi</option>
                                <option value="item" <?= $sortBy == 'item' ? 'selected' : '' ?>>Nama Produk</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Urutan:</label>
                            <select name="sort_order" class="form-control form-control-sm">
                                <option value="DESC" <?= $sortOrder == 'DESC' ? 'selected' : '' ?>>Tertinggi</option>
                                <option value="ASC" <?= $sortOrder == 'ASC' ? 'selected' : '' ?>>Terendah</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label>&nbsp;</label><br>
                            <button type="submit" class="btn btn-sm btn-info">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="<?= base_url('laporan/product-sales') ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= number_format($summary['total_products']) ?></h3>
                        <p>Total Produk Terjual</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-box"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= number_format($summary['total_quantity_sold']) ?></h3>
                        <p>Total Qty Terjual</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cubes"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>Rp <?= number_format($summary['total_revenue'], 0, ',', '.') ?></h3>
                        <p>Total Revenue</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?= number_format($summary['total_transactions']) ?></h3>
                        <p>Total Transaksi</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Sales Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm">
                <thead class="bg-primary">
                    <tr>
                        <th width="3%">No</th>
                        <th width="8%">Kode</th>
                        <th width="25%">Nama Produk</th>
                        <th width="10%">Kategori</th>
                        <th width="8%">Merk</th>
                        <th width="6%">Satuan</th>
                        <th width="8%" class="text-center">Qty Terjual</th>
                        <th width="12%" class="text-right">Total Revenue</th>
                        <th width="10%" class="text-right">Rata-rata Harga</th>
                        <th width="8%" class="text-center">Transaksi</th>
                        <th width="2%">Rank</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($productSales)): ?>
                        <?php $no = 1; foreach ($productSales as $product): ?>
                            <tr>
                                <td class="text-center"><?= $no ?></td>
                                <td><strong><?= esc($product->kode) ?></strong></td>
                                <td>
                                    <strong><?= esc($product->item) ?></strong>
                                    <?php if (!empty($product->barcode)): ?>
                                        <br><small class="text-muted">Barcode: <?= esc($product->barcode) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($product->kategori) ?></td>
                                <td><?= esc($product->merk) ?></td>
                                <td><?= esc($product->satuan) ?></td>
                                <td class="text-center">
                                    <span class="badge badge-primary"><?= number_format((float) $product->total_qty, 0) ?></span>
                                </td>
                                <td class="text-right">
                                    <strong>Rp <?= number_format((float) $product->total_amount, 0, ',', '.') ?></strong>
                                </td>
                                <td class="text-right">
                                    Rp <?= number_format((float) $product->avg_price, 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info"><?= $product->total_transactions ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($no <= 3): ?>
                                        <span class="badge badge-<?= $no == 1 ? 'warning' : ($no == 2 ? 'secondary' : 'success') ?>">
                                            #<?= $no ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">#<?= $no ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php $no++; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center">
                                <div class="py-3">
                                    <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">Tidak ada data penjualan produk untuk periode yang dipilih</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Top Products -->
        <?php if (!empty($productSales)): ?>
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Top 5 Produk Berdasarkan Qty</h5>
                        </div>
                        <div class="card-body">
                            <?php 
                            $topByQty = array_slice($productSales, 0, 5);
                            usort($topByQty, function($a, $b) {
                                return (float)$b->total_qty <=> (float)$a->total_qty;
                            });
                            ?>
                            <ol>
                                <?php foreach ($topByQty as $product): ?>
                                    <li>
                                        <strong><?= esc($product->item) ?></strong><br>
                                        <small class="text-muted">
                                            Qty: <?= number_format((float) $product->total_qty) ?> | 
                                            Revenue: Rp <?= number_format((float) $product->total_amount, 0, ',', '.') ?>
                                        </small>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Top 5 Produk Berdasarkan Revenue</h5>
                        </div>
                        <div class="card-body">
                            <?php 
                            $topByRevenue = array_slice($productSales, 0, 5);
                            usort($topByRevenue, function($a, $b) {
                                return (float)$b->total_amount <=> (float)$a->total_amount;
                            });
                            ?>
                            <ol>
                                <?php foreach ($topByRevenue as $product): ?>
                                    <li>
                                        <strong><?= esc($product->item) ?></strong><br>
                                        <small class="text-muted">
                                            Revenue: Rp <?= number_format((float) $product->total_amount, 0, ',', '.') ?> | 
                                            Qty: <?= number_format((float) $product->total_qty) ?>
                                        </small>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
