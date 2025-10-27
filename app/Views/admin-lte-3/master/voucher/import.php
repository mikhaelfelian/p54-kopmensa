<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card rounded-0">
            <div class="card-header bg-success">
                <h3 class="card-title">
                    <i class="fas fa-file-import"></i> Import Data Voucher
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Peringatan!</h5>
                    <p class="mb-2"><strong>Perhatikan format tanggal harus YYYY-MM-DD (contoh: 2025-01-15)</strong></p>
                </div>

                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Petunjuk Import!</h5>
                    <ol>
                        <li>Download template Excel terlebih dahulu dengan klik tombol <strong>"Template"</strong></li>
                        <li>Isi data voucher sesuai dengan format template</li>
                        <li>Kolom yang diperlukan:
                            <ul>
                                <li><strong>Kode</strong> - Kode voucher (Wajib, Max 50 karakter)</li>
                                <li><strong>Jumlah</strong> - Jumlah voucher (Wajib, angka > 0)</li>
                                <li><strong>Jenis Voucher</strong> - 0 = Nominal, 1 = Persen (Wajib, angka 0 atau 1)</li>
                                <li><strong>Nominal</strong> - Nominal voucher (Wajib, angka > 0)</li>
                                <li><strong>Jml Max</strong> - Max penggunaan per voucher (Opsi, default: 1)</li>
                                <li><strong>Tgl Masuk</strong> - Tanggal mulai berlaku (Opsi, format: <strong>YYYY-MM-DD</strong>)</li>
                                <li><strong>Tgl Keluar</strong> - Tanggal berakhir (Opsi, format: <strong>YYYY-MM-DD</strong>)</li>
                                <li><strong>Status</strong> - 0 = Tidak Aktif, 1 = Aktif (Opsi, default: 1)</li>
                                <li><strong>Keterangan</strong> - Catatan (Opsi)</li>
                            </ul>
                        </li>
                        <li>Upload file Excel dengan klik tombol <strong>"Pilih File"</strong></li>
                        <li>Klik <strong>"Import Data"</strong> untuk memproses</li>
                    </ol>
                </div>

                <?= form_open_multipart('master/voucher/import', ['class' => 'needs-validation', 'novalidate' => '']) ?>
                
                <div class="form-group">
                    <label>File Excel <span class="text-danger">*</span></label>
                    <div class="custom-file">
                        <?= form_upload([
                            'name' => 'excel_file',
                            'id' => 'excel_file',
                            'class' => 'custom-file-input',
                            'accept' => '.xlsx,.xls',
                            'required' => true
                        ]) ?>
                        <label class="custom-file-label" for="excel_file">Pilih file Excel...</label>
                    </div>
                    <small class="form-text text-muted">
                        Format file: .xlsx atau .xls | Maksimal 5MB
                    </small>
                    <div class="invalid-feedback">
                        File Excel harus diupload!
                    </div>
                </div>

                <div class="card-footer text-right">
                    <a href="<?= base_url('master/voucher') ?>" class="btn btn-default rounded-0">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                    <a href="<?= base_url('master/voucher/template') ?>" class="btn btn-info rounded-0">
                        <i class="fas fa-download mr-2"></i> Download Template
                    </a>
                    <button type="submit" class="btn btn-success rounded-0">
                        <i class="fas fa-file-import mr-2"></i> Import Data
                    </button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Bootstrap validation
    $('.needs-validation').on('submit', function(event) {
        if (!this.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        $(this).addClass('was-validated');
    });

    // Custom file input label
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });
});
</script>

<?= $this->endSection() ?>
