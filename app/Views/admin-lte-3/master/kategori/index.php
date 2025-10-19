<?= $this->extend(theme_path('main')) ?>

<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-12
 * Github : github.com/mikhaelfelian
 * description : View for displaying kategori/category data
 * This file represents the Kategori Index View.
 */
?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('master/kategori/create') ?>" class="btn btn-sm btn-primary rounded-0">
                            <i class="fas fa-plus"></i> Tambah Data
                        </a>
                        <a href="<?= base_url('master/kategori/import') ?>" class="btn btn-sm btn-success rounded-0">
                            <i class="fas fa-file-import"></i> IMPORT
                        </a>
                        <a href="<?= base_url('master/kategori/template') ?>" class="btn btn-sm btn-info rounded-0">
                            <i class="fas fa-download"></i> Template
                        </a>
                        <button type="button" id="bulk-delete-btn" class="btn btn-sm btn-danger rounded-0" style="display: none;">
                            <i class="fas fa-trash"></i> Hapus Terpilih (<span id="selected-count">0</span>)
                        </button>
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

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="select-all" title="Pilih semua">
                            </th>
                            <th width="50">No</th>
                            <th>Kode</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($kategori)): ?>
                            <?php foreach ($kategori as $key => $row): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="item-checkbox" value="<?= $row->id ?>">
                                    </td>
                                    <td><?= (($currentPage - 1) * $perPage) + $key + 1 ?></td>
                                    <td><?= $row->kode ?></td>
                                    <td><?= $row->kategori ?></td>
                                    <td><?= $row->keterangan ?></td>
                                    <td>
                                        <span class="badge badge-<?= ($row->status == '1') ? 'success' : 'danger' ?>">
                                            <?= ($row->status == '1') ? 'Aktif' : 'Tidak Aktif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= base_url("master/kategori/edit/$row->id") ?>"
                                                class="btn btn-warning btn-sm rounded-0">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url("master/kategori/delete/$row->id") ?>"
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
                                <td colspan="7" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>

                </div>
            <?php if ($pager): ?>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        <?= $pager->links('kategori', 'adminlte_pagination') ?>
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

<script>
$(document).ready(function() {
    $('#select-all').change(function() {
        $('.item-checkbox').prop('checked', $(this).is(':checked'));
        updateBulkDeleteButton();
    });
    
    $(document).on('change', '.item-checkbox', function() {
        updateBulkDeleteButton();
        var totalCheckboxes = $('.item-checkbox').length;
        var checkedCheckboxes = $('.item-checkbox:checked').length;
        if (checkedCheckboxes === 0) {
            $('#select-all').prop('indeterminate', false).prop('checked', false);
        } else if (checkedCheckboxes === totalCheckboxes) {
            $('#select-all').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#select-all').prop('indeterminate', true);
        }
    });
    
    $('#bulk-delete-btn').click(function() {
        var selectedItems = $('.item-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        if (selectedItems.length === 0) {
            alert('Pilih kategori yang akan dihapus');
            return;
        }
        if (confirm('Apakah anda yakin ingin menghapus ' + selectedItems.length + ' kategori yang dipilih?')) {
            bulkDeleteItems(selectedItems);
        }
    });
    
    function updateBulkDeleteButton() {
        var selectedCount = $('.item-checkbox:checked').length;
        $('#selected-count').text(selectedCount);
        if (selectedCount > 0) {
            $('#bulk-delete-btn').show();
        } else {
            $('#bulk-delete-btn').hide();
        }
    }
    
    function bulkDeleteItems(itemIds) {
        $.ajax({
            url: '<?= base_url('master/kategori/bulk_delete') ?>',
            type: 'POST',
            data: {
                item_ids: itemIds,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            beforeSend: function() {
                $('#bulk-delete-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
            },
            success: function(response) {
                if (response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    } else {
                        alert(response.message);
                    }
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message);
                    } else {
                        alert(response.message);
                    }
                    $('#bulk-delete-btn').prop('disabled', false).html('<i class="fas fa-trash"></i> Hapus Terpilih (<span id="selected-count">' + $('.item-checkbox:checked').length + '</span>)');
                }
            },
            error: function(xhr, status, error) {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Terjadi kesalahan saat menghapus kategori');
                } else {
                    alert('Terjadi kesalahan saat menghapus kategori');
                }
                $('#bulk-delete-btn').prop('disabled', false).html('<i class="fas fa-trash"></i> Hapus Terpilih (<span id="selected-count">' + $('.item-checkbox:checked').length + '</span>)');
            }
        });
    }
});
</script>

<?= $this->endSection() ?>