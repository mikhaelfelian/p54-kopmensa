<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2024-07-15
 * Github : github.com/mikhaelfelian
 * description : View for displaying item stock details.
 * This file represents the inventory detail view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Data Item</h3>
                    <div class="card-tools">

                    </div>
                </div>
                <div class="card-body table-responsive">
                    <div class="form-group ">
                        <label class="control-label">Kode</label>
                        <input type="text" value="<?= $item->kode ?? '' ?>" class="form-control rounded-0" readonly>
                    </div>
                    <div class="form-group ">
                        <label class="control-label">Item</label>
                        <input type="text" value="<?= $item->item ?? '' ?>" class="form-control rounded-0" readonly>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label class="control-label">Jumlah</label>
                            <input type="text" value="<?= $item->jumlah ?? 0 ?>" class="form-control text-right rounded-0" readonly>
                        </div>
                        <div class="col-8">
                            <div class="form-group ">
                                <label class="control-label">Satuan</label>
                                <select class="form-control rounded-0" disabled>
                                    <option value="">- Pilih -</option>
                                    <option value="<?= $item->id_satuan ?? '' ?>" selected=""><?= $item->id_satuan ?? 'PCS' ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="<?= base_url('gudang/stok') ?>" class="btn btn-primary btn-flat">« Kembali</a>
                        </div>
                        <div class="col-lg-6 text-right">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Inventori per Outlet</h3>
                    <div class="card-tools">
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <?= form_open(base_url("gudang/stok/update/{$item->id}"), ['autocomplete' => 'off', 'id' => 'stock-update-form']) ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Outlet</th>
                                    <th class="text-center"></th>
                                    <th colspan="4" class="text-left">Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($outlets as $outlet): ?>
                                <tr>
                                    <th><?= $outlet->outlet_nama ?? $outlet->gudang_nama ?? $outlet->nama ?></th>
                                    <th>:</th>
                                    <td class="text-right" style="width: 120px;">
                                        <input type="number" 
                                               name="jml[<?= $outlet->id_outlet ?? $outlet->id_gudang ?>]" 
                                               value="<?= $outlet->jml ?? 0 ?>" 
                                               class="form-control rounded-0" 
                                               min="0" 
                                               step="0.01">
                                    </td>
                                    <td class="text-left">PCS</td>
                                    <td class="text-left">
                                        <button type="submit" 
                                                class="btn btn-primary btn-flat btn-sm" 
                                                onclick="updateSingleStock(this, <?= $outlet->id_outlet ?? $outlet->id_gudang ?>)">
                                            <i class="fa fa-save"></i>
                                        </button>
                                    </td>
                                    <td class="text-left">
                                        <?php if ($outlet->status == '1'): ?>
                                            <label class="badge badge-success">Utama</label>
                                        <?php else: ?>
                                            <label class="badge badge-secondary">Tidak Aktif</label>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">            
            <!-- Stock History Section -->
            <div class="card card-default rounded-0">
                <div class="card-header">
                    <h3 class="card-title">Data Mutasi Stok</h3>
                    <div class="card-tools">
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <form method="get" action="<?= current_url() ?>">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Gudang</th>
                                    <th class="text-right">Jml</th>
                                    <th>Satuan</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="form-group ">
                                            <select name="filter_gd" class="form-control rounded-0">
                                                <option value="">- [Pilih] -</option>
                                                <option value="1" <?= ($filter_gd == '1') ? 'selected' : '' ?>>
                                                    Gd. Bawah</option>
                                                <option value="2" <?= ($filter_gd == '2') ? 'selected' : '' ?>>
                                                    Gd. Atas</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td style="width: 100px;">
                                        <input type="number" name="filter_jml" class="form-control rounded-0"
                                            placeholder="Jumlah" value="">
                                    </td>
                                    <td style="width: 100px;"></td>
                                    <td>
                                        <input type="text" name="filter_ket" class="form-control rounded-0"
                                            placeholder="Keterangan" value="">
                                    </td>
                                    <td>
                                        <select name="filter_status" class="form-control rounded-0">
                                            <option value="">- [Semua] -</option>
                                            <option value="1" <?= ($filter_status == '1') ? 'selected' : '' ?>>Stok Masuk Pembelian</option>
                                            <option value="2" <?= ($filter_status == '2') ? 'selected' : '' ?>>Stok Masuk</option>
                                            <option value="3" <?= ($filter_status == '3') ? 'selected' : '' ?>>Stok Masuk Retur Jual</option>
                                            <option value="4" <?= ($filter_status == '4') ? 'selected' : '' ?>>Stok Keluar Penjualan</option>
                                            <option value="5" <?= ($filter_status == '5') ? 'selected' : '' ?>>Stok Keluar Retur Beli</option>
                                            <option value="6" <?= ($filter_status == '6') ? 'selected' : '' ?>>SO (Stock Opname)</option>
                                            <option value="7" <?= ($filter_status == '7') ? 'selected' : '' ?>>Stok Keluar</option>
                                            <option value="8" <?= ($filter_status == '8') ? 'selected' : '' ?>>Mutasi Antar Gudang</option>
                                            <option value="9" <?= ($filter_status == '9') ? 'selected' : '' ?>>Adjust stok</option>
                                        </select>
                                    </td>
                                    <td><button type="submit" class="btn btn-primary btn-flat rounded-0"><i
                                                class="fa fa-search"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                    
                    <!-- Stock History Data Table -->
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Gudang</th>
                                <th class="text-right">Jml</th>
                                <th>Satuan</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>No. Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($stokData)): ?>
                                <?php foreach ($stokData as $row): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($row->created_at)) ?></td>
                                        <td><?= $row->gudang_name ?? '-' ?></td>
                                        <td class="text-right"><?= $row->jml; ?></td>
                                        <td><?= $row->satuan_name ?? 'PCS' ?></td>
                                        <td><?= $row->keterangan ?></td>
                                        <td>
                                            <span class="badge badge-<?= in_array($row->status, ['1', '2', '3']) ? 'success' : 'danger' ?>">
                                                <?= (new \App\Models\ItemHistModel())->getStatusLabel($row->status) ?>
                                            </span>
                                        </td>
                                        <td><?= $row->no_nota ?? '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data riwayat stok</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="pagination-info">
                                Tampilkan <?= (($current_page - 1) * $per_page) + 1 ?> dari <?= min($current_page * $per_page, $total) ?> of <?= $total ?> data
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pagination-wrapper float-right">
                                <?= $pager->links('item_hist', 'adminlte_pagination') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
