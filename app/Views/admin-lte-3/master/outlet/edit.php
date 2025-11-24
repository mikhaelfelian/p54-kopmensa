<?= $this->extend(theme_path('main')) ?>
<?= $this->section('content') ?>
<div class="row">
    <!-- Form Edit Outlet -->
    <div class="col-md-6">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit"></i> Form Edit Outlet
                </h3>
            </div>
            <?= form_open('master/outlet/update/' . $outlet->id) ?>
            <div class="card-body">
                <div class="form-group">
                    <label>Nama <span class="text-danger">*</span></label>
                    <?= form_input([
                        'type' => 'text',
                        'name' => 'nama',
                        'class' => 'form-control rounded-0',
                        'value' => $outlet->nama,
                        'placeholder' => 'Nama Outlet',
                        'required' => true
                    ]) ?>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <?= form_textarea([
                        'name' => 'deskripsi',
                        'class' => 'form-control rounded-0',
                        'value' => $outlet->deskripsi,
                        'placeholder' => 'Deskripsi Outlet',
                        'rows' => 3
                    ]) ?>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" name="status" value="1" id="statusAktif"
                            <?= ($outlet->status == '1') ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="statusAktif">
                            Aktif
                        </label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" name="status" value="0" id="statusNonaktif"
                            <?= ($outlet->status == '0') ? 'checked' : '' ?>>
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
    <div class="col-md-6">
        <?= view('admin-lte-3/master/outlet/partials/platform_manager', [
            'outlet'             => $outlet,
            'assignedPlatforms'  => $assignedPlatforms ?? [],
            'availablePlatforms' => $availablePlatforms ?? [],
            'embedded'           => true,
        ]) ?>
    </div>
</div>

<?= $this->endSection() ?>