<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-04
 * 
 * Purchase Transaction List View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">Data Pembelian</h3>
        <div class="card-tools">
            
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
                        <form method="GET" action="<?= current_url() ?>">
                            <div class="row">
                                <!-- Date Range Filter -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Tanggal Mulai</label>
                                        <input type="date" name="start_date" class="form-control form-control-sm rounded-0" 
                                               value="<?= $_GET['start_date'] ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Tanggal Akhir</label>
                                        <input type="date" name="end_date" class="form-control form-control-sm rounded-0" 
                                               value="<?= $_GET['end_date'] ?? '' ?>">
                                    </div>
                                </div>
                                
                                <!-- Supplier Filter -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Supplier</label>
                                        <select name="id_supplier" class="form-control form-control-sm rounded-0">
                                            <option value="">Semua Supplier</option>
                                            <?php if (!empty($suppliers)): ?>
                                                <?php foreach ($suppliers as $supplier): ?>
                                                    <option value="<?= $supplier->id ?>" <?= ($_GET['id_supplier'] ?? '') == $supplier->id ? 'selected' : '' ?>>
                                                        <?= esc($supplier->nama) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Search Filter -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Pencarian</label>
                                        <input type="text" name="search" class="form-control form-control-sm rounded-0" 
                                               placeholder="No. Faktur, No. PO..." 
                                               value="<?= $_GET['search'] ?? '' ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- Status PPN Filter -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Status PPN</label>
                                        <select name="status_ppn" class="form-control form-control-sm rounded-0">
                                            <option value="">Semua Status</option>
                                            <option value="0" <?= ($_GET['status_ppn'] ?? '') === '0' ? 'selected' : '' ?>>Non PPN</option>
                                            <option value="1" <?= ($_GET['status_ppn'] ?? '') === '1' ? 'selected' : '' ?>>Tambah PPN</option>
                                            <option value="2" <?= ($_GET['status_ppn'] ?? '') === '2' ? 'selected' : '' ?>>Include PPN</option>
                                        </select>
                                    </div>
                                </div>
                            
                            <div class="row">
                                <!-- Payment Status Filter -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Status Bayar</label>
                                        <select name="status_bayar" class="form-control form-control-sm rounded-0">
                                            <option value="">Semua Status</option>
                                            <option value="0" <?= ($_GET['status_bayar'] ?? '') === '0' ? 'selected' : '' ?>>Belum Lunas</option>
                                            <option value="1" <?= ($_GET['status_bayar'] ?? '') === '1' ? 'selected' : '' ?>>Lunas</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Amount Range Filter -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Total Minimal</label>
                                        <input type="number" name="min_total" class="form-control form-control-sm rounded-0" 
                                               placeholder="0" step="1000" 
                                               value="<?= $_GET['min_total'] ?? '' ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Total Maksimal</label>
                                        <input type="number" name="max_total" class="form-control form-control-sm rounded-0" 
                                               placeholder="999999999" step="1000" 
                                               value="<?= $_GET['max_total'] ?? '' ?>">
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
                        </form>
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
                        if (isset($_GET['status_ppn']) && $_GET['status_ppn'] !== '') $activeFilters[] = 'Status PPN: ' . ['Non PPN', 'Tambah PPN', 'Include PPN'][$_GET['status_ppn']];
                        if (isset($_GET['status_bayar']) && $_GET['status_bayar'] !== '') $activeFilters[] = 'Status Bayar: ' . ['Belum Lunas', 'Lunas'][$_GET['status_bayar']];
                        if (!empty($_GET['min_total'])) $activeFilters[] = 'Total Min: ' . number_format($_GET['min_total'], 0, ',', '.');
                        if (!empty($_GET['max_total'])) $activeFilters[] = 'Total Max: ' . number_format($_GET['max_total'], 0, ',', '.');
                        echo implode(', ', $activeFilters);
                        ?>
                    <?php endif; ?>
                </small>
            </div>
            <div class="col-md-6 text-right">
                <small class="text-muted">
                    Total: <?= number_format($totalRecords ?? 0, 0, ',', '.') ?> transaksi
                </small>
            </div>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th>No. Faktur</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>No. PO</th>
                    <th>Total</th>
                    <th>Status PPN</th>
                    <th>Status Bayar</th>
                    <th width="10%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transaksi)) : ?>
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data</td>
                    </tr>
                <?php else : ?>
                    <?php 
                    $startNumber = ($currentPage - 1) * $perPage;
                    foreach ($transaksi as $index => $row) : 
                    ?>
                        <tr>
                            <td class="text-center"><?= $startNumber + $index + 1 ?></td>
                            <td><?= esc($row->no_nota) ?></td>
                            <td><?= date('d/m/Y', strtotime($row->created_at)) ?></td>
                            <td><?= esc($row->supplier) ?></td>
                            <td><?= esc($row->no_po) ?></td>
                            <td class="text-right">
                                <?= number_format($row->jml_gtotal, 2, ',', '.') ?>
                            </td>
                            <td>
                                <?php
                                $ppnStatus = [
                                    '0' => '<span class="badge badge-secondary">Non PPN</span>',
                                    '1' => '<span class="badge badge-info">Tambah PPN</span>',
                                    '2' => '<span class="badge badge-primary">Include PPN</span>'
                                ];
                                echo $ppnStatus[$row->status_ppn] ?? '';
                                ?>
                            </td>
                            <td>
                                <?php
                                $paymentStatus = [
                                    '0' => '<span class="badge badge-warning">Belum Lunas</span>',
                                    '1' => '<span class="badge badge-success">Lunas</span>'
                                ];
                                echo $paymentStatus[$row->status_bayar] ?? '';
                                ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= base_url("transaksi/beli/detail/{$row->id}") ?>" 
                                       class="btn btn-default btn-sm" 
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($row->status_bayar != '1') : ?>
                                        <a href="<?= base_url("transaksi/beli/edit/{$row->id}") ?>" 
                                           class="btn btn-default btn-sm" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        <?= $pager->links('transbeli', 'adminlte_pagination') ?>
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

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Delete confirmation
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        })
    });
});
</script>
<?= $this->endSection() ?> 