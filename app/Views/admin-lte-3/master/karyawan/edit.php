<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * Karyawan Edit View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <?= form_open('master/karyawan/update/' . $karyawan->id) ?>
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Edit Karyawan</h3>
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
                                'value' => $karyawan->kode,
                                'readonly' => true
                            ]) ?>
                        </div>
                        <!-- NIK -->
                        <div class="form-group">
                            <label>NIK <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <?= form_input([
                                    'name' => 'nik',
                                    'id' => 'nik',
                                    'type' => 'text',
                                    'class' => 'form-control rounded-0 ' . ($validation->hasError('nik') ? 'is-invalid' : ''),
                                    'placeholder' => 'Nomor Identitas...',
                                    'value' => old('nik', $karyawan->nik)
                                ]) ?>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('nik') ?>
                                </div>
                            </div>
                        </div>
                        <!-- Nama Lengkap -->
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Gelar</label>
                                    <?= form_input([
                                        'name' => 'nama_dpn',
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0',
                                        'placeholder' => 'dr.',
                                        'value' => old('nama_dpn', $karyawan->nama_dpn)
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label>Nama Lengkap <span class="text-danger">*</span></label>
                                    <?= form_input([
                                        'name' => 'nama',
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0 ' . ($validation->hasError('nama') ? 'is-invalid' : ''),
                                        'placeholder' => 'John Doe...',
                                        'value' => old('nama', $karyawan->nama)
                                    ]) ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('nama') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Gelar</label>
                                    <?= form_input([
                                        'name' => 'nama_blk',
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0',
                                        'placeholder' => 'Sp.PD',
                                        'value' => old('nama_blk', $karyawan->nama_blk)
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                        <!-- Tempat & Tanggal Lahir -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tmp Lahir <span class="text-danger">*</span></label>
                                    <?= form_input([
                                        'name' => 'tmp_lahir',
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0 ' . ($validation->hasError('tmp_lahir') ? 'is-invalid' : ''),
                                        'placeholder' => 'Semarang...',
                                        'value' => old('tmp_lahir', $karyawan->tmp_lahir)
                                    ]) ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('tmp_lahir') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Tgl Lahir <span class="text-danger">*</span></label>
                                    <?= form_input([
                                        'name' => 'tgl_lahir',
                                        'type' => 'date',
                                        'class' => 'form-control rounded-0 ' . ($validation->hasError('tgl_lahir') ? 'is-invalid' : ''),
                                        'value' => old('tgl_lahir', $karyawan->tgl_lahir)
                                    ]) ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('tgl_lahir') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <!-- Jenis Kelamin -->
                                <div class="form-group">
                                    <label>L/P <span class="text-danger">*</span></label>
                                    <?= form_dropdown(
                                        'jns_klm',
                                        [
                                            '' => '- Pilih -',
                                            'L' => 'Laki-laki',
                                            'P' => 'Perempuan'
                                        ],
                                        old('jns_klm', $karyawan->jns_klm),
                                        'class="form-control rounded-0 ' . ($validation->hasError('jns_klm') ? 'is-invalid' : '') . '"'
                                    ) ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('jns_klm') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Alamat KTP -->
                        <div class="form-group">
                            <label>Alamat KTP</label>
                            <?= form_textarea([
                                'name' => 'alamat',
                                'class' => 'form-control rounded-0',
                                'rows' => 5,
                                'placeholder' => 'Mohon diisi alamat lengkap sesuai ktp...',
                                'value' => old('alamat', $karyawan->alamat)
                            ]) ?>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= form_label('RT', 'rt') ?>
                                    <?= form_input([
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0',
                                        'id' => 'rt',
                                        'name' => 'rt',
                                        'maxlength' => 3,
                                        'placeholder' => 'RT',
                                        'value' => old('rt', $karyawan->rt)
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= form_label('RW', 'rw') ?>
                                    <?= form_input([
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0',
                                        'id' => 'rw',
                                        'name' => 'rw',
                                        'maxlength' => 3,
                                        'placeholder' => 'RW',
                                        'value' => old('rw', $karyawan->rw)
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= form_label('Kelurahan', 'kelurahan') ?>
                                    <?= form_input([
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0',
                                        'id' => 'kelurahan',
                                        'name' => 'kelurahan',
                                        'placeholder' => 'Masukkan kelurahan',
                                        'value' => old('kelurahan', $karyawan->kelurahan)
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= form_label('Kecamatan', 'kecamatan') ?>
                                    <?= form_input([
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0',
                                        'id' => 'kecamatan',
                                        'name' => 'kecamatan',
                                        'placeholder' => 'Masukkan kecamatan',
                                        'value' => old('kecamatan', $karyawan->kecamatan)
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= form_label('Kota', 'kota') ?>
                                    <?= form_input([
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0',
                                        'id' => 'kota',
                                        'name' => 'kota',
                                        'placeholder' => 'Masukkan kota',
                                        'value' => old('kota', $karyawan->kota)
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- SIP -->
                        <div class="form-group">
                            <label>SIP</label>
                            <?= form_input([
                                'name' => 'sip',
                                'type' => 'text',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Nomor SIP...',
                                'value' => old('sip', $karyawan->sip)
                            ]) ?>
                        </div>
                        <!-- STR -->
                        <div class="form-group">
                            <label>STR</label>
                            <?= form_input([
                                'name' => 'str',
                                'type' => 'text',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Nomor STR...',
                                'value' => old('str', $karyawan->str)
                            ]) ?>
                        </div>
                        <!-- Jabatan -->
                        <div class="form-group">
                            <label>Jabatan <span class="text-danger">*</span></label>
                            <select name="id_user_group" class="form-control rounded-0">
                                <option value="">- Pilih -</option>
                                <?php foreach ($jabatans as $jabatan): ?>
                                    <option value="<?= $jabatan->id ?>" <?= $karyawan->id_user_group == $jabatan->id ? 'selected' : '' ?>>
                                        <?= $jabatan->description ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                <?= $validation->getError('jabatan') ?>
                            </div>
                        </div>
                        <!-- No HP -->
                        <div class="form-group">
                            <label>No. HP <span class="text-danger">*</span></label>
                            <?= form_input([
                                'name' => 'no_hp',
                                'type' => 'text',
                                'class' => 'form-control rounded-0 ' . ($validation->hasError('no_hp') ? 'is-invalid' : ''),
                                'placeholder' => 'Nomor kontak WA karyawan / keluarga terdekat...',
                                'value' => old('no_hp', $karyawan->no_hp)
                            ]) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('no_hp') ?>
                            </div>
                        </div>
                        <!-- Alamat Domisili -->
                        <div class="form-group">
                            <label>Alamat Domisili</label>
                            <?= form_textarea([
                                'name' => 'alamat_domisili',
                                'class' => 'form-control rounded-0',
                                'rows' => 5,
                                'placeholder' => 'Mohon diisi alamat lengkap sesuai domisili...',
                                'value' => old('alamat_domisili', $karyawan->alamat_domisili)
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/karyawan') ?>" class="btn btn-default rounded-0">
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