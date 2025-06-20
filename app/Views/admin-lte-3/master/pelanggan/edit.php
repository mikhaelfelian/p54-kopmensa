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
                    <label class="control-label">Kode</label>
                    <?= form_input([
                        'id' => 'kode',
                        'name' => 'kode',
                        'class' => 'form-control rounded-0' . (!empty($psnGagal['kode']) ? ' is-invalid' : ''),
                        'placeholder' => 'Isikan kode ...',
                        'value' => $pelanggan->kode,
                        'readonly' => 'true'
                    ]) ?>
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
});
</script>

<?= $this->endSection() ?>