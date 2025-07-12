<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-04
 * 
 * Purchase Transaction Create View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-6">
        <?= form_open('transaksi/beli/store', ['id' => 'form-transaksi']) ?>
            <div class="card rounded-0">
                <div class="card-body">
                    <!-- PO Selection -->
                    <div class="form-group">
                        <label for="id_po">Kode PO</label>
                        <select name="id_po" id="id_po" class="form-control select2 rounded-0">
                            <option value="">Pilih PO</option>
                            <?php foreach ($po_list as $po): ?>
                                <option value="<?= $po->id ?>" 
                                    data-supplier="<?= $po->id_supplier ?>"
                                    data-no-po="<?= $po->no_nota ?>"
                                    <?= (old('id_po') == $po->id || ($selected_po && $selected_po->id == $po->id)) ? 'selected' : '' ?>>
                                    <?= esc($po->no_nota). ' - '.esc($po->supplier) ?>
                                </option>
                            <?php endforeach ?>
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

                            <!-- Tanggal Faktur -->
                            <div class="form-group">
                                <label for="tgl_faktur">Tanggal Faktur <span class="text-danger">*</span></label>
                                <?= form_input([
                                    'type' => 'date',
                                    'name' => 'tgl_masuk',
                                    'id' => 'tgl_masuk',
                                    'class' => 'form-control rounded-0',
                                    'value' => old('tgl_masuk', date('Y-m-d')),
                                    'required' => true
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- No PO -->
                            <div class="form-group">
                                <label for="no_po">No. PO</label>
                                <?= form_input([
                                    'type' => 'text',
                                    'name' => 'no_po',
                                    'id' => 'no_po',
                                    'class' => 'form-control rounded-0',
                                    'readonly' => true
                                ]) ?>
                            </div>

                            <!-- Tanggal Tempo -->
                            <div class="form-group">
                                <label for="tgl_tempo">Tanggal Tempo</label>
                                <?= form_input([
                                    'type' => 'date',
                                    'name' => 'tgl_keluar',
                                    'id' => 'tgl_keluar',
                                    'class' => 'form-control rounded-0',
                                    'value' => old('tgl_keluar')
                                ]) ?>
                            </div>
                        </div>
                    </div>

                    <!-- No Faktur -->
                    <div class="form-group">
                        <label for="no_nota">No. Faktur <span class="text-danger">*</span></label>
                        <?= form_input([
                            'type' => 'text',
                            'name' => 'no_nota',
                            'id' => 'no_nota',
                            'class' => 'form-control rounded-0',
                            'value' => old('no_nota'),
                            'required' => true
                        ]) ?>
                    </div>

                    <!-- Status PPN -->
                    <div class="form-group">
                        <label>Status PPN <span class="text-danger">*</span></label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status_ppn" id="ppn_non" value="0"
                                    <?= old('status_ppn', '0') == '0' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ppn_non">Non PPN</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status_ppn" id="ppn_tambah" value="1"
                                    <?= old('status_ppn') == '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ppn_tambah">Tambah PPN</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status_ppn" id="ppn_include" value="2"
                                    <?= old('status_ppn') == '2' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ppn_include">Include PPN</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <?= anchor('transaksi/beli', '<i class="fas fa-arrow-left mr-1"></i> Kembali', [
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

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Handle PO selection
    $('#id_po').on('change', function() {
        updatePOFields();
    });

    // Function to update fields based on PO selection
    function updatePOFields() {
        const selectedOption = $('#id_po').find('option:selected');
        const supplierId = selectedOption.data('supplier');
        const noPo = selectedOption.data('no-po');
        
        // Set supplier dropdown value
        $('#id_supplier').val(supplierId).trigger('change');
        
        // Set No PO field value
        $('#no_po').val(noPo);
    }

    // Auto trigger change if PO is selected
    <?php if ($selected_po): ?>
    updatePOFields();
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>