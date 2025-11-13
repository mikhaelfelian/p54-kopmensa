<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-21
 * 
 * Supplier Trash View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <a href="<?= base_url('master/supplier') ?>" class="btn btn-sm btn-secondary rounded-0">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="button" id="bulk-restore-btn" class="btn btn-sm btn-success rounded-0" style="display:none;">
                    <i class="fas fa-undo"></i> Kembalikan Terpilih (<span id="selected-restore-count">0</span>)
                </button>
                <button type="button" id="bulk-delete-btn" class="btn btn-sm btn-danger rounded-0" style="display:none;">
                    <i class="fas fa-trash"></i> Hapus Permanen Terpilih (<span id="selected-delete-count">0</span>)
                </button>
                <button type="button" id="delete-all-btn" class="btn btn-sm btn-danger rounded-0">
                    <i class="fas fa-trash"></i> Hapus Permanen Semua
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?= form_open('master/supplier/trash', ['method' => 'get']) ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th width="5%">
                            <div class="icheck-primary d-inline">
                                <input type="checkbox" id="select-all-trash">
                                <label for="select-all-trash"></label>
                            </div>
                        </th>
                        <th width="5%">No</th>
                        <th width="10%">Kode</th>
                        <th width="30%">Nama</th>
                        <th width="15%" class="text-center">Tipe</th>
                        <th width="15%" class="text-center">Status</th>
                        <th width="20%" class="text-center">Aksi</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>
                            <?= form_input([
                                'name' => 'search',
                                'value' => $search,
                                'class' => 'form-control form-control-sm rounded-0',
                                'placeholder' => 'Cari...'
                            ]) ?>
                        </th>
                        <th></th>
                        <th class="text-center">
                            <?= form_dropdown(
                                'tipe',
                                [
                                    '' => '- Semua -',
                                    '1' => 'Instansi',
                                    '2' => 'Personal'
                                ],
                                $selectedTipe,
                                'class="form-control form-control-sm rounded-0"'
                            ) ?>
                        </th>
                        <th></th>
                        <th class="text-center">
                            <button type="submit" class="btn btn-sm btn-primary rounded-0">
                                <i class="fas fa-search"></i>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($suppliers)): ?>
                        <?php $no = 1; foreach ($suppliers as $supplier): ?>
                            <tr>
                                <td class="text-center">
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" class="supplier-checkbox-trash" value="<?= $supplier->id ?>" id="supplier-trash-<?= $supplier->id ?>">
                                        <label for="supplier-trash-<?= $supplier->id ?>"></label>
                                    </div>
                                </td>
                                <td><?= $no++ ?></td>
                                <td><?= esc($supplier->kode) ?></td>
                                <td><?= esc($supplier->nama) ?></td>
                                <td class="text-center"><?= $getTipeLabel($supplier->tipe) ?></td>
                                <td class="text-center"><?= $getStatusLabel($supplier->status) ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/supplier/restore/{$supplier->id}") ?>"
                                            class="btn btn-success btn-sm rounded-0" title="Pulihkan">
                                            <i class="fas fa-trash-restore"></i>
                                        </a>
                                        <a href="<?= base_url("master/supplier/delete-permanent/{$supplier->id}") ?>"
                                            class="btn btn-danger btn-sm rounded-0" title="Hapus Permanen"
                                            onclick="return confirm('Data akan dihapus secara permanen. Lanjutkan?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
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
            <?= form_close() ?>
        </div>
    </div>
    <div class="card-footer">
        <?= $pager->links('suppliers', 'adminlte_pagination') ?>
    </div>
</div>

