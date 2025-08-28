<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-money-bill-wave"></i> Manajemen Kas Kecil
        </h3>
        <div class="card-tools">
            <a href="<?= base_url('transaksi/petty/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Kas Kecil
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pettyEntries)) : ?>
                        <?php foreach ($pettyEntries as $entry) : ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($entry['created_at'])) ?></td>
                                <td><?= $entry['category_name'] ?? 'N/A' ?></td>
                                <td>
                                    <?php if ($entry['type'] === 'in') : ?>
                                        <span class="badge badge-success">Cash In</span>
                                    <?php else : ?>
                                        <span class="badge badge-danger">Cash Out</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right"><?= number_format($entry['amount'], 2) ?></td>
                                <td><?= $entry['description'] ?? '-' ?></td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch ($entry['status']) {
                                        case 'pending':
                                            $statusClass = 'badge badge-warning';
                                            $statusText = 'Pending';
                                            break;
                                        case 'approved':
                                            $statusClass = 'badge badge-success';
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
                                <td>
                                    <div class="btn-group">
                                                                                 <a href="<?= base_url('transaksi/petty/view/' . $entry['id']) ?>" 
                                           class="btn btn-info btn-sm" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($entry['status'] === 'pending') : ?>
                                                                                         <a href="<?= base_url('transaksi/petty/edit/' . $entry['id']) ?>" 
                                               class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                                                                         <a href="<?= base_url('transaksi/petty/approve/' . $entry['id']) ?>" 
                                               class="btn btn-success btn-sm" title="Approve"
                                               onclick="return confirm('Are you sure you want to approve this entry?')">
                                                <i class="fas fa-check"></i>
                                            </a>
                                                                                         <a href="<?= base_url('transaksi/petty/void/' . $entry['id']) ?>" 
                                               class="btn btn-danger btn-sm" title="Void"
                                               onclick="return confirm('Are you sure you want to void this entry?')">
                                                <i class="fas fa-ban"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center">No petty cash entries found</td>
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
