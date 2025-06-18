<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-18
 * Github : github.com/mikhaelfelian
 * description : View for creating new item data
 * This file represents the View for creating items.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-6">
        <form action="<?= base_url('master/item/store') ?>" method="post" accept-charset="utf-8">
            <?= csrf_field() ?>
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Data Item</h3>
                    <div class="card-tools"></div>
                </div>
                <div class="card-body">
                    
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="route" value="">
                    <input type="hidden" name="id_item" value="">
                    <input type="hidden" name="status_item" value="">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Kategori</label>
                                <select name="id_kategori" class="form-control rounded-0">
                                    <option value="">-[Kategori]-</option>
                                    <?php foreach ($kategori as $k) : ?>
                                        <option value="<?= $k['id'] ?>" <?= old('id_kategori') == $k['id'] ? 'selected' : '' ?>><?= $k['nama'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Merk</label>
                                <select name="id_merk" class="form-control rounded-0">
                                    <option value="">-[Merk]-</option>
                                    <?php foreach ($merk as $m) : ?>
                                        <option value="<?= $m['id'] ?>" <?= old('id_merk') == $m['id'] ? 'selected' : '' ?>><?= $m['nama'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">SKU</label>
                                <input type="text" name="kode" value="" id="kode" class="form-control rounded-0" placeholder="Isikan SKU ..." readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Barcode</label>
                                <input type="text" name="barcode" value="<?= old('barcode') ?>" id="barcode" class="form-control rounded-0" placeholder="Isikan barcode ...">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Item*</label>
                        <input type="text" name="item" value="<?= old('item') ?>" id="item" class="form-control rounded-0 <?= ($validation->hasError('item')) ? 'is-invalid' : '' ?>" placeholder="Isikan nama item / produk ..." required>
                        <?php if ($validation->hasError('item')) : ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('item') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3">Harga Beli</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Rp. </span>
                                    </div>
                                    <input type="text" name="harga_beli" value="<?= old('harga_beli') ?>" id="harga_beli" class="form-control rounded-0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3">Harga Jual</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Rp. </span>
                                    </div>
                                    <input type="text" name="harga_jual" value="<?= old('harga_jual') ?>" id="harga_jual" class="form-control rounded-0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Stok Minimum</label>
                                <input type="number" name="jml_min" value="<?= old('jml_min') ?>" id="jml_min" class="form-control rounded-0" placeholder="Stok minimum ...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Tipe</label>
                                <select name="tipe" class="form-control rounded-0">
                                    <option value="1" <?= old('tipe') == '1' ? 'selected' : '' ?>>Item</option>
                                    <option value="2" <?= old('tipe') == '2' ? 'selected' : '' ?>>Jasa</option>
                                    <option value="3" <?= old('tipe') == '3' ? 'selected' : '' ?>>Paket</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Deskripsi</label>
                        <textarea name="deskripsi" cols="40" rows="3" id="deskripsi" class="form-control rounded-0" placeholder="Isikan deskripsi item / spek produk / dll ..."><?= old('deskripsi') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Status*</label>                                
                        <div class="custom-control custom-radio">
                            <input type="radio" name="status" value="1" id="statusAktif" class="custom-control-input" <?= old('status') == '1' ? 'checked' : '' ?>>
                            <label for="statusAktif" class="custom-control-label">Aktif</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" name="status" value="0" id="statusNonAktif" class="custom-control-input custom-control-input-danger" <?= old('status') == '0' ? 'checked' : '' ?>>
                            <label for="statusNonAktif" class="custom-control-label">Non - Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <button type="button" onclick="window.location.href = '<?= base_url('master/item') ?>'" class="btn btn-primary btn-flat">« Kembali</button>
                        </div>
                        <div class="col-lg-6 text-right">
                            <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-save" aria-hidden="true"></i> Simpan</button>
                        </div>
                    </div>                            
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?> 