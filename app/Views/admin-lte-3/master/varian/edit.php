<?= $this->extend(theme_path('main')) ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Edit Varian</h3>
            </div>
            <?= form_open('master/varian/update/' . $varian->id) ?>
            <div class="card-body">
                <div class="form-group">
                    <label>Kode</label>
                    <?= form_input([
                        'type'        => 'text',
                        'name'        => 'kode',
                        'class'       => 'form-control rounded-0', 
                        'placeholder' => 'Kode',
                        'readonly'    => true,
                        'value'       => $varian->kode
                    ]) ?>
                </div>
                <div class="form-group">
                    <label>Nama Varian</label>
                    <?= form_input([
                        'type'        => 'text',
                        'name'        => 'nama',
                        'class'       => 'form-control rounded-0',
                        'placeholder' => 'Contoh: Warna Merah, Ukuran XL, dll.', 
                        'value'       => $varian->nama
                    ]) ?>
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <?= form_textarea([
                        'name'        => 'keterangan',
                        'class'       => 'form-control rounded-0',
                        'placeholder' => 'Penjelasan detail varian jika perlu...',
                        'value'       => $varian->keterangan
                    ]) ?>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" name="status" value="1" id="statusAktif"
                            <?= $varian->status == 1 ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="statusAktif">
                            Aktif
                        </label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" name="status" value="0" id="statusNonaktif"
                            <?= $varian->status == 0 ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="statusNonaktif">
                            Tidak Aktif
                        </label>
                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/varian') ?>" class="btn btn-default rounded-0">
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