<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-17
 * 
 * Karyawan Index View
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
                        <a href="<?= base_url('master/karyawan/create') ?>" class="btn btn-sm btn-primary rounded-0">
                            <i class="fas fa-plus"></i> Tambah Data
                        </a>
                        <a href="<?= base_url('master/karyawan/import') ?>" class="btn btn-sm btn-success rounded-0">
                            <i class="fas fa-file-import"></i> IMPORT
                        </a>
                        <a href="<?= base_url('master/karyawan/template') ?>" class="btn btn-sm btn-info rounded-0">
                            <i class="fas fa-download"></i> Template
                        </a>
                        <a href="<?= base_url('master/karyawan/export') ?>?<?= $_SERVER['QUERY_STRING'] ?>"
                            class="btn btn-sm btn-success rounded-0">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <?php if (isset($trashCount) && $trashCount > 0): ?>
                            <a href="<?= base_url('master/karyawan/trash') ?>" class="btn btn-sm btn-danger rounded-0">
                                <i class="fas fa-trash"></i> Arsip (<?= $trashCount ?>)
                            </a>
                        <?php endif ?>
                        <button type="button" id="bulk-delete-btn" class="btn btn-sm btn-danger rounded-0" style="display: none;">
                            <i class="fas fa-trash-alt"></i> Hapus <span id="selected-count">0</span> Terpilih
                        </button>
                    </div>
                    <div class="col-md-6">
                        <?= form_open('', ['method' => 'get', 'class' => 'float-right']) ?>
                        <div class="input-group input-group-sm">
                            <?= form_input([
                                'name'        => 'search',
                                'class'       => 'form-control rounded-0',
                                'value'       => $search ?? '',
                                'placeholder' => 'Cari...',
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
                                <input type="checkbox" id="select-all">
                            </th>
                            <th width="50">No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>L/P</th>
                            <th>Jabatan</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($karyawans)): ?>
                            <?php
                            $no = ($perPage * ($currentPage - 1)) + 1;
                            foreach ($karyawans as $karyawan):
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="select-item" value="<?= $karyawan->id ?>">
                                    </td>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($karyawan->kode) ?></td>
                                    <td>
                                        <b><?= esc($karyawan->nama) ?></b><br>
                                        <small><?= esc($karyawan->nik) ?></small><?= br() ?>
                                        <small><?= usia_lkp($karyawan->tgl_lahir) ?></small><?= br() ?>
                                        <small><i><?= esc($karyawan->alamat) ?></i></small>
                                    </td>
                                    <td><?= jns_klm($karyawan->jns_klm) ?></td>
                                    <td><?= esc($karyawan->jabatan) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= base_url("master/karyawan/detail/{$karyawan->id}") ?>"
                                                class="btn btn-info btn-sm rounded-0">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url("master/karyawan/edit/{$karyawan->id}") ?>"
                                                class="btn btn-warning btn-sm rounded-0">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url("master/karyawan/delete/{$karyawan->id}") ?>"
                                                class="btn btn-danger btn-sm rounded-0"
                                                onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($pager): ?>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        <?= $pager->links('karyawan', 'adminlte_pagination') ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<script>
// Bulk delete functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const selectItems = document.querySelectorAll('.select-item');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    const selectedCount = document.getElementById('selected-count');

    // Handle select all checkbox
    selectAll.addEventListener('change', function() {
        selectItems.forEach(item => {
            item.checked = this.checked;
        });
        updateBulkDeleteButton();
    });

    // Handle individual checkboxes
    selectItems.forEach(item => {
        item.addEventListener('change', function() {
            console.log('Karyawan - Checkbox changed:', this.checked, 'Value:', this.value);
            updateSelectAllState();
            updateBulkDeleteButton();
        });
    });

    // Update select-all checkbox state (checked, unchecked, or indeterminate)
    function updateSelectAllState() {
        const totalItems = selectItems.length;
        const checkedItems = document.querySelectorAll('.select-item:checked').length;

        if (checkedItems === 0) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        } else if (checkedItems === totalItems) {
            selectAll.checked = true;
            selectAll.indeterminate = false;
        } else {
            selectAll.checked = false;
            selectAll.indeterminate = true;
        }
    }

    // Show/hide bulk delete button based on selection
    function updateBulkDeleteButton() {
        const checkedItems = document.querySelectorAll('.select-item:checked');
        console.log('Karyawan - updateBulkDeleteButton - Checked items:', checkedItems.length);
        
        if (checkedItems.length > 0) {
            bulkDeleteBtn.style.display = 'inline-block';
            selectedCount.textContent = checkedItems.length;
        } else {
            bulkDeleteBtn.style.display = 'none';
        }
    }

    // Handle bulk delete
    bulkDeleteBtn.addEventListener('click', function() {
        const checkedItems = document.querySelectorAll('.select-item:checked');
        const itemIds = Array.from(checkedItems).map(item => item.value);

        console.log('Karyawan - Checked items:', checkedItems.length);
        console.log('Karyawan - Item IDs:', itemIds);
        console.log('Karyawan - All select items:', document.querySelectorAll('.select-item').length);

        if (itemIds.length === 0) {
            alert('Tidak ada item yang dipilih');
            return;
        }

        if (!confirm(`Apakah Anda yakin ingin menghapus ${itemIds.length} karyawan yang dipilih?`)) {
            return;
        }

        // Show loading state
        bulkDeleteBtn.disabled = true;
        bulkDeleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';

        // Send AJAX request using FormData with proper array notation
        const formData = new FormData();
        itemIds.forEach(id => {
            formData.append('item_ids[]', id);
        });
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        fetch('<?= base_url('master/karyawan/bulk_delete') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
                bulkDeleteBtn.disabled = false;
                bulkDeleteBtn.innerHTML = '<i class="fas fa-trash-alt"></i> Hapus <span id="selected-count">' + itemIds.length + '</span> Terpilih';
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
            bulkDeleteBtn.disabled = false;
            bulkDeleteBtn.innerHTML = '<i class="fas fa-trash-alt"></i> Hapus <span id="selected-count">' + itemIds.length + '</span> Terpilih';
        });
    });
});
</script>

<?= $this->endSection() ?>