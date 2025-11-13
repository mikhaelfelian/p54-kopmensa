<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-18
 * Github : github.com/mikhaelfelian
 * description : View for displaying deleted items
 * This file represents the View for deleted items.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('master/item') ?>" class="btn btn-sm btn-secondary rounded-0">
                            <i class="fas fa-arrow-left"></i> Kembali
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
                <div class="mb-2">
                    <button type="button" id="bulk-restore-btn" class="btn btn-success btn-sm rounded-0" style="display:none;">
                        <i class="fas fa-undo"></i> Kembalikan Terpilih (<span id="selected-restore-count">0</span>)
                    </button>
                    <button type="button" id="bulk-delete-btn" class="btn btn-danger btn-sm rounded-0" style="display:none;">
                        <i class="fas fa-trash"></i> Hapus Permanen Terpilih (<span id="selected-delete-count">0</span>)
                    </button>
                    <button type="button" id="delete-all-btn" class="btn btn-danger btn-sm rounded-0">
                        <i class="fas fa-trash"></i> Hapus Permanen Semua
                    </button>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="40" class="text-center">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="select-all-trash">
                                    <label for="select-all-trash"></label>
                                </div>
                            </th>
                            <th width="50">No</th>
                            <th>Kode</th>
                            <th>Barcode</th>
                            <th>Nama Item</th>
                            <th>Kategori</th>
                            <th>Merk</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Stok Min</th>
                            <th>Status</th>
                            <th>Dihapus Pada</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (
                            $items as $key => $row): ?>
                            <tr>
                                <td class="text-center">
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" class="item-checkbox-trash" value="<?= $row->id ?>" id="item-trash-<?= $row->id ?>">
                                        <label for="item-trash-<?= $row->id ?>"></label>
                                    </div>
                                </td>
                                <td><?= (($currentPage - 1) * $perPage) + $key + 1 ?></td>
                                <td><?= $row->kode ?></td>
                                <td><?= $row->barcode ?></td>
                                <td><?= $row->item ?></td>
                                <td><?= $row->nama_kategori ?? $row->id_kategori ?></td>
                                <td><?= $row->nama_merk ?? $row->id_merk ?></td>
                                <td><?= number_format($row->harga_beli, 0, ',', '.') ?></td>
                                <td><?= number_format($row->harga_jual, 0, ',', '.') ?></td>
                                <td><?= $row->jml_min ?></td>
                                <td>
                                    <span class="badge badge-<?= ($row->status == '1') ? 'success' : 'danger' ?>">
                                        <?= ($row->status == '1') ? 'Aktif' : 'Tidak Aktif' ?>
                                    </span>
                                </td>
                                <td><?= tgl_indo8($row->deleted_at) ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/item/restore/{$row->id}") ?>"
                                            class="btn btn-success btn-sm rounded-0"
                                            onclick="return confirm('Apakah anda yakin ingin mengembalikan data ini?')">
                                            <i class="fas fa-undo"></i>
                                        </a>
                                        <a href="<?= base_url("master/item/delete_permanent/{$row->id}") ?>"
                                            class="btn btn-danger btn-sm rounded-0"
                                            onclick="return confirm('Apakah anda yakin ingin menghapus permanen data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="13" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <?php if ($pager): ?>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        <?= $pager->links('items', 'adminlte_pagination') ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
<?= $this->endSection() ?>

<script>
$(document).ready(function () {
    // Select all checkboxes
    $('#select-all-trash').on('change', function () {
        var checked = $(this).is(':checked');
        $('.item-checkbox-trash').prop('checked', checked);
        updateBulkButtons();
    });

    // Individual checkbox change
    $(document).on('change', '.item-checkbox-trash', function () {
        updateBulkButtons();
        // Update select-all state
        var total = $('.item-checkbox-trash').length;
        var checked = $('.item-checkbox-trash:checked').length;
        if (checked === 0) {
            $('#select-all-trash').prop('indeterminate', false).prop('checked', false);
        } else if (checked === total) {
            $('#select-all-trash').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#select-all-trash').prop('indeterminate', true);
        }
    });

    function updateBulkButtons() {
        var checkedCount = $('.item-checkbox-trash:checked').length;
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
        var ids = $('.item-checkbox-trash:checked').map(function () { return $(this).val(); }).get();
        if (ids.length === 0) return;
        if (!confirm('Kembalikan ' + ids.length + ' item terpilih?')) return;
        $.ajax({
            url: '<?= base_url('master/item/bulk_restore') ?>',
            type: 'POST',
            data: { item_ids: ids, '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
            dataType: 'json',
            beforeSend: function () {
                $('#bulk-restore-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengembalikan...');
            },
            success: function (res) {
                if (res.success) {
                    toastr.success(res.message);
                    setTimeout(function () { location.reload(); }, 1000);
                } else {
                    toastr.error(res.message);
                }
            },
            complete: function () {
                $('#bulk-restore-btn').prop('disabled', false).html('<i class="fas fa-undo"></i> Kembalikan Terpilih (<span id="selected-restore-count">' + $('.item-checkbox-trash:checked').length + '</span>)');
            }
        });
    });

    // Bulk delete permanent
    $('#bulk-delete-btn').click(function () {
        var ids = $('.item-checkbox-trash:checked').map(function () { return $(this).val(); }).get();
        if (ids.length === 0) return;
        if (!confirm('Hapus permanen ' + ids.length + ' item terpilih?')) return;
        $.ajax({
            url: '<?= base_url('master/item/bulk_delete_permanent') ?>',
            type: 'POST',
            data: { item_ids: ids, '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
            dataType: 'json',
            beforeSend: function () {
                $('#bulk-delete-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
            },
            success: function (res) {
                if (res.success) {
                    toastr.success(res.message);
                    setTimeout(function () { location.reload(); }, 1000);
                } else {
                    toastr.error(res.message);
                }
            },
            complete: function () {
                $('#bulk-delete-btn').prop('disabled', false).html('<i class="fas fa-trash"></i> Hapus Permanen Terpilih (<span id="selected-delete-count">' + $('.item-checkbox-trash:checked').length + '</span>)');
            }
        });
    });

    // Delete all permanent
    $('#delete-all-btn').click(function () {
        if (!confirm('Hapus permanen semua item di tempat sampah?')) return;
        $.ajax({
            url: '<?= base_url('master/item/delete_all_permanent') ?>',
            type: 'POST',
            data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
            dataType: 'json',
            beforeSend: function () {
                $('#delete-all-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghapus Semua...');
            },
            success: function (res) {
                if (res.success) {
                    toastr.success(res.message);
                    setTimeout(function () { location.reload(); }, 1000);
                } else {
                    toastr.error(res.message);
                }
            },
            complete: function () {
                $('#delete-all-btn').prop('disabled', false).html('<i class="fas fa-trash"></i> Hapus Permanen Semua');
            }
        });
    });
});
</script> 