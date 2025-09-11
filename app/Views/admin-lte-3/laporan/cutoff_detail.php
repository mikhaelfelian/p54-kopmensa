<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cut mr-2"></i>Transaction Detail - <?= tgl_indo2($cutoff->tgl_masuk) ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/cutoff') ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Back to Report
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Cut-off Summary -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-info-circle mr-2"></i>Transaction Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Date:</strong></td>
                                        <td><?= tgl_indo2($cutoff->tgl_masuk) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Invoice:</strong></td>
                                        <td><?= esc($cutoff->no_nota ?: 'N/A') ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Outlet:</strong></td>
                                        <td><?= esc($cutoff->gudang ?: 'N/A') ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>User:</strong></td>
                                        <td><?= esc($cutoff->first_name ?: 'N/A') ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Customer:</strong></td>
                                        <td><?= esc($cutoff->nama_pelanggan ?: 'Walk-in Customer') ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-calculator mr-2"></i>Financial Summary
                                </h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Total Amount:</strong></td>
                                        <td class="text-right text-success">
                                            <strong>Rp <?= number_format($cutoff->jml_gtotal ?? 0, 2) ?></strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Payment Amount:</strong></td>
                                        <td class="text-right text-info">
                                            <strong>Rp <?= number_format($cutoff->jml_bayar ?? 0, 2) ?></strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Change Amount:</strong></td>
                                        <td class="text-right text-warning">
                                            <strong>Rp <?= number_format($cutoff->jml_kembali ?? 0, 2) ?></strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Payment Method:</strong></td>
                                        <td class="text-right">
                                            <span class="badge badge-primary">
                                                <?= $cutoff->metode_bayar ?? 'Cash' ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="border-top">
                                        <td><strong>Status:</strong></td>
                                        <td class="text-right">
                                            <span class="badge badge-<?= $cutoff->status_bayar == '1' ? 'success' : 'warning' ?> badge-lg">
                                                <?= $cutoff->status_bayar == '1' ? 'Paid' : 'Pending' ?>
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($cutoff->notes)): ?>
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Notes:</h5>
                        <?= nl2br(esc($cutoff->notes)) ?>
                    </div>
                <?php endif; ?>

                <!-- Sales Transactions -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-success">
                                <h3 class="card-title">
                                    <i class="fas fa-shopping-cart mr-2"></i>Sales Transactions (<?= count($sales) ?>)
                                </h3>
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                <?php if (!empty($sales)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Invoice</th>
                                                    <th>Customer</th>
                                                    <th class="text-right">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($sales as $sale): ?>
                                                    <tr>
                                                        <td>
                                                            <small><?= esc($sale->no_nota) ?></small><br>
                                                            <small class="text-muted"><?= date('H:i', strtotime($sale->created_at)) ?></small>
                                                        </td>
                                                        <td>
                                                            <small><?= esc($sale->customer_name ?: 'Walk-in Customer') ?></small>
                                                        </td>
                                                        <td class="text-right">
                                                            <small>Rp <?= number_format($sale->jml_gtotal, 0) ?></small>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-3">
                                        <i class="fas fa-shopping-cart fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">No sales transactions</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Transactions -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-warning">
                                <h3 class="card-title">
                                    <i class="fas fa-shopping-bag mr-2"></i>Purchase Transactions (<?= count($purchases) ?>)
                                </h3>
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                <?php if (!empty($purchases)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Invoice</th>
                                                    <th>Supplier</th>
                                                    <th class="text-right">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($purchases as $purchase): ?>
                                                    <tr>
                                                        <td>
                                                            <small><?= esc($purchase->no_nota) ?></small><br>
                                                            <small class="text-muted"><?= date('H:i', strtotime($purchase->created_at)) ?></small>
                                                        </td>
                                                        <td>
                                                            <small><?= esc($purchase->supplier_name ?: 'N/A') ?></small>
                                                        </td>
                                                        <td class="text-right">
                                                            <small>Rp <?= number_format($purchase->jml_gtotal, 0) ?></small>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-3">
                                        <i class="fas fa-shopping-bag fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">No purchase transactions</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
