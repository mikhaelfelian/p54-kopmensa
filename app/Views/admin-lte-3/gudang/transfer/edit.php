<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-28
 * Github : github.com/mikhaelfelian
 * description : View for editing transfer/mutasi data.
 * This file represents the transfer edit view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Edit Transfer/Mutasi</h3>
                <div class="card-tools">
                    <a href="<?= base_url('gudang/transfer') ?>" class="btn btn-sm btn-secondary rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <form action="<?= base_url("gudang/transfer/update/{$transfer->id}") ?>" method="post" id="transferForm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tgl_masuk">Tanggal Transfer <span class="text-danger">*</span></label>
                                <input type="date" class="form-control rounded-0" id="tgl_masuk" name="tgl_masuk" 
                                       value="<?= date('Y-m-d', strtotime($transfer->tgl_masuk)) ?>" required>
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
                                    <option value="1" <?= $transfer->tipe == '1' ? 'selected' : '' ?>>Pindah Gudang</option>
                                    <option value="2" <?= $transfer->tipe == '2' ? 'selected' : '' ?>>Stok Masuk</option>
                                    <option value="3" <?= $transfer->tipe == '3' ? 'selected' : '' ?>>Stok Keluar</option>
                                </select>
                                <?php if (isset($errors['tipe'])): ?>
                                    <small class="text-danger"><?= $errors['tipe'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_gd_asal">Gudang Asal <span class="text-danger">*</span></label>
                                <select class="form-control rounded-0" id="id_gd_asal" name="id_gd_asal" required>
                                    <option value="">Pilih Gudang Asal</option>
                                    <?php foreach ($gudang as $gd): ?>
                                        <option value="<?= $gd->id ?>" <?= $transfer->id_gd_asal == $gd->id ? 'selected' : '' ?>>
                                            <?= $gd->gudang ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['id_gd_asal'])): ?>
                                    <small class="text-danger"><?= $errors['id_gd_asal'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_gd_tujuan">Gudang Tujuan <span class="text-danger">*</span></label>
                                <select class="form-control rounded-0" id="id_gd_tujuan" name="id_gd_tujuan" required>
                                    <option value="">Pilih Gudang Tujuan</option>
                                    <?php foreach ($gudang as $gd): ?>
                                        <option value="<?= $gd->id ?>" <?= $transfer->id_gd_tujuan == $gd->id ? 'selected' : '' ?>>
                                            <?= $gd->gudang ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['id_gd_tujuan'])): ?>
                                    <small class="text-danger"><?= $errors['id_gd_tujuan'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_outlet">Outlet (Opsional)</label>
                                <select class="form-control rounded-0" id="id_outlet" name="id_outlet">
                                    <option value="">Pilih Outlet</option>
                                    <?php foreach ($outlet as $ot): ?>
                                        <option value="<?= $ot->id ?>" <?= $transfer->id_outlet == $ot->id ? 'selected' : '' ?>>
                                            <?= $ot->nama ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control rounded-0" id="keterangan" name="keterangan" rows="3" 
                                  placeholder="Masukkan keterangan transfer..."><?= old('keterangan', $transfer->keterangan) ?></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Informasi!</h5>
                        <p>Transfer ini memiliki status: 
                            <strong>
                                <?php
                                $statusNotaLabels = [
                                    '0' => 'Draft',
                                    '1' => 'Pending',
                                    '2' => 'Diproses',
                                    '3' => 'Selesai',
                                    '4' => 'Dibatalkan'
                                ];
                                echo $statusNotaLabels[$transfer->status_nota] ?? 'Unknown';
                                ?>
                            </strong>
                        </p>
                        <?php if ($transfer->status_nota == '3'): ?>
                            <p class="text-warning">Transfer yang sudah selesai tidak dapat diedit!</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer">
                    <?php if ($transfer->status_nota != '3'): ?>
                        <button type="submit" class="btn btn-primary rounded-0">
                            <i class="fas fa-save"></i> Update
                        </button>
                    <?php endif; ?>
                    <a href="<?= base_url('gudang/transfer') ?>" class="btn btn-secondary rounded-0">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Form validation
    $('#transferForm').on('submit', function(e) {
        var tipe = $('#tipe').val();
        var gdAsal = $('#id_gd_asal').val();
        var gdTujuan = $('#id_gd_tujuan').val();
        
        if (tipe === '1') { // Pindah Gudang
            if (gdAsal === gdTujuan) {
                e.preventDefault();
                alert('Gudang asal dan tujuan tidak boleh sama untuk tipe Pindah Gudang!');
                return false;
            }
            if (!gdAsal || !gdTujuan) {
                e.preventDefault();
                alert('Gudang asal dan tujuan harus dipilih untuk tipe Pindah Gudang!');
                return false;
            }
        } else if (tipe === '2') { // Stok Masuk
            if (gdAsal) {
                e.preventDefault();
                alert('Untuk tipe Stok Masuk, gudang asal harus kosong!');
                return false;
            }
            if (!gdTujuan) {
                e.preventDefault();
                alert('Gudang tujuan harus dipilih untuk tipe Stok Masuk!');
                return false;
            }
        } else if (tipe === '3') { // Stok Keluar
            if (gdTujuan) {
                e.preventDefault();
                alert('Untuk tipe Stok Keluar, gudang tujuan harus kosong!');
                return false;
            }
            if (!gdAsal) {
                e.preventDefault();
                alert('Gudang asal harus dipilih untuk tipe Stok Keluar!');
                return false;
            }
        }
    });
    
    // Auto-hide gudang fields based on tipe
    $('#tipe').on('change', function() {
        var tipe = $(this).val();
        
        if (tipe === '2') { // Stok Masuk
            $('#id_gd_asal').val('').prop('disabled', true);
            $('#id_gd_tujuan').prop('disabled', false);
        } else if (tipe === '3') { // Stok Keluar
            $('#id_gd_asal').prop('disabled', false);
            $('#id_gd_tujuan').val('').prop('disabled', true);
        } else { // Pindah Gudang
            $('#id_gd_asal').prop('disabled', false);
            $('#id_gd_tujuan').prop('disabled', false);
        }
    });
    
    // Initialize based on current tipe
    $('#tipe').trigger('change');
});
</script>
<?= $this->endSection() ?> 