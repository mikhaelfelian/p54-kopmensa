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
<div class="row">
    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Input Item Opname - <?= $opname->id ?></h3>
                <div class="card-tools">
                    <a href="<?= base_url('gudang/opname') ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <a href="<?= base_url("gudang/opname/detail/{$opname->id}") ?>" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i> Detail
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Opname Info -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="120"><strong>Tanggal</strong></td>
                                <td>: <?= isset($opname->tgl_masuk) ? tgl_indo2($opname->tgl_masuk) : tgl_indo2($opname->created_at) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Gudang</strong></td>
                                <td>: <?= $opname->gudang ?? 'N/A' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="120"><strong>Status</strong></td>
                                <td>: 
                                    <?php if ($opname->status == '0'): ?>
                                        <span class="badge badge-warning">Draft</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Selesai</span>
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
                    <!-- Input Form -->
                    <?= form_open(base_url("gudang/opname/process/{$opname->id}"), ['id' => 'opname_input_form']) ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Kode</th>
                                    <th width="25%">Item</th>
                                    <th width="10%">Satuan</th>
                                    <th width="15%">Stok Sistem</th>
                                    <th width="15%">Stok Fisik</th>
                                    <th width="15%">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($items)): ?>
                                    <?php foreach ($items as $index => $item): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= esc($item->item_kode) ?></td>
                                            <td><?= esc($item->item_name) ?></td>
                                            <td><?= esc($item->satuan_name) ?></td>
                                            <td class="text-center">
                                                <strong><?= number_format($item->jml, 2) ?></strong>
                                                <input type="hidden" name="items[]" value="<?= $item->id_item ?>">
                                            </td>
                                            <td class="text-center">
                                                <input type="number" 
                                                       name="quantities[<?= $index ?>]" 
                                                       value="<?= $item->jml ?>" 
                                                       min="0" 
                                                       step="0.01"
                                                       class="form-control form-control-sm text-center"
                                                       required>
                                            </td>
                                            <td>
                                                <input type="text" 
                                                       name="notes[<?= $index ?>]" 
                                                       class="form-control form-control-sm"
                                                       placeholder="Keterangan (opsional)">
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada item di gudang ini</td>
                                    </tr>
                                <?php endif ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (!empty($items)): ?>
                        <div class="row mt-3">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-secondary rounded-0" onclick="history.back()">
                                    <i class="fas fa-times mr-1"></i> Batal
                                </button>
                                <button type="submit" class="btn btn-success rounded-0">
                                    <i class="fas fa-check mr-1"></i> Proses Opname
                                </button>
                            </div>
                        </div>
                    <?php endif ?>
                    <?= form_close() ?>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Opname ini sudah selesai diproses dan tidak dapat diubah lagi.
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Form validation
    $('#opname_input_form').on('submit', function(e) {
        let isValid = true;
        let errorMessages = [];
        
        // Check if all quantities are valid
        $('input[name^="quantities"]').each(function() {
            const quantity = parseFloat($(this).val()) || 0;
            
            if (quantity < 0) {
                errorMessages.push('Stok fisik tidak boleh negatif');
                isValid = false;
                return false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            toastr.error(errorMessages.join('<br>'), 'Validasi Gagal');
            return false;
        }
        
        // Confirmation dialog
        if (!confirm('Apakah anda yakin ingin memproses opname ini? Stok akan diperbarui sesuai data yang diinput.')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?> 