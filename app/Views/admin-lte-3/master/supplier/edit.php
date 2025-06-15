<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * Supplier Edit View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <?= form_open('master/supplier/update/' . $supplier->id) ?>
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Edit Supplier</h3>
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
                                'value' => $supplier->kode,
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
                                'value' => old('nama', $supplier->nama)
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
                                'value' => old('npwp', $supplier->npwp)
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
                                'value' => old('alamat', $supplier->alamat)
                            ]) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('alamat') ?>
                            </div>
                        </div>

                        <!-- RT/RW -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>RT</label>
                                    <?= form_input([
                                        'name' => 'rt',
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0',
                                        'placeholder' => 'RT',
                                        'value' => old('rt', $supplier->rt)
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>RW</label>
                                    <?= form_input([
                                        'name' => 'rw',
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0',
                                        'placeholder' => 'RW',
                                        'value' => old('rw', $supplier->rw)
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Kelurahan -->
                        <div class="form-group">
                            <label>Kelurahan</label>
                            <?= form_input([
                                'name' => 'kelurahan',
                                'type' => 'text',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Kelurahan...',
                                'value' => old('kelurahan', $supplier->kelurahan)
                            ]) ?>
                        </div>

                        <!-- Kecamatan -->
                        <div class="form-group">
                            <label>Kecamatan</label>
                            <?= form_input([
                                'name' => 'kecamatan',
                                'type' => 'text',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Kecamatan...',
                                'value' => old('kecamatan', $supplier->kecamatan)
                            ]) ?>
                        </div>

                        <!-- Kota -->
                        <div class="form-group">
                            <label>Kota</label>
                            <?= form_input([
                                'name' => 'kota',
                                'type' => 'text',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Kota...',
                                'value' => old('kota', $supplier->kota)
                            ]) ?>
                        </div>

                        <!-- No Telepon -->
                        <div class="form-group">
                            <label>No. Telepon</label>
                            <?= form_input([
                                'name' => 'no_tlp',
                                'type' => 'text',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Nomor telepon...',
                                'value' => old('no_tlp', $supplier->no_tlp)
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
                                'value' => old('no_hp', $supplier->no_hp)
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
                                    '1' => 'Instansi',
                                    '2' => 'Personal'
                                ],
                                old('tipe', $supplier->tipe),
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
                    <i class="fas fa-save"></i> Update
                </button>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>
<?= $this->endSection() ?> 