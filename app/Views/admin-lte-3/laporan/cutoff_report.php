<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cut mr-2"></i>Cut-off Report
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/cutoff/export?' . http_build_query($filters)) ?>" 
                       class="btn btn-success btn-sm">
                        <i class="fas fa-download mr-1"></i>Export CSV
                    </a>
                </div>
            </div>
            <div class="card-body">
                
                
                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-12">
                        <?= form_open(base_url('laporan/cutoff'), ['method' => 'GET', 'class' => 'form-inline']) ?>
                        <div class="form-group mr-3">
                            <label for="start_date" class="mr-2">Start Date:</label>
                            <input type="date" name="start_date" id="start_date" 
                                   class="form-control form-control-sm" 
                                   value="<?= $filters['start_date'] ?>">
                        </div>
                        <div class="form-group mr-3">
                            <label for="end_date" class="mr-2">End Date:</label>
                            <input type="date" name="end_date" id="end_date" 
                                   class="form-control form-control-sm" 
                                   value="<?= $filters['end_date'] ?>">
                        </div>
                        <div class="form-group mr-3">
                            <label for="outlet" class="mr-2">Outlet:</label>
                            <select name="outlet" id="outlet" class="form-control form-control-sm">
                                <option value="">All Outlets</option>
                                <?php foreach ($outlets as $outletItem): ?>
                                    <option value="<?= $outletItem->id ?>" 
                                            <?= $filters['outlet'] == $outletItem->id ? 'selected' : '' ?>>
                                        <?= esc($outletItem->nama) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search mr-1"></i>Filter
                        </button>
                        <?= form_close() ?>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= $summary['total_cutoffs'] ?></h3>
                                <p>Total Cut-offs</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-cut"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= number_format($summary['total_sales'], 0) ?></h3>
                                <p>Total Sales</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= number_format($summary['total_purchases'], 0) ?></h3>
                                <p>Total Purchases</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-<?= $summary['net_amount'] >= 0 ? 'success' : 'danger' ?>">
                            <div class="inner">
                                <h3><?= number_format($summary['net_amount'], 0) ?></h3>
                                <p>Net Amount</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-calculator"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cut-off Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="12%">Cut-off Date</th>
                                <th width="15%">Outlet</th>
                                <th width="12%">User</th>
                                <th width="12%">Total Sales</th>
                                <th width="12%">Total Purchases</th>
                                <th width="12%">Cash Amount</th>
                                <th width="12%">Net Amount</th>
                                <th width="8%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($cutoffs)): ?>
                                <?php $no = 1; foreach ($cutoffs as $cutoff): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= tgl_indo2($cutoff->{$dateField}) ?></td>
                                        <td><?= esc($cutoff->gudang ?? 'N/A') ?></td>
                                        <td><?= esc($cutoff->first_name ?? 'N/A') ?></td>
                                        <td class="text-right">Rp <?= number_format($cutoff->jml_gtotal ?? 0, 2) ?></td>
                                        <td class="text-right">Rp 0.00</td>
                                        <td class="text-right">Rp <?= number_format($cutoff->jml_bayar ?? 0, 2) ?></td>
                                        <td class="text-right">
                                            <span class="badge badge-success">
                                                Rp <?= number_format($cutoff->jml_gtotal ?? 0, 2) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php 
                                            $cutoffId = $cutoff->{$idField} ?? $cutoff->id ?? $cutoff->id_trans_jual ?? $cutoff->trans_id ?? 0;
                                            ?>
                                            <a href="<?= base_url('laporan/cutoff/detail/' . $cutoffId) ?>" 
                                               class="btn btn-info btn-sm" title="View Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="py-4">
                                            <i class="fas fa-cut fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No cut-off data found</h5>
                                            <p class="text-muted">Try adjusting your filter criteria</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
