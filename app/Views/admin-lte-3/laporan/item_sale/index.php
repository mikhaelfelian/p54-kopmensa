<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h3 class="card-title">Laporan Penjualan Item</h3>
            </div>
            <div class="col-md-6 text-right">
                <a href="<?= base_url('laporan/item-sale/export_excel') ?>?<?= http_build_query($_GET) ?>" 
                   class="btn btn-sm btn-success rounded-0">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="<?= base_url('laporan/item-sale/export_pdf') ?>?<?= http_build_query($_GET) ?>" 
                   class="btn btn-sm btn-danger rounded-0">
                    <i class="fas fa-file-pdf"></i> Export PDF
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
                            <label>Outlet:</label>
                            <select name="id_gudang" class="form-control form-control-sm">
                                <option value="">Semua Outlet</option>
                                <?php foreach ($gudangList as $gudang): ?>
                                    <option value="<?= $gudang->id ?>" <?= ($idGudang ?? '') == $gudang->id ? 'selected' : '' ?>>
                                        <?= esc($gudang->nama) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Urutkan:</label>
                            <select name="sort_by" class="form-control form-control-sm">
                                <option value="total_qty" <?= ($sortBy ?? 'total_qty') == 'total_qty' ? 'selected' : '' ?>>Qty Terjual</option>
                                <option value="total_amount" <?= ($sortBy ?? '') == 'total_amount' ? 'selected' : '' ?>>Total Revenue</option>
                                <option value="total_transactions" <?= ($sortBy ?? '') == 'total_transactions' ? 'selected' : '' ?>>Jumlah Transaksi</option>
                                <option value="item" <?= ($sortBy ?? '') == 'item' ? 'selected' : '' ?>>Nama Item</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Urutan:</label>
                            <select name="sort_order" class="form-control form-control-sm">
                                <option value="DESC" <?= ($sortOrder ?? 'DESC') == 'DESC' ? 'selected' : '' ?>>Tertinggi</option>
                                <option value="ASC" <?= ($sortOrder ?? '') == 'ASC' ? 'selected' : '' ?>>Terendah</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-sm btn-info">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="<?= base_url('laporan/item-sale') ?>" class="btn btn-sm btn-secondary">
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
                        <h3><?= format_angka($summary['total_items']) ?></h3>
                        <p>Total Item Terjual</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-box"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= format_angka($summary['total_quantity_sold']) ?></h3>
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
                        <h3><?= format_angka($summary['total_revenue']) ?></h3>
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
                        <h3><?= format_angka($summary['total_transactions']) ?></h3>
                        <p>Total Transaksi</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Item Sales Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm">
                <thead class="bg-primary">
                    <tr>
                        <th width="3%">No</th>
                        <th width="10%">Kode</th>
                        <th width="30%">Nama Item</th>
                        <th width="8%">Satuan</th>
                        <th width="10%" class="text-center">Qty Terjual</th>
                        <th width="15%" class="text-right">Total Revenue</th>
                        <th width="12%" class="text-right">Rata-rata Harga</th>
                        <th width="10%" class="text-center">Transaksi</th>
                        <th width="2%">Rank</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($itemSales)): ?>
                        <?php $no = 1; foreach ($itemSales as $item): ?>
                            <tr>
                                <td class="text-center"><?= $no ?></td>
                                <td><strong><?= esc($item->kode) ?></strong></td>
                                <td>
                                    <strong><?= esc($item->item) ?></strong>
                                    <?php if (!empty($item->barcode)): ?>
                                        <br><small class="text-muted">Barcode: <?= esc($item->barcode) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($item->satuan ?? '-') ?></td>
                                <td class="text-center">
                                    <span class="badge badge-primary"><?= format_angka($item->total_qty) ?></span>
                                </td>
                                <td class="text-right">
                                    <strong><?= format_angka($item->total_amount) ?></strong>
                                </td>
                                <td class="text-right">
                                    <?= format_angka($item->avg_price) ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info"><?= $item->total_transactions ?></span>
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
                            <td colspan="9" class="text-center">
                                <div class="py-3">
                                    <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">Tidak ada data penjualan item untuk periode yang dipilih</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

