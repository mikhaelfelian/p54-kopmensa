<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-import mr-2"></i>Import Data Stok Opname
                </h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Petunjuk Import Excel</h5>
                    <ol>
                        <li>Download template Excel terlebih dahulu dengan mengklik tombol <strong>"Download Template"</strong></li>
                        <li>Isi data pada file Excel sesuai dengan format template</li>
                        <li>Upload file Excel yang sudah diisi melalui form di bawah ini</li>
                        <li>Pastikan file Excel tidak melebihi 5MB</li>
                        <li>Format kolom: Tanggal Opname, Tipe (Gudang/Outlet), Lokasi (ID atau Nama), Keterangan, Status (0=Draft, 1=Selesai)</li>
                    </ol>
                </div>

                <?= form_open_multipart('gudang/opname/import', ['class' => 'form-horizontal']) ?>
                <div class="form-group">
                    <label for="excel_file">Pilih File Excel</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                            <label class="custom-file-label" for="excel_file">Pilih file Excel...</label>
                        </div>
                    </div>
                    <small class="form-text text-muted">Format file: Excel (.xlsx atau .xls), Maksimal 5MB</small>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <a href="<?= base_url('gudang/opname') ?>" class="btn btn-default rounded-0">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <a href="<?= base_url('gudang/opname/template') ?>" class="btn btn-info rounded-0">
                    <i class="fas fa-download mr-2"></i>Download Template
                </a>
                <button type="submit" class="btn btn-success rounded-0">
                    <i class="fas fa-upload mr-2"></i>Import Excel
                </button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
// Update file input label when file is selected
document.getElementById('excel_file').addEventListener('change', function(e) {
    var fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file Excel...';
    e.target.nextElementSibling.textContent = fileName;
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('excel_file');
    if (!fileInput.files.length) {
        e.preventDefault();
        alert('Silakan pilih file Excel terlebih dahulu!');
        return false;
    }
    
    const file = fileInput.files[0];
    if (file.size > 5 * 1024 * 1024) { // 5MB
        e.preventDefault();
        alert('Ukuran file terlalu besar! Maksimal 5MB.');
        return false;
    }
    
    const validExtensions = ['.xlsx', '.xls'];
    const fileExtension = file.name.toLowerCase().substring(file.name.lastIndexOf('.'));
    if (!validExtensions.includes(fileExtension)) {
        e.preventDefault();
        alert('File harus berformat Excel (.xlsx atau .xls)!');
        return false;
    }
});
</script>
<?= $this->endSection() ?>

