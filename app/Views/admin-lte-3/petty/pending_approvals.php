<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-28
 * Github: github.com/mikhaelfelian
 * Description: Pending Approvals view for Petty Cash Management
 * This file represents the View.
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
                    <i class="fas fa-clock"></i> <?= $title ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('transaksi/petty') ?>" class="btn btn-secondary btn-sm rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($pendingApprovals)) : ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                        <h5>Tidak ada transaksi yang menunggu persetujuan</h5>
                        <p>Semua transaksi petty cash telah disetujui atau diproses</p>
                    </div>
                <?php else : ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="pendingApprovalsTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Alasan</th>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                foreach ($pendingApprovals as $approval) : 
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($approval['created_at'])) ?></td>
                                        <td>
                                            <?php if ($approval['category_name']) : ?>
                                                <span class="badge badge-info"><?= $approval['category_name'] ?></span>
                                            <?php else : ?>
                                                <span class="badge badge-secondary">Tanpa Kategori</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($approval['direction'] == 'IN') : ?>
                                                <span class="badge badge-success">Kas Masuk</span>
                                            <?php else : ?>
                                                <span class="badge badge-danger">Kas Keluar</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right font-weight-bold">
                                            Rp <?= number_format($approval['amount'], 0, ',', '.') ?>
                                        </td>
                                        <td>
                                            <span title="<?= $approval['reason'] ?>">
                                                <?= strlen($approval['reason']) > 50 ? substr($approval['reason'], 0, 50) . '...' : $approval['reason'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary"><?= $approval['user_name'] ?? 'Unknown' ?></span>
                                        </td>
                                        <td>
                                            <?php 
                                            $statusClass = '';
                                            $statusText = '';
                                            switch ($approval['status']) {
                                                case 'draft':
                                                    $statusClass = 'badge-warning';
                                                    $statusText = 'Draft';
                                                    break;
                                                case 'pending':
                                                    $statusClass = 'badge-info';
                                                    $statusText = 'Menunggu';
                                                    break;
                                                case 'approved':
                                                    $statusClass = 'badge-success';
                                                    $statusText = 'Disetujui';
                                                    break;
                                                case 'rejected':
                                                    $statusClass = 'badge-danger';
                                                    $statusText = 'Ditolak';
                                                    break;
                                                default:
                                                    $statusClass = 'badge-secondary';
                                                    $statusText = $approval['status'];
                                            }
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('transaksi/petty/view/' . $approval['id']) ?>" 
                                                   class="btn btn-info btn-sm" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <?php if (in_array($approval['status'], ['draft', 'pending'])) : ?>
                                                    <button type="button" 
                                                            class="btn btn-success btn-sm" 
                                                            title="Setujui"
                                                            onclick="approveTransaction(<?= $approval['id'] ?>)">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    
                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm" 
                                                            title="Tolak"
                                                            onclick="rejectTransaction(<?= $approval['id'] ?>)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tolak Transaksi</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="rejectionForm" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required 
                                  placeholder="Berikan alasan mengapa transaksi ini ditolak..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#pendingApprovalsTable').DataTable({
        "responsive": true,
        "autoWidth": false,
        "pageLength": 25,
        "order": [[1, "desc"]], // Sort by date descending
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });
});

function approveTransaction(id) {
    if (confirm('Apakah Anda yakin ingin menyetujui transaksi ini?')) {
        // Create form and submit
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url('transaksi/petty/approve/') ?>' + id;
        
        // Add CSRF token
        var csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '<?= csrf_token() ?>';
        csrf.value = '<?= csrf_hash() ?>';
        form.appendChild(csrf);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function rejectTransaction(id) {
    // Set the form action
    $('#rejectionForm').attr('action', '<?= base_url('transaksi/petty/reject/') ?>' + id);
    
    // Show the modal
    $('#rejectionModal').modal('show');
}

// Handle rejection form submission
$('#rejectionForm').on('submit', function(e) {
    var reason = $('#rejection_reason').val().trim();
    if (reason.length < 10) {
        e.preventDefault();
        alert('Alasan penolakan minimal 10 karakter!');
        return false;
    }
    
    if (confirm('Apakah Anda yakin ingin menolak transaksi ini?')) {
        // Add CSRF token to form
        var csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '<?= csrf_token() ?>';
        csrf.value = '<?= csrf_hash() ?>';
        this.appendChild(csrf);
        
        return true;
    }
    return false;
});

// Reset modal when closed
$('#rejectionModal').on('hidden.bs.modal', function () {
    $('#rejection_reason').val('');
    $('#rejectionForm').attr('action', '');
});
</script>
<?= $this->endSection() ?>
