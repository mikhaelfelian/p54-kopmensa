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
                    <h5><i class="icon fas fa-info"></i> Petunjuk Import Excel</h5>
                    <ol>
                        <li>Download template Excel terlebih dahulu dengan mengklik tombol <strong>"Download Template"</strong></li>
                        <li>Isi data pada file Excel sesuai dengan format template</li>
                        <li>Upload file Excel yang sudah diisi melalui form di bawah ini</li>
                        <li>Pastikan file Excel tidak melebihi 5MB</li>
                    </ol>
                    <hr>
                    <p class="mb-2"><strong>Aturan pengisian template</strong></p>
                    <ul class="mb-0">
                        <li><strong>Wajib diisi</strong>: Item, ID Kategori, ID Merk, ID Supplier, ID Satuan, Harga Beli, Harga Jual, Status, Status Stok, Status PPN, Tipe.</li>
                        <li><strong>Opsional</strong>: Barcode, Deskripsi, Jml Min, Weight.</li>
                        <li><strong>Harga Beli / Harga Jual</strong>: isi angka saja. Boleh <code>12000</code> atau <code>12.000</code>. Jangan pakai teks <code>Rp</code>.</li>
                        <li><strong>Jml Min</strong>: stok minimum untuk peringatan “Stok Rendah”. Isi <code>0</code> bila tidak ingin ada peringatan.</li>
                        <li><strong>Weight</strong>: berat item (angka). Gunakan titik (<code>.</code>) untuk desimal, contoh <code>1.25</code>.</li>
                        <li><strong>ID Kategori / ID Merk / ID Supplier / ID Satuan</strong>: gunakan ID sesuai master data di sistem.</li>
                        <li><strong>Tipe</strong>: <code>1</code>=Item, <code>2</code>=Jasa, <code>3</code>=Paket.</li>
                        <li><strong>Status</strong>: <code>1</code>=Aktif, <code>0</code>=Non Aktif.</li>
                        <li><strong>Status Stok</strong>: <code>1</code>=Stockable, <code>0</code>=Non Stockable.</li>
                        <li><strong>Status PPN</strong>: <code>1</code>=Kena PPN, <code>0</code>=Tidak Kena PPN.</li>
                    </ul>
                </div>

                <?= form_open_multipart('master/item/import', ['class' => 'form-horizontal']) ?>
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
