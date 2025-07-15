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
<input type="hidden" name="outlet_id" value="<?= $opname->id_outlet ?>" id="outlet_id">
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
                                <td>: <?= $opname->location_name ?? ($opname->gudang ?? 'N/A') ?></td>
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
                        <table class="table table-striped rounded-0">
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
                            <tbody class="rounded-0">
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>
                                        <select name="items[0][id_item]"
                                            class="form-control form-control-sm rounded-0 select2">
                                            <option value="">Pilih Item</option>
                                            <?php foreach ($dropdownItems as $item): ?>
                                                <option value="<?= $item->id ?>">
                                                    <?= esc($item->item) ?> (<?= esc($item->kode) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="items[0][satuan]"
                                            class="form-control form-control-sm rounded-0 satuan-outlet"
                                            placeholder="Satuan" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm text-right stok-outlet"
                                            name="items[0][stok_sistem]" value="" readonly>
                                    </td>
                                    <td>
                                        <input type="number" min="0" step="any" name="items[0][stok_fisik]"
                                            class="form-control form-control-sm text-right rounded-0"
                                            placeholder="Stok Fisik">
                                    </td>
                                    <td>
                                        <input type="text" name="items[0][keterangan]"
                                            class="form-control form-control-sm rounded-0" placeholder="Keterangan">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-sm rounded-0 btn-add-opname-item">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php if (!empty($items)): ?>
                                    <?php $no = 1;
                                    foreach ($items as $item): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= esc($item->item) ?></td>
                                            <td><?= esc($item->satuan) ?></td>
                                            <td>
                                                <?= form_input([
                                                    'type' => 'text',
                                                    'class' => 'form-control form-control-sm text-right rounded-0',
                                                    'value' => (float) $item->jml_sys,
                                                    'readonly' => true
                                                ]) ?>
                                            </td>
                                            <td>
                                                <?= form_input([
                                                    'type' => 'number',
                                                    'min' => '0',
                                                    'step' => 'any',
                                                    'class' => 'form-control form-control-sm text-right rounded-0',
                                                    'name' => "items[{$item->id}][stok_fisik]",
                                                    'value' => old("items.{$item->id}.stok_fisik", (float)$item->jml_so ?? ''),
                                                    'required' => true,
                                                    'readonly' => false // set to true if you want it readonly, false otherwise
                                                ]) ?>
                                            </td>
                                            <td>
                                                <?= form_input([
                                                    'type' => 'text',
                                                    'class' => 'form-control form-control-sm rounded-0',
                                                    'name' => "items[{$item->id}][keterangan]",
                                                    'value' => old("items.{$item->id}.keterangan", $item->keterangan ?? ''),
                                                    'readonly' => true
                                                ]) ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm rounded-0 btn-delete-opname-item"
                                                    data-id="<?= $item->id ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
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
                    <?php if (!empty($items)): ?>
                <div class="card-footer text-right">
                                <button type="button" class="btn btn-secondary rounded-0" onclick="history.back()">
                                    <i class="fas fa-times mr-1"></i> Batal
                                </button>
                                <button type="submit" class="btn btn-success rounded-0">
                                    <i class="fas fa-check mr-1"></i> Proses Opname
                                </button>
                    </div>
                <?php endif ?>
        </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->section('js') ?>
<script>
    $(document).ready(function () {
        // Form validation and AJAX submission
        $('#opname_input_form').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission
            
        let isValid = true;
        let errorMessages = [];
        
        // Check if all quantities are valid
            $('input[name^="quantities"]').each(function () {
            const quantity = parseFloat($(this).val()) || 0;
            
            if (quantity < 0) {
                errorMessages.push('Stok fisik tidak boleh negatif');
                isValid = false;
                return false;
            }
        });
        
        if (!isValid) {
            toastr.error(errorMessages.join('<br>'), 'Validasi Gagal');
            return false;
        }
        
        // Confirmation dialog
        if (!confirm('Apakah anda yakin ingin memproses opname ini? Stok akan diperbarui sesuai data yang diinput.')) {
            return false;
            }

            // AJAX submission
            var opnameId = $('#id_so').val();
            var $submitBtn = $(this).find('button[type="submit"]');
            var originalBtnText = $submitBtn.html();
            
            // Disable submit button and show loading
            $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');
            
            var data = {
                [csrfName]: csrfHash
            };

            $.post('<?= base_url('gudang/opname/process/') ?>' + opnameId, data, function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    csrfHash = response.csrfHash || csrfHash;
                    
                    // Redirect to opname list after success
                    setTimeout(function() {
                        window.location.href = '<?= base_url('gudang/opname') ?>';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Gagal memproses opname!');
                    // Re-enable submit button
                    $submitBtn.prop('disabled', false).html(originalBtnText);
                }
            }, 'json').fail(function(xhr) {
                var errorMsg = 'Terjadi kesalahan saat memproses opname.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
                // Re-enable submit button
                $submitBtn.prop('disabled', false).html(originalBtnText);
            });
        });

        $('.select2').select2({
            theme: 'bootstrap4', // if you have the Bootstrap 4 theme for Select2
            width: '100%'
        });

        $('.select2').on('change', function () {
            var $row = $(this).closest('tr');
            var itemId = $(this).val();
            var outletId = $('#outlet_id').val(); // or get the correct outlet id for this row

            if (itemId && outletId) {
                $.get('<?= base_url('gudang/opname/get-stock-outlet') ?>', { item_id: itemId, outlet_id: outletId }, function (response) {
                    // response is an array of objects, e.g. [{id, item, satuan, jml}]
                    if (response.id !== null) {
                        $row.find('.stok-outlet').val(response.jml);
                        $row.find('.satuan-outlet').val(response.satuan);
                    } else {
                        $row.find('.stok-outlet').val('0');
                        $row.find('.satuan-outlet').val('');
                    }
                }, 'json');
            } else {
                $row.find('.stok-outlet').val('');
                $row.find('.satuan-outlet').val('');
            }
        });
    });

    var csrfName = '<?= csrf_token() ?>';
    var csrfHash = '<?= csrf_hash() ?>';

    // Function to reload the table with updated data
    function reloadOpnameTable() {
        var opnameId = $('#id_so').val();
        $.get('<?= base_url('gudang/opname/get-table-data/') ?>' + opnameId, function(response) {
            if (response.status === 'success') {
                $('tbody.rounded-0').html(response.html);
                
                // Reinitialize Select2 for new elements
                $('.select2').select2({
                    theme: 'bootstrap4',
                    width: '100%'
                });
                
                // Rebind the change event for new Select2 elements
                $('.select2').off('change').on('change', function() {
                    var $row = $(this).closest('tr');
                    var itemId = $(this).val();
                    var outletId = $('#outlet_id').val() || '';

                    if (itemId) {
                        $.get('<?= base_url('gudang/opname/get-stock-outlet') ?>', {item_id: itemId, outlet_id: outletId}, function(response) {
                            if (response.id !== null) {
                                $row.find('.stok-outlet').val(response.jml);
                                $row.find('.satuan-outlet').val(response.satuan);
                            } else {
                                $row.find('.stok-outlet').val('0');
                                $row.find('.satuan-outlet').val('');
                            }
                        }, 'json');
                    } else {
                        $row.find('.stok-outlet').val('');
                        $row.find('.satuan-outlet').val('');
                    }
                });
            }
        }, 'json');
    }

    $(document).on('click', '.btn-add-opname-item', function () {
        var $row = $(this).closest('tr');
        var data = {
            id_so: $('#id_so').val(), // <-- use opname id, not outlet id
            id_item: $row.find('select[name$="[id_item]"]').val(),
            stok_sistem: $row.find('input[name$="[stok_sistem]"]').val(),
            stok_fisik: $row.find('input[name$="[stok_fisik]"]').val(),
            satuan: $row.find('input[name$="[satuan]"]').val(),
            keterangan: $row.find('input[name$="[keterangan]"]').val()
        };
        data[csrfName] = csrfHash;

        $.post('<?= base_url('gudang/opname/add-item') ?>', data, function (response) {
            if (response.status === 'success') {
                toastr.success(response.message);
                csrfHash = response.csrfHash || csrfHash; // update token
                
                // Clear the form inputs
                $row.find('select[name$="[id_item]"]').val('').trigger('change');
                $row.find('input[name$="[stok_sistem]"]').val('');
                $row.find('input[name$="[stok_fisik]"]').val('');
                $row.find('input[name$="[satuan]"]').val('');
                $row.find('input[name$="[keterangan]"]').val('');
                
                // Reload the table to show updated data
                reloadOpnameTable();
            } else {
                toastr.error(response.message || 'Gagal menambah item!');
            }
        }, 'json');
    });

    // Delete item functionality
    $(document).on('click', '.btn-delete-opname-item', function() {
        if (!confirm('Apakah anda yakin ingin menghapus item ini dari opname?')) {
            return;
        }

        var itemId = $(this).data('id');
        var $row = $(this).closest('tr');
        
        var data = {
            item_id: itemId,
            [csrfName]: csrfHash
        };

        $.post('<?= base_url('gudang/opname/delete-item') ?>', data, function(response) {
            if (response.status === 'success') {
                toastr.success(response.message);
                csrfHash = response.csrfHash || csrfHash;
                
                // Reload table after successful deletion
                reloadOpnameTable();
            } else {
                toastr.error(response.message || 'Gagal menghapus item!');
            }
        }, 'json').fail(function(xhr) {
            var errorMsg = 'Terjadi kesalahan saat menghapus item.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            toastr.error(errorMsg);
        });
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?> 