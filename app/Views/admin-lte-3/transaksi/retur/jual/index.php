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
                <!-- Filter Section -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card border-secondary">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-filter"></i> Filter Data
                                    <button type="button" class="btn btn-sm btn-outline-secondary float-right" onclick="toggleFilter()">
                                        <i class="fas fa-chevron-up"></i>
                                    </button>
                                </h6>
                            </div>
                            <div class="card-body" id="filterSection">
                                <?= form_open(current_url(), ['method' => 'get']) ?>
                                    <div class="row">
                                        <!-- Date Range Filter -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="start_date">Tanggal Mulai</label>
                                                <?= form_input([
                                                    'type' => 'date',
                                                    'name' => 'start_date',
                                                    'id' => 'start_date',
                                                    'class' => 'form-control form-control-sm rounded-0',
                                                    'value' => $_GET['start_date'] ?? ''
                                                ]) ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="end_date">Tanggal Akhir</label>
                                                <?= form_input([
                                                    'type' => 'date',
                                                    'name' => 'end_date',
                                                    'id' => 'end_date',
                                                    'class' => 'form-control form-control-sm rounded-0',
                                                    'value' => $_GET['end_date'] ?? ''
                                                ]) ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Search Filter -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="search">Pencarian</label>
                                                <?= form_input([
                                                    'type' => 'text',
                                                    'name' => 'search',
                                                    'id' => 'search',
                                                    'class' => 'form-control form-control-sm rounded-0',
                                                    'placeholder' => 'No. Retur, Pelanggan, No. Penjualan...',
                                                    'value' => $_GET['search'] ?? esc($search ?? '')
                                                ]) ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Return Type Filter -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="retur_type">Tipe Retur</label>
                                                <?= form_dropdown('retur_type', [
                                                    '' => 'Semua Tipe',
                                                    'refund' => 'Refund',
                                                    'exchange' => 'Tukar Barang'
                                                ], $_GET['retur_type'] ?? '', [
                                                    'class' => 'form-control form-control-sm rounded-0',
                                                    'id' => 'retur_type'
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <!-- Status Filter -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="status_retur">Status Retur</label>
                                                <?= form_dropdown('status_retur', [
                                                    '' => 'Semua Status',
                                                    '0' => 'Draft',
                                                    '1' => 'Selesai'
                                                ], $_GET['status_retur'] ?? '', [
                                                    'class' => 'form-control form-control-sm rounded-0',
                                                    'id' => 'status_retur'
                                                ]) ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Amount Range Filter -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="min_total">Total Minimal</label>
                                                <?= form_input([
                                                    'type' => 'number',
                                                    'name' => 'min_total',
                                                    'id' => 'min_total',
                                                    'class' => 'form-control form-control-sm rounded-0',
                                                    'placeholder' => '0',
                                                    'step' => '1000',
                                                    'value' => $_GET['min_total'] ?? ''
                                                ]) ?>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="max_total">Total Maksimal</label>
                                                <?= form_input([
                                                    'type' => 'number',
                                                    'name' => 'max_total',
                                                    'id' => 'max_total',
                                                    'class' => 'form-control form-control-sm rounded-0',
                                                    'placeholder' => '999999999',
                                                    'step' => '1000',
                                                    'value' => $_GET['max_total'] ?? ''
                                                ]) ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Filter Actions -->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div class="btn-group btn-block">
                                                    <button type="submit" class="btn btn-primary btn-sm rounded-0">
                                                        <i class="fas fa-search"></i> Filter
                                                    </button>
                                                    <a href="<?= current_url() ?>" class="btn btn-secondary btn-sm rounded-0">
                                                        <i class="fas fa-times"></i> Reset
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?= form_close() ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Results Summary -->
                <div class="row mb-2">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <?php if (!empty($_GET)) : ?>
                                <i class="fas fa-info-circle"></i> 
                                Filter aktif: 
                                <?php
                                $activeFilters = [];
                                if (!empty($_GET['start_date'])) $activeFilters[] = 'Tanggal dari ' . $_GET['start_date'];
                                if (!empty($_GET['end_date'])) $activeFilters[] = 'Tanggal sampai ' . $_GET['end_date'];
                                if (!empty($_GET['search'])) $activeFilters[] = 'Pencarian: "' . $_GET['search'] . '"';
                                if (isset($_GET['retur_type']) && $_GET['retur_type'] !== '') $activeFilters[] = 'Tipe: ' . ($_GET['retur_type'] === 'refund' ? 'Refund' : 'Tukar Barang');
                                if (isset($_GET['status_retur']) && $_GET['status_retur'] !== '') $activeFilters[] = 'Status: ' . ($_GET['status_retur'] === '0' ? 'Draft' : 'Selesai');
                                if (!empty($_GET['min_total'])) $activeFilters[] = 'Total Min: ' . number_format($_GET['min_total'], 0, ',', '.');
                                if (!empty($_GET['max_total'])) $activeFilters[] = 'Total Max: ' . number_format($_GET['max_total'], 0, ',', '.');
                                echo implode(', ', $activeFilters);
                                ?>
                            <?php endif; ?>
                        </small>
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

<!-- Filter Toggle JavaScript -->
<script>
function toggleFilter() {
    const filterSection = document.getElementById('filterSection');
    const toggleBtn = document.querySelector('[onclick="toggleFilter()"]');
    const icon = toggleBtn.querySelector('i');
    
    if (filterSection.style.display === 'none') {
        filterSection.style.display = 'block';
        icon.className = 'fas fa-chevron-up';
        toggleBtn.title = 'Sembunyikan Filter';
    } else {
        filterSection.style.display = 'none';
        icon.className = 'fas fa-chevron-down';
        toggleBtn.title = 'Tampilkan Filter';
    }
}

// Auto-hide filter on mobile devices
document.addEventListener('DOMContentLoaded', function() {
    if (window.innerWidth < 768) {
        document.getElementById('filterSection').style.display = 'none';
        document.querySelector('[onclick="toggleFilter()"] i').className = 'fas fa-chevron-down';
    }
});
</script>

<!-- Filter Styles -->
<style>
.card.border-secondary {
    border-color: #6c757d !important;
}

.card-header.bg-light {
    background-color: #f8f9fa !important;
    border-bottom: 1px solid #dee2e6;
}

.form-control-sm {
    height: calc(1.5em + 0.5rem + 2px);
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}

.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.2rem;
    border-bottom-left-radius: 0.2rem;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.2rem;
    border-bottom-right-radius: 0.2rem;
}

@media (max-width: 768px) {
    .col-md-3 {
        margin-bottom: 10px;
    }
    
    .btn-group {
        display: flex;
        flex-direction: column;
    }
    
    .btn-group .btn {
        border-radius: 0.2rem !important;
        margin-bottom: 5px;
    }
}
</style>

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