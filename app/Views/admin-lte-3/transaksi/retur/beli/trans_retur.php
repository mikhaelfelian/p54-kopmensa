<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-25
 * Github : github.com/mikhaelfelian
 * description : Purchase Return Transaction Create View
 * This file represents the View.
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-6">
        <?= form_open('transaksi/retur/beli/store', ['id' => 'form-retur']) ?>
            <div class="card rounded-0">
                <div class="card-body">
                    <!-- Purchase Order Selection -->
                    <div class="form-group">
                        <label for="id_beli">No. Pembelian</label>
                        <select name="id_beli" id="id_beli" class="form-control select2 rounded-0">
                            <option value="">Pilih Transaksi Pembelian</option>
                            <?php foreach ($sql_beli as $purchase): ?>
                                <option value="<?= $purchase->id ?>" 
                                    data-supplier="<?= $purchase->id_supplier ?>"
                                    data-no-nota="<?= esc($purchase->no_nota) ?>"
                                    <?= old('id_beli') == $purchase->id ? 'selected' : '' ?>>
                                    <?= esc($purchase->no_nota) . ' - ' . esc($purchase->supplier_nama) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Supplier -->
                            <div class="form-group">
                                <label for="id_supplier">Supplier <span class="text-danger">*</span></label>
                                <select name="id_supplier" id="id_supplier" class="form-control select2 rounded-0" required>
                                    <option value="">Pilih Supplier</option>
                                    <?php foreach ($suppliers as $supplier): ?>
                                        <option value="<?= $supplier->id ?>" <?= old('id_supplier') == $supplier->id ? 'selected' : '' ?>>
                                            <?= esc($supplier->nama) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <!-- Tanggal Retur -->
                            <div class="form-group">
                                <label for="tgl_retur">Tanggal Retur <span class="text-danger">*</span></label>
                                <?= form_input([
                                    'type' => 'date',
                                    'name' => 'tgl_retur',
                                    'id' => 'tgl_retur',
                                    'class' => 'form-control rounded-0',
                                    'value' => old('tgl_retur', date('Y-m-d')),
                                    'required' => true
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- No Nota Asal -->
                            <div class="form-group">
                                <label for="no_nota_asal">No. Nota Asal</label>
                                <?= form_input([
                                    'type' => 'text',
                                    'name' => 'no_nota_asal',
                                    'id' => 'no_nota_asal',
                                    'class' => 'form-control rounded-0',
                                    'readonly' => true
                                ]) ?>
                            </div>

                            <!-- User Terima -->
                            <div class="form-group">
                                <label for="id_user_terima">User Penerima</label>
                                        <select name="id_user_terima" id="id_user_terima" class="form-control select2 rounded-0">
                                            <option value="">Pilih User</option>
                                            <?php if (isset($users) && is_array($users)): ?>
                                                <?php foreach ($users as $user): ?>
                                                    <option value="<?= $user->id ?>" <?= old('id_user_terima', isset($user->id) && isset($users[0]) && $users[0]->id == $user->id ? $user->id : '') == $user->id ? 'selected' : '' ?>>
                                                        <?= esc($user->first_name . ' ' . $user->last_name) ?> (<?= esc($user->username) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                            </div>
                        </div>
                    </div>

                    <!-- No Nota Retur -->
                    <div class="form-group">
                        <label for="no_nota_retur">No. Nota Retur <span class="text-danger">*</span></label>
                        <?= form_input([
                            'type' => 'text',
                            'name' => 'no_nota_retur',
                            'id' => 'no_nota_retur',
                            'class' => 'form-control rounded-0',
                            'value' => old('no_nota_retur'),
                            'required' => true
                        ]) ?>
                    </div>

                    <!-- Alasan Retur -->
                    <div class="form-group">
                        <label for="alasan_retur">Alasan Retur</label>
                        <?= form_textarea([
                            'name' => 'alasan_retur',
                            'id' => 'alasan_retur',
                            'class' => 'form-control rounded-0',
                            'rows' => 3,
                            'placeholder' => 'Jelaskan alasan retur...',
                            'value' => old('alasan_retur')
                        ]) ?>
                    </div>

                    <!-- Status PPN -->
                    <div class="form-group">
                        <label>Status PPN</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status_ppn" id="ppn_non" value="0"
                                    <?= old('status_ppn', '0') == '0' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ppn_non">Non PPN</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status_ppn" id="ppn_tambah" value="1"
                                    <?= old('status_ppn') == '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ppn_tambah">Dengan PPN</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status_ppn" id="ppn_tangguh" value="2"
                                    <?= old('status_ppn') == '2' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ppn_tangguh">PPN Ditangguhkan</label>
                            </div>
                        </div>
                    </div>

                    <!-- Status Retur -->
                    <div class="form-group">
                        <label>Status Retur</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status_retur" id="status_draft" value="0"
                                    <?= old('status_retur', '0') == '0' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="status_draft">Draft</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status_retur" id="status_selesai" value="1"
                                    <?= old('status_retur') == '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="status_selesai">Selesai</label>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div class="form-group">
                        <label for="catatan">Catatan</label>
                        <?= form_textarea([
                            'name' => 'catatan',
                            'id' => 'catatan',
                            'class' => 'form-control rounded-0',
                            'rows' => 2,
                            'placeholder' => 'Catatan tambahan...',
                            'value' => old('catatan')
                        ]) ?>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <?= anchor('transaksi/retur/beli', '<i class="fas fa-arrow-left mr-1"></i> Kembali', [
                        'class' => 'btn btn-default float-left rounded-0'
                    ]) ?>
                    <?= form_submit('submit', 'Simpan', [
                        'class' => 'btn btn-primary rounded-0'
                    ]) ?>
                </div>
            </div>
        <?= form_close() ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Auto-generate nota retur number
    generateNotaRetur();

    // Handle purchase selection
    $('#id_beli').on('change', function() {
        updatePurchaseFields();
    });

    // Function to update fields based on purchase selection
    function updatePurchaseFields() {
        const selectedOption = $('#id_beli').find('option:selected');
        const supplierId = selectedOption.data('supplier');
        const noNotaAsal = selectedOption.data('no-nota');
        
        // Set supplier dropdown value
        $('#id_supplier').val(supplierId).trigger('change');
        
        // Set No Nota Asal field value
        $('#no_nota_asal').val(noNotaAsal);
    }

    // Generate nota retur number
    function generateNotaRetur() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        
        const notaNumber = `RET-${year}${month}${day}-${random}`;
        $('#no_nota_retur').val(notaNumber);
    }
});
</script>
<?= $this->endSection() ?> 