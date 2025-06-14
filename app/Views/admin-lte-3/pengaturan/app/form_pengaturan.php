<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <?= form_open_multipart('pengaturan/app/update', ['csrf_id' => 'pengaturan_form']) ?>
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Pengaturan Aplikasi</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="form-group">
                    <label for="judul_app">Judul Aplikasi <span class="text-danger">*</span></label>
                    <?= form_input([
                        'type' => 'text',
                        'class' => 'form-control rounded-0',
                        'id' => 'judul_app',
                        'name' => 'judul_app',
                        'value' => old('judul_app', $Pengaturan->judul_app),
                        'required' => true
                    ]) ?>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi Aplikasi <span class="text-danger">*</span></label>
                    <?= form_textarea([
                        'class' => 'form-control rounded-0',
                        'id' => 'deskripsi',
                        'name' => 'deskripsi',
                        'value' => old('deskripsi', $Pengaturan->deskripsi),
                        'rows' => 3,
                        'required' => true
                    ]) ?>
                </div>

                <div class="form-group">
                    <label for="logo_header">Logo Header</label>
                    <div class="custom-file">
                        <?= form_upload([
                            'class' => 'custom-file-input',
                            'id' => 'logo_header',
                            'name' => 'logo_header',
                            'accept' => 'image/jpg,image/jpeg,image/png'
                        ]) ?>
                        <label class="custom-file-label" for="logo_header">Pilih file...</label>
                    </div>
                    <small class="text-muted">Format: JPG, JPEG, PNG. Maksimal 2MB</small>
                    <?php if ($Pengaturan->logo_header): ?>
                        <div class="mt-2">
                            <img src="<?= base_url($Pengaturan->logo_header) ?>" alt="Logo Header" class="img-fluid" style="max-height: 100px">
                        </div>
                    <?php endif ?>
                </div>

                <div class="form-group">
                    <label for="favicon">Favicon</label>
                    <div class="custom-file">
                        <?= form_upload([
                            'class' => 'custom-file-input',
                            'id' => 'favicon',
                            'name' => 'favicon',
                            'accept' => 'image/x-icon,image/png'
                        ]) ?>
                        <label class="custom-file-label" for="favicon">Pilih file...</label>
                    </div>
                    <small class="text-muted">Format: ICO, PNG. Maksimal 1MB</small>
                    <?php if ($Pengaturan->favicon): ?>
                        <div class="mt-2">
                            <img src="<?= base_url($Pengaturan->favicon) ?>" alt="Favicon" class="img-fluid" style="max-height: 32px">
                        </div>
                    <?php endif ?>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="submit" class="btn btn-primary rounded-0">
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

<?= $this->section('js') ?>
<script>
// File input preview
$(document).on('change', '.custom-file-input', function() {
    let fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').addClass("selected").html(fileName);
});
</script>
<?= $this->endSection() ?> 