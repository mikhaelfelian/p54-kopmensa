<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-12
 * Github : github.com/mikhaelfelian
 * description : View for inputting items to stock opname.
 * This file represents the opname input view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<input type="hidden" name="outlet_id" value="<?= $opname->id_outlet ?? $opname->id_gudang ?>" id="outlet_id">
<input type="hidden" id="csrf_token_name" name="csrf_token_name" value="<?= csrf_token() ?>">
<input type="hidden" id="csrf_token_value" name="csrf_token_value" value="<?= csrf_hash() ?>">
<input type="hidden" id="id_so" name="id_so" value="<?= $opname->id ?>">

<div class="row">
    <div class="col-md-12">
        <!-- Input Form -->
        <?= form_open('', ['id' => 'opname_input_form']) ?>
        <div class="card card-default rounded-0">
            <div class="card-header rounded-0">
                <h3 class="card-title">Input Item Opname <?= $opname->location_type ?? 'Gudang' ?> - <?= $opname->id ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('gudang/opname') ?>" class="btn btn-secondary btn-sm rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <a href="<?= base_url("gudang/opname/detail/{$opname->id}") ?>"
                        class="btn btn-info btn-sm rounded-0">
                        <i class="fas fa-eye"></i> Detail
                    </a>
                </div>
            </div>
            <div class="card-body rounded-0">
                <!-- Opname Info -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-borderless rounded-0">
                            <tr>
                                <td width="120"><strong>Tanggal</strong></td>
                                <td>:
                                    <?= isset($opname->tgl_masuk) ? tgl_indo2($opname->tgl_masuk) : tgl_indo2($opname->created_at) ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong><?= $opname->location_type ?? 'Gudang' ?></strong></td>
                                <td>: <?= $gudang->nama ?? ($opname->nama_gudang ?? 'N/A') ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless rounded-0">
                            <tr>
                                <td width="120"><strong>Status</strong></td>
                                <td>: 
                                    <?php if ($opname->status == '0'): ?>
                                        <span class="badge badge-warning rounded-0">Draft</span>
                                    <?php else: ?>
                                        <span class="badge badge-success rounded-0">Selesai</span>
                                    <?php endif ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Keterangan</strong></td>
                                <td>: <?= $opname->keterangan ?: '-' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <?php if ($opname->status == '0'): ?>
                    <div class="table-responsive rounded-0">
                        <table class="table table-striped rounded-0" id="opname-table">
                            <thead class="rounded-0">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="25%">Item</th>
                                    <th width="10%">Satuan</th>
                                    <th width="15%">Stok Sistem</th>
                                    <th width="15%">Stok Fisik</th>
                                    <th width="15%">Keterangan</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="rounded-0" id="opname-tbody">
                                <!-- Add Item Row -->
                                <tr id="add-item-row">
                                    <td class="text-center">+</td>
                                    <td>
                                        <select name="new_item_id" id="new_item_select"
                                            class="form-control form-control-sm rounded-0 select2">
                                            <option value="">Pilih Item</option>
                                            <?php if (!empty($dropdownItems)): ?>
                                                <?php foreach ($dropdownItems as $item): ?>
                                                    <option value="<?= $item->id ?>" data-satuan="<?= esc($item->satuan ?? '') ?>">
                                                        <?= esc($item->item) ?> (<?= esc($item->kode) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" id="new_item_satuan"
                                            class="form-control form-control-sm rounded-0"
                                            placeholder="Satuan" readonly>
                                    </td>
                                    <td>
                                        <input type="text" id="new_item_stok_sistem"
                                            class="form-control form-control-sm text-right rounded-0"
                                            placeholder="0" readonly>
                                    </td>
                                    <td>
                                        <input type="number" min="0" step="any" id="new_item_stok_fisik"
                                            class="form-control form-control-sm text-right rounded-0"
                                            placeholder="Stok Fisik">
                                    </td>
                                    <td>
                                        <input type="text" id="new_item_keterangan"
                                            class="form-control form-control-sm rounded-0" placeholder="Keterangan">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-sm rounded-0" id="btn-add-item">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Existing Items -->
                                <?php if (!empty($items)): ?>
                                    <?php $no = 1;
                                    foreach ($items as $item): ?>
                                        <tr data-item-id="<?= $item->id ?>">
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td>
                                                <strong><?= esc($item->item) ?></strong><br>
                                                <small class="text-muted"><?= esc($item->kode) ?></small>
                                            </td>
                                            <td><?= esc($item->satuan ?? '-') ?></td>
                                            <td class="text-right">
                                                <input type="text" class="form-control form-control-sm text-right rounded-0"
                                                    value="<?= number_format((float)($item->current_stock ?? $item->jml_sys ?? 0), 0) ?>" readonly>
                                            </td>
                                            <td>
                                                <input type="number" min="0" step="any"
                                                    class="form-control form-control-sm text-right rounded-0"
                                                    name="items[<?= $item->id ?>][stok_fisik]"
                                                    value="<?= old("items.{$item->id}.stok_fisik", (float)($item->jml_so ?? $item->jml_sys)) ?>"
                                                    data-item-id="<?= $item->id ?>"
                                                    onchange="updateItemStok(<?= $item->id ?>, this.value)">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm rounded-0"
                                                    name="items[<?= $item->id ?>][keterangan]"
                                                    value="<?= old("items.{$item->id}.keterangan", $item->keterangan ?? '') ?>"
                                                    data-item-id="<?= $item->id ?>"
                                                    onchange="updateItemKeterangan(<?= $item->id ?>, this.value)">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm rounded-0 btn-delete-item"
                                                    data-id="<?= $item->id ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else: ?>
                                    <tr id="no-items-row">
                                        <td colspan="7" class="text-center text-muted">
                                            <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                            Belum ada item yang ditambahkan
                                        </td>
                                    </tr>
                                <?php endif ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning rounded-0">
                        <i class="fas fa-exclamation-triangle"></i> Opname ini sudah selesai diproses dan tidak dapat diubah
                        lagi.
                    </div>
                <?php endif ?>
            </div>
                    <div class="card-footer text-right">
                        <button type="button" class="btn btn-secondary rounded-0" onclick="history.back()">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <?php if (!empty($items)): ?>
                            <button type="submit" class="btn btn-success rounded-0" id="btn-process-opname">
                                <i class="fas fa-check mr-1"></i> Proses Opname
                            </button>
                        <?php endif ?>
                    </div>
        </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->section('js') ?>
<script>
    var csrfName = '<?= csrf_token() ?>';
    var csrfHash = '<?= csrf_hash() ?>';

    $(document).ready(function () {
        // Initialize Select2
        initializeSelect2();
        
        // Debug outlet_id value
        console.log('Outlet ID:', $('#outlet_id').val());

        // Form validation and AJAX submission
        $('#opname_input_form').on('submit', function (e) {
            e.preventDefault();
            processOpname();
        });

        // Add item functionality
        $('#btn-add-item').on('click', function() {
            addItemToOpname();
        });

        // Item selection change event
        $('#new_item_select').on('change', function() {
            var selectedValue = $(this).val();
            console.log('Item selected:', selectedValue); // Debug
            loadItemStock(selectedValue);
        });

        // Delete item functionality
        $(document).on('click', '.btn-delete-item', function() {
            deleteItemFromOpname($(this).data('id'));
        });
    });

    function initializeSelect2() {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Pilih Item...'
        });
    }

    function loadItemStock(itemId) {
        if (!itemId) {
            $('#new_item_satuan').val('');
            $('#new_item_stok_sistem').val('');
            $('#new_item_stok_fisik').val('');
            return;
        }

        var outletId = $('#outlet_id').val();
        
        // Get satuan from option data first
        var selectedOption = $('#new_item_select option:selected');
        var satuan = selectedOption.data('satuan') || '';
        $('#new_item_satuan').val(satuan);

        // Show loading indicator
        $('#new_item_stok_sistem').val('Loading...');

        console.log('Loading stock for item:', itemId, 'outlet:', outletId); // Debug

        // Get stock from server
        $.ajax({
            url: '<?= base_url('gudang/opname/get-stock-outlet') ?>',
            type: 'GET',
            data: {
                item_id: itemId,
                outlet_id: outletId
            },
            dataType: 'json'
        })
        .done(function(response) {
            console.log('Stock response:', response); // Debug
            
            if (response && response.jml !== undefined) {
                $('#new_item_stok_sistem').val(Math.round(parseFloat(response.jml)));
                
                // Update satuan if provided from server
                if (response.satuan) {
                    $('#new_item_satuan').val(response.satuan);
                }
                
                // Auto-fill stok fisik with system stock as default
                if (!$('#new_item_stok_fisik').val()) {
                    $('#new_item_stok_fisik').val(Math.round(parseFloat(response.jml)));
                }
                
                console.log('Stock loaded successfully:', response.jml); // Debug
            } else {
                $('#new_item_stok_sistem').val('0');
                $('#new_item_stok_fisik').val('0');
                console.log('No stock data in response'); // Debug
            }
        })
        .fail(function(xhr, status, error) {
            console.error('Failed to load stock:', error, xhr.responseText); // Debug
            $('#new_item_stok_sistem').val('0');
            $('#new_item_stok_fisik').val('0');
            toastr.warning('Gagal memuat stok item: ' + error);
        });
    }

    function addItemToOpname() {
        var itemId = $('#new_item_select').val();
        var stokSistem = $('#new_item_stok_sistem').val();
        var stokFisik = $('#new_item_stok_fisik').val();
        var satuan = $('#new_item_satuan').val();
        var keterangan = $('#new_item_keterangan').val();

        // Validation
        if (!itemId) {
            toastr.error('Pilih item terlebih dahulu');
            return;
        }

        if (!stokFisik || stokFisik < 0) {
            toastr.error('Masukkan stok fisik yang valid');
            return;
        }

        // Check if item already exists
        if ($(`tr[data-item-id="${itemId}"]`).length > 0) {
            toastr.error('Item sudah ada dalam daftar opname');
            return;
        }

        var $btn = $('#btn-add-item');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');

        var data = {
            id_so: $('#id_so').val(),
            id_item: itemId,
            stok_sistem: stokSistem,
            stok_fisik: stokFisik,
            satuan: satuan,
            keterangan: keterangan,
            [csrfName]: csrfHash
        };

        $.post('<?= base_url('gudang/opname/add-item') ?>', data)
        .done(function(response) {
            if (response.status === 'success') {
                toastr.success(response.message || 'Item berhasil ditambahkan');
                csrfHash = response.csrfHash || csrfHash;
                
                // Clear form
                clearAddItemForm();
                
                // Redirect to base URL after successful input
                setTimeout(function() {
                    window.location.href = '<?= base_url('gudang/opname/input/'.$opname->id) ?>';
                }, 0);
            } else {
                toastr.error(response.message || 'Gagal menambah item');
            }
        })
        .fail(function(xhr) {
            var errorMsg = 'Terjadi kesalahan saat menambah item';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            toastr.error(errorMsg);
        })
        .always(function() {
            $btn.prop('disabled', false).html(originalText);
        });
    }

    function clearAddItemForm() {
        $('#new_item_select').val('').trigger('change');
        $('#new_item_satuan').val('');
        $('#new_item_stok_sistem').val('');
        $('#new_item_stok_fisik').val('');
        $('#new_item_keterangan').val('');
    }

    function deleteItemFromOpname(itemId) {
        if (!confirm('Apakah anda yakin ingin menghapus item ini dari opname?')) {
            return;
        }

        var data = {
            item_id: itemId,
            id_so: $('#id_so').val(),
            [csrfName]: csrfHash
        };

        $.post('<?= base_url('gudang/opname/delete-item') ?>', data)
        .done(function(response) {
            if (response.status === 'success') {
                toastr.success(response.message || 'Item berhasil dihapus');
                csrfHash = response.csrfHash || csrfHash;
                
                // Remove row from table
                $(`tr[data-item-id="${itemId}"]`).fadeOut(300, function() {
                    $(this).remove();
                    updateRowNumbers();
                    
                    // Show no items message if table is empty
                    if ($('#opname-tbody tr[data-item-id]').length === 0) {
                        $('#opname-tbody').append(`
                            <tr id="no-items-row">
                                <td colspan="7" class="text-center text-muted">
                                    <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                    Belum ada item yang ditambahkan
                                </td>
                            </tr>
                        `);
                    }
                });
            } else {
                toastr.error(response.message || 'Gagal menghapus item');
            }
        })
        .fail(function(xhr) {
            var errorMsg = 'Terjadi kesalahan saat menghapus item';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            toastr.error(errorMsg);
        });
    }

    function updateRowNumbers() {
        $('#opname-tbody tr[data-item-id]').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    }

    function updateItemStok(itemId, value) {
        // Auto-save functionality when stok fisik is changed
        var data = {
            id_so: $('#id_so').val(),
            item_id: itemId,
            stok_fisik: value,
            [csrfName]: csrfHash
        };

        $.post('<?= base_url('gudang/opname/update-item-stok') ?>', data)
        .done(function(response) {
            if (response.status === 'success') {
                csrfHash = response.csrfHash || csrfHash;
                // Show subtle success indicator
                $(`tr[data-item-id="${itemId}"] input[name*="stok_fisik"]`).addClass('border-success').removeClass('border-danger');
                setTimeout(function() {
                    $(`tr[data-item-id="${itemId}"] input[name*="stok_fisik"]`).removeClass('border-success');
                }, 1000);
            } else {
                $(`tr[data-item-id="${itemId}"] input[name*="stok_fisik"]`).addClass('border-danger');
                toastr.error(response.message || 'Gagal menyimpan perubahan');
            }
        })
        .fail(function() {
            $(`tr[data-item-id="${itemId}"] input[name*="stok_fisik"]`).addClass('border-danger');
        });
    }

    function updateItemKeterangan(itemId, value) {
        // Auto-save functionality when keterangan is changed
        var data = {
            id_so: $('#id_so').val(),
            item_id: itemId,
            keterangan: value,
            [csrfName]: csrfHash
        };

        $.post('<?= base_url('gudang/opname/update-item-keterangan') ?>', data)
        .done(function(response) {
            if (response.status === 'success') {
                csrfHash = response.csrfHash || csrfHash;
            }
        });
    }

    function processOpname() {
        if (!confirm('Apakah anda yakin ingin memproses opname ini? Stok akan diperbarui sesuai data yang diinput dan tidak dapat dibatalkan.')) {
            return;
        }

        var $btn = $('#btn-process-opname');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');

        var opnameId = $('#id_so').val();
        var data = {
            [csrfName]: csrfHash
        };

        $.post('<?= base_url('gudang/opname/process/') ?>' + opnameId, data)
        .done(function(response) {
            if (response.status === 'success') {
                toastr.success(response.message || 'Opname berhasil diproses');
                
                // Redirect after success
                setTimeout(function() {
                    window.location.href = '<?= base_url('gudang/opname') ?>';
                }, 1500);
            } else {
                toastr.error(response.message || 'Gagal memproses opname');
                $btn.prop('disabled', false).html(originalText);
            }
        })
        .fail(function(xhr) {
            var errorMsg = 'Terjadi kesalahan saat memproses opname';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            toastr.error(errorMsg);
            $btn.prop('disabled', false).html(originalText);
        });
    }
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?> 