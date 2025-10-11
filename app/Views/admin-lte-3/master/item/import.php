<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-import mr-2"></i>Import Data Item
                </h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Petunjuk Import CSV</h5>
                    <ol>
                        <li>Download template CSV terlebih dahulu dengan mengklik tombol <strong>"Download Template"</strong></li>
                        <li>Isi data pada file CSV sesuai dengan format template</li>
                        <li>Upload file CSV yang sudah diisi melalui form di bawah ini</li>
                        <li>Pastikan file CSV tidak melebihi 2MB</li>
                    </ol>
                </div>

                <?= form_open_multipart('master/item/import', ['class' => 'form-horizontal']) ?>
                <div class="form-group">
                    <label for="csv_file">Pilih File CSV</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="csv_file" name="csv_file" accept=".csv" required>
                            <label class="custom-file-label" for="csv_file">Pilih file CSV...</label>
                        </div>
                    </div>
                    <small class="form-text text-muted">Format file: CSV, Maksimal 2MB</small>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="skip_header" name="skip_header" value="1" checked>
                        <label class="custom-control-label" for="skip_header">
                            Skip baris pertama (header)
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="update_existing" name="update_existing" value="1">
                        <label class="custom-control-label" for="update_existing">
                            Update data yang sudah ada (berdasarkan kode)
                        </label>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <a href="<?= base_url('master/item') ?>" class="btn btn-default rounded-0">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <a href="<?= base_url('master/item/template') ?>" class="btn btn-info rounded-0">
                    <i class="fas fa-download mr-2"></i>Download Template
                </a>
                <button type="submit" class="btn btn-success rounded-0">
                    <i class="fas fa-upload mr-2"></i>Import CSV
                </button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
// Update file input label when file is selected
document.getElementById('csv_file').addEventListener('change', function(e) {
    var fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file CSV...';
    e.target.nextElementSibling.textContent = fileName;
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('csv_file');
    if (!fileInput.files.length) {
        e.preventDefault();
        alert('Silakan pilih file CSV terlebih dahulu!');
        return false;
    }
    
    const file = fileInput.files[0];
    if (file.size > 2 * 1024 * 1024) { // 2MB
        e.preventDefault();
        alert('Ukuran file terlalu besar! Maksimal 2MB.');
        return false;
    }
    
    if (!file.name.toLowerCase().endsWith('.csv')) {
        e.preventDefault();
        alert('File harus berformat CSV!');
        return false;
    }
});
</script>
<?= $this->endSection() ?>
