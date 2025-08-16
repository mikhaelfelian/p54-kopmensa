<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <!-- Summary Cards -->
                    <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                        <h3><?= number_format($summary->total_items ?? 0) ?></h3>
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
                        <h3><?= number_format($summary->total_sisa ?? 0, 2) ?></h3>
                        <p>Total Sisa Stok</p>
                            </div>
                            <div class="icon">
                        <i class="fas fa-warehouse"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                        <h3><?= number_format($summary->total_masuk ?? 0, 2) ?></h3>
                        <p>Total Stok Masuk</p>
                            </div>
                            <div class="icon">
                        <i class="fas fa-arrow-down"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                        <h3><?= number_format($summary->total_keluar ?? 0, 2) ?></h3>
                        <p>Total Stok Keluar</p>
                            </div>
                            <div class="icon">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Aging Analysis -->
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Analisis Level Stok</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Level Stok</th>
                                        <th>Jumlah Item</th>
                                        <th>Total Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stockAging as $aging): ?>
                                    <tr>
                                        <td><?= esc($aging->stock_level) ?></td>
                                        <td><?= number_format($aging->item_count) ?></td>
                                        <td><?= number_format($aging->total_quantity, 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ringkasan per Gudang</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Gudang</th>
                                        <th>Total Item</th>
                                        <th>In Stock</th>
                                        <th>Out of Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($warehouseSummary as $warehouse): ?>
                                    <tr>
                                        <td><?= esc($warehouse->gudang) ?></td>
                                        <td><?= number_format($warehouse->total_items) ?></td>
                                        <td><?= number_format($warehouse->items_in_stock) ?></td>
                                        <td><?= number_format($warehouse->items_out_of_stock) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ringkasan per Tipe Lokasi</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tipe</th>
                                        <th>Lokasi</th>
                                        <th>Total Item</th>
                                        <th>In Stock</th>
                                        <th>Out of Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($outletTypeSummary as $summary): ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-<?= $summary->status_otl == '1' ? 'success' : 'info' ?>">
                                                <?= esc($summary->outlet_type) ?>
                                            </span>
                                        </td>
                                        <td><?= number_format($summary->total_locations) ?></td>
                                        <td><?= number_format($summary->total_items) ?></td>
                                        <td><?= number_format($summary->items_in_stock) ?></td>
                                        <td><?= number_format($summary->items_out_of_stock) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Alerts -->
        <div class="row">
            <div class="col-md-4">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exclamation-triangle"></i> Stok Menipis (≤10)
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Gudang</th>
                                        <th>Sisa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($lowStock, 0, 5) as $item): ?>
                                    <tr>
                                        <td><?= esc($item->item) ?></td>
                                        <td><?= esc($item->gudang) ?></td>
                                        <td><span class="badge badge-warning"><?= number_format($item->sisa, 2) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if (count($lowStock) > 5): ?>
                    <div class="card-footer text-center">
                        <small>Dan <?= count($lowStock) - 5 ?> item lainnya...</small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-times-circle"></i> Stok Habis
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Gudang</th>
                                        <th>Sisa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($outOfStock, 0, 5) as $item): ?>
                                    <tr>
                                        <td><?= esc($item->item) ?></td>
                                        <td><?= esc($item->gudang) ?></td>
                                        <td><span class="badge badge-danger"><?= number_format($item->sisa, 2) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if (count($outOfStock) > 5): ?>
                    <div class="card-footer text-center">
                        <small>Dan <?= count($outOfStock) - 5 ?> item lainnya...</small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line"></i> Top Items by Stock
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Gudang</th>
                                        <th>Sisa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $topItems = array_slice($stock, 0, 5);
                                    foreach ($topItems as $item): 
                                    ?>
                                    <tr>
                                        <td><?= esc($item->item) ?></td>
                                        <td><?= esc($item->gudang) ?></td>
                                        <td><span class="badge badge-success"><?= number_format($item->sisa, 2) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                            </div>
                        </div>
                    </div>
                </div>

        <!-- Main Stock Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Laporan Stok Detail</h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/stock/export_excel') ?>?<?= http_build_query($_GET) ?>" 
                       class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="get" class="mb-3">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="gudang_id">Gudang</label>
                                <select class="form-control" id="gudang_id" name="gudang_id">
                                    <option value="">Semua Gudang</option>
                                    <?php foreach ($gudang as $g): ?>
                                    <option value="<?= $g->id ?>" <?= $selectedGudang == $g->id ? 'selected' : '' ?>>
                                        <?= esc($g->nama) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="outlet_type">Tipe Lokasi</label>
                                <select class="form-control" id="outlet_type" name="outlet_type">
                                    <option value="">Semua</option>
                                    <option value="warehouse" <?= $outletType == 'warehouse' ? 'selected' : '' ?>>Warehouse</option>
                                    <option value="outlet" <?= $outletType == 'outlet' ? 'selected' : '' ?>>Outlet</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="keyword">Keyword</label>
                                <input type="text" class="form-control" id="keyword" name="keyword" 
                                       value="<?= esc($keyword) ?>" placeholder="Cari item...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="stock_status">Status Stok</label>
                                <select class="form-control" id="stock_status" name="stock_status">
                                    <option value="">Semua</option>
                                    <option value="positive" <?= $stockStatus == 'positive' ? 'selected' : '' ?>>Ada Stok</option>
                                    <option value="negative" <?= $stockStatus == 'negative' ? 'selected' : '' ?>>Stok Negatif</option>
                                    <option value="zero" <?= $stockStatus == 'zero' ? 'selected' : '' ?>>Stok Kosong</option>
                                    <option value="low" <?= $stockStatus == 'low' ? 'selected' : '' ?>>Stok Menipis</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="sort_by">Urutkan</label>
                                <select class="form-control" id="sort_by" name="sort_by">
                                    <option value="item" <?= $sortBy == 'item' ? 'selected' : '' ?>>Nama Item</option>
                                    <option value="kode" <?= $sortBy == 'kode' ? 'selected' : '' ?>>Kode</option>
                                    <option value="gudang" <?= $sortBy == 'gudang' ? 'selected' : '' ?>>Gudang</option>
                                    <option value="sisa" <?= $sortBy == 'sisa' ? 'selected' : '' ?>>Sisa Stok</option>
                                    <option value="so" <?= $sortBy == 'so' ? 'selected' : '' ?>>SO</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="sort_order">Urutan</label>
                                <select class="form-control" id="sort_order" name="sort_order">
                                    <option value="ASC" <?= $sortOrder == 'ASC' ? 'selected' : '' ?>>A-Z</option>
                                    <option value="DESC" <?= $sortOrder == 'DESC' ? 'selected' : '' ?>>Z-A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="<?= base_url('laporan/stock') ?>" class="btn btn-secondary">
                                <i class="fas fa-refresh"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Stock Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Kode Item</th>
                                <th>Nama Item</th>
                                <th>Gudang</th>
                                <th>SO</th>
                                <th>Stok Masuk</th>
                                <th>Stok Keluar</th>
                                <th>Sisa</th>
                                <th>Status</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($stock)): ?>
                                <tr>
                                <td colspan="10" class="text-center">Tidak ada data stok</td>
                                </tr>
                            <?php else: ?>
                            <?php foreach ($stock as $index => $item): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                <td><strong><?= esc($item->kode) ?></strong></td>
                                <td><?= esc($item->item) ?></td>
                                <td><?= esc($item->gudang) ?></td>
                                <td>
                                    <?php if ($item->so !== null): ?>
                                        <span class="badge badge-info"><?= number_format($item->so, 2) ?></span>
                                             <?php else: ?>
                                        <span class="badge badge-secondary">-</span>
                                             <?php endif; ?>
                                         </td>
                                <td><?= number_format($item->stok_masuk ?? 0, 2) ?></td>
                                <td><?= number_format($item->stok_keluar ?? 0, 2) ?></td>
                                <td>
                                    <?php 
                                    $sisa = $item->sisa ?? 0;
                                    if ($sisa > 0) {
                                        echo '<span class="badge badge-success">' . number_format($sisa, 2) . '</span>';
                                    } elseif ($sisa == 0) {
                                        echo '<span class="badge badge-warning">' . number_format($sisa, 2) . '</span>';
                                    } else {
                                        echo '<span class="badge badge-danger">' . number_format($sisa, 2) . '</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    if ($sisa > 0) {
                                        echo '<span class="badge badge-success">Ada Stok</span>';
                                    } elseif ($sisa == 0) {
                                        echo '<span class="badge badge-warning">Stok Kosong</span>';
                                    } else {
                                        echo '<span class="badge badge-danger">Stok Negatif</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('laporan/stock/detail/' . $item->id_item) ?>?gudang_id=<?= $item->id_gudang ?>" 
                                       class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if (isset($stock['pager'])): ?>
                <div class="d-flex justify-content-center mt-3">
                    <?= $stock['pager']->links() ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Auto-submit form when filters change
    $('#gudang_id, #outlet_type, #stock_status, #sort_by, #sort_order').on('change', function() {
        $('form').submit();
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Stock status color coding
    $('.badge').each(function() {
        var text = $(this).text();
        if (text.includes('-')) {
            $(this).removeClass().addClass('badge badge-danger');
        }
    });
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
