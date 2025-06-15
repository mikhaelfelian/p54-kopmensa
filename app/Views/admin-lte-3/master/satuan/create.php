<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <?= form_open('master/satuan/store') ?>
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Tambah Satuan</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="form-group">
                    <label for="satuanKecil">Satuan Kecil <span class="text-danger">*</span></label>
                    <?= form_input([
                        'type' => 'text',
                        'class' => 'form-control rounded-0',
                        'id' => 'satuanKecil',
                        'name' => 'satuanKecil',
                        'required' => true
                    ]) ?>
                </div>

                <div class="form-group">
                    <label for="satuanBesar">Satuan Besar</label>
                    <?= form_input([
                        'type' => 'text',
                        'class' => 'form-control rounded-0',
                        'id' => 'satuanBesar',
                        'name' => 'satuanBesar'
                    ]) ?>

                </div>

                <div class="form-group">
                    <label for="jml">Jumlah <span class="text-danger">*</span></label>
                    <?= form_input([
                        'type' => 'number',
                        'class' => 'form-control rounded-0',
                        'id' => 'jml',
                        'name' => 'jml',
                        'required' => true,
                        'min' => 1
                    ]) ?>
                    <small class="text-muted">Jumlah satuan kecil dalam 1 satuan besar</small>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" id="status1" name="status" value="1"
                            <?= old('status', '1') == '1' ? 'checked' : '' ?>>
                        <label for="status1" class="custom-control-label">Aktif</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" id="status0" name="status" value="0"
                            <?= old('status') == '0' ? 'checked' : '' ?>>
                        <label for="status0" class="custom-control-label">Non Aktif</label>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer text-left">
                <a href="<?= base_url('master/satuan') ?>" class="btn btn-default rounded-0">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary rounded-0 float-right">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </div>
        <!-- /.card -->
        <?= form_close() ?>
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
<?= $this->endSection() ?>