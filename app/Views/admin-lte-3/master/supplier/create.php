<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * Supplier Create View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <?= form_open('master/supplier/store') ?>
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Data Supplier</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Kode -->
                        <div class="form-group">
                            <label>Kode <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <?= form_input([
                                    'name' => 'kode',
                                    'type' => 'text',
                                    'class' => 'form-control rounded-0',
                                    'value' => $kode,
                                    'required' => true,
                                    'id' => 'supplier_kode',
                                    'placeholder' => 'SUP0001'
                                ]) ?>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="generate_kode" title="Generate new code">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Leave empty for auto-generation or enter custom code</small>
                        </div>

                        <!-- Nama -->
                        <div class="form-group">
                            <label>Nama <span class="text-danger">*</span></label>
                            <?= form_input([
                                'name' => 'nama',
                                'type' => 'text',
                                'class' => 'form-control rounded-0 ' . ($validation->hasError('nama') ? 'is-invalid' : ''),
                                'placeholder' => 'Nama supplier...',
                                'value' => old('nama')
                            ]) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('nama') ?>
                            </div>
                        </div>


                        <!-- Alamat -->
                        <div class="form-group">
                            <label>Alamat <span class="text-danger">*</span></label>
                            <?= form_textarea([
                                'name' => 'alamat',
                                'class' => 'form-control rounded-0 ' . ($validation->hasError('alamat') ? 'is-invalid' : ''),
                                'rows' => 3,
                                'placeholder' => 'Alamat lengkap...',
                                'value' => old('alamat')
                            ]) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('alamat') ?>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">


                        <!-- No HP -->
                        <div class="form-group">
                            <label>No. HP <span class="text-danger">*</span></label>
                            <?= form_input([
                                'name' => 'no_hp',
                                'type' => 'text',
                                'class' => 'form-control rounded-0 ' . ($validation->hasError('no_hp') ? 'is-invalid' : ''),
                                'placeholder' => 'Nomor HP...',
                                'value' => old('no_hp')
                            ]) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('no_hp') ?>
                            </div>
                        </div>

                        <!-- Tipe -->
                        <div class="form-group">
                            <label>Tipe <span class="text-danger">*</span></label>
                            <?= form_dropdown(
                                'tipe',
                                [
                                    '' => '- Pilih -',
                                    '1' => 'Pabrikan',
                                    '2' => 'Personal'
                                ],
                                old('tipe'),
                                'class="form-control rounded-0 ' . ($validation->hasError('tipe') ? 'is-invalid' : '') . '"'
                            ) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('tipe') ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/supplier') ?>" class="btn btn-default rounded-0">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary rounded-0 float-right">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>


<script>
// Supplier code generation
document.addEventListener('DOMContentLoaded', function() {
    const kodeInput = document.getElementById('supplier_kode');
    const generateBtn = document.getElementById('generate_kode');
    
    // Generate supplier code function
    function generateSupplierCode() {
        // Get the last supplier code from the database or start from 0001
        // For now, we'll generate a sequential number based on timestamp
        const timestamp = Date.now().toString().slice(-4); // Last 4 digits of timestamp
        const paddedNumber = timestamp.padStart(4, '0'); // Ensure 4 digits with leading zeros
        return 'SUP' + paddedNumber;
    }
    
    // Auto-generate code on page load if field is empty
    if (!kodeInput.value.trim()) {
        kodeInput.value = generateSupplierCode();
    }
    
    // Generate new code when button is clicked
    generateBtn.addEventListener('click', function() {
        kodeInput.value = generateSupplierCode();
        kodeInput.focus();
    });
    
    // Auto-generate when field is cleared
    kodeInput.addEventListener('input', function() {
        if (!this.value.trim()) {
            this.value = generateSupplierCode();
        }
    });
    
    // Add visual feedback for generate button
    generateBtn.addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-sync-alt"></i>';
        }, 500);
    });
});
</script>

<?= $this->endSection() ?> 