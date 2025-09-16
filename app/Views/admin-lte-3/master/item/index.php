<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-18
 * Github : github.com/mikhaelfelian
 * description : View for displaying item/product data
 * This file represents the Items Index View.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<!-- CSRF Token -->
<?= csrf_field() ?>

<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('master/item/create') ?>" class="btn btn-sm btn-primary rounded-0">
                            <i class="fas fa-plus"></i> Tambah Data
                        </a>
                        <?php if ($trashCount > 0): ?>
                            <a href="<?= base_url('master/item/trash') ?>" class="btn btn-sm btn-danger rounded-0">
                                <i class="fas fa-trash"></i> Arsip (<?= $trashCount ?>)
                            </a>
                        <?php endif ?>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="<?= base_url('master/item/export_excel') . '?' . http_build_query($_GET) ?>" class="btn btn-sm btn-success rounded-0">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <button type="button" id="bulk-delete-btn" class="btn btn-sm btn-danger rounded-0" style="display: none;">
                            <i class="fas fa-trash"></i> Hapus Terpilih (<span id="selected-count">0</span>)
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Section -->
                <div class="card card-outline card-primary mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-filter"></i> Filter Data
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?= form_open('master/item', ['method' => 'get']) ?>
                        <div class="row">
                            <!-- Keyword Search -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Cari Item</label>
                                    <?= form_input([
                                        'name' => 'keyword',
                                        'class' => 'form-control rounded-0',
                                        'placeholder' => 'Kode / Nama Item / Barcode',
                                        'value' => esc($keyword)
                                    ]) ?>
                                </div>
                            </div>
                            
                            <!-- Stockable Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status Stok</label>
                                    <select name="stok" class="form-control rounded-0">
                                        <option value="">- Semua -</option>
                                        <option value="1" <?= ($stok == '1') ? 'selected' : '' ?>>Stockable</option>
                                        <option value="0" <?= ($stok == '0') ? 'selected' : '' ?>>Non Stockable</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Category Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Kategori</label>
                                    <select name="kategori" class="form-control rounded-0">
                                        <option value="">- Semua Kategori -</option>
                                        <?php foreach ($kategori as $kat_item): ?>
                                            <option value="<?= $kat_item->id ?>" <?= ($kat == $kat_item->id) ? 'selected' : '' ?>>
                                                <?= $kat_item->kategori ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Brand/Merk Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Merk</label>
                                    <select name="merk" class="form-control rounded-0">
                                        <option value="">- Semua Merk -</option>
                                        <?php foreach ($merk_list as $merk_item): ?>
                                            <option value="<?= $merk_item->id ?>" <?= ($merk == $merk_item->id) ? 'selected' : '' ?>>
                                                <?= $merk_item->merk ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Supplier Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Supplier</label>
                                    <select name="supplier" class="form-control rounded-0">
                                        <option value="">- Semua Supplier -</option>
                                        <?php if (isset($supplier_list) && is_array($supplier_list)): ?>
                                            <?php foreach ($supplier_list as $supplier_item): ?>
                                                <option value="<?= $supplier_item->id ?>" <?= ($supplier == $supplier_item->id) ? 'selected' : '' ?>>
                                                    <?= $supplier_item->nama ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Min Stock Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Stok Minimum</label>
                                    <div class="input-group">
                                        <select name="min_stok_operator" class="form-control rounded-0" style="max-width: 80px;">
                                            <option value="">-</option>
                                            <option value="=" <?= ($min_stok_operator == '=') ? 'selected' : '' ?>>=</option>
                                            <option value="<=" <?= ($min_stok_operator == '<=') ? 'selected' : '' ?>>≤</option>
                                            <option value=">=" <?= ($min_stok_operator == '>=') ? 'selected' : '' ?>>≥</option>
                                            <option value=">" <?= ($min_stok_operator == '>') ? 'selected' : '' ?>>></option>
                                            <option value="<" <?= ($min_stok_operator == '<') ? 'selected' : '' ?>><</option>
                                        </select>
                                        <input type="number" name="min_stok_value" class="form-control rounded-0" placeholder="Nilai" value="<?= esc($min_stok_value) ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Harga Beli Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Harga Beli</label>
                                    <div class="input-group">
                                        <select name="harga_beli_operator" class="form-control rounded-0" style="max-width: 80px;">
                                            <option value="">-</option>
                                            <option value="=" <?= ($harga_beli_operator == '=') ? 'selected' : '' ?>>=</option>
                                            <option value="<=" <?= ($harga_beli_operator == '<=') ? 'selected' : '' ?>>≤</option>
                                            <option value=">=" <?= ($harga_beli_operator == '>=') ? 'selected' : '' ?>>≥</option>
                                            <option value=">" <?= ($harga_beli_operator == '>') ? 'selected' : '' ?>>></option>
                                            <option value="<" <?= ($harga_beli_operator == '<') ? 'selected' : '' ?>><</option>
                                        </select>
                                        <input type="text" name="harga_beli_value" class="form-control rounded-0" placeholder="Rp 0" value="<?= esc($harga_beli_value) ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Harga Jual Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Harga Jual</label>
                                    <div class="input-group">
                                        <select name="harga_jual_operator" class="form-control rounded-0" style="max-width: 80px;">
                                            <option value="">-</option>
                                            <option value="=" <?= ($harga_jual_operator == '=') ? 'selected' : '' ?>>=</option>
                                            <option value="<=" <?= ($harga_jual_operator == '<=') ? 'selected' : '' ?>>≤</option>
                                            <option value=">=" <?= ($harga_jual_operator == '>=') ? 'selected' : '' ?>>≥</option>
                                            <option value=">" <?= ($harga_jual_operator == '>') ? 'selected' : '' ?>>></option>
                                            <option value="<" <?= ($harga_jual_operator == '<') ? 'selected' : '' ?>><</option>
                                        </select>
                                        <input type="text" name="harga_jual_value" class="form-control rounded-0" placeholder="Rp 0" value="<?= esc($harga_jual_value) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary rounded-0">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="<?= base_url('master/item') ?>" class="btn btn-secondary rounded-0">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
                
                <!-- Table Section -->
                <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="select-all">
                                    <label for="select-all"></label>
                                </div>
                            </th>
                            <th width="50" class="text-center">No.</th>
                            <th width="80">Foto</th>
                            <th>Kategori</th>
                            <th>Item</th>
                            <th>Supplier</th>
                            <th class="text-right">Harga Beli</th>
                            <th class="text-center">Stok Min</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $key => $row): ?>
                                <tr>
                                    <td class="text-center">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" class="item-checkbox" value="<?= $row->id ?>" id="item-<?= $row->id ?>">
                                            <label for="item-<?= $row->id ?>"></label>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= (($currentPage - 1) * $perPage) + $key + 1 ?>.</td>
                                    <td>
                                        <?php if (!empty($row->foto)): ?>
                                            <img src="<?= base_url($row->foto) ?>" alt="<?= $row->item ?>" class="img-thumbnail"
                                                style="width: 50px; height: 50px; object-fit: cover;" data-toggle="tooltip"
                                                title="<?= $row->item ?>">
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center"
                                                style="width: 50px; height: 50px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $row->kategori ?></td>
                                    <td>
                                        <?= $row->kode ?>
                                        <?= br() ?>
                                        <?= $row->item ?>
                                        <?= br() ?>
                                        <small><b>Rp. <?= format_angka($row->harga_jual) ?></b></small>
                                        <?php if (!empty($row->deskripsi)): ?>
                                            <?= br() ?>
                                            <small><i>(<?= strtolower($row->deskripsi) ?>)</i></small>
                                        <?php endif; ?>
                                        <?= br() ?>
                                        <small><i><?= $row->barcode ?></i></small>
                                        <?php echo br() ?>
                                    <?php $statusInfo = isItemActive($row->status); ?>
                                    <span class="badge badge-<?= $statusInfo['badge'] ?>"><?= $statusInfo['label'] ?></span>
                                    <?php echo isPPN($row->status_ppn); ?>
                                    </td>
                                    <td><?= $row->supplier_nama ?? '-' ?></td>
                                    <td class="text-right"><?= format_angka($row->harga_beli) ?></td>
                                    <td class="text-center"><?= $row->jml_min ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= base_url("master/item/edit/{$row->id}") ?>"
                                                class="btn btn-warning btn-sm rounded-0">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url("master/item/upload/{$row->id}") ?>"
                                                class="btn btn-info btn-sm rounded-0">
                                                <i class="fas fa-upload"></i>
                                            </a>
                                            <a href="<?= base_url("master/item/delete/{$row->id}") ?>"
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
                                <td colspan="8" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
                </div>
            <?php if ($pager): ?>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        <?= $pager->links('items', 'adminlte_pagination') ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
        
        // Handle select all checkbox
        $('#select-all').change(function() {
            $('.item-checkbox').prop('checked', $(this).is(':checked'));
            updateBulkDeleteButton();
        });
        
        // Handle individual checkboxes
        $(document).on('change', '.item-checkbox', function() {
            updateBulkDeleteButton();
            
            // Update select all checkbox
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
        
        // Handle bulk delete button
        $('#bulk-delete-btn').click(function() {
            var selectedItems = $('.item-checkbox:checked').map(function() {
                return $(this).val();
            }).get();
            
            if (selectedItems.length === 0) {
                alert('Pilih item yang akan dihapus');
                return;
            }
            
            if (confirm('Apakah anda yakin ingin menghapus ' + selectedItems.length + ' item yang dipilih ke arsip?')) {
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
                url: '<?= base_url('master/item/bulk_delete') ?>',
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
                        // Show success message
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message);
                        } else {
                            alert(response.message);
                        }
                        
                        // Reload page after short delay
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        if (typeof toastr !== 'undefined') {
                            toastr.error(response.message);
                        } else {
                            alert(response.message);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Terjadi kesalahan saat menghapus item');
                    } else {
                        alert('Terjadi kesalahan saat menghapus item');
                    }
                },
                complete: function() {
                    $('#bulk-delete-btn').prop('disabled', false).html('<i class="fas fa-trash"></i> Hapus Terpilih (<span id="selected-count">' + $('.item-checkbox:checked').length + '</span>)');
                }
            });
        }
        
        // Price input formatting
        $('input[name="harga_beli_value"], input[name="harga_jual_value"]').on('input', function() {
            var value = $(this).val().replace(/[^\d]/g, '');
            if (value !== '') {
                value = parseInt(value).toLocaleString('id-ID');
                $(this).val(value);
            }
        });
        
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<?= $this->endSection() ?>