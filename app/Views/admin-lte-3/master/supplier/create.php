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
                            <label>Kode</label>
                            <?= form_input([
                                'name' => 'kode',
                                'type' => 'text',
                                'class' => 'form-control rounded-0',
                                'value' => $kode,
                                'readonly' => true
                            ]) ?>
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

                        <!-- NPWP -->
                        <div class="form-group">
                            <label>NPWP</label>
                            <?= form_input([
                                'name' => 'npwp',
                                'type' => 'text',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Nomor NPWP...',
                                'value' => old('npwp')
                            ]) ?>
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
                        <!-- Kota -->
                        <div class="form-group">
                            <label>Kota</label>
                            <?= form_input([
                                'name' => 'kota',
                                'type' => 'text',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Kota...',
                                'value' => old('kota')
                            ]) ?>
                        </div>

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
                                    '3' => 'Umum',
                                    '4' => 'Anggota'
                                ],
                                old('tipe'),
                                'class="form-control rounded-0 ' . ($validation->hasError('tipe') ? 'is-invalid' : '') . '"'
                            ) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('tipe') ?>
                            </div>
                        </div>

                        <!-- Status Limit -->
                        <div class="form-group">
                            <label>Status Limit</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="status_limit" name="status_limit" value="1" <?= old('status_limit') == '1' ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="status_limit">Aktifkan Limit Kredit</label>
                            </div>
                            <small class="form-text text-muted">Centang untuk mengaktifkan limit kredit pelanggan</small>
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

<!-- Bootstrap Switch CSS -->
<style>
.custom-switch .custom-control-label::before {
    width: 2rem;
    height: 1.25rem;
    border-radius: 0.625rem;
}

.custom-switch .custom-control-label::after {
    width: calc(1.25rem - 4px);
    height: calc(1.25rem - 4px);
    border-radius: calc(0.625rem - 2px);
}

.custom-switch .custom-control-input:checked ~ .custom-control-label::after {
    transform: translateX(0.75rem);
}

.custom-switch .custom-control-input:checked ~ .custom-control-label::before {
    background-color: #007bff;
    border-color: #007bff;
}
</style>

<!-- Bootstrap Switch JavaScript -->
<script>
$(document).ready(function() {
    // Handle status limit switch change
    $('#status_limit').on('change', function() {
        const isChecked = $(this).is(':checked');
        console.log('Status Limit:', isChecked ? '1' : '0');
        
        // You can add additional logic here if needed
        // For example, show/hide related fields based on the switch state
    });
    
    // Initialize switch state on page load
    const initialValue = '<?= old('status_limit', '0') ?>';
    if (initialValue === '1') {
        $('#status_limit').prop('checked', true);
    } else {
        $('#status_limit').prop('checked', false);
    }
});
</script>

<?= $this->endSection() ?> 