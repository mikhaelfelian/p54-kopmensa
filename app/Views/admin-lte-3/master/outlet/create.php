<?= $this->extend(theme_path('main')) ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Tambah Outlet</h3>
            </div>
            <?= form_open('master/outlet/store') ?>
            <div class="card-body">
                <div class="form-group">
                    <label>Nama</label>
                    <?= form_input([
                        'type' => 'text',
                        'name' => 'nama',
                        'class' => 'form-control rounded-0',
                        'placeholder' => 'Nama Outlet'
                    ]) ?>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <?= form_textarea([
                        'name' => 'deskripsi',
                        'class' => 'form-control rounded-0',
                        'placeholder' => 'Deskripsi Outlet'
                    ]) ?>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" name="status" value="1" id="statusAktif"
                            checked>
                        <label class="custom-control-label" for="statusAktif">
                            Aktif
                        </label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" name="status" value="0" id="statusNonaktif">
                        <label class="custom-control-label" for="statusNonaktif">
                            Tidak Aktif
                        </label>
                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/outlet') ?>" class="btn btn-default rounded-0">
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