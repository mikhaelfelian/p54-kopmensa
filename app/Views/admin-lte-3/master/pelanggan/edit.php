<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-20
 * Github : github.com/mikhaelfelian
 * description : View for editing customer data
 * This file represents the View for editing customers.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-6">
        <?= form_open('master/customer/update/' . $pelanggan->id) ?>
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Form Edit Data Pelanggan</h3>
                <div class="card-tools"></div>
            </div>
            <div class="card-body">
                <?php $psnGagal = session()->getFlashdata('psn_gagal'); ?>

                <div class="form-group <?= (!empty($psnGagal['kode']) ? 'has-error' : '') ?>">
                    <label class="control-label">Kode <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <?= form_input([
                            'id' => 'kode',
                            'name' => 'kode',
                            'class' => 'form-control rounded-0' . (!empty($psnGagal['kode']) ? ' is-invalid' : ''),
                            'placeholder' => 'CUS0001',
                            'value' => $pelanggan->kode,
                            'required' => true
                        ]) ?>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="generate_kode" title="Generate new code">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted">Auto-generated or enter custom code</small>
                </div>

                <div class="form-group <?= (!empty($psnGagal['nama']) ? 'has-error' : '') ?>">
                    <label class="control-label">Nama*</label>
                    <?= form_input([
                        'id' => 'nama',
                        'name' => 'nama',
                        'class' => 'form-control rounded-0' . (!empty($psnGagal['nama']) ? ' is-invalid' : ''),
                        'placeholder' => 'Isikan nama pelanggan ...',
                        'value' => old('nama', $pelanggan->nama)
                    ]) ?>
                </div>

                <div class="form-group <?= (!empty($psnGagal['no_telp']) ? 'has-error' : '') ?>">
                    <label class="control-label">No. Telp</label>
                    <?= form_input([
                        'id' => 'no_telp',
                        'name' => 'no_telp',
                        'class' => 'form-control rounded-0' . (!empty($psnGagal['no_telp']) ? ' is-invalid' : ''),
                        'placeholder' => 'Isikan nomor telepon pelanggan ...',
                        'value' => old('no_telp', $pelanggan->no_telp)
                    ]) ?>
                </div>

                <div class="form-group <?= (!empty($psnGagal['alamat']) ? 'has-error' : '') ?>">
                    <label class="control-label">Alamat*</label>
                    <?= form_textarea([
                        'id' => 'alamat',
                        'name' => 'alamat',
                        'class' => 'form-control rounded-0' . (!empty($psnGagal['alamat']) ? ' is-invalid' : ''),
                        'placeholder' => 'Isikan alamat pelanggan ...',
                        'value' => old('alamat', $pelanggan->alamat)
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
                                'value' => old('kota', $pelanggan->kota)
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
                                'value' => old('provinsi', $pelanggan->provinsi)
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
                                <option value="1"<?= old('tipe', $pelanggan->tipe) == '1' ? 'selected' : '' ?>>Anggota</option>
                                <option value="2"<?= old('tipe', $pelanggan->tipe) == '2' ? 'selected' : '' ?>>Umum</option>
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
                                    'checked' => old('status', $pelanggan->status) == '1',
                                    'value' => '1'
                                ]) ?>
                                <label for="statusAktif" class="custom-control-label">Aktif</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <?= form_radio([
                                    'id' => 'statusNonAktif',
                                    'name' => 'status',
                                    'class' => 'custom-control-input custom-control-input-danger',
                                    'checked' => old('status', $pelanggan->status) == '0',
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
                            'value' => old('limit', $pelanggan->limit ?? 0),
                            'data-inputmask' => "'alias': 'numeric', 'groupSeparator': '.', 'radixPoint': ',', 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0'"
                        ]) ?>
                    </div>
                    <small class="form-text text-muted">Batas maksimal saldo yang dapat digunakan pelanggan</small>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-lg-6">
                        <button type="button" onclick="window.location.href = '<?= base_url('master/customer') ?>'" class="btn btn-primary btn-flat">&laquo; Kembali</button>
                    </div>
                    <div class="col-lg-6 text-right">
                        <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-save"></i> Update</button>
                    </div>
                </div>                            
            </div>
        </div>
        <?= form_close() ?>
    </div>
    
    <!-- User Account Management Panel -->
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-cog"></i> Manajemen Akun User</h3>
            </div>
            <div class="card-body">
                <?php
                // Get user data from controller-passed variable
                $ionAuthUsername = '-';
                $ionAuthEmail = '-';
                $ionAuthActive = '0';
                $ionAuthPhoto = null;
                if (!empty($ionAuthUser)) {
                    $ionAuthUsername = $ionAuthUser->username ?? '-';
                    $ionAuthEmail = $ionAuthUser->email ?? '-';
                    $ionAuthActive = $ionAuthUser->active ?? '0';
                    $ionAuthPhoto = $ionAuthUser->profile ?? null;
                }
                ?>
                
                <!-- Profile Photo -->
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <?php if ($ionAuthPhoto && file_exists(FCPATH . $ionAuthPhoto)): ?>
                            <img src="<?= base_url($ionAuthPhoto) ?>" 
                                 class="img-circle elevation-2" 
                                 alt="User Image" 
                                 style="width: 120px; height: 120px; object-fit: cover;"
                                 id="profilePreview">
                        <?php else: ?>
                            <div class="img-circle elevation-2 d-flex align-items-center justify-content-center bg-light" 
                                 style="width: 120px; height: 120px; margin: 0 auto; border: 2px solid #dee2e6;">
                                <i class="fas fa-user fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-info rounded-0" id="uploadPhotoBtn">
                        <i class="fas fa-camera"></i> Upload Foto Profil
                    </button>
                    <input type="file" id="photoInput" accept="image/*" style="display: none;">
                    <p class="text-muted small mt-2">Max 2MB (JPG, PNG, GIF)</p>
                </div>

                <hr>

                <!-- Username -->
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username</label>
                    <div class="input-group">
                        <input type="text" class="form-control rounded-0" 
                               value="<?= esc($ionAuthUsername) ?>" 
                               id="usernameDisplay" readonly>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-warning" id="editUsernameBtn" title="Edit Username">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="text" class="form-control rounded-0" 
                           value="<?= esc($ionAuthEmail) ?>" readonly>
                </div>

                <!-- Account Status -->
                <div class="form-group">
                    <label><i class="fas fa-shield-alt"></i> Status Akun</label>
                    <div>
                        <span class="badge badge-<?= $ionAuthActive == '1' ? 'success' : 'danger' ?> p-2" id="accountStatusBadge">
                            <i class="fas fa-<?= $ionAuthActive == '1' ? 'check-circle' : 'ban' ?>"></i>
                            <?= $ionAuthActive == '1' ? 'Aktif' : 'Terblokir' ?>
                        </span>
                    </div>
                </div>

                <hr>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-block btn-warning rounded-0" id="resetPasswordBtn">
                            <i class="fas fa-key"></i> Reset Password
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-block btn-<?= $ionAuthActive == '1' ? 'danger' : 'success' ?> rounded-0" id="toggleBlockBtn" data-user-id="<?= $pelanggan->id_user ?>" data-status="<?= $ionAuthActive ?>">
                            <i class="fas fa-<?= $ionAuthActive == '1' ? 'ban' : 'check' ?>"></i> 
                            <?= $ionAuthActive == '1' ? 'Blokir Akun' : 'Aktifkan Akun' ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    
    <!-- Contact Person Section - Show if customer type is Instansi/Swasta -->
    <div class="col-md-6" id="contactPersonSection" style="display: <?= $pelanggan->tipe > 1 ? 'block' : 'none' ?>;">
        <?= form_open('master/customer/store_contact') ?>
        <?= form_hidden('id_pelanggan', $pelanggan->id) ?>

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
                <?php if (!empty($contacts)): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th>Nama</th>
                            <th>HP</th>
                            <th>Jabatan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $key => $contact): ?>
                        <tr>
                            <td class="text-center"><?= $key + 1 ?></td>
                            <td><?= esc($contact->nama) ?></td>
                            <td><?= esc($contact->no_hp) ?: '-' ?></td>
                            <td><?= esc($contact->jabatan) ?></td>
                            <td class="text-center">
                                <a href="<?= base_url("master/customer/delete_contact/{$contact->id}") ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Apakah anda yakin ingin menghapus kontak ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <p>Belum ada data kontak</p>
                </div>
                <?php endif; ?>
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
    // Show/hide contact person section based on tipe selection
    $('select[name="tipe"]').change(function() {
        var tipe = $(this).val();
        if (tipe > 1) {
            $('#contactPersonSection').show();
        } else {
            $('#contactPersonSection').hide();
        }
    });

    $("input[id=limit]").autoNumeric({aSep: '.', aDec: ',', aPad: false});

    // Customer code generation
    const kodeInput = document.getElementById('kode');
    const generateBtn = document.getElementById('generate_kode');
    
    function generateCustomerCode() {
        const timestamp = Date.now().toString().slice(-4);
        const paddedNumber = timestamp.padStart(4, '0');
        return 'CUS' + paddedNumber;
    }
    
    generateBtn.addEventListener('click', function() {
        kodeInput.value = generateCustomerCode();
        kodeInput.focus();
        
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-sync-alt"></i>';
        }, 500);
    });
    
    kodeInput.addEventListener('input', function() {
        if (!this.value.trim()) {
            this.value = generateCustomerCode();
        }
    });

    // Reset Password functionality
    $('#resetPasswordBtn').click(function() {
        const newPassword = prompt('Masukkan password baru untuk pelanggan ini:');
        if (newPassword && newPassword.length >= 6) {
            if (confirm('Apakah anda yakin ingin mereset password pelanggan ini?')) {
                $.ajax({
                    url: '<?= base_url('master/customer/reset_password') ?>',
                    type: 'POST',
                    data: {
                        id: <?= $pelanggan->id ?>,
                        password: newPassword,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('Password berhasil direset');
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat mereset password');
                    }
                });
            }
        } else if (newPassword !== null) {
            alert('Password minimal 6 karakter');
        }
    });

    // Toggle Block/Unblock User
    $('#toggleBlockBtn').click(function() {
        const userId = $(this).data('user-id');
        const currentStatus = $(this).data('status');
        const newStatus = currentStatus == '1' ? '0' : '1';
        const action = newStatus == '1' ? 'mengaktifkan' : 'memblokir';
        
        if (confirm(`Apakah anda yakin ingin ${action} akun user ini?`)) {
            $.ajax({
                url: '<?= base_url('master/customer/toggle_block') ?>',
                type: 'POST',
                data: {
                    id: <?= $pelanggan->id ?>,
                    id_user: userId,
                    status: newStatus,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success toastr
                        if (response.auto_created) {
                            toastr.success(
                                response.message,
                                'Akun Berhasil Dibuat',
                                {
                                    timeOut: 10000,
                                    closeButton: true,
                                    positionClass: 'toast-top-right',
                                    progressBar: true
                                }
                            );
                        } else {
                            toastr.success(
                                response.message,
                                'Status Akun',
                                {
                                    timeOut: 3000,
                                    closeButton: true,
                                    positionClass: 'toast-top-right'
                                }
                            );
                        }
                        
                        // Reload page after short delay to show toastr
                        setTimeout(function() {
                            location.reload();
                        }, response.auto_created ? 12000 : 3000);
                    } else {
                        toastr.error(response.message, 'Error');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Terjadi kesalahan saat mengubah status akun', 'Error');
                }
            });
        }
    });

    // Edit Username
    $('#editUsernameBtn').click(function() {
        const currentUsername = $('#usernameDisplay').val();
        const newUsername = prompt('Masukkan username baru:', currentUsername);
        
        if (newUsername && newUsername.length >= 3 && newUsername !== currentUsername) {
            if (confirm('Apakah anda yakin ingin mengubah username?')) {
                $.ajax({
                    url: '<?= base_url('master/customer/update_username') ?>',
                    type: 'POST',
                    data: {
                        id: <?= $pelanggan->id ?>,
                        id_user: <?= $pelanggan->id_user ?>,
                        username: newUsername,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('Username berhasil diubah');
                            $('#usernameDisplay').val(newUsername);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat mengubah username');
                    }
                });
            }
        } else if (newUsername !== null && newUsername !== currentUsername) {
            alert('Username minimal 3 karakter');
        }
    });

    // Upload Profile Photo
    $('#uploadPhotoBtn').click(function() {
        $('#photoInput').click();
    });

    $('#photoInput').change(function() {
        const file = this.files[0];
        if (file) {
            // Validate file type
            if (!file.type.match('image.*')) {
                alert('File harus berupa gambar');
                return;
            }
            
            // Validate file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file maksimal 2MB');
                return;
            }
            
            // Preview image
            const reader = new FileReader();
            reader.onload = function(e) {
                // Replace the default icon with actual image
                if ($('#profilePreview').is('div')) {
                    $('#profilePreview').replaceWith(`<img src="${e.target.result}" 
                         class="img-circle elevation-2" 
                         alt="User Image" 
                         style="width: 120px; height: 120px; object-fit: cover;"
                         id="profilePreview">`);
                } else {
                    $('#profilePreview').attr('src', e.target.result);
                }
            };
            reader.readAsDataURL(file);
            
            // Upload image
            const formData = new FormData();
            formData.append('photo', file);
            formData.append('id', <?= $pelanggan->id ?>);
            formData.append('id_user', <?= $pelanggan->id_user ?>);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            
            $.ajax({
                url: '<?= base_url('master/customer/upload_photo') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Foto profil berhasil diupload');
                    } else {
                        alert('Error: ' + response.message);
                        // Restore original image if upload fails
                        location.reload();
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat mengupload foto');
                    location.reload();
                }
            });
        }
    });
});
</script>

<?= $this->endSection() ?>