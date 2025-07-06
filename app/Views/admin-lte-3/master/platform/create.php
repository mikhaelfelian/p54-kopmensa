<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * Platform Create View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <?= form_open('master/platform/store') ?>
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Data Platform</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Outlet -->
                        <div class="form-group">
                            <label>Outlet <span class="text-danger">*</span></label>
                            <select name="id_outlet"
                                class="form-control rounded-0 <?= ($validation->hasError('id_outlet') ? 'is-invalid' : '') ?>">
                                <option value="">Pilih Outlet...</option>
                                <?php if (isset($outlets) && is_array($outlets)): ?>
                                    <?php foreach ($outlets as $outlet): ?>
                                        <option value="<?= $outlet->id ?>" <?= old('id_outlet', $platform->id_outlet ?? '') == $outlet->id ? 'selected' : '' ?>>
                                            <?= (!empty($outlet->kode) ? '[' . $outlet->kode . '] ' : '') . esc($outlet->nama) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback">
                                <?= $validation->getError('id_outlet') ?>
                            </div>
                        </div>
                        
                        <!-- Kode -->
                        <div class="form-group">
                            <label>Kode <span class="text-danger">*</span></label>
                            <?= form_input([
                                'name' => 'kode',
                                'type' => 'text',
                                'class' => 'form-control rounded-0 ' . ($validation->hasError('kode') ? 'is-invalid' : ''),
                                'placeholder' => 'Kode platform...',
                                'value' => old('kode', $kode)
                            ]) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('kode') ?>
                            </div>
                        </div>

                        <!-- Platform -->
                        <div class="form-group">
                            <label>Platform <span class="text-danger">*</span></label>
                            <?= form_input([
                                'name' => 'platform',
                                'type' => 'text',
                                'class' => 'form-control rounded-0 ' . ($validation->hasError('platform') ? 'is-invalid' : ''),
                                'placeholder' => 'Nama platform...',
                                'value' => old('platform')
                            ]) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('platform') ?>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="form-group">
                            <label>Keterangan</label>
                            <?= form_textarea([
                                'name' => 'keterangan',
                                'class' => 'form-control rounded-0 ' . ($validation->hasError('keterangan') ? 'is-invalid' : ''),
                                'placeholder' => 'Keterangan platform...',
                                'value' => old('keterangan'),
                                'rows' => 3
                            ]) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('keterangan') ?>
                            </div>
                        </div>

                        <!-- Persentase -->
                        <div class="form-group">
                            <label>Persentase</label>
                            <div class="input-group">
                                <?= form_input([
                                    'name' => 'persen',
                                    'type' => 'number',
                                    'class' => 'form-control rounded-0 ' . ($validation->hasError('persen') ? 'is-invalid' : ''),
                                    'placeholder' => 'Persentase platform...',
                                    'value' => old('persen'),
                                    'step' => '0.1',
                                    'min' => '0',
                                    'max' => '100'
                                ]) ?>
                                <div class="input-group-append">
                                    <span class="input-group-text rounded-0">%</span>
                                </div>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('persen') ?>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <?= form_dropdown(
                                'status',
                                [
                                    '1' => 'Aktif',
                                    '0' => 'Tidak Aktif'
                                ],
                                old('status', '1'),
                                'class="form-control rounded-0 ' . ($validation->hasError('status') ? 'is-invalid' : '') . '"'
                            ) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('status') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/platform') ?>" class="btn btn-default rounded-0">
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
<?= $this->endSection() ?>