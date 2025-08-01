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
            <?= form_open('master/voucher/store') ?>
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
                                'readonly' => true,
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