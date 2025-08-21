<?= $this->extend(theme_path('main')) ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Edit Voucher</h3>
                <div class="card-tools">
                    <a href="<?= base_url('master/voucher') ?>" class="btn btn-sm btn-secondary rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <?= form_open('master/voucher/store', ['id' => 'editVoucherForm', 'csrf' => false]) ?>
            <?= form_hidden('id', $voucher->id) ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kode Voucher <span class="text-danger">*</span></label>
                            <?= form_input([
                                'type' => 'text',
                                'name' => 'kode',
                                'class' => 'form-control rounded-0',
                                'value' => old('kode', $voucher->kode),
                                'placeholder' => 'Kode voucher'
                            ]) ?>
                        </div>
                        
                        <div class="form-group">
                            <label>Jumlah Voucher <span class="text-danger">*</span></label>
                            <?= form_input([
                                'type' => 'number',
                                'name' => 'jml',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Masukkan jumlah voucher',
                                'min' => '1',
                                'value' => old('jml', $voucher->jml)
                            ]) ?>
                            <small class="text-muted">Berapa banyak voucher yang akan dibuat (Min: 1)</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Jenis Voucher <span class="text-danger">*</span></label>
                            <div class="custom-control custom-radio">
                                <?= form_radio([
                                    'name' => 'jenis_voucher',
                                    'id' => 'jenis_nominal',
                                    'value' => 'nominal',
                                    'checked' => old('jenis_voucher', $voucher->jenis_voucher) == 'nominal',
                                    'class' => 'custom-control-input'
                                ]) ?>
                                <label class="custom-control-label" for="jenis_nominal">Nominal (Rp)</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <?= form_radio([
                                    'name' => 'jenis_voucher',
                                    'id' => 'jenis_persen',
                                    'value' => 'persen',
                                    'checked' => old('jenis_voucher', $voucher->jenis_voucher) == 'persen',
                                    'class' => 'custom-control-input'
                                ]) ?>
                                <label class="custom-control-label" for="jenis_persen">Persentase (%)</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label id="nominalLabel">Nominal Voucher <span class="text-danger">*</span></label>
                            <?= form_input([
                                'type' => 'number',
                                'name' => 'nominal',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Masukkan nominal voucher',
                                'min' => '1',
                                'value' => old('nominal', $voucher->nominal)
                            ]) ?>
                            <small class="text-muted">Nilai nominal atau persentase diskon voucher</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Batas Maksimal Penggunaan <span class="text-danger">*</span></label>
                            <?= form_input([
                                'type' => 'number',
                                'name' => 'jml_max',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Maksimal berapa kali voucher dapat digunakan',
                                'min' => $voucher->jml_keluar,
                                'value' => old('jml_max', $voucher->jml_max)
                            ]) ?>
                            <small class="text-muted">
                                Berapa kali voucher ini dapat digunakan 
                                (Min: <?= $voucher->jml_keluar ?> - sudah digunakan)
                            </small>
                        </div>
                        
                        <?php if ($voucher->jml_keluar > 0): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Voucher ini sudah digunakan <?= $voucher->jml_keluar ?> kali
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Mulai <span class="text-danger">*</span></label>
                            <?= form_input([
                                'type' => 'date',
                                'name' => 'tgl_masuk',
                                'class' => 'form-control rounded-0',
                                'value' => old('tgl_masuk', $voucher->tgl_masuk)
                            ]) ?>
                        </div>
                        
                        <div class="form-group">
                            <label>Tanggal Berakhir <span class="text-danger">*</span></label>
                            <?= form_input([
                                'type' => 'date',
                                'name' => 'tgl_keluar',
                                'class' => 'form-control rounded-0',
                                'value' => old('tgl_keluar', $voucher->tgl_keluar)
                            ]) ?>
                        </div>
                        
                        <div class="form-group">
                            <label>Status</label>
                            <div class="custom-control custom-radio">
                                <?= form_radio([
                                    'name' => 'status',
                                    'id' => 'status_aktif',
                                    'value' => '1',
                                    'checked' => old('status', $voucher->status) == '1',
                                    'class' => 'custom-control-input'
                                ]) ?>
                                <label class="custom-control-label" for="status_aktif">Aktif</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <?= form_radio([
                                    'name' => 'status',
                                    'id' => 'status_nonaktif',
                                    'value' => '0',
                                    'checked' => old('status', $voucher->status) == '0',
                                    'class' => 'custom-control-input'
                                ]) ?>
                                <label class="custom-control-label" for="status_nonaktif">Nonaktif</label>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Statistik Penggunaan</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="text-success">
                                            <h4><?= $voucher->jml_keluar ?></h4>
                                            <small>Terpakai</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-warning">
                                            <h4><?= $voucher->jml_max - $voucher->jml_keluar ?></h4>
                                            <small>Sisa</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-info">
                                            <h4><?= number_format(($voucher->jml_keluar / $voucher->jml_max) * 100, 1) ?>%</h4>
                                            <small>Persentase</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Keterangan</label>
                    <?= form_textarea([
                        'name' => 'keterangan',
                        'class' => 'form-control rounded-0',
                        'rows' => '3',
                        'placeholder' => 'Deskripsi voucher, syarat dan ketentuan, dll...',
                        'value' => old('keterangan', $voucher->keterangan)
                    ]) ?>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary rounded-0" id="submitBtn">
                            <i class="fas fa-save"></i> Update
                        </button>
                        <button type="button" class="btn btn-info rounded-0" onclick="testForm()">
                            <i class="fas fa-bug"></i> Test Form
                        </button>
                        <a href="<?= base_url('master/voucher') ?>" class="btn btn-secondary rounded-0">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </div>
            </div>
            <?= form_close() ?>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
