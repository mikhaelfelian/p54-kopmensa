<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h3 class="card-title">Laporan Omset Penjualan</h3>
            </div>
            <div class="col-md-6 text-right">
                <a href="<?= base_url('laporan/sales-turnover/export') ?>?<?= http_build_query($_GET) ?>" 
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
                <form method="GET" class="form-inline">
                    <div class="form-group mr-3">
                        <label class="mr-2">Dari Tanggal:</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" 
                               value="<?= $startDate ?>">
                    </div>
                    <div class="form-group mr-3">
                        <label class="mr-2">Sampai Tanggal:</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" 
                               value="<?= $endDate ?>">
                    </div>
                    <div class="form-group mr-3">
                        <label class="mr-2">Store:</label>
                        <select name="id_gudang" class="form-control form-control-sm">
                            <option value="">Semua Gudang</option>
                            <?php foreach ($gudangList as $gudang): ?>
                                <option value="<?= $gudang->id ?>" <?= $idGudang == $gudang->id ? 'selected' : '' ?>>
                                    <?= esc($gudang->nama) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mr-3">
                        <label class="mr-2">Sales:</label>
                        <select name="id_sales" class="form-control form-control-sm">
                            <option value="">Semua Sales</option>
                            <?php foreach ($salesList as $sales): ?>
                                <option value="<?= $sales->id ?>" <?= $idSales == $sales->id ? 'selected' : '' ?>>
                                    <?= esc($sales->nama) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mr-3">
                        <label class="mr-2">Metode Bayar:</label>
                        <select name="payment_method" class="form-control form-control-sm">
                            <option value="">Semua Metode</option>
                            <option value="cash" <?= $paymentMethod == 'cash' ? 'selected' : '' ?>>Cash</option>
                            <option value="tunai" <?= $paymentMethod == 'tunai' ? 'selected' : '' ?>>Tunai</option>
                            <option value="transfer" <?= $paymentMethod == 'transfer' ? 'selected' : '' ?>>Transfer</option>
                            <option value="debit" <?= $paymentMethod == 'debit' ? 'selected' : '' ?>>Debit</option>
                            <option value="credit" <?= $paymentMethod == 'credit' ? 'selected' : '' ?>>Credit</option>
                            <option value="qris" <?= $paymentMethod == 'qris' ? 'selected' : '' ?>>QRIS</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-info">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="<?= base_url('laporan/sales-turnover') ?>" class="btn btn-sm btn-secondary ml-2">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= number_format($summary['total_transactions']) ?></h3>
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
                        <h3>Rp <?= number_format($summary['total_sales'], 0, ',', '.') ?></h3>
                        <p>Total Omset</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>Rp <?= number_format($summary['total_cash'], 0, ',', '.') ?></h3>
                        <p>Penjualan Cash</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>Rp <?= number_format($summary['total_non_cash'], 0, ',', '.') ?></h3>
                        <p>Penjualan Non-Cash</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Data Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm">
                <thead class="bg-primary">
                    <tr>
                        <th width="3%">No</th>
                        <th width="10%">Tanggal</th>
                        <th width="12%">No. Nota</th>
                        <th width="12%">Warehouse / Store</th>
                        <th width="10%">Sales</th>
                        <th width="12%">Pelanggan</th>
                        <th width="10%">Metode Bayar</th>
                        <th width="8%" class="text-center">Items</th>
                        <th width="13%" class="text-right">Total</th>
                        <th width="10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($salesData)): ?>
                        <?php $no = 1; foreach ($salesData as $sale): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($sale->tgl_masuk)) ?></td>
                                <td><strong><?= esc($sale->no_nota) ?></strong></td>
                                <td><?= esc($sale->gudang_nama) ?></td>
                                <td><?= esc($sale->sales_nama) ?></td>
                                <td><?= esc($sale->pelanggan_nama ?: 'Umum') ?></td>
                                <td>
                                    <span class="badge badge-<?= $sale->metode_bayar == 'cash' || $sale->metode_bayar == 'tunai' ? 'success' : 'info' ?>">
                                        <?= ucfirst($sale->metode_bayar) ?>
                                    </span>
                                </td>
                                <td class="text-center"><?= $sale->total_items ?></td>
                                <td class="text-right">
                                    <strong>Rp <?= number_format((float) $sale->total_amount, 0, ',', '.') ?></strong>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url("laporan/sale/detail/{$sale->id}") ?>" 
                                       class="btn btn-info btn-xs" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="bg-light font-weight-bold">
                            <td colspan="8" class="text-right">GRAND TOTAL:</td>
                            <td class="text-right">
                                <strong>Rp <?= number_format($summary['total_sales'], 0, ',', '.') ?></strong>
                            </td>
                            <td></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">
                                <div class="py-3">
                                    <i class="fas fa-chart-line fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">Tidak ada data penjualan untuk periode yang dipilih</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Additional Summary -->
        <?php if (!empty($salesData)): ?>
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Ringkasan Periode</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td>Periode</td>
                                    <td>:</td>
                                    <td><strong><?= date('d/m/Y', strtotime($startDate)) ?> - <?= date('d/m/Y', strtotime($endDate)) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Total Transaksi</td>
                                    <td>:</td>
                                    <td><strong><?= number_format($summary['total_transactions']) ?> transaksi</strong></td>
                                </tr>
                                <tr>
                                    <td>Rata-rata per Transaksi</td>
                                    <td>:</td>
                                    <td><strong>Rp <?= number_format($summary['average_per_transaction'], 0, ',', '.') ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Breakdown Metode Pembayaran</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td>Cash/Tunai</td>
                                    <td>:</td>
                                    <td><strong>Rp <?= number_format($summary['total_cash'], 0, ',', '.') ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Non-Cash</td>
                                    <td>:</td>
                                    <td><strong>Rp <?= number_format($summary['total_non_cash'], 0, ',', '.') ?></strong></td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>Total</strong></td>
                                    <td>:</td>
                                    <td><strong>Rp <?= number_format($summary['total_sales'], 0, ',', '.') ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
