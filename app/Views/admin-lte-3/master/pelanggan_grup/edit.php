<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-08-23
 * Github : github.com/mikhaelfelian
 * description : View for editing customer group data
 * This file represents the View for editing customer groups.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-6">
        <?= form_open('master/customer-group/update/' . $grup->id) ?>
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Form Edit Data Grup Pelanggan</h3>
                <div class="card-tools"></div>
            </div>
            <div class="card-body">
                <?php $psnGagal = session()->getFlashdata('psn_gagal'); ?>

                <div class="form-group <?= (!empty($psnGagal['grup']) ? 'has-error' : '') ?>">
                    <label class="control-label">Nama Grup*</label>
                    <?= form_input([
                        'id' => 'grup',
                        'name' => 'grup',
                        'class' => 'form-control rounded-0' . (!empty($psnGagal['grup']) ? ' is-invalid' : ''),
                        'placeholder' => 'Contoh: Umum, Anggota, Reseller',
                        'value' => old('grup') ?: $grup->grup,
                        'maxlength' => '100'
                    ]) ?>
                </div>

                <div class="form-group <?= (!empty($psnGagal['deskripsi']) ? 'has-error' : '') ?>">
                    <label class="control-label">Deskripsi</label>
                    <?= form_textarea([
                        'id' => 'deskripsi',
                        'name' => 'deskripsi',
                        'class' => 'form-control rounded-0' . (!empty($psnGagal['deskripsi']) ? ' is-invalid' : ''),
                        'placeholder' => 'Keterangan tambahan tentang grup pelanggan',
                        'value' => old('deskripsi') ?: $grup->deskripsi,
                        'rows' => '3'
                    ]) ?>
                </div>

                <div class="form-group <?= (!empty($psnGagal['status']) ? 'has-error' : '') ?>">
                    <label class="control-label">Status</label>
                    <select class="form-control rounded-0<?= (!empty($psnGagal['status']) ? ' is-invalid' : '') ?>" name="status" id="status">
                        <option value="1" <?= (old('status') ?: $grup->status) == '1' ? 'selected' : '' ?>>Aktif</option>
                        <option value="0" <?= (old('status') ?: $grup->status) == '0' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary rounded-0">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="<?= base_url('master/customer-group') ?>" class="btn btn-secondary rounded-0">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>
<?= $this->endSection() ?>
