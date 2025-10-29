<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-28
 * Github: github.com/mikhaelfelian
 * Description: View Detail for Petty Cash Management
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
                    <i class="fas fa-eye"></i> <?= $title ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('transaksi/petty') ?>" class="btn btn-secondary btn-sm rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                    <?php if (in_array($petty['status'], ['draft', 'posted'])) : ?>
                        <a href="<?= base_url('transaksi/petty/edit/' . $petty['id']) ?>" class="btn btn-warning btn-sm rounded-0">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    <?php endif; ?>
                    <?php if ($petty['status'] !== 'void') : ?>
                        <button type="button" class="btn btn-danger btn-sm rounded-0" onclick="voidTransaction()">
                            <i class="fas fa-ban"></i> Void
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Transaction Details -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-info-circle"></i> Detail Transaksi
                                </h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="150"><strong>ID Transaksi:</strong></td>
                                        <td>#<?= $petty['id'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal:</strong></td>
                                        <td><?= date('d/m/Y H:i', strtotime($petty['created_at'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jenis:</strong></td>
                                        <td>
                                            <?php if ($petty['direction'] == 'IN') : ?>
                                                <span class="badge badge-success badge-lg">Kas Masuk</span>
                                            <?php else : ?>
                                                <span class="badge badge-danger badge-lg">Kas Keluar</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jumlah:</strong></td>
                                        <td>
                                            <h4 class="text-<?= $petty['direction'] == 'IN' ? 'success' : 'danger' ?>">
                                                Rp <?= number_format($petty['amount'], 0, ',', '.') ?>
                                            </h4>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kategori:</strong></td>
                                        <td>
                                            <?php if ($petty['category_name']) : ?>
                                                <span class="badge badge-info"><?= $petty['category_name'] ?></span>
                                            <?php else : ?>
                                                <span class="badge badge-secondary">Tanpa Kategori</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Alasan:</strong></td>
                                        <td><?= $petty['reason'] ?></td>
                                    </tr>
                                    <?php if ($petty['ref_no']) : ?>
                                    <tr>
                                        <td><strong>Referensi:</strong></td>
                                        <td><?= $petty['ref_no'] ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <?php 
                                            $statusClass = '';
                                            $statusText = '';
                                            switch ($petty['status']) {
                                                case 'draft':
                                                    $statusClass = 'badge-warning';
                                                    $statusText = 'Draft';
                                                    break;
                                                case 'posted':
                                                    $statusClass = 'badge-info';
                                                    $statusText = 'Posted';
                                                    break;
                                                case 'approved':
                                                    $statusClass = 'badge-success';
                                                    $statusText = 'Disetujui';
                                                    break;
                                                case 'rejected':
                                                    $statusClass = 'badge-danger';
                                                    $statusText = 'Ditolak';
                                                    break;
                                                case 'void':
                                                    $statusClass = 'badge-secondary';
                                                    $statusText = 'Void';
                                                    break;
                                                default:
                                                    $statusClass = 'badge-secondary';
                                                    $statusText = $petty['status'];
                                            }
                                            ?>
                                            <span class="badge <?= $statusClass ?> badge-lg"><?= $statusText ?></span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-user"></i> Informasi User
                                </h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Kasir:</strong></td>
                                        <td><?= $petty['user_name'] ?? 'Unknown' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Outlet:</strong></td>
                                        <td><?= $petty['outlet_name'] ?? 'Unknown' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Shift:</strong></td>
                                        <td><?= $petty['shift_code'] ?? 'Unknown' ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <?php if ($petty['status'] == 'void' && $petty['void_reason']) : ?>
                        <div class="card mt-3">
                            <div class="card-header bg-danger text-white">
                                <h5 class="card-title">
                                    <i class="fas fa-ban"></i> Alasan Void
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted"><?= $petty['void_reason'] ?></p>
                                <small class="text-muted">
                                    Divoid pada: <?= date('d/m/Y H:i', strtotime($petty['void_at'])) ?>
                                </small>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($petty['status'] == 'rejected' && $petty['rejection_reason']) : ?>
                        <div class="card mt-3">
                            <div class="card-header bg-danger text-white">
                                <h5 class="card-title">
                                    <i class="fas fa-times"></i> Alasan Penolakan
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted"><?= $petty['rejection_reason'] ?></p>
                                <small class="text-muted">
                                    Ditolak pada: <?= date('d/m/Y H:i', strtotime($petty['rejected_at'])) ?>
                                </small>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <a href="<?= base_url('transaksi/petty') ?>" class="btn btn-secondary rounded-0">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                        </a>
                        
                        <?php if (in_array($petty['status'], ['draft', 'posted'])) : ?>
                            <a href="<?= base_url('transaksi/petty/edit/' . $petty['id']) ?>" class="btn btn-warning rounded-0">
                                <i class="fas fa-edit"></i> Edit Transaksi
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($petty['status'] !== 'void') : ?>
                            <button type="button" class="btn btn-danger rounded-0" onclick="voidTransaction()">
                                <i class="fas fa-ban"></i> Void Transaksi
                            </button>
                        <?php endif; ?>
                        
                        <button type="button" class="btn btn-info rounded-0" onclick="printTransaction()">
                            <i class="fas fa-print"></i> Cetak
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Void Modal -->
<div class="modal fade" id="voidModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Void Transaksi</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="voidForm" method="POST" action="<?= base_url('transaksi/petty/void/' . $petty['id']) ?>">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Peringatan!</strong> Tindakan void tidak dapat dibatalkan dan akan mempengaruhi laporan keuangan.
                    </div>
                    <div class="form-group">
                        <label for="void_reason">Alasan Void <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="void_reason" name="reason" rows="3" required 
                                  placeholder="Berikan alasan mengapa transaksi ini di-void..."></textarea>
                        <small class="form-text text-muted">Alasan void minimal 10 karakter</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Void Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
function voidTransaction() {
    $('#voidModal').modal('show');
}

function printTransaction() {
    // Open print window
    var printWindow = window.open('', '_blank');
    var content = document.querySelector('.card-body').innerHTML;
    
    printWindow.document.write(`
        <html>
            <head>
                <title>Petty Cash - #<?= $petty['id'] ?></title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                    .table td, .table th { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    .badge { padding: 5px 10px; border-radius: 3px; color: white; }
                    .badge-success { background-color: #28a745; }
                    .badge-danger { background-color: #dc3545; }
                    .badge-info { background-color: #17a2b8; }
                    .badge-warning { background-color: #ffc107; color: #212529; }
                    .text-center { text-align: center; }
                    .text-right { text-align: right; }
                    .font-weight-bold { font-weight: bold; }
                    @media print {
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <h2>Detail Transaksi Petty Cash</h2>
                <h3>#<?= $petty['id'] ?></h3>
                ${content}
                <div class="no-print">
                    <p><small>Dicetak pada: ${new Date().toLocaleString('id-ID')}</small></p>
                </div>
            </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    // Print functionality disabled - use new unified print system
    printWindow.close();
}

// Handle void form submission
$('#voidForm').on('submit', function(e) {
    var reason = $('#void_reason').val().trim();
    if (reason.length < 10) {
        e.preventDefault();
        alert('Alasan void minimal 10 karakter!');
        return false;
    }
    
    if (confirm('Apakah Anda yakin ingin void transaksi ini? Tindakan ini tidak dapat dibatalkan!')) {
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
$('#voidModal').on('hidden.bs.modal', function () {
    $('#void_reason').val('');
});
</script>
<?= $this->endSection() ?>