// Global validation function (fallback)
function validateForm() {
    const kode = document.querySelector('input[name="kode"]').value.trim();
    const jml = parseInt(document.querySelector('input[name="jml"]').value);
    const jmlMax = parseInt(document.querySelector('input[name="jml_max"]').value);
    const nominal = parseFloat(document.querySelector('input[name="nominal"]').value);
    const jenisVoucher = document.querySelector('input[name="jenis_voucher"]:checked')?.value;
    const tglMasuk = document.querySelector('input[name="tgl_masuk"]').value;
    const tglKeluar = document.querySelector('input[name="tgl_keluar"]').value;
    
    if (!kode) {
        alert('Kode voucher harus diisi');
        document.querySelector('input[name="kode"]').focus();
        return false;
    }
    
    if (!jml || jml < 1) {
        alert('Jumlah voucher harus lebih dari 0');
        document.querySelector('input[name="jml"]').focus();
        return false;
    }
    
    if (!jmlMax || jmlMax < <?= $voucher->jml_keluar ?>) {
        alert('Batas maksimal tidak boleh kurang dari jumlah yang sudah digunakan (<?= $voucher->jml_keluar ?>)');
        document.querySelector('input[name="jml_max"]').focus();
        return false;
    }
    
    if (!nominal || nominal <= 0) {
        alert('Nominal voucher harus lebih dari 0');
        document.querySelector('input[name="nominal"]').focus();
        return false;
    }
    
    if (jenisVoucher === 'persen' && nominal > 100) {
        alert('Persentase voucher tidak boleh lebih dari 100%');
        document.querySelector('input[name="nominal"]').focus();
        return false;
    }
    
    if (!tglMasuk) {
        alert('Tanggal mulai harus diisi');
        document.querySelector('input[name="tgl_masuk"]').focus();
        return false;
    }
    
    if (!tglKeluar) {
        alert('Tanggal berakhir harus diisi');
        document.querySelector('input[name="tgl_keluar"]').focus();
        return false;
    }
    
    if (tglKeluar <= tglMasuk) {
        alert('Tanggal berakhir harus setelah tanggal mulai');
        document.querySelector('input[name="tgl_keluar"]').focus();
        return false;
    }
    
    return true;
}

// Test function to debug form
function testForm() {
    console.log('Testing form...');
    
    const form = document.getElementById('editVoucherForm');
    const formData = new FormData(form);
    
    console.log('Form action:', form.action);
    console.log('Form method:', form.method);
    
    // Log all form data
    for (let [key, value] of formData.entries()) {
        console.log(key + ':', value);
    }
    
    // Test validation
    if (validateForm()) {
        console.log('Form validation passed');
        alert('Form validation passed! You can now submit the form.');
    } else {
        console.log('Form validation failed');
    }
}

