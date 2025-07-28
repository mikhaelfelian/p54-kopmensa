<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github: github.com/mikhaelfelian
 * Description: Index view for Sales Return transactions
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
                    <i class="fas fa-undo"></i> <?= $title ?>
                </h3>
                <div class="card-tools">
                    <div class="btn-group">
                        <a href="<?= base_url('transaksi/retur/jual/refund') ?>" class="btn btn-primary btn-sm rounded-0">
                            <i class="fas fa-money-bill-wave"></i> Retur Refund
                        </a>
                        <a href="<?= base_url('transaksi/retur/jual/exchange') ?>" class="btn btn-success btn-sm rounded-0">
                            <i class="fas fa-exchange-alt"></i> Retur Tukar Barang
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Search Form -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <?= form_open(base_url('transaksi/retur/jual'), ['method' => 'GET']) ?>
                        <div class="input-group">
                            <input type="text" class="form-control rounded-0" name="search" 
                                   value="<?= esc($search ?? '') ?>" placeholder="Cari berdasarkan nomor retur, nama pelanggan...">
                            <div class="input-group-append">
                                <button class="btn btn-primary rounded-0" type="submit">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>
                    <div class="col-md-6 text-right">
                        <small class="text-muted">
                            Total: <?= format_angka($totalReturns) ?> retur
                        </small>
                    </div>
                </div>

                <!-- Returns Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="12%">No. Retur</th>
                                <th width="12%">Tgl Retur</th>
                                <th width="15%">Pelanggan</th>
                                <th width="12%">No. Penjualan</th>
                                <th width="10%">Tipe</th>
                                <th width="12%">Total</th>
                                <th width="8%">Status</th>
                                <th width="8%">User</th>
                                <th width="6%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($returns)): ?>
                                <tr>
                                    <td colspan="10" class="text-center">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada data retur penjualan</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                $no = ($currentPage - 1) * $perPage + 1; 
                                foreach ($returns as $row): 
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <strong><?= esc($row->no_retur ?? '-') ?></strong>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($row->tgl_masuk ?? date('Y-m-d'))) ?></td>
                                        <td>
                                            <?= esc($row->customer_nama ?? 'N/A') ?>
                                        </td>
                                        <td>
                                            <?= esc($row->no_nota ?? '-') ?>
                                        </td>
                                        <td>
                                            <?php if (($row->retur_type ?? '') === 'refund'): ?>
                                                <span class="badge badge-info">
                                                    <i class="fas fa-money-bill-wave"></i> Refund
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-exchange-alt"></i> Tukar Barang
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <?= format_angka_rp($row->total_amount ?? 0) ?>
                                        </td>
                                        <td>
                                            <?php if (($row->status_retur ?? '0') == '1'): ?>
                                                <span class="badge badge-success">Selesai</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Draft</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?= esc($row->username ?? '-') ?></small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url("transaksi/retur/jual/" . (isset($row->id) ? $row->id : 1)) ?>" 
                                                   class="btn btn-info btn-sm rounded-0" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if (($row->status_retur ?? '0') == '0'): ?>
                                                    <a href="<?= base_url("transaksi/retur/jual/edit/" . (isset($row->id) ? $row->id : 1)) ?>" 
                                                       class="btn btn-warning btn-sm rounded-0" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm rounded-0" 
                                                            onclick="deleteRetur(<?= $row->id ?? 1 ?>)" title="Hapus">
                                                        <i class="fas fa-trash"></i>
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
                <?php if ($totalReturns > $perPage): ?>
                    <div class="row mt-3">
                        <div class="col-sm-12 col-md-5">
                            <div class="dataTables_info">
                                Menampilkan <?= ($currentPage - 1) * $perPage + 1 ?> sampai 
                                <?= min($currentPage * $perPage, $totalReturns) ?> dari <?= $totalReturns ?> data
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <?= $pager->makeLinks($currentPage, $perPage, $totalReturns, 'default_full') ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data retur ini?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Data yang sudah dihapus tidak dapat dikembalikan!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
let deleteId = null;

function deleteRetur(id) {
    deleteId = id;
    $('#deleteModal').modal('show');
}

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (deleteId) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url('transaksi/retur/jual/delete/') ?>' + deleteId;
        
        // Add CSRF token if available
        <?php if (csrf_token()): ?>
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= csrf_token() ?>';
        csrfInput.value = '<?= csrf_hash() ?>';
        form.appendChild(csrfInput);
        <?php endif; ?>
        
        document.body.appendChild(form);
        form.submit();
    }
});

// Auto hide alerts after 5 seconds
$(document).ready(function() {
    $('.alert').delay(5000).fadeOut();
});
</script>
<?= $this->endSection() ?> 