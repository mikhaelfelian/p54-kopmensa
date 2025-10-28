<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * Supplier Detail View with Item Management
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Validasi Gagal:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Supplier Information -->
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <i class="fas fa-building fa-3x text-primary mb-3"></i>
                            <h3 class="profile-username text-center"><?= esc($supplier->nama) ?></h3>
                            <p class="text-muted text-center">
                                <span class="badge badge-<?= $supplier->tipe == '1' ? 'success' : 'info' ?>">
                                    <?= $getTipeLabel($supplier->tipe) ?>
                                </span>
                            </p>
                        </div>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Kode Supplier</b> 
                                <span class="float-right"><?= esc($supplier->kode) ?></span>
                            </li>
                            <li class="list-group-item">
                                <b>NPWP</b> 
                                <span class="float-right"><?= esc($supplier->npwp) ?: '-' ?></span>
                            </li>
                            <li class="list-group-item">
                                <b>Status</b> 
                                <span class="float-right">
                                    <span class="badge badge-<?= $supplier->status == '1' ? 'success' : 'danger' ?>">
                                        <?= $getStatusLabel($supplier->status) ?>
                                    </span>
                                </span>
                            </li>
                            <li class="list-group-item">
                                <b>Total Item</b> 
                                <span class="float-right">
                                    <span class="badge badge-info"><?= $supplierStats->total_items ?></span>
                                </span>
                            </li>
                        </ul>

                        <div class="text-center">
                            <a href="<?= base_url('master/supplier') ?>" class="btn btn-default">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <a href="<?= base_url("master/supplier/edit/{$supplier->id}") ?>" 
                               class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Supplier Statistics -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Statistik Item
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="description-block border-right">
                                    <span class="description-percentage text-success">
                                        <i class="fas fa-tags"></i>
                                    </span>
                                    <h5 class="description-header"><?= $supplierStats->total_items ?></h5>
                                    <span class="description-text">Total Item</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="description-block">
                                    <span class="description-percentage text-warning">
                                        <i class="fas fa-dollar-sign"></i>
                                    </span>
                                    <h5 class="description-header">Rp <?= number_format($supplierStats->avg_price, 0, ',', '.') ?></h5>
                                    <span class="description-text">Harga Rata-rata</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <small class="text-muted">
                                    <i class="fas fa-arrow-down text-success"></i>
                                    Rp <?= number_format($supplierStats->min_price, 0, ',', '.') ?>
                                </small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">
                                    <i class="fas fa-arrow-up text-danger"></i>
                                    Rp <?= number_format($supplierStats->max_price, 0, ',', '.') ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supplier Details & Item Management -->
            <div class="col-md-8">
                <!-- Supplier Details -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-1"></i>
                            Detail Informasi
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-map-marker-alt mr-1"></i> Alamat</strong>
                                <p class="text-muted">
                                    <?= esc($supplier->alamat) ?><br>
                                    RT <?= esc($supplier->rt) ?> / RW <?= esc($supplier->rw) ?><br>
                                    Kel. <?= esc($supplier->kelurahan) ?><br>
                                    Kec. <?= esc($supplier->kecamatan) ?><br>
                                    <?= esc($supplier->kota) ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-phone mr-1"></i> Kontak</strong>
                                <p class="text-muted">
                                    <strong>Telepon:</strong> <?= esc($supplier->no_tlp) ?: '-' ?><br>
                                    <strong>HP:</strong> <?= esc($supplier->no_hp) ?: '-' ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item Management -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-boxes mr-1"></i>
                            Manajemen Item
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addItemsModal">
                                <i class="fas fa-plus"></i> Tambah Item
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" id="bulkRemoveBtn" disabled>
                                <i class="fas fa-trash"></i> Hapus Terpilih
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="supplierItemsTable" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">
                                            <input type="checkbox" id="selectAllItems" class="form-check-input">
                                        </th>
                                        <th width="15%">Kode Item</th>
                                        <th width="25%">Nama Item</th>
                                        <th width="15%">Harga Beli</th>
                                        <th width="15%">Harga Jual</th>
                                        <th width="10%">Prioritas</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Items Modal -->
<div class="modal fade" id="addItemsModal" tabindex="-1" role="dialog" aria-labelledby="addItemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addItemsModalLabel">
                    <i class="fas fa-plus mr-1"></i>
                    Tambah Item ke Supplier
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addItemsForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="itemSelect">Pilih Item</label>
                                <select id="itemSelect" name="item_ids[]" class="form-control" multiple required>
                                    <!-- Options will be loaded via AJAX -->
                                </select>
                                <small class="form-text text-muted">Pilih satu atau lebih item untuk ditambahkan</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="defaultHargaBeli">Harga Beli Default</label>
                                <input type="number" id="defaultHargaBeli" name="default_harga_beli" 
                                       class="form-control" value="0" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="defaultPrioritas">Prioritas Default</label>
                                <input type="number" id="defaultPrioritas" name="default_prioritas" 
                                       class="form-control" value="0" min="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Item Mapping Modal -->
