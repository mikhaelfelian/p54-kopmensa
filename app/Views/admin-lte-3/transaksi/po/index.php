<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-20
 * 
 * Purchase Order Index View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <a href="<?= base_url('transaksi/po/export') ?>?<?= $_SERVER['QUERY_STRING'] ?>"
                    class="btn btn-sm btn-success rounded-0">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="<?= base_url('transaksi/po/trash') ?>" class="btn btn-sm btn-danger rounded-0">
                    <i class="fas fa-trash"></i> Sampah (<?= $transBeliPOModel->getTrashCount() ?>)
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
                        <?= form_open('transaksi/po', ['method' => 'get']) ?>
                            <div class="row">
                                <!-- PO Number Filter -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>No. PO</label>
                                        <?= form_input([
                                            'name' => 'no_nota',
                                            'class' => 'form-control form-control-sm rounded-0',
                                            'placeholder' => 'Nomor PO...',
                                            'value' => $_GET['no_nota'] ?? ($filters['no_nota'] ?? '')
                                        ]) ?>
                                    </div>
                                </div>
                                
                                <!-- Date Range Filter -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Tanggal Mulai</label>
                                        <input type="date" name="date_start" class="form-control form-control-sm rounded-0"
                                               value="<?= $_GET['date_start'] ?? ($filters['date_start'] ?? '') ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Tanggal Akhir</label>
                                        <input type="date" name="date_end" class="form-control form-control-sm rounded-0"
                                               value="<?= $_GET['date_end'] ?? '' ?>">
                                    </div>
                                </div>
                                
                                <!-- Supplier Filter -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Supplier</label>
                                        <select name="supplier" class="form-control form-control-sm rounded-0">
                                            <option value="">Semua Supplier</option>
                                            <?php foreach ($suppliers as $supplier): ?>
                                                <option value="<?= $supplier->id ?>" 
                                                        <?= ($_GET['supplier'] ?? ($filters['supplier'] ?? '')) == $supplier->id ? 'selected' : '' ?>>
                                                    <?= esc($supplier->nama) ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- Status Filter -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Status PO</label>
                                        <select name="status" class="form-control form-control-sm rounded-0">
                                            <option value="">Semua Status</option>
                                            <option value="0" <?= ($_GET['status'] ?? '') === '0' ? 'selected' : '' ?>>Draft</option>
                                            <option value="1" <?= ($_GET['status'] ?? '') === '1' ? 'selected' : '' ?>>Approved</option>
                                            <option value="2" <?= ($_GET['status'] ?? '') === '2' ? 'selected' : '' ?>>In Progress</option>
                                            <option value="3" <?= ($_GET['status'] ?? '') === '3' ? 'selected' : '' ?>>Completed</option>
                                            <option value="4" <?= ($_GET['status'] ?? '') === '4' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Total Items Range Filter -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Total Item Minimal</label>
                                        <input type="number" name="min_items" class="form-control form-control-sm rounded-0" 
                                               placeholder="0" min="0" 
                                               value="<?= $_GET['min_items'] ?? '' ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Total Item Maksimal</label>
                                        <input type="number" name="max_items" class="form-control form-control-sm rounded-0" 
                                               placeholder="999" min="0" 
                                               value="<?= $_GET['max_items'] ?? '' ?>">
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
                        if (!empty($_GET['no_nota'])) $activeFilters[] = 'No. PO: "' . $_GET['no_nota'] . '"';
                        if (!empty($_GET['date_start'])) $activeFilters[] = 'Tanggal dari ' . $_GET['date_start'];
                        if (!empty($_GET['date_end'])) $activeFilters[] = 'Tanggal sampai ' . $_GET['date_end'];
                        if (!empty($_GET['supplier'])) {
                            $selectedSupplier = '';
                            foreach ($suppliers as $supplier) {
                                if ($supplier->id == $_GET['supplier']) {
                                    $selectedSupplier = $supplier->nama;
                                    break;
                                }
                            }
                            $activeFilters[] = 'Supplier: ' . $selectedSupplier;
                        }
                        if (isset($_GET['status']) && $_GET['status'] !== '') {
                            $statusLabels = ['0' => 'Draft', '1' => 'Approved', '2' => 'In Progress', '3' => 'Completed', '4' => 'Cancelled'];
                            $activeFilters[] = 'Status: ' . $statusLabels[$_GET['status']];
                        }
                        if (!empty($_GET['min_items'])) $activeFilters[] = 'Item Min: ' . $_GET['min_items'];
                        if (!empty($_GET['max_items'])) $activeFilters[] = 'Item Max: ' . $_GET['max_items'];
                        echo implode(', ', $activeFilters);
                        ?>
                    <?php endif; ?>
                </small>
            </div>
            <div class="col-md-6 text-right">
                <small class="text-muted">
                    Total: <?= count($po_list ?? []) ?> PO
                </small>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>No. PO</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Total Item</th>
                        <th>Status</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($po_list)): ?>
                        <?php $no = 1 + ($pager->getCurrentPage() - 1) * $pager->getPerPage() ?>
                        <?php foreach ($po_list as $po): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($po->no_nota) ?></td>
                                <td><?= tgl_indo2($po->tgl_masuk) ?></td>
                                <td><?= esc($po->supplier_name) ?></td>
                                <td><?= $po->total_items ?></td>
                                <td>
                                    <?php $status = statusPO($po->status); ?>
                                    <span class="badge badge-<?= $status['badge'] ?>">
                                        <?= $status['label'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url("transaksi/po/detail/{$po->id}") ?>"
                                            class="btn btn-primary btn-sm rounded-0" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($po->status == 0): // Only show edit button for draft POs ?>
                                            <a href="<?= base_url("transaksi/po/edit/{$po->id}") ?>"
                                                class="btn btn-warning btn-sm rounded-0" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <?= $pager->links('po', 'adminlte_pagination') ?>
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

<?= $this->endSection() ?>