// Store original values for reset functionality
var originalValues = {};

$(document).ready(function() {
    // Store original stock values
    $('input[name^="jml["]').each(function() {
        originalValues[$(this).attr('name')] = $(this).val();
    });
    
    // Show success/error messages
    <?php if (session()->getFlashdata('success')): ?>
        toastr.success('<?= session()->getFlashdata('success') ?>');
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        toastr.error('<?= session()->getFlashdata('error') ?>');
    <?php endif; ?>
    

});

// Function to update single stock item
function updateSingleStock(button, locationId) {
    event.preventDefault();
    
    var $button = $(button);
    var $row = $button.closest('tr');
    var $input = $row.find('input[name^="jml["]');
    var quantity = $input.val();
    
    if (quantity === '' || isNaN(quantity) || parseFloat(quantity) < 0) {
        toastr.error('Jumlah stok harus berupa angka yang valid dan tidak boleh negatif');
        return false;
    }
    
    // Create data object for single item
    var data = {};
    data[$input.attr('name')] = quantity;
    
    // Debug: Log the data being sent
    console.log('Sending data:', data);
    
    // Show loading state
    var originalHtml = $button.html();
    $button.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);
    
    $.ajax({
        url: '<?= base_url("gudang/stok/update/{$item->id}") ?>',
        type: 'POST',
        data: data,
        success: function(response) {
            toastr.success('Stok berhasil diupdate');
            // Update the original value
            originalValues[$input.attr('name')] = quantity;
        },
        error: function(xhr, status, error) {
            console.error('Error:', xhr.responseText);
            toastr.error('Gagal mengupdate stok: ' + error);
        },
        complete: function() {
            // Restore button state
            $button.html(originalHtml).prop('disabled', false);
        }
    });
}

// Function to reset form to original values
function resetForm() {
    if (confirm('Apakah Anda yakin ingin mereset semua nilai ke nilai awal?')) {
        $('input[name^="jml["]').each(function() {
            var fieldName = $(this).attr('name');
            if (originalValues[fieldName] !== undefined) {
                $(this).val(originalValues[fieldName]);
            }
        });
        toastr.info('Form telah direset ke nilai awal');
    }
}

// Handle form submission for updating all stocks
$('#stock-update-form').on('submit', function(e) {
    e.preventDefault();
    
    var $form = $(this);
    var hasChanges = false;
    
    // Check if any values have changed
    $('input[name^="jml["]').each(function() {
        var fieldName = $(this).attr('name');
        if (originalValues[fieldName] !== $(this).val()) {
            hasChanges = true;
            return false; // break loop
        }
    });
    
    if (!hasChanges) {
        toastr.warning('Tidak ada perubahan data untuk diupdate');
        return false;
    }
    
    // Validate all inputs
    var isValid = true;
    $('input[name^="jml["]').each(function() {
        var value = $(this).val();
        if (value !== '' && (isNaN(value) || parseFloat(value) < 0)) {
            $(this).addClass('is-invalid');
            isValid = false;
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    if (!isValid) {
        toastr.error('Pastikan semua jumlah stok berupa angka yang valid dan tidak negatif');
        return false;
    }
    
    if (confirm('Apakah Anda yakin ingin mengupdate semua stok?')) {
        // Show loading state
        var $submitBtn = $form.find('button[type="submit"]');
        var originalBtnText = $submitBtn.html();
        $submitBtn.html('<i class="fa fa-spinner fa-spin mr-1"></i> Mengupdate...').prop('disabled', true);
        
        // Submit form normally
        $form.off('submit').submit();
    }
});

// Remove invalid class when user types
$('input[name^="jml["]').on('input', function() {
    $(this).removeClass('is-invalid');
});
</script>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
.is-invalid {
    border-color: #dc3545 !important;
}

.is-invalid:focus {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.btn-flat {
    border-radius: 0 !important;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.stock-update-buttons {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-top: 10px;
}

.badge {
    font-size: 0.75em;
}

.table-responsive {
    border-radius: 5px;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
}
</style>
<?= $this->endSection() ?>