$(document).ready(function() {
    // Function to update nominal field based on voucher type
    function updateNominalField() {
        const jenisVoucher = $('input[name="jenis_voucher"]:checked').val();
        const nominalField = $('input[name="nominal"]');
        const nominalLabel = $('#nominalLabel');
        
        if (jenisVoucher === 'nominal') {
            nominalLabel.html('Nominal Voucher (Rp) <span class="text-danger">*</span>');
            nominalField.attr('placeholder', 'Masukkan nominal voucher dalam Rupiah');
            nominalField.attr('min', '1000');
            nominalField.attr('step', '1000');
        } else if (jenisVoucher === 'persen') {
            nominalLabel.html('Persentase Voucher (%) <span class="text-danger">*</span>');
            nominalField.attr('placeholder', 'Masukkan persentase diskon (1-100)');
            nominalField.attr('min', '1');
            nominalField.attr('max', '100');
            nominalField.attr('step', '1');
        }
    }
    
    // Update on page load
    updateNominalField();
    
    // Update when radio button changes
    $('input[name="jenis_voucher"]').on('change', function() {
        updateNominalField();
    });
    
    // Form validation
    $('#editVoucherForm').on('submit', function(e) {
        // Remove preventDefault to allow normal form submission
        // e.preventDefault();
        
        console.log('Form submission started...');
        
        // Basic validation
        const kode = $('input[name="kode"]').val().trim();
        const jml = parseInt($('input[name="jml"]').val());
        const jmlMax = parseInt($('input[name="jml_max"]').val());
        const nominal = parseFloat($('input[name="nominal"]').val());
        const jenisVoucher = $('input[name="jenis_voucher"]:checked').val();
        const tglMasuk = $('input[name="tgl_masuk"]').val();
        const tglKeluar = $('input[name="tgl_keluar"]').val();
        
        console.log('Form values:', { kode, jml, jmlMax, nominal, jenisVoucher, tglMasuk, tglKeluar });
        
        // Validation checks
        if (!kode) {
            if (typeof toastr !== 'undefined') {
                toastr.error('Kode voucher harus diisi');
            } else {
                alert('Kode voucher harus diisi');
            }
            $('input[name="kode"]').focus();
            return false;
        }
        
        if (!jml || jml < 1) {
            if (typeof toastr !== 'undefined') {
                toastr.error('Jumlah voucher harus lebih dari 0');
            } else {
                alert('Jumlah voucher harus lebih dari 0');
            }
            $('input[name="jml"]').focus();
            return false;
        }
        
        if (!jmlMax || jmlMax < <?= $voucher->jml_keluar ?>) {
            if (typeof toastr !== 'undefined') {
                toastr.error('Batas maksimal tidak boleh kurang dari jumlah yang sudah digunakan (<?= $voucher->jml_keluar ?>)');
            } else {
                alert('Batas maksimal tidak boleh kurang dari jumlah yang sudah digunakan (<?= $voucher->jml_keluar ?>)');
            }
            $('input[name="jml_max"]').focus();
            return false;
        }
        
        if (!nominal || nominal <= 0) {
            if (typeof toastr !== 'undefined') {
                toastr.error('Nominal voucher harus lebih dari 0');
            } else {
                alert('Nominal voucher harus lebih dari 0');
            }
            $('input[name="nominal"]').focus();
            return false;
        }
        
        if (jenisVoucher === 'persen' && nominal > 100) {
            if (typeof toastr !== 'undefined') {
                toastr.error('Persentase voucher tidak boleh lebih dari 100%');
            } else {
                alert('Persentase voucher tidak boleh lebih dari 100%');
            }
            $('input[name="nominal"]').focus();
            return false;
        }
        
        if (!tglMasuk) {
            if (typeof toastr !== 'undefined') {
                toastr.error('Tanggal mulai harus diisi');
            } else {
                alert('Tanggal mulai harus diisi');
            }
            $('input[name="tgl_masuk"]').focus();
            return false;
        }
        
        if (!tglKeluar) {
            if (typeof toastr !== 'undefined') {
                toastr.error('Tanggal berakhir harus diisi');
            } else {
                alert('Tanggal berakhir harus diisi');
            }
            $('input[name="tgl_keluar"]').focus();
            return false;
        }
        
        if (tglKeluar <= tglMasuk) {
            if (typeof toastr !== 'undefined') {
                toastr.error('Tanggal berakhir harus setelah tanggal mulai');
            } else {
                alert('Tanggal berakhir harus setelah tanggal mulai');
            }
            $('input[name="tgl_keluar"]').focus();
            return false;
        }
        
        console.log('Validation passed, submitting form...');
        
        // If validation passes, show loading state and submit the form
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
        
        // Test form submission
        console.log('About to submit form to:', this.action);
        
        // Submit the form
        // this.submit();
    });
    
    // Reset button state if form validation fails
    $(document).on('invalid', function(e) {
        const submitBtn = $('#submitBtn');
        submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Update');
    });
});
</script>
<?= $this->endSection() ?>