<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-20
 * Github : github.com/mikhaelfelian
 * description : View for creating customer data
 * This file represents the View for creating customers.
 * ----------------------------------------------------------------
 * NOTE:
 * - Tipe pelanggan/anggota = 2 (anggota/pelanggan)
 * - Status pelanggan/anggota = 1 (aktif)
 * - Status pelanggan/anggota = 0 (non aktif)
 * - Limit pelanggan/anggota = 0 (tidak ada limit)
 * - Limit pelanggan/anggota = 1 (ada limit)
 * - Limit pelanggan/anggota = 2 (ada limit)
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-6"> 
        <?= form_open('master/customer/store') ?>
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Form Data Pelanggan</h3>
                <div class="card-tools"></div>
            </div>
            <div class="card-body">
                <?php
                $psnGagal = session()->getFlashdata('psn_gagal');
                $initialKode = old('kode', $kode ?? '');
                if (!preg_match('/^\d{5}$/', (string) $initialKode)) {
                    $initialKode = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
                }
                ?>

                <div class="form-group <?= (!empty($psnGagal['kode']) ? 'has-error' : '') ?>">
                    <label class="control-label">Kode <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <?= form_input([
                            'id' => 'kode',
                            'name' => 'kode',
                            'class' => 'form-control rounded-0' . (!empty($psnGagal['kode']) ? ' is-invalid' : ''),
                            'placeholder' => '12345',
                            'value' => $initialKode,
                            'required' => true,
                            'maxlength' => 5,
                            'pattern' => '\d{5}',
                            'inputmode' => 'numeric',
                            'title' => 'Kode harus 5 digit angka'
                        ]) ?>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="generate_kode" title="Generate new code">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted">Kode hanya terdiri dari 5 digit angka</small>
                </div>

                <div class="form-group <?= (!empty($psnGagal['no_agt']) ? 'has-error' : '') ?>">
                    <label class="control-label">Nomor Anggota</label>
                    <?= form_input([
                        'id' => 'no_agt',
                        'name' => 'no_agt',
                        'class' => 'form-control rounded-0' . (!empty($psnGagal['no_agt']) ? ' is-invalid' : ''),
                        'placeholder' => 'Isikan nomor anggota (opsional) ...',
                        'value' => old('no_agt')
                    ]) ?>
                    <small class="form-text text-muted">Nomor anggota koperasi</small>
                </div>

                <div class="form-group <?= (!empty($psnGagal['nama']) ? 'has-error' : '') ?>">
                    <label class="control-label">Nama*</label>
                    <?= form_input([
                        'id' => 'nama',
                        'name' => 'nama',
                        'class' => 'form-control rounded-0' . (!empty($psnGagal['nama']) ? ' is-invalid' : ''),
                        'placeholder' => 'Isikan nama pelanggan ...',
                        'value' => old('nama')
                    ]) ?>
                </div>

                <div class="form-group <?= (!empty($psnGagal['no_telp']) ? 'has-error' : '') ?>">
                    <label class="control-label">No. Telp</label>
                    <?= form_input([
                        'id' => 'no_telp',
                        'name' => 'no_telp',
                        'class' => 'form-control rounded-0' . (!empty($psnGagal['no_telp']) ? ' is-invalid' : ''),
                        'placeholder' => 'Isikan nomor telepon pelanggan ...',
                        'value' => old('no_telp')
                    ]) ?>
                </div>

                <div class="form-group <?= (!empty($psnGagal['alamat']) ? 'has-error' : '') ?>">
                    <label class="control-label">Alamat*</label>
                    <?= form_textarea([
                        'id' => 'alamat',
                        'name' => 'alamat',
                        'class' => 'form-control rounded-0' . (!empty($psnGagal['alamat']) ? ' is-invalid' : ''),
                        'placeholder' => 'Isikan alamat pelanggan ...',
                        'value' => old('alamat')
                    ]) ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group <?= (!empty($psnGagal['kota']) ? 'has-error' : '') ?>">
                            <label class="control-label">Kota*</label>
                            <?= form_input([
                                'id' => 'kota',
                                'name' => 'kota',
                                'class' => 'form-control rounded-0' . (!empty($psnGagal['kota']) ? ' is-invalid' : ''),
                                'placeholder' => 'Isikan kota ...',
                                'value' => old('kota')
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group <?= (!empty($psnGagal['provinsi']) ? 'has-error' : '') ?>">
                            <label class="control-label">Provinsi*</label>
                            <?= form_input([
                                'id' => 'provinsi',
                                'name' => 'provinsi',
                                'class' => 'form-control rounded-0' . (!empty($psnGagal['provinsi']) ? ' is-invalid' : ''),
                                'placeholder' => 'Isikan provinsi ...',
                                'value' => old('provinsi')
                            ]) ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group <?= (!empty($psnGagal['tipe']) ? 'has-error' : '') ?>">
                            <label class="control-label">Tipe*</label>
                            <select name="tipe" class="form-control rounded-0<?= (!empty($psnGagal['tipe']) ? ' is-invalid' : '') ?>">
                                <option value="">- [Pilih] -</option>
                                <option value="1"<?= old('tipe') == '1' ? 'selected' : '' ?>>Anggota</option>
                                <option value="2"<?= old('tipe') == '2' ? 'selected' : '' ?>>Umum</option>
                            </select>
                        </div>
                    </div>                                
                    <div class="col-md-6">
                        <div class="form-group <?= (!empty($psnGagal['status']) ? 'has-error' : '') ?>">
                            <label class="control-label">Status*</label>                                
                            <div class="custom-control custom-radio">
                                <?= form_radio([
                                    'id' => 'statusAktif',
                                    'name' => 'status',
                                    'class' => 'custom-control-input',
                                    'checked' => old('status') == '1' || empty(old('status')),
                                    'value' => '1'
                                ]) ?>
                                <label for="statusAktif" class="custom-control-label">Aktif</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <?= form_radio([
                                    'id' => 'statusNonAktif',
                                    'name' => 'status',
                                    'class' => 'custom-control-input custom-control-input-danger',
                                    'checked' => old('status') == '0',
                                    'value' => '0'
                                ]) ?>
                                <label for="statusNonAktif" class="custom-control-label">Non - Aktif</label>
                            </div>
                        </div>
                    </div>                                
                </div>
                
                <div class="form-group <?= (!empty($psnGagal['limit']) ? 'has-error' : '') ?>">
                    <label class="control-label">Limit Saldo</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <?= form_input([
                            'id' => 'limit',
                            'name' => 'limit',
                            'class' => 'form-control rounded-0' . (!empty($psnGagal['limit']) ? ' is-invalid' : ''),
                            'placeholder' => '0',
                            'value' => old('limit', 0),
                            'data-inputmask' => "'alias': 'numeric', 'groupSeparator': '.', 'radixPoint': ',', 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0'"
                        ]) ?>
                    </div>
                    <small class="form-text text-muted">Batas maksimal saldo yang dapat digunakan pelanggan (dalam Rupiah)</small>
                </div>

                <hr>
                <h5 class="text-primary"><i class="fas fa-user-lock"></i> Data Login User</h5>
                <small class="text-muted">Data ini akan digunakan untuk login ke sistem</small>

                <div class="form-group <?= (!empty($psnGagal['email']) ? 'has-error' : '') ?>">
                    <label class="control-label">Email <span class="text-danger">*</span></label>
                    <?= form_input([
                        'id' => 'email',
                        'name' => 'email',
                        'type' => 'email',
                        'class' => 'form-control rounded-0' . (!empty($psnGagal['email']) ? ' is-invalid' : ''),
                        'placeholder' => 'contoh@email.com',
                        'value' => old('email')
                    ]) ?>
                    <small class="form-text text-muted">Email akan digunakan untuk login</small>
                </div>

                <div class="form-group <?= (!empty($psnGagal['username']) ? 'has-error' : '') ?>">
                    <label class="control-label">Username <span class="text-danger">*</span></label>
                    <?= form_input([
                        'id' => 'username',
                        'name' => 'username',
                        'class' => 'form-control rounded-0' . (!empty($psnGagal['username']) ? ' is-invalid' : ''),
                        'placeholder' => 'Akan di-generate otomatis',
                        'value' => old('username'),
                        'readonly' => true
                    ]) ?>
                    <small class="form-text text-muted">Username akan di-generate otomatis berdasarkan nama</small>
                </div>

                <div class="form-group <?= (!empty($psnGagal['password']) ? 'has-error' : '') ?>">
                    <label class="control-label">Password <span class="text-danger">*</span></label>
                    <?= form_password([
                        'id' => 'password',
                        'name' => 'password',
                        'class' => 'form-control rounded-0' . (!empty($psnGagal['password']) ? ' is-invalid' : ''),
                        'placeholder' => 'Minimal 6 karakter'
                    ]) ?>
                    <small class="form-text text-muted">Password minimal 6 karakter</small>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-lg-6">
                        <button type="button" onclick="window.location.href = '<?= base_url('master/customer') ?>'" class="btn btn-primary btn-flat">&laquo; Kembali</button>
                    </div>
                    <div class="col-lg-6 text-right">
                        <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-save"></i> Simpan</button>
                    </div>
                </div>                            
            </div>
        </div>
        <?= form_close() ?>
    </div>
    
    <!-- Contact Person Section - Will be shown when tipe > 1 (Instansi/Swasta) -->
    <div class="col-md-6" id="contactPersonSection" style="display: none;">
        <?= form_open('master/customer/store_contact') ?>
        <?= form_hidden('id_pelanggan', '') ?>

        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Form Data Kontak</h3>
                <div class="card-tools"></div>
            </div>
            <div class="card-body">
                <div class="form-group <?= (!empty($psnGagal['nama']) ? 'has-error' : '') ?>">
                    <label class="control-label">Nama*</label>
                    <?= form_input([
                        'id' => 'cp_nama',
                        'name' => 'nama',
                        'class' => 'form-control rounded-0' . (!empty($psnGagal['nama']) ? ' is-invalid' : ''),
                        'placeholder' => 'Isikan nama CP ...'
                    ]) ?>
                </div>
                <div class="form-group <?= (!empty($psnGagal['no_hp']) ? 'has-error' : '') ?>">
                    <label class="control-label">No. HP</label>
                    <?= form_input([
                        'id' => 'cp_no_hp',
                        'name' => 'no_hp',
                        'class' => 'form-control rounded-0' . (!empty($psnGagal['no_hp']) ? ' is-invalid' : ''),
                        'placeholder' => 'Isikan nomor telepon CP ...'
                    ]) ?>
                </div>
                <div class="form-group <?= (!empty($psnGagal['jabatan']) ? 'has-error' : '') ?>">
                    <label class="control-label">Jabatan*</label>
                    <?= form_input([
                        'id' => 'cp_jabatan',
                        'name' => 'jabatan',
                        'class' => 'form-control rounded-0' . (!empty($psnGagal['jabatan']) ? ' is-invalid' : ''),
                        'placeholder' => 'Isikan Jabatan ...'
                    ]) ?>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-lg-6"></div>
                    <div class="col-lg-6 text-right">
                        <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-save"></i> Simpan</button>
                    </div>
                </div>                            
            </div>
        </div>
        <?= form_close() ?>

        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Data Kontak</h3>
                <div class="card-tools"></div>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th>Nama</th>
                            <th>HP</th>
                            <th>Jabatan</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="contactList">
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data kontak</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-lg-6"></div>
                    <div class="col-lg-6"></div>
                </div>                            
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize input mask for limit field
    $('#limit').inputmask('currency', {
        radixPoint: ',',
        groupSeparator: '.',
        digits: 2,
        digitsOptional: false,
        prefix: '',
        placeholder: '0'
    });
    
    // Show/hide contact person section based on tipe selection
    $('select[name="tipe"]').change(function() {
        var tipe = $(this).val();
        if (tipe > 1) {
            $('#contactPersonSection').show();
        } else {
            $('#contactPersonSection').hide();
        }
    });

    // Customer code generation
    const kodeInput = document.getElementById('kode');
    const generateBtn = document.getElementById('generate_kode');
    
    // Generate customer code function (5-digit numeric)
    function generateCustomerCode() {
        return Math.floor(Math.random() * 90000 + 10000).toString();
    }
    
    if (kodeInput) {
        // Ensure valid code on load
        if (!/^\d{5}$/.test(kodeInput.value.trim())) {
            kodeInput.value = generateCustomerCode();
        }

        // Restrict input to digits and max length 5
        kodeInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 5);
        });

        kodeInput.addEventListener('blur', function() {
            if (!/^\d{5}$/.test(this.value)) {
                this.value = generateCustomerCode();
            }
        });
    }
    
    if (generateBtn && kodeInput) {
        // Generate new code when button is clicked
        generateBtn.addEventListener('click', function() {
            kodeInput.value = generateCustomerCode();
            kodeInput.focus();
            
            // Visual feedback
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-sync-alt"></i>';
            }, 500);
        });
    }

    // Auto-generate username and email based on name
    const namaInput = document.getElementById('nama');
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    function generateUsernameFromName(name) {
        const cleanName = name.replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
        return cleanName + Math.floor(Math.random() * 900 + 100);
    }

    function generateEmailFromUsername(username) {
        return username + '@kopmensa.com';
    }

    namaInput.addEventListener('input', function() {
        if (this.value.trim() && !usernameInput.value.trim()) {
            const generatedUsername = generateUsernameFromName(this.value);
            usernameInput.value = generatedUsername;
            
            if (!emailInput.value.trim()) {
                emailInput.value = generateEmailFromUsername(generatedUsername);
            }
            
            if (!passwordInput.value.trim()) {
                passwordInput.value = generatedUsername;
            }
        }
    });

    // Clear dependent fields when name is cleared
    namaInput.addEventListener('input', function() {
        if (!this.value.trim()) {
            if (emailInput.value.includes('@kopmensa.com')) {
                emailInput.value = '';
            }
            if (passwordInput.value === usernameInput.value) {
                passwordInput.value = '';
            }
        }
    });
});
</script>

<?= $this->endSection() ?>