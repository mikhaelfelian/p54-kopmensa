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
                                'value' => $karyawan->kode
                            ]) ?>
                        </div>
                        <!-- NIP -->
                        <div class="form-group">
                            <label>NIP <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <?= form_input([
                                    'name' => 'nik',
                                    'id' => 'nik',
                                    'type' => 'text',
                                    'class' => 'form-control rounded-0 ' . ($validation->hasError('nik') ? 'is-invalid' : ''),
                                    'placeholder' => 'Nomor Induk Pegawai...',
                                    'value' => old('nik', $karyawan->nik)
                                ]) ?>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('nik') ?>
                                </div>
                            </div>
                        </div>
                        <!-- Nama Lengkap -->
                        <div class="form-group">
                            <label>Nama Lengkap <span class="text-danger">*</span></label>
                            <?= form_input([
                                'name' => 'nama',
                                'type' => 'text',
                                'class' => 'form-control rounded-0 ' . ($validation->hasError('nama') ? 'is-invalid' : ''),
                                'placeholder' => 'Nama lengkap karyawan...',
                                'value' => old('nama', $karyawan->nama)
                            ]) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('nama') ?>
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
                        <!-- Jabatan -->
                        <div class="form-group">
                            <label>Jabatan <span class="text-danger">*</span></label>
                            <?= form_input([
                                'name' => 'jabatan',
                                'type' => 'text',
                                'class' => 'form-control rounded-0 ' . ($validation->hasError('jabatan') ? 'is-invalid' : ''),
                                'placeholder' => 'Jabatan karyawan...',
                                'value' => old('jabatan', $karyawan->jabatan)
                            ]) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('jabatan') ?>
                            </div>
                        </div>
                        <!-- User Group -->
                        <div class="form-group">
                            <label>User Group</label>
                            <select name="id_user_group" class="form-control rounded-0">
                                <option value="">- Pilih -</option>
                                <?php foreach ($jabatans as $jabatan): ?>
                                    <?php if ($jabatan->id != 1 && $jabatan->id != 7): ?>
                                        <option value="<?= $jabatan->id ?>" <?= old('id_user_group', $karyawan->id_user_group) == $jabatan->id ? 'selected' : '' ?>>
                                            <?= $jabatan->description ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Username -->
                        <div class="form-group">
                            <label>Username</label>
                            <div class="input-group">
                                <?= form_input([
                                    'name' => 'username',
                                    'type' => 'text',
                                    'class' => 'form-control rounded-0',
                                    'placeholder' => 'Username untuk login...',
                                    'value' => old('username', $ionAuthUser->username ?? '')
                                ]) ?>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="generateUsernameBtn">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- <small class="form-text text-muted">Username untuk login ke sistem</small> -->
                        </div>
                        <!-- Password Fields -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password</label>
                                    <?= form_input([
                                        'name' => 'password',
                                        'type' => 'password',
                                        'class' => 'form-control rounded-0',
                                        'placeholder' => 'Password baru...',
                                        'value' => old('password')
                                    ]) ?>
                                    <!-- <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password</small> -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Konfirmasi Password</label>
                                    <?= form_input([
                                        'name' => 'password_confirm',
                                        'type' => 'password',
                                        'class' => 'form-control rounded-0',
                                        'placeholder' => 'Konfirmasi password...',
                                        'value' => old('password_confirm')
                                    ]) ?>
                                    <!-- <small class="form-text text-muted">Ulangi password yang sama</small> -->
                                </div>
                            </div>
                        </div>
                        <!-- No HP -->
                        <div class="form-group">
                            <label>No. HP <span class="text-danger">*</span></label>
                            <?= form_input([
                                'name' => 'no_hp',
                                'type' => 'text',
                                'class' => 'form-control rounded-0 ' . ($validation->hasError('no_hp') ? 'is-invalid' : ''),
                                'placeholder' => 'Nomor kontak karyawan...',
                                'value' => old('no_hp', $karyawan->no_hp)
                            ]) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('no_hp') ?>
                            </div>
                        </div>
                        <!-- Email -->
                        <div class="form-group">
                            <label>Email</label>
                            <?= form_input([
                                'name' => 'email',
                                'type' => 'email',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Alamat email karyawan...',
                                'value' => old('email', $karyawan->email ?? '')
                            ]) ?>
                        </div>
                        <!-- Alamat Domisili -->
                        <div class="form-group">
                            <label>Alamat Domisili</label>
                            <?= form_textarea([
                                'name' => 'alamat_domisili',
                                'class' => 'form-control rounded-0',
                                'rows' => 5,
                                'placeholder' => 'Alamat tempat tinggal saat ini...',
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

<script>
$(document).ready(function() {
    // Username generation functionality
    $('#generateUsernameBtn').click(function() {
        const nama = $('input[name="nama"]').val();
        
        if (nama.trim()) {
            // Generate username from nama
            const cleanName = nama.toLowerCase()
                .replace(/[^a-zA-Z0-9]/g, '')
                .substring(0, 10);
            
            // Add random number to make it unique
            const randomNum = Math.floor(Math.random() * 900) + 100;
            const generatedUsername = cleanName + randomNum;
            
            $('input[name="username"]').val(generatedUsername);
        } else {
            alert('Silakan isi nama lengkap terlebih dahulu');
        }
    });

    // Password confirmation validation
    $('input[name="password_confirm"]').on('input', function() {
        const password = $('input[name="password"]').val();
        const confirmPassword = $(this).val();
        
        if (confirmPassword && password !== confirmPassword) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Password tidak sama</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });

    // Form submission validation
    $('form').on('submit', function(e) {
        const password = $('input[name="password"]').val();
        const confirmPassword = $('input[name="password_confirm"]').val();
        
        if (password && password !== confirmPassword) {
            e.preventDefault();
            alert('Password dan konfirmasi password tidak sama');
            return false;
        }
    });
});
</script>

<?= $this->endSection() ?>