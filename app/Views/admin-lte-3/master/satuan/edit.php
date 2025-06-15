<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-06
 * 
 * Edit Satuan View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <?= form_open('master/satuan/update/' . $satuan->id) ?>
            <div class="card rounded-0">
                <div class="card-header">
                    <h3 class="card-title">Edit Satuan</h3>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <label for="satuanKecil">Satuan Kecil</label>
                        <input type="text" name="satuanKecil" id="satuanKecil" 
                               class="form-control rounded-0 <?= session('validation_errors.satuanKecil') ? 'is-invalid' : '' ?>"
                               value="<?= old('satuanKecil', $satuan->satuanKecil) ?>">
                        <?php if (session('validation_errors.satuanKecil')) : ?>
                            <div class="invalid-feedback">
                                <?= session('validation_errors.satuanKecil') ?>
                            </div>
                        <?php endif ?>
                    </div>

                    <div class="form-group">
                        <label for="satuanBesar">Satuan Besar</label>
                        <input type="text" name="satuanBesar" id="satuanBesar" 
                               class="form-control rounded-0 <?= session('validation_errors.satuanBesar') ? 'is-invalid' : '' ?>"
                               value="<?= old('satuanBesar', $satuan->satuanBesar) ?>">
                        <?php if (session('validation_errors.satuanBesar')) : ?>
                            <div class="invalid-feedback">
                                <?= session('validation_errors.satuanBesar') ?>
                            </div>
                        <?php endif ?>
                    </div>

                    <div class="form-group">
                        <label for="jml">Jumlah</label>
                        <input type="number" name="jml" id="jml" 
                               class="form-control rounded-0"
                               value="<?= old('jml', $satuan->jml) ?>">
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" 
                                class="form-control rounded-0 <?= session('validation_errors.status') ? 'is-invalid' : '' ?>">
                            <option value="1" <?= old('status', $satuan->status) == '1' ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= old('status', $satuan->status) == '0' ? 'selected' : '' ?>>Tidak Aktif</option>
                        </select>
                        <?php if (session('validation_errors.status')) : ?>
                            <div class="invalid-feedback">
                                <?= session('validation_errors.status') ?>
                            </div>
                        <?php endif ?>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary rounded-0">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                    <a href="<?= base_url('master/satuan') ?>" class="btn btn-default rounded-0">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
        <?= form_close() ?>
    </div>
</div>

<?= $this->endSection() ?> 