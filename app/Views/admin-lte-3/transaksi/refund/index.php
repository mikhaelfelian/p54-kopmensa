<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Description: Index view for Refund Requests
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
                    <i class="fas fa-money-bill-wave"></i> <?= $title ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('transaksi/refund/create') ?>" class="btn btn-primary btn-sm rounded-0">
                        <i class="fas fa-plus"></i> Refund
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Search and Filter -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="GET" action="<?= base_url('transaksi/refund') ?>" class="form-inline">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control rounded-0" 
                                       placeholder="Cari nota, pelanggan, atau alasan..." 
                                       value="<?= $search ?? '' ?>">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-outline-secondary rounded-0">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" action="<?= base_url('transaksi/refund') ?>" class="form-inline justify-content-end">
                            <select name="status" class="form-control rounded-0 mr-2" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="pending" <?= ($status ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="approved" <?= ($status ?? '') === 'approved' ? 'selected' : '' ?>>Disetujui</option>
                                <option value="rejected" <?= ($status ?? '') === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
                            </select>
                        </form>
                    </div>
                </div>

                <!-- Refund Requests Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>No. Nota</th>
                                <th>Pelanggan</th>
                                <th>Jumlah Refund</th>
                                <th>Alasan</th>
                                <th>Status</th>
                                <th>Tanggal Request</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($refundRequests)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data permintaan refund</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($refundRequests as $index => $refund): ?>
                                    <tr>
                                        <td><?= ($currentPage - 1) * $perPage + $index + 1 ?></td>
                                        <td>
                                            <strong><?= $refund->no_nota ?></strong>
                                            <br>
                                            <small class="text-muted"><?= $refund->transaction_no ?? '-' ?></small>
                                        </td>
                                        <td><?= $refund->customer_name ?? '-' ?></td>
                                        <td>
                                            <span class="badge badge-info">
                                                Rp <?= number_format($refund->amount, 0, ',', '.') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= character_limiter($refund->reason, 50) ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            $statusText = '';
                                            switch ($refund->status) {
                                                case 'pending':
                                                    $statusClass = 'badge-warning';
                                                    $statusText = 'Pending';
                                                    break;
                                                case 'approved':
                                                    $statusClass = 'badge-success';
                                                    $statusText = 'Disetujui';
                                                    break;
                                                case 'rejected':
                                                    $statusClass = 'badge-danger';
                                                    $statusText = 'Ditolak';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                        </td>
                                        <td>
                                            <?= date('d/m/Y H:i', strtotime($refund->created_at)) ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= base_url('transaksi/refund/' . $refund->id) ?>" 
                                                   class="btn btn-info btn-sm rounded-0" 
                                                   title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalRefunds > $perPage): ?>
                    <div class="d-flex justify-content-center mt-3">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
