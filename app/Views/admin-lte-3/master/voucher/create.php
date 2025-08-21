<?= $this->extend(theme_path('main')) ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Tambah Voucher</h3>
                <div class="card-tools">
                    <a href="<?= base_url('master/voucher') ?>" class="btn btn-sm btn-secondary rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <?= form_open(base_url('master/voucher/store'), ['csrf' => false]) ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kode Voucher <span class="text-danger">*</span></label>
                            <?= form_input([
                                'type' => 'text',
                                'name' => 'kode',
                                'class' => 'form-control rounded-0',
                                'value' => $kode,
                                'placeholder' => 'Kode voucher otomatis'
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
                                'value' => old('jml')
                            ]) ?>
                            <small class="text-muted">Berapa banyak voucher yang akan dibuat</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Jenis Voucher <span class="text-danger">*</span></label>
                            <div class="custom-control custom-radio">
                                <?= form_radio([
                                    'name' => 'jenis_voucher',
                                    'id' => 'jenis_nominal',
                                    'value' => 'nominal',
                                    'checked' => old('jenis_voucher', 'nominal') == 'nominal',
                                    'class' => 'custom-control-input'
                                ]) ?>
                                <label class="custom-control-label" for="jenis_nominal">Nominal (Rp)</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <?= form_radio([
                                    'name' => 'jenis_voucher',
                                    'id' => 'jenis_persen',
                                    'value' => 'persen',
                                    'checked' => old('jenis_voucher') == 'persen',
                                    'class' => 'custom-control-input'
                                ]) ?>
                                <label class="custom-control-label" for="jenis_persen">Persentase (%)</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Nominal Voucher <span class="text-danger">*</span></label>
                            <?= form_input([
                                'type' => 'number',
                                'name' => 'nominal',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Masukkan nominal voucher',
                                'min' => '1',
                                'value' => old('nominal')
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
                                'min' => '1',
                                'value' => old('jml_max')
                            ]) ?>
                            <small class="text-muted">Berapa kali voucher ini dapat digunakan</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Mulai <span class="text-danger">*</span></label>
                            <?= form_input([
                                'type' => 'date',
                                'name' => 'tgl_masuk',
                                'class' => 'form-control rounded-0',
                                'value' => old('tgl_masuk') ?: date('Y-m-d')
                            ]) ?>
                        </div>
                        
                        <div class="form-group">
                            <label>Tanggal Berakhir <span class="text-danger">*</span></label>
                            <?= form_input([
                                'type' => 'date',
                                'name' => 'tgl_keluar',
                                'class' => 'form-control rounded-0',
                                'value' => old('tgl_keluar')
                            ]) ?>
                        </div>
                        
                        <div class="form-group">
                            <label>Status</label>
                            <div class="custom-control custom-radio">
                                <?= form_radio([
                                    'name' => 'status',
                                    'id' => 'status_aktif',
                                    'value' => '1',
                                    'checked' => old('status', '1') == '1',
                                    'class' => 'custom-control-input'
                                ]) ?>
                                <label class="custom-control-label" for="status_aktif">Aktif</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <?= form_radio([
                                    'name' => 'status',
                                    'id' => 'status_nonaktif',
                                    'value' => '0',
                                    'checked' => old('status') == '0',
                                    'class' => 'custom-control-input'
                                ]) ?>
                                <label class="custom-control-label" for="status_nonaktif">Nonaktif</label>
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
                        'value' => old('keterangan')
                    ]) ?>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary rounded-0">
                            <i class="fas fa-save"></i> Simpan
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
$(document).ready(function() {
    // Function to update nominal field based on voucher type
    function updateNominalField() {
        const jenisVoucher = $('input[name="jenis_voucher"]:checked').val();
        const nominalField = $('input[name="nominal"]');
        const nominalLabel = $('label[for="nominal"]');
        
        if (jenisVoucher === 'nominal') {
            nominalLabel.text('Nominal Voucher (Rp)');
            nominalField.attr('placeholder', 'Masukkan nominal voucher dalam Rupiah');
            nominalField.attr('min', '1000');
            nominalField.attr('step', '1000');
        } else if (jenisVoucher === 'persen') {
            nominalLabel.text('Persentase Voucher (%)');
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
});
</script>
<?= $this->endSection() ?>