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
                    <label for="judul">Judul Perusahaan <span class="text-danger">*</span></label>
                    <?= form_input([
                        'type' => 'text',
                        'class' => 'form-control rounded-0',
                        'id' => 'judul',
                        'name' => 'judul',
                        'value' => old('judul', $Pengaturan->judul ?? ''),
                        'required' => true,
                        'placeholder' => 'Contoh: KLINIK UTAMA dan LABORATORIUM "ESENSIA"'
                    ]) ?>
                    <small class="text-muted">Judul yang akan muncul di header dokumen PDF</small>
                </div>

                <div class="form-group">
                    <label for="judul_app">Judul Aplikasi <span class="text-danger">*</span></label>
                    <?= form_input([
                        'type' => 'text',
                        'class' => 'form-control rounded-0',
                        'id' => 'judul_app',
                        'name' => 'judul_app',
                        'value' => old('judul_app', $Pengaturan->judul_app ?? ''),
                        'required' => true,
                        'placeholder' => 'Contoh: PURCHASE ORDER'
                    ]) ?>
                    <small class="text-muted">Judul yang akan muncul di pojok kanan atas dokumen PDF</small>
                </div>

                <div class="form-group">
                    <label for="alamat">Alamat Perusahaan <span class="text-danger">*</span></label>
                    <?= form_textarea([
                        'class' => 'form-control rounded-0',
                        'id' => 'alamat',
                        'name' => 'alamat',
                        'value' => old('alamat', $Pengaturan->alamat ?? ''),
                        'rows' => 2,
                        'required' => true,
                        'placeholder' => 'Contoh: Perum Mutiara Pandanaran Blok D11'
                    ]) ?>
                    <small class="text-muted">Alamat yang akan muncul di bawah judul perusahaan</small>
                </div>

                <div class="form-group">
                    <label for="kota">Kota <span class="text-danger">*</span></label>
                    <?= form_input([
                        'type' => 'text',
                        'class' => 'form-control rounded-0',
                        'id' => 'kota',
                        'name' => 'kota',
                        'value' => old('kota', $Pengaturan->kota ?? ''),
                        'required' => true,
                        'placeholder' => 'Contoh: Semarang'
                    ]) ?>
                    <small class="text-muted">Kota yang akan muncul di footer dokumen PDF</small>
                </div>

                <div class="form-group">
                    <label for="apt_apa">APA (Apoteker Penanggung Jawab)</label>
                    <?= form_input([
                        'type' => 'text',
                        'class' => 'form-control rounded-0',
                        'id' => 'apt_apa',
                        'name' => 'apt_apa',
                        'value' => old('apt_apa', $Pengaturan->apt_apa ?? ''),
                        'placeholder' => 'Contoh: APT. UNGSARI RIZKI EKA PURWANTO, M.SC'
                    ]) ?>
                    <small class="text-muted">Nama apoteker penanggung jawab (opsional)</small>
                </div>

                <div class="form-group">
                    <label for="apt_sipa">SIPA (Surat Izin Praktik Apoteker)</label>
                    <?= form_input([
                        'type' => 'text',
                        'class' => 'form-control rounded-0',
                        'id' => 'apt_sipa',
                        'name' => 'apt_sipa',
                        'value' => old('apt_sipa', $Pengaturan->apt_sipa ?? ''),
                        'placeholder' => 'Contoh: 449.1/61/DPM-PTSP/SIPA/II/2022'
                    ]) ?>
                    <small class="text-muted">Nomor SIPA (opsional)</small>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi Aplikasi <span class="text-danger">*</span></label>
                    <?= form_textarea([
                        'class' => 'form-control rounded-0',
                        'id' => 'deskripsi',
                        'name' => 'deskripsi',
                        'value' => old('deskripsi', $Pengaturan->deskripsi ?? ''),
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
                    <?php if (!empty($Pengaturan->logo_header)): ?>
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
                    <?php if (!empty($Pengaturan->favicon)): ?>
                        <div class="mt-2">
                            <img src="<?= base_url($Pengaturan->favicon) ?>" alt="Favicon" class="img-fluid" style="max-height: 32px">
                        </div>
                    <?php endif ?>
                </div>

                <div class="form-group">
                    <label for="logo_invoice">Logo Invoice/Laporan</label>
                    <div class="custom-file">
                        <?= form_upload([
                            'class' => 'custom-file-input',
                            'id' => 'logo_invoice',
                            'name' => 'logo_invoice',
                            'accept' => 'image/jpg,image/jpeg,image/png'
                        ]) ?>
                        <label class="custom-file-label" for="logo_invoice">Pilih file...</label>
                    </div>
                    <small class="text-muted">Format: JPG, JPEG, PNG. Maksimal 2MB. Logo ini akan digunakan pada invoice dan laporan</small>
                    <?php if (!empty($Pengaturan->logo_invoice)): ?>
                        <div class="mt-2">
                            <img src="<?= base_url($Pengaturan->logo_invoice) ?>" alt="Logo Invoice" class="img-fluid" style="max-height: 100px">
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