<?= $this->extend(theme_path('main')) ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Edit Kategori</h3>
            </div>
            <?= form_open('master/kategori/update/' . $kategori->id) ?>
            <div class="card-body">
                <div class="form-group">
                    <label>Kode</label>
                    <?= form_input([
                        'type'        => 'text',
                        'name'        => 'kode',
                        'class'       => 'form-control rounded-0', 
                        'placeholder' => 'Kode',
                        'readonly'    => true,
                        'value'       => $kategori->kode
                    ]) ?>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <?= form_input([
                        'type'        => 'text',
                        'name'        => 'kategori',
                        'class'       => 'form-control rounded-0',
                        'placeholder' => 'Kategori', 
                        'value'       => $kategori->kategori
                    ]) ?>
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <?= form_textarea([
                        'name'        => 'keterangan',
                        'class'       => 'form-control rounded-0',
                        'placeholder' => 'Keterangan',
                        'value'       => $kategori->keterangan
                    ]) ?>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" name="status" value="1" id="statusAktif"
                            <?= $kategori->status == 1 ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="statusAktif">
                            Aktif
                        </label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" name="status" value="0" id="statusNonaktif"
                            <?= $kategori->status == 0 ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="statusNonaktif">
                            Tidak Aktif
                        </label>
                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/kategori') ?>" class="btn btn-default rounded-0">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary rounded-0 float-right">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>