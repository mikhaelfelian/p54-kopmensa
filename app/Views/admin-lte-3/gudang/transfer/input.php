<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-28
 * Github : github.com/mikhaelfelian
 * description : View for inputting items to transfer/mutasi.
 * This file represents the transfer input view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Input Item Transfer</h3>
                <div class="card-tools">
                    <a href="<?= base_url('gudang/transfer') ?>" class="btn btn-sm btn-secondary rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="120"><strong>No. Nota</strong></td>
                                <td>: <?= $transfer->no_nota ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Gudang Asal</strong></td>
                                <td>: <?= $transfer->gudang_asal_name ?></td>
                            </tr>
                            <tr>
                                <td><strong>Gudang Tujuan</strong></td>
                                <td>: <?= $transfer->gudang_tujuan_name ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="120"><strong>Tipe</strong></td>
                                <td>: 
                                    <?php
                                    $tipeLabels = [
                                        '1' => 'Pindah Gudang',
                                        '2' => 'Stok Masuk',
                                        '3' => 'Stok Keluar'
                                    ];
                                    $tipeColors = [
                                        '1' => 'info',
                                        '2' => 'success',
                                        '3' => 'warning'
                                    ];
                                    ?>
                                    <span class="badge badge-<?= $tipeColors[$transfer->tipe] ?? 'secondary' ?>">
                                        <?= $tipeLabels[$transfer->tipe] ?? 'Unknown' ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: 
                                    <?php
                                    $statusNotaLabels = [
                                        '0' => 'Draft',
                                        '1' => 'Pending',
                                        '2' => 'Diproses',
                                        '3' => 'Selesai'
                                    ];
                                    $statusNotaColors = [
                                        '0' => 'secondary',
                                        '1' => 'warning',
                                        '2' => 'info',
                                        '3' => 'success'
                                    ];
                                    ?>
                                    <span class="badge badge-<?= $statusNotaColors[$transfer->status_nota] ?? 'secondary' ?>">
                                        <?= $statusNotaLabels[$transfer->status_nota] ?? 'Unknown' ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <?php if ($transfer->status_nota == '3'): ?>
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Peringatan!</h5>
                        <p>Transfer ini sudah selesai dan tidak dapat ditambahkan item lagi.</p>
                    </div>
                <?php else: ?>
                    <form action="<?= base_url("gudang/transfer/process/{$transfer->id}") ?>" method="post" id="transferItemForm">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="itemTable">
                                        <thead>
                                            <tr>
                                                <th width="50" class="text-center">
                                                    <input type="checkbox" id="selectAll">
                                                </th>
                                                <th>Kode Item</th>
                                                <th>Nama Item</th>
                                                <th>Satuan</th>
                                                <th class="text-center">Stok Tersedia</th>
                                                <th class="text-center">Jumlah Transfer</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($items)): ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">Tidak ada item tersedia di gudang asal</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($items as $item): ?>
                                                    <tr>
                                                        <td class="text-center">
                                                            <input type="checkbox" name="items[]" value="<?= $item->id_item ?>" 
                                                                   class="item-checkbox" data-stok="<?= $item->jml ?>">
                                                        </td>
                                                        <td><?= $item->item_kode ?? '-' ?></td>
                                                        <td><?= $item->item_name ?? '-' ?></td>
                                                        <td><?= $item->satuan_name ?? '-' ?></td>
                                                        <td class="text-center">
                                                            <span class="badge badge-info"><?= number_format($item->jml, 2) ?></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="number" name="quantities[]" class="form-control form-control-sm quantity-input" 
                                                                   min="0" max="<?= $item->jml ?>" step="0.01" placeholder="0" disabled>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="notes[]" class="form-control form-control-sm" 
                                                                   placeholder="Keterangan..." disabled>
                                                        </td>
                                                    </tr>
                                                <?php endforeach ?>
                                            <?php endif ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-success rounded-0" id="btnProcess" disabled>
                                    <i class="fas fa-check"></i> Proses Transfer
                                </button>
                                <a href="<?= base_url('gudang/transfer') ?>" class="btn btn-secondary rounded-0">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('.item-checkbox').prop('checked', $(this).is(':checked'));
        $('.item-checkbox').trigger('change');
    });
    
    // Individual checkbox change
    $('.item-checkbox').on('change', function() {
        var row = $(this).closest('tr');
        var quantityInput = row.find('.quantity-input');
        var noteInput = row.find('input[name="notes[]"]');
        
        if ($(this).is(':checked')) {
            quantityInput.prop('disabled', false).focus();
            noteInput.prop('disabled', false);
        } else {
            quantityInput.prop('disabled', true).val('');
            noteInput.prop('disabled', true).val('');
        }
        
        updateProcessButton();
    });
    
    // Quantity input validation
    $('.quantity-input').on('input', function() {
        var max = parseFloat($(this).attr('max'));
        var value = parseFloat($(this).val());
        
        if (value > max) {
            $(this).val(max);
            alert('Jumlah tidak boleh melebihi stok tersedia!');
        }
        
        if (value <= 0) {
            $(this).val('');
        }
        
        updateProcessButton();
    });
    
    // Update process button state
    function updateProcessButton() {
        var checkedItems = $('.item-checkbox:checked').length;
        var hasQuantities = false;
        
        $('.item-checkbox:checked').each(function() {
            var row = $(this).closest('tr');
            var quantity = parseFloat(row.find('.quantity-input').val());
            if (quantity > 0) {
                hasQuantities = true;
                return false;
            }
        });
        
        $('#btnProcess').prop('disabled', !(checkedItems > 0 && hasQuantities));
    }
    
    // Form submission
    $('#transferItemForm').on('submit', function(e) {
        var hasValidItems = false;
        
        $('.item-checkbox:checked').each(function() {
            var row = $(this).closest('tr');
            var quantity = parseFloat(row.find('.quantity-input').val());
            if (quantity > 0) {
                hasValidItems = true;
                return false;
            }
        });
        
        if (!hasValidItems) {
            e.preventDefault();
            alert('Pilih minimal satu item dengan jumlah lebih dari 0!');
            return false;
        }
        
        if (!confirm('Apakah Anda yakin ingin memproses transfer ini? Stok akan diperbarui secara otomatis.')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
<?= $this->endSection() ?> 