<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Description: Approval view for Refund Requests (Superadmin)
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
                    <i class="fas fa-gavel"></i> <?= $title ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('transaksi/refund') ?>" class="btn btn-secondary btn-sm rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Summary Cards -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Pending</span>
                                <span class="info-box-number"><?= $pendingCount ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Disetujui</span>
                                <span class="info-box-number"><?= $totalRefunds - $pendingCount ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="GET" action="<?= base_url('transaksi/refund/approval') ?>" class="form-inline">
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
                        <form method="GET" action="<?= base_url('transaksi/refund/approval') ?>" class="form-inline justify-content-end">
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
                                <th>Kasir</th>
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
                                    <td colspan="9" class="text-center">Tidak ada data permintaan refund</td>
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
                                            <?= ($refund->first_name ?? '') . ' ' . ($refund->last_name ?? '') ?>
                                            <br>
                                            <small class="text-muted"><?= $refund->username ?? '' ?></small>
                                        </td>
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
                                                <?php if ($refund->status === 'pending'): ?>
                                                    <button type="button" 
                                                            class="btn btn-success btn-sm rounded-0 approve-btn"
                                                            data-id="<?= $refund->id ?>"
                                                            data-nota="<?= $refund->no_nota ?>"
                                                            title="Setujui">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm rounded-0 reject-btn"
                                                            data-id="<?= $refund->id ?>"
                                                            data-nota="<?= $refund->no_nota ?>"
                                                            title="Tolak">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                <?php endif; ?>
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

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="reject-form" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Permintaan Refund</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menolak permintaan refund untuk nota <strong id="reject-nota"></strong>?</p>
                    <div class="form-group">
                        <label for="rejection_reason">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="3" 
                                  class="form-control rounded-0" 
                                  placeholder="Jelaskan alasan penolakan" required></textarea>
                        <div class="invalid-feedback">Alasan penolakan wajib diisi.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-0" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-0">
                        <i class="fas fa-times"></i> Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    console.log('Refund approval page loaded');
    
    // Check if jQuery is loaded
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded!');
        return;
    }
    
    // Approve refund request
    $('.approve-btn').on('click', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const nota = $(this).data('nota');
        
        console.log('Approve button clicked for ID:', id, 'Nota:', nota);
        
        if (confirm(`Anda yakin ingin menyetujui permintaan refund untuk nota ${nota}?`)) {
            window.location.href = `<?= base_url('transaksi/refund/approve') ?>/${id}`;
        }
    });

    // Reject refund request
    $('.reject-btn').on('click', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const nota = $(this).data('nota');
        
        console.log('Reject button clicked for ID:', id, 'Nota:', nota);
        
        $('#reject-nota').text(nota);
        $('#reject-form').attr('action', `<?= base_url('transaksi/refund/reject') ?>/${id}`);
        $('#rejectModal').modal('show');
    });

    // Form validation for reject form
    $('#reject-form').on('submit', function(e) {
        const form = this;
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>
<?= $this->endSection() ?>