<div class="modal fade" id="editItemModal" tabindex="-1" role="dialog" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editItemModalLabel">
                    <i class="fas fa-edit mr-1"></i>
                    Edit Item Mapping
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editItemForm">
                <div class="modal-body">
                    <input type="hidden" id="editItemId" name="item_id">
                    <div class="form-group">
                        <label for="editHargaBeli">Harga Beli</label>
                        <input type="number" id="editHargaBeli" name="harga_beli" 
                               class="form-control" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="editPrioritas">Prioritas</label>
                        <input type="number" id="editPrioritas" name="prioritas" 
                               class="form-control" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    const supplierId = <?= $supplier->id ?>;
    let itemsTable;

    // Initialize DataTable
    function initItemsTable() {
        itemsTable = $('#supplierItemsTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: `<?= base_url('master/supplier/get-items/') ?>${supplierId}`,
                type: 'GET',
                dataSrc: function(json) {
                    if (json.status === 'success') {
                        return json.data;
                    } else {
                        toastr.error(json.message);
                        return [];
                    }
                }
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `<input type="checkbox" class="form-check-input item-checkbox" value="${row.item_id}">`;
                    }
                },
                { data: 'item_kode' },
                { data: 'item_nama' },
                {
                    data: 'harga_beli',
                    render: function(data, type, row) {
                        return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                    }
                },
                {
                    data: 'harga_jual',
                    render: function(data, type, row) {
                        return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                    }
                },
                { data: 'prioritas' },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-sm btn-warning edit-item-btn" 
                                    data-item-id="${row.item_id}" 
                                    data-harga-beli="${row.harga_beli}" 
                                    data-prioritas="${row.prioritas}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger remove-item-btn" 
                                    data-item-id="${row.item_id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    }
                }
            ],
            order: [[2, 'asc']], // Sort by item name
            pageLength: 25,
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    }

    // Initialize Select2 for item selection
    function initItemSelect() {
        $('#itemSelect').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Pilih item...',
            allowClear: true,
            ajax: {
                url: `<?= base_url('master/supplier/unassigned-items/') ?>${supplierId}`,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    if (data.status === 'success') {
                        return {
                            results: data.data.map(item => ({
                                id: item.id,
                                text: item.text
                            }))
                        };
                    }
                    return { results: [] };
                },
                cache: true
            },
            minimumInputLength: 1
        });
    }

    // Handle select all checkbox
    $('#selectAllItems').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.item-checkbox').prop('checked', isChecked);
        updateBulkRemoveButton();
    });

    // Handle individual item checkboxes
    $(document).on('change', '.item-checkbox', function() {
        updateBulkRemoveButton();
    });

    // Update bulk remove button state
    function updateBulkRemoveButton() {
        const checkedItems = $('.item-checkbox:checked').length;
        $('#bulkRemoveBtn').prop('disabled', checkedItems === 0);
    }

    // Handle bulk remove
    $('#bulkRemoveBtn').on('click', function() {
        const checkedItems = $('.item-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (checkedItems.length === 0) {
            toastr.warning('Pilih item yang akan dihapus');
            return;
        }

        if (confirm(`Apakah Anda yakin ingin menghapus ${checkedItems.length} item dari supplier ini?`)) {
            $.ajax({
                url: `<?= base_url('master/supplier/bulk-remove-items/') ?>${supplierId}`,
                type: 'POST',
                data: {
                    item_ids: checkedItems,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        itemsTable.ajax.reload();
                        $('#selectAllItems').prop('checked', false);
                        updateBulkRemoveButton();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Terjadi kesalahan: ' + error);
                }
            });
        }
    });

    // Handle add items form
    $('#addItemsForm').on('submit', function(e) {
        e.preventDefault();
        
        const itemIds = $('#itemSelect').val();
        if (!itemIds || itemIds.length === 0) {
            toastr.error('Pilih minimal satu item');
            return;
        }

        const formData = {
            item_ids: itemIds,
            default_harga_beli: $('#defaultHargaBeli').val(),
            default_prioritas: $('#defaultPrioritas').val(),
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        };

        $.ajax({
            url: `<?= base_url('master/supplier/bulk-assign-items/') ?>${supplierId}`,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    $('#addItemsModal').modal('hide');
                    $('#addItemsForm')[0].reset();
                    $('#itemSelect').val(null).trigger('change');
                    itemsTable.ajax.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error('Terjadi kesalahan: ' + error);
            }
        });
    });

    // Handle edit item button
    $(document).on('click', '.edit-item-btn', function() {
        const itemId = $(this).data('item-id');
        const hargaBeli = $(this).data('harga-beli');
        const prioritas = $(this).data('prioritas');

        $('#editItemId').val(itemId);
        $('#editHargaBeli').val(hargaBeli);
        $('#editPrioritas').val(prioritas);
        $('#editItemModal').modal('show');
    });

    // Handle edit item form
    $('#editItemForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            item_id: $('#editItemId').val(),
            harga_beli: $('#editHargaBeli').val(),
            prioritas: $('#editPrioritas').val(),
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        };

        $.ajax({
            url: `<?= base_url('master/supplier/update-item-mapping/') ?>${supplierId}`,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    $('#editItemModal').modal('hide');
                    itemsTable.ajax.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error('Terjadi kesalahan: ' + error);
            }
        });
    });

    // Handle remove item button
    $(document).on('click', '.remove-item-btn', function() {
        const itemId = $(this).data('item-id');
        
        if (confirm('Apakah Anda yakin ingin menghapus item ini dari supplier?')) {
            $.ajax({
                url: `<?= base_url('master/supplier/remove-item-mapping/') ?>${supplierId}`,
                type: 'POST',
                data: {
                    item_id: itemId,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        itemsTable.ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Terjadi kesalahan: ' + error);
                }
            });
        }
    });

    // Initialize everything
    initItemsTable();
    initItemSelect();

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
<?= $this->endSection() ?>