<?= $this->section('js') ?>
<script>
$(document).ready(function () {
    // Select all checkboxes
    $('#select-all-trash').on('change', function () {
        var checked = $(this).is(':checked');
        $('.supplier-checkbox-trash').prop('checked', checked);
        updateBulkButtons();
    });

    // Individual checkbox change
    $(document).on('change', '.supplier-checkbox-trash', function () {
        updateBulkButtons();
        // Update select-all state
        var total = $('.supplier-checkbox-trash').length;
        var checked = $('.supplier-checkbox-trash:checked').length;
        if (checked === 0) {
            $('#select-all-trash').prop('indeterminate', false).prop('checked', false);
        } else if (checked === total) {
            $('#select-all-trash').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#select-all-trash').prop('indeterminate', true);
        }
    });

    function updateBulkButtons() {
        var checkedCount = $('.supplier-checkbox-trash:checked').length;
        $('#selected-restore-count').text(checkedCount);
        $('#selected-delete-count').text(checkedCount);
        if (checkedCount > 0) {
            $('#bulk-restore-btn').show();
            $('#bulk-delete-btn').show();
        } else {
            $('#bulk-restore-btn').hide();
            $('#bulk-delete-btn').hide();
        }
    }

    // Bulk restore
    $('#bulk-restore-btn').click(function () {
        var ids = $('.supplier-checkbox-trash:checked').map(function () { return $(this).val(); }).get();
        if (ids.length === 0) return;
        if (!confirm('Kembalikan ' + ids.length + ' supplier terpilih?')) return;
        $.ajax({
            url: '<?= base_url('master/supplier/bulk_restore') ?>',
            type: 'POST',
            data: { item_ids: ids, '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
            dataType: 'json',
            beforeSend: function () {
                $('#bulk-restore-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengembalikan...');
            },
            success: function (res) {
                if (res.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(res.message);
                    } else {
                        alert(res.message);
                    }
                    setTimeout(function () { location.reload(); }, 1000);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(res.message);
                    } else {
                        alert(res.message);
                    }
                }
            },
            complete: function () {
                $('#bulk-restore-btn').prop('disabled', false).html('<i class="fas fa-undo"></i> Kembalikan Terpilih (<span id="selected-restore-count">' + $('.supplier-checkbox-trash:checked').length + '</span>)');
            }
        });
    });

    // Bulk delete permanent
    $('#bulk-delete-btn').click(function () {
        var ids = $('.supplier-checkbox-trash:checked').map(function () { return $(this).val(); }).get();
        if (ids.length === 0) return;
        if (!confirm('Hapus permanen ' + ids.length + ' supplier terpilih?')) return;
        $.ajax({
            url: '<?= base_url('master/supplier/bulk_delete_permanent') ?>',
            type: 'POST',
            data: { item_ids: ids, '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
            dataType: 'json',
            beforeSend: function () {
                $('#bulk-delete-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
            },
            success: function (res) {
                if (res.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(res.message);
                    } else {
                        alert(res.message);
                    }
                    setTimeout(function () { location.reload(); }, 1000);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(res.message);
                    } else {
                        alert(res.message);
                    }
                }
            },
            complete: function () {
                $('#bulk-delete-btn').prop('disabled', false).html('<i class="fas fa-trash"></i> Hapus Permanen Terpilih (<span id="selected-delete-count">' + $('.supplier-checkbox-trash:checked').length + '</span>)');
            }
        });
    });

    // Delete all permanent
    $('#delete-all-btn').click(function () {
        if (!confirm('Hapus permanen semua supplier di tempat sampah?')) return;
        $.ajax({
            url: '<?= base_url('master/supplier/delete_all_permanent') ?>',
            type: 'POST',
            data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
            dataType: 'json',
            beforeSend: function () {
                $('#delete-all-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghapus Semua...');
            },
            success: function (res) {
                if (res.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(res.message);
                    } else {
                        alert(res.message);
                    }
                    setTimeout(function () { location.reload(); }, 1000);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(res.message);
                    } else {
                        alert(res.message);
                    }
                }
            },
            complete: function () {
                $('#delete-all-btn').prop('disabled', false).html('<i class="fas fa-trash"></i> Hapus Permanen Semua');
            }
        });
    });
});
</script>
<?= $this->endSection() ?> 