<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-29
 * 
 * Purchase Receiving List View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-truck-loading mr-1"></i> Penerimaan Barang
        </h3>
        <div class="card-tools">
            <span class="badge badge-info">Transaksi Siap Diterima</span>
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
                                <!-- Invoice Number Filter -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>No. Faktur</label>
                                        <input type="text" name="no_nota" class="form-control form-control-sm rounded-0" 
                                               placeholder="Nomor faktur..." 
                                               value="<?= $_GET['no_nota'] ?? '' ?>">
                                    </div>
                                </div>
                                
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
                                
                                <!-- PO Number Filter -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>No. PO</label>
                                        <input type="text" name="no_po" class="form-control form-control-sm rounded-0" 
                                               placeholder="Nomor PO..." 
                                               value="<?= $_GET['no_po'] ?? '' ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- Supplier Filter -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Supplier</label>
                                        <input type="text" name="supplier" class="form-control form-control-sm rounded-0" 
                                               placeholder="Nama supplier..." 
                                               value="<?= $_GET['supplier'] ?? '' ?>">
                                    </div>
                                </div>
                                
                                <!-- PPN Status Filter -->
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
                                
                                <!-- Receive Status Filter -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Status Terima</label>
                                        <select name="status_terima" class="form-control form-control-sm rounded-0">
                                            <option value="">Semua Status</option>
                                            <option value="0" <?= ($_GET['status_terima'] ?? '') === '0' ? 'selected' : '' ?>>Belum Diterima</option>
                                            <option value="1" <?= ($_GET['status_terima'] ?? '') === '1' ? 'selected' : '' ?>>Sudah Diterima</option>
                                            <option value="2" <?= ($_GET['status_terima'] ?? '') === '2' ? 'selected' : '' ?>>Ditolak</option>
                                        </select>
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
                        if (!empty($_GET['no_nota'])) $activeFilters[] = 'No. Faktur: "' . $_GET['no_nota'] . '"';
                        if (!empty($_GET['start_date'])) $activeFilters[] = 'Tanggal dari ' . $_GET['start_date'];
                        if (!empty($_GET['end_date'])) $activeFilters[] = 'Tanggal sampai ' . $_GET['end_date'];
                        if (!empty($_GET['no_po'])) $activeFilters[] = 'No. PO: "' . $_GET['no_po'] . '"';
                        if (!empty($_GET['supplier'])) $activeFilters[] = 'Supplier: "' . $_GET['supplier'] . '"';
                        if (isset($_GET['status_ppn']) && $_GET['status_ppn'] !== '') {
                            $ppnLabels = ['0' => 'Non PPN', '1' => 'Tambah PPN', '2' => 'Include PPN'];
                            $activeFilters[] = 'Status PPN: ' . $ppnLabels[$_GET['status_ppn']];
                        }
                        if (isset($_GET['status_terima']) && $_GET['status_terima'] !== '') {
                            $receiveLabels = ['0' => 'Belum Diterima', '1' => 'Sudah Diterima', '2' => 'Ditolak'];
                            $activeFilters[] = 'Status Terima: ' . $receiveLabels[$_GET['status_terima']];
                        }
                        echo implode(', ', $activeFilters);
                        ?>
                    <?php endif; ?>
                </small>
            </div>
            <div class="col-md-6 text-right">
                <small class="text-muted">
                    Total: <?= count($transactions ?? []) ?> transaksi
                </small>
            </div>
        </div>

        <div class="table-responsive p-0">
            <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>No. Faktur</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>No. PO</th>
                    <th>Total</th>
                    <th>Status PPN</th>
                    <th>Status Terima</th>
                    <th width="10%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)) : ?>
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada transaksi siap diterima</td>
                    </tr>
                <?php else : ?>
                    <?php 
                    $startNumber = ($currentPage - 1) * $perPage;
                    foreach ($transactions as $index => $row) : 
                    ?>
                        <tr>
                            <td><?= $startNumber + $index + 1 ?></td>
                            <td>
                                <strong><?= esc($row->no_nota) ?></strong>
                                <br>
                                <small class="text-muted">ID: <?= $row->id ?></small>
                            </td>
                            <td><?= date('d/m/Y', strtotime($row->tgl_masuk)) ?></td>
                            <td>
                                <?= esc($row->supplier_nama ?? $row->supplier) ?>
                                <?php if (!empty($row->supplier_nama)): ?>
                                    <br><small class="text-muted"><?= esc($row->supplier) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($row->no_po)): ?>
                                    <span class="badge badge-info"><?= esc($row->no_po) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <strong><?= number_format($row->jml_gtotal ?? 0, 2, ',', '.') ?></strong>
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
                                $receiveStatus = [
                                    '0' => '<span class="badge badge-warning">Belum Diterima</span>',
                                    '1' => '<span class="badge badge-success">Sudah Diterima</span>',
                                    '2' => '<span class="badge badge-danger">Ditolak</span>'
                                ];
                                $statusKey = $row->status_terima ?? '0';
                                echo $receiveStatus[$statusKey] ?? $receiveStatus['0'];
                                ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <?php if (($row->status_terima ?? '0') != '1'): ?>
                                        <a href="<?= base_url("gudang/terima/{$row->id}") ?>" 
                                        class="btn btn-success btn-sm" 
                                        title="Terima Barang">
                                            <i class="fas fa-check"></i> Terima
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= base_url("transaksi/beli/detail/{$row->id}") ?>" 
                                       class="btn btn-info btn-sm" 
                                       title="Detail Transaksi">
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
    <div class="card-footer clearfix">
        <?= $pager->links('transbeli', 'adminlte_pagination') ?>
    </div>
</div>

<!-- Info Card -->
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-1"></i> Informasi
                </h3>
            </div>
            <div class="card-body">
                <p class="mb-1">
                    <strong>Status Transaksi:</strong>
                </p>
                <ul class="mb-0">
                    <li><span class="badge badge-warning">Belum Diterima</span> - Transaksi sudah diproses, siap untuk diterima</li>
                    <li><span class="badge badge-success">Sudah Diterima</span> - Barang sudah diterima dan stok sudah diupdate</li>
                    <li><span class="badge badge-danger">Ditolak</span> - Barang ditolak karena tidak sesuai spesifikasi</li>
                </ul>
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

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Add hover effect to table rows
    $('table tbody tr').hover(
        function() {
            $(this).addClass('table-active');
        },
        function() {
            $(this).removeClass('table-active');
        }
    );
    
    // Confirm receiving action
    $('.btn-success').on('click', function(e) {
        const href = $(this).attr('href');
        const noNota = $(this).closest('tr').find('td:eq(1) strong').text();
        
        Swal.fire({
            title: 'Terima Barang?',
            text: `Apakah anda yakin ingin menerima barang untuk faktur ${noNota}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Terima!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });
});
</script>
<?= $this->endSection() ?> 