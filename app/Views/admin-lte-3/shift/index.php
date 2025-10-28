<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-clock"></i> Shift Manajemen
        </h3>
        <div class="card-tools">
            <a href="<?= base_url('transaksi/shift/open') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Buka Shift Baru
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Kode Shift</th>
                        <th>Outlet</th>
                        <th>User Buka</th>
                        <th>Waktu Mulai</th>
                        <th>Waktu Selesai</th>
                        <th>Status</th>
                        <th>Uang Modal</th>
                        <th>Penjualan Tunai</th>
                        <th>Kas Kecil</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($shifts)) : ?>
                        <?php foreach ($shifts as $shift) : ?>
                            <tr>
                                <td><?= $shift['shift_code'] ?></td>
                                <td><?= $shift['outlet_name'] ?? 'Outlet ID: ' . $shift['outlet_id'] ?></td>
                                <td>
                                    <?php 
                                    $userName = trim(($shift['user_open_name'] ?? '') . ' ' . ($shift['user_open_lastname'] ?? ''));
                                    if (empty($userName) || $userName === 'Unknown') {
                                        echo 'User ID: ' . ($shift['user_open_id'] ?? 'N/A');
                                    } else {
                                        echo $userName;
                                    }
                                    ?>
                                </td>
                                <td><?= tgl_indo8($shift['start_at']) ?></td>
                                <td><?= $shift['end_at'] ? tgl_indo8($shift['end_at']) : '-' ?></td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch ($shift['status']) {
                                        case 'open':
                                            $statusClass = 'badge badge-success';
                                            $statusText = 'Open';
                                            break;
                                        case 'closed':
                                            $statusClass = 'badge badge-warning';
                                            $statusText = 'Closed';
                                            break;
                                        case 'approved':
                                            $statusClass = 'badge badge-info';
                                            $statusText = 'Approved';
                                            break;
                                        case 'void':
                                            $statusClass = 'badge badge-danger';
                                            $statusText = 'Void';
                                            break;
                                        default:
                                            $statusClass = 'badge badge-secondary';
                                            $statusText = 'Unknown';
                                    }
                                    ?>
                                    <span class="<?= $statusClass ?>"><?= $statusText ?></span>
                                </td>
                                <td class="text-right"><?= format_angka($shift['open_float']) ?></td>
                                <td class="text-right"><?= format_angka($shift['sales_cash_total']) ?></td>
                                <td class="text-right">
                                    <?= format_angka($shift['petty_in_total'] - $shift['petty_out_total']) ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <?php if ($shift['status'] === 'open') : ?>
                                            <?php if ($shift['user_open_id'] == session('user_id')) : ?>
                                                <a href="<?= base_url('transaksi/shift/close/' . $shift['id']) ?>" 
                                                   class="btn btn-warning btn-sm" title="Close Shift">
                                                    <i class="fas fa-stop"></i>
                                                </a>
                                                <a href="<?= base_url('transaksi/shift/reopen/' . $shift['id']) ?>" 
                                                   class="btn btn-info btn-sm" title="Buka Kembali Sesi">
                                                    <i class="fas fa-redo"></i> Buka Kembali
                                                </a>
                                            <?php else : ?>
                                                <button class="btn btn-secondary btn-sm" disabled title="Hanya user yang membuka shift yang dapat menutup shift ini">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php elseif ($shift['status'] === 'closed') : ?>
                                            <a href="<?= base_url('transaksi/shift/approve/' . $shift['id']) ?>" 
                                               class="btn btn-success btn-sm" title="Approve Shift"
                                               onclick="return confirm('Are you sure you want to approve this shift?')">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="10" class="text-center">No shifts found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize DataTable if available
    if ($.fn.DataTable) {
        $('.table').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print"]
        }).buttons().container().appendTo('.card-header .card-tools');
    }
});
</script>
<?= $this->endSection() ?>
