<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('master/voucher/create') ?>" class="btn btn-sm btn-primary rounded-0">
                            <i class="fas fa-plus"></i> Tambah Voucher
                        </a>
                        <button type="button" id="bulk-delete-btn" class="btn btn-sm btn-danger rounded-0" style="display: none;">
                            <i class="fas fa-trash-alt"></i> Hapus <span id="selected-count">0</span> Terpilih
                        </button>
                    </div>
                    <div class="col-md-6">
                        <?= form_open('', ['method' => 'get', 'class' => 'float-right']) ?>
                        <div class="input-group input-group-sm">
                            <?= form_input([
                                'name' => 'keyword',
                                'class' => 'form-control rounded-0',
                                'value' => $keyword ?? '',
                                'placeholder' => 'Cari voucher...'
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
            
            <!-- Summary Dashboard -->
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= number_format($summary['total']) ?></h3>
                                <p>Total Voucher</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= number_format($summary['active']) ?></h3>
                                <p>Voucher Aktif</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3><?= number_format($summary['nominal']) ?></h3>
                                <p>Voucher Nominal</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= number_format($summary['percentage']) ?></h3>
                                <p>Voucher Persen</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="select-all">
                            </th>
                            <th width="50">No</th>
                            <th>Kode Voucher</th>
                            <th>Jenis</th>
                            <th>Nominal</th>
                            <th>Jumlah</th>
                            <th>Terpakai</th>
                            <th>Maksimal</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead> 
                    <tbody>
                        <?php if (!empty($vouchers)): ?>
                            <?php foreach ($vouchers as $key => $voucher): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="select-item" value="<?= $voucher->id ?>">
                                    </td>
                                    <td><?= (($currentPage - 1) * $perPage) + $key + 1 ?></td>
                                    <td>
                                        <span class="badge badge-secondary"><?= esc($voucher->kode) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($voucher->jenis_voucher === 'nominal'): ?>
                                            <span class="badge badge-primary">Nominal</span>
                                        <?php else: ?>
                                            <span class="badge badge-info">Persen</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($voucher->jenis_voucher === 'nominal'): ?>
                                            Rp <?= number_format($voucher->nominal) ?>
                                        <?php else: ?>
                                            <?= $voucher->nominal ?>%
                                        <?php endif; ?>
                                    </td>
                                    <td><?= number_format($voucher->jml) ?></td>
                                    <td>
                                        <span class="badge badge-info"><?= number_format($voucher->jml_keluar) ?></span>
                                    </td>
                                    <td><?= number_format($voucher->jml_max) ?></td>
                                    <td>
                                        <small>
                                            <?= date('d/m/Y', strtotime($voucher->tgl_masuk)) ?> - 
                                            <?= date('d/m/Y', strtotime($voucher->tgl_keluar)) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php 
                                        $today = date('Y-m-d');
                                        $isExpired = $voucher->tgl_keluar < $today;
                                        $isNotStarted = $voucher->tgl_masuk > $today;
                                        $isFull = $voucher->jml_keluar >= $voucher->jml_max;
                                        ?>
                                        
                                        <?php if ($voucher->status == '0'): ?>
                                            <span class="badge badge-secondary">Nonaktif</span>
                                        <?php elseif ($isExpired): ?>
                                            <span class="badge badge-danger">Kadaluarsa</span>
                                        <?php elseif ($isNotStarted): ?>
                                            <span class="badge badge-warning">Belum Aktif</span>
                                        <?php elseif ($isFull): ?>
                                            <span class="badge badge-dark">Habis</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('master/voucher/detail/' . $voucher->id) ?>" 
                                               class="btn btn-sm btn-info rounded-0" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('master/voucher/edit/' . $voucher->id) ?>" 
                                               class="btn btn-sm btn-warning rounded-0" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($voucher->jml_keluar == 0): ?>
                                                <a href="<?= base_url('master/voucher/delete/' . $voucher->id) ?>" 
                                                   class="btn btn-sm btn-danger rounded-0" 
                                                   onclick="return confirm('Yakin ingin menghapus voucher ini?')" 
                                                   title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> Belum ada data voucher
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <?php if (!empty($vouchers)): ?>
                <div class="card-footer">
                    <?= $pager->links('voucher', 'default_full') ?>
                </div>
            <?php endif; ?>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<script>
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
            console.log('Voucher - Checkbox changed:', this.checked, 'Value:', this.value);
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
        console.log('Voucher - updateBulkDeleteButton - Checked items:', checkedItems.length);
        
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

        console.log('Voucher - Checked items:', checkedItems.length);
        console.log('Voucher - Item IDs:', itemIds);
        console.log('Voucher - All select items:', document.querySelectorAll('.select-item').length);

        if (itemIds.length === 0) {
            alert('Tidak ada item yang dipilih');
            return;
        }

        if (!confirm(`Apakah Anda yakin ingin menghapus ${itemIds.length} voucher yang dipilih?`)) {
            return;
        }

        // Show loading state
        bulkDeleteBtn.disabled = true;
        bulkDeleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';

        // Get fresh CSRF token first
        fetch('<?= base_url('master/voucher/bulk_delete') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                'item_ids': itemIds
            })
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