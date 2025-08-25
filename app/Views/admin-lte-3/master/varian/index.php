<?= $this->extend(theme_path('main')) ?>

<?php
/**
 * Required variables from controller:
 * - $varian: array of varian data
 * - $total: total count of records
 * - $currentPage: current page number
 * - $perPage: items per page
 * - $pager: pagination object
 * - $keyword: search keyword (optional)
 * - $status: status filter (optional)
 * - $per_page: items per page setting (optional)
 */

// Set default values for required variables to prevent undefined variable errors
$varian = $varian ?? [];
$total = $total ?? 0;
$currentPage = $currentPage ?? 1;
$perPage = $perPage ?? 10;
$pager = $pager ?? null;
$keyword = $keyword ?? '';
$status = $status ?? '';
$per_page = $per_page ?? '10';
?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('master/varian/create') ?>" class="btn btn-sm btn-primary rounded-0">
                            <i class="fas fa-plus"></i> Tambah Data
                        </a>
                    </div>
                    <div class="col-md-6">
                        <?= form_open('', ['method' => 'get', 'class' => 'float-right']) ?>
                        <div class="input-group input-group-sm">
                            <?= form_input([
                                'name' => 'keyword',
                                'class' => 'form-control rounded-0',
                                'value' => $keyword ?? '',
                                'placeholder' => 'Cari...'
                            ]) ?>
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-primary rounded-0" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive">
                <!-- Filter Section -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-filter"></i> Filter Data
                        </h5>
                    </div>
                    <div class="card-body">
                        <?= form_open('', ['method' => 'get', 'id' => 'filterForm']) ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= form_label('Cari Kode/Nama Varian:', 'keyword') ?>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                            </div>
                                            <?= form_input([
                                                'type' => 'text',
                                                'class' => 'form-control',
                                                'id' => 'search',
                                                'name' => 'keyword',
                                                'value' => $keyword ?? '',
                                                'placeholder' => 'Masukkan kode atau nama varian...',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Tekan Ctrl+F untuk fokus cepat, Escape untuk hapus'
                                            ]) ?>
                                        </div>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> 
                                            Pencarian otomatis setelah 0.5 detik berhenti mengetik. <strong>Fokus pada kolom Nama Varian</strong>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <?= form_label('Status:', 'status') ?>
                                        <?= form_dropdown('status', [
                                            '' => 'Semua Status',
                                            '1' => 'Aktif',
                                            '0' => 'Tidak Aktif'
                                        ], $status ?? '', [
                                            'class' => 'form-control',
                                            'id' => 'status'
                                        ]) ?>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> 
                                            Filter berdasarkan status aktif/tidak aktif
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <?= form_label('Data per Halaman:', 'per_page') ?>
                                        <?= form_dropdown('per_page', [
                                            '10' => '10',
                                            '25' => '25',
                                            '50' => '50',
                                            '100' => '100'
                                        ], $per_page ?? '10', [
                                            'class' => 'form-control',
                                            'id' => 'per_page'
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div class="d-flex">
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-search"></i> Filter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <a href="<?= base_url('master/varian') ?>" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-refresh"></i> Reset Filter
                                    </a>
                                    <span class="text-muted ml-2">
                                        Total: <strong><?= $total ?></strong> data
                                    </span>
                                </div>
                            </div>
                        <?= form_close() ?>
                    </div>
                </div>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Kode</th>
                            <th>Nama Varian</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($varian)): ?>
                            <?php foreach ($varian as $key => $row): ?>
                                <tr>
                                    <td><?= (($currentPage - 1) * $perPage) + $key + 1 ?></td>
                                    <td><?= $row->kode ?></td>
                                    <td><?= $row->nama ?></td>
                                    <td><?= $row->keterangan ?></td>
                                    <td>
                                        <span class="badge badge-<?= ($row->status == '1') ? 'success' : 'danger' ?>">
                                            <?= ($row->status == '1') ? 'Aktif' : 'Tidak Aktif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= base_url("master/varian/edit/$row->id") ?>"
                                                class="btn btn-warning btn-sm rounded-0">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url("master/varian/delete/$row->id") ?>"
                                                class="btn btn-danger btn-sm rounded-0"
                                                onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($pager): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Menampilkan <?= (($currentPage - 1) * $perPage) + 1 ?> - 
                                <?= min($currentPage * $perPage, $total) ?> dari <?= $total ?> data
                            </small>
                        </div>
                        <div>
                            <?= $pager->links() ?>
                        </div>
                    </div>
                <?php endif ?>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<!-- JavaScript for Enhanced Filtering -->
<script>
$(document).ready(function() {
    // Auto-submit form when per_page changes
    $('#per_page').change(function() {
        $('#filterForm').submit();
    });

    // Auto-submit form when status changes
    $('#status').change(function() {
        $('#filterForm').submit();
    });

    // Debounced search input
    let searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            $('#filterForm').submit();
        }, 500); // Wait 500ms after user stops typing
    });

    // Highlight search terms in table - focus only on Nama Varian column
    function highlightSearchTerms() {
        const searchTerm = $('#search').val().toLowerCase();
        if (searchTerm.length > 0) {
            $('tbody tr').each(function() {
                const row = $(this);
                // Only check the Nama Varian column (3rd column, index 2)
                const namaVarianCell = row.find('td:eq(2)'); // Nama Varian column
                const namaVarianText = namaVarianCell.text().toLowerCase();
                if (namaVarianText.includes(searchTerm)) {
                    row.addClass('table-warning');
                } else {
                    row.removeClass('table-warning');
                }
            });
        } else {
            $('tbody tr').removeClass('table-warning');
        }
    }

    // Apply highlighting on page load
    highlightSearchTerms();

    // Show loading state during filter
    $('#filterForm').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Loading...');
        submitBtn.prop('disabled', true);
        
        // Re-enable button after a short delay (in case of errors)
        setTimeout(function() {
            submitBtn.html(originalText);
            submitBtn.prop('disabled', false);
        }, 3000);
    });

    // Keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl/Cmd + F to focus search
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 70) {
            e.preventDefault();
            $('#search').focus();
        }
        
        // Escape to clear search
        if (e.keyCode === 27) {
            $('#search').val('').trigger('input');
        }
    });

    // Tooltip for filter help
    $('[data-toggle="tooltip"]').tooltip();
});
</script>

<?= $this->endSection() ?> 