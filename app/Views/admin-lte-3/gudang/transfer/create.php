<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-28
 * Github : github.com/mikhaelfelian
 * description : View for creating new transfer/mutasi data.
 * This file represents the transfer create view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Tambah Transfer/Mutasi</h3>
                <div class="card-tools">
                    <a href="<?= base_url('gudang/transfer') ?>" class="btn btn-sm btn-secondary rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <?= form_open(base_url('gudang/transfer/store'), ['id' => 'transferForm']) ?>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tgl_masuk">Tanggal Transfer <span class="text-danger">*</span></label>
                                <?= form_input([
                                    'type' => 'date',
                                    'class' => 'form-control rounded-0',
                                    'id' => 'tgl_masuk',
                                    'name' => 'tgl_masuk',
                                    'value' => date('Y-m-d'),
                                    'required' => 'required'
                                ]) ?>
                                <?php if (isset($errors['tgl_masuk'])): ?>
                                    <small class="text-danger"><?= $errors['tgl_masuk'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipe">Tipe Transfer <span class="text-danger">*</span></label>
                                <select class="form-control rounded-0" id="tipe" name="tipe" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="1">Pindah Gudang</option>
                                    <option value="2">Stok Masuk</option>
                                    <option value="3">Stok Keluar</option>
                                    <option value="4">Pindah Outlet</option>
                                </select>
                                <?php if (isset($errors['tipe'])): ?>
                                    <small class="text-danger"><?= $errors['tipe'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6" id="gudang-asal-section">
                            <div class="form-group">
                                <label for="id_gd_asal">Gudang Asal <span class="text-danger" id="gudang-asal-required">*</span></label>
                                <select class="form-control rounded-0" id="id_gd_asal" name="id_gd_asal">
                                    <option value="">Pilih Gudang Asal</option>
                                    <?php foreach ($gudang as $gd): ?>
                                        <option value="<?= $gd->id ?>"><?= $gd->gudang ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['id_gd_asal'])): ?>
                                    <small class="text-danger"><?= $errors['id_gd_asal'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6" id="gudang-tujuan-section">
                            <div class="form-group">
                                <label for="id_gd_tujuan">Gudang Tujuan <span class="text-danger" id="gudang-tujuan-required">*</span></label>
                                <select class="form-control rounded-0" id="id_gd_tujuan" name="id_gd_tujuan">
                                    <option value="">Pilih Gudang Tujuan</option>
                                    <?php foreach ($gudang as $gd): ?>
                                        <option value="<?= $gd->id ?>"><?= $gd->gudang ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['id_gd_tujuan'])): ?>
                                    <small class="text-danger"><?= $errors['id_gd_tujuan'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6" id="outlet-section">
                            <div class="form-group">
                                <label for="id_outlet">Outlet <span class="text-danger" id="outlet-required" style="display: none;">*</span></label>
                                <select class="form-control rounded-0" id="id_outlet" name="id_outlet">
                                    <option value="">Pilih Outlet</option>
                                    <?php foreach ($outlet as $ot): ?>
                                        <option value="<?= $ot->id ?>"><?= $ot->nama ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['id_outlet'])): ?>
                                    <small class="text-danger"><?= $errors['id_outlet'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <?= form_textarea([
                            'class' => 'form-control rounded-0',
                            'id' => 'keterangan',
                            'name' => 'keterangan',
                            'rows' => 3,
                            'placeholder' => 'Masukkan keterangan transfer...',
                            'value' => old('keterangan')
                        ]) ?>
                    </div>
                </div>
                <div class="card-footer" id="form-actions" style="display: none;">
                    <button type="submit" class="btn btn-primary rounded-0">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="<?= base_url('gudang/transfer') ?>" class="btn btn-secondary rounded-0">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize on page load - hide all sections
    $('#gudang-asal-section').hide();
    $('#gudang-tujuan-section').hide();
    $('#outlet-section').hide();
    $('#form-actions').hide();
    
    // Form validation
    $('#transferForm').on('submit', function(e) {
        var tipe = $('#tipe').val();
        var gdAsal = $('#id_gd_asal').val();
        var gdTujuan = $('#id_gd_tujuan').val();
        var outlet = $('#id_outlet').val();
        
        // Reset previous error states
        $('.is-invalid').removeClass('is-invalid');
        
        if (tipe === '1') { // Pindah Gudang
            if (!gdAsal || !gdTujuan) {
                e.preventDefault();
                if (!gdAsal) $('#id_gd_asal').addClass('is-invalid');
                if (!gdTujuan) $('#id_gd_tujuan').addClass('is-invalid');
                alert('Gudang asal dan tujuan harus dipilih untuk tipe Pindah Gudang!');
                return false;
            }
            if (gdAsal === gdTujuan) {
                e.preventDefault();
                $('#id_gd_asal').addClass('is-invalid');
                $('#id_gd_tujuan').addClass('is-invalid');
                alert('Gudang asal dan tujuan tidak boleh sama untuk tipe Pindah Gudang!');
                return false;
            }
        } else if (tipe === '2') { // Stok Masuk
            if (!gdTujuan) {
                e.preventDefault();
                $('#id_gd_tujuan').addClass('is-invalid');
                alert('Gudang tujuan harus dipilih untuk tipe Stok Masuk!');
                return false;
            }
        } else if (tipe === '3') { // Stok Keluar
            if (!gdAsal) {
                e.preventDefault();
                $('#id_gd_asal').addClass('is-invalid');
                alert('Gudang asal harus dipilih untuk tipe Stok Keluar!');
                return false;
            }
        } else if (tipe === '4') { // Pindah Outlet
            if (!gdAsal || !outlet) {
                e.preventDefault();
                if (!gdAsal) $('#id_gd_asal').addClass('is-invalid');
                if (!outlet) $('#id_outlet').addClass('is-invalid');
                alert('Gudang asal dan outlet harus dipilih untuk tipe Pindah Outlet!');
                return false;
            }
        } else {
            e.preventDefault();
            alert('Tipe transfer harus dipilih!');
            return false;
        }
    });
    
    // Auto-hide gudang fields based on tipe
    $('#tipe').on('change', function() {
        var tipe = $(this).val();
        
        // Reset all fields and hide form actions initially
        $('#id_gd_asal').val('').prop('disabled', false).prop('required', false);
        $('#id_gd_tujuan').val('').prop('disabled', false).prop('required', false);
        $('#id_outlet').val('').prop('required', false);
        $('#gudang-asal-section').hide();
        $('#gudang-tujuan-section').hide();
        $('#outlet-section').hide();
        $('#gudang-asal-required').hide();
        $('#gudang-tujuan-required').hide();
        $('#outlet-required').hide();
        $('#form-actions').hide();
        
        if (tipe === '1') { // Pindah Gudang
            $('#gudang-asal-section').show();
            $('#gudang-tujuan-section').show();
            $('#id_gd_asal').prop('required', true);
            $('#id_gd_tujuan').prop('required', true);
            $('#gudang-asal-required').show();
            $('#gudang-tujuan-required').show();
            $('#form-actions').show();
        } else if (tipe === '2') { // Stok Masuk
            $('#gudang-tujuan-section').show();
            $('#id_gd_tujuan').prop('required', true);
            $('#gudang-tujuan-required').show();
            $('#form-actions').show();
        } else if (tipe === '3') { // Stok Keluar
            $('#gudang-asal-section').show();
            $('#id_gd_asal').prop('required', true);
            $('#gudang-asal-required').show();
            $('#form-actions').show();
        } else if (tipe === '4') { // Pindah Outlet
            $('#gudang-asal-section').show();
            $('#outlet-section').show();
            $('#id_gd_asal').prop('required', true);
            $('#id_outlet').prop('required', true);
            $('#gudang-asal-required').show();
            $('#outlet-required').show();
            $('#form-actions').show();
        }
    });
});
</script>

<style>
.is-invalid {
    border-color: #dc3545 !important;
}

.is-invalid:focus {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

#form-actions {
    transition: all 0.3s ease;
}

.form-group {
    transition: all 0.3s ease;
}
</style>
<?= $this->endSection() ?> 