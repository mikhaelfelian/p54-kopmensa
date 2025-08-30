<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card rounded-0">
            <div class="card-header rounded-0">
                <h3 class="card-title">
                    <i class="fas fa-plus mr-2"></i>
                    Form Kategori
                </h3>
            </div>
            <?= form_open('transaksi/petty/category/store', ['id' => 'categoryForm']) ?>
                <div class="card-body rounded-0">
                    <div class="form-group">
                        <label for="kode">Kode Kategori <span class="text-danger">*</span></label>
                        <?= form_input([
                            'type' => 'text',
                            'class' => 'form-control rounded-0',
                            'id' => 'kode',
                            'name' => 'kode',
                            'value' => old('kode'),
                            'placeholder' => 'Contoh: OPR, ADM, MKT',
                            'maxlength' => 10,
                            'required' => 'required'
                        ]) ?>
                        <small class="form-text text-muted">
                            Kode unik untuk kategori (2-10 karakter, huruf kapital)
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="nama">Nama Kategori <span class="text-danger">*</span></label>
                        <?= form_input([
                            'type' => 'text',
                            'class' => 'form-control rounded-0',
                            'id' => 'nama',
                            'name' => 'nama',
                            'value' => old('nama'),
                            'placeholder' => 'Contoh: Operasional, Administrasi, Marketing',
                            'maxlength' => 100,
                            'required' => 'required'
                        ]) ?>
                        <small class="form-text text-muted">
                            Nama lengkap kategori (3-100 karakter)
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <?= form_textarea([
                            'class' => 'form-control rounded-0',
                            'id' => 'deskripsi',
                            'name' => 'deskripsi',
                            'rows' => 3,
                            'placeholder' => 'Penjelasan detail kategori (opsional)'
                        ], (string) old('deskripsi')) ?>
                        <small class="form-text text-muted">
                            Deskripsi tambahan untuk kategori (maksimal 255 karakter)
                        </small>
                    </div>
                </div>

                <div class="card-footer rounded-0">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="<?= base_url('transaksi/petty/category') ?>" class="btn btn-secondary rounded-0">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="reset" class="btn btn-warning rounded-0">
                                <i class="fas fa-undo mr-2"></i>
                                Reset
                            </button>
                            <button type="submit" class="btn btn-primary rounded-0">
                                <i class="fas fa-save mr-2"></i>
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
            <?= form_close() ?>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card rounded-0">
            <div class="card-header rounded-0">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Petunjuk
                </h3>
            </div>
            <div class="card-body rounded-0">
                <h6>Contoh Kategori Umum:</h6>
                <ul class="list-unstyled">
                    <li><strong>OPR</strong> - Operasional</li>
                    <li><strong>ADM</strong> - Administrasi</li>
                    <li><strong>MKT</strong> - Marketing</li>
                    <li><strong>HRD</strong> - Human Resource</li>
                    <li><strong>IT</strong> - Information Technology</li>
                    <li><strong>MAINT</strong> - Maintenance</li>
                    <li><strong>UTIL</strong> - Utilities</li>
                </ul>
                
                <hr>
                
                <h6>Tips:</h6>
                <ul class="list-unstyled">
                    <li>• Gunakan kode yang mudah diingat</li>
                    <li>• Nama kategori harus jelas dan spesifik</li>
                    <li>• Deskripsi membantu user memahami kategori</li>
                    <li>• Kode akan otomatis dikapitalkan</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function () {
        // Auto-capitalize kode input
        $('#kode').on('input', function () {
            $(this).val($(this).val().toUpperCase());
        });

        // Form validation
        $('#categoryForm').on('submit', function (e) {
            var kode = $('#kode').val().trim();
            var nama = $('#nama').val().trim();

            if (kode.length < 2) {
                e.preventDefault();
                alert('Kode kategori minimal 2 karakter');
                $('#kode').focus();
                return false;
            }

            if (nama.length < 3) {
                e.preventDefault();
                alert('Nama kategori minimal 3 karakter');
                $('#nama').focus();
                return false;
            }
        });

        // Auto-focus on kode field
        $('#kode').focus();
    });
</script>
<?= $this->endSection() ?>