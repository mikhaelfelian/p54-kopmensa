<?= $this->extend(theme_path('main')) ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Edit Gudang</h3>
            </div>
            <?= form_open('master/gudang/update/' . $gudang->id) ?>
            <div class="card-body">
                <div class="form-group">
                    <label>Kode</label>
                    <?= form_input([
                        'type'        => 'text',
                        'name'        => 'kode',
                        'class'       => 'form-control rounded-0', 
                        'placeholder' => 'Kode',
                        'readonly'    => true,
                        'value'       => $gudang->kode
                    ]) ?>
                </div>
                <div class="form-group">
                    <label>Nama Gudang</label>
                    <?= form_input([
                        'type'        => 'text',
                        'name'        => 'gudang',
                        'class'       => 'form-control rounded-0',
                        'placeholder' => 'Nama Gudang', 
                        'value'       => $gudang->gudang
                    ]) ?>
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <?= form_textarea([
                        'name'        => 'keterangan',
                        'class'       => 'form-control rounded-0',
                        'placeholder' => 'Keterangan',
                        'value'       => $gudang->keterangan
                    ]) ?>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" name="status" value="1" id="statusAktif"
                            <?= $gudang->status == 1 ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="statusAktif">
                            Aktif
                        </label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" name="status" value="0" id="statusNonaktif"
                            <?= $gudang->status == 0 ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="statusNonaktif">
                            Tidak Aktif
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Status Gudang</label>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" name="status_gd" value="1" id="statusGudangUtama"
                            <?= $gudang->status_gd == 1 ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="statusGudangUtama">
                            Gudang Utama
                        </label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" name="status_gd" value="0" id="statusBukanGudangUtama"
                            <?= $gudang->status_gd == 0 ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="statusBukanGudangUtama">
                            Bukan Gudang Utama
                        </label>
                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/gudang') ?>" class="btn btn-default rounded-0">
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