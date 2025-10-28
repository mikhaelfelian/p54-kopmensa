<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-21
 * 
 * Purchase Order Form View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-file-invoice"></i> Buat Purchase Order Baru</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('transaksi/po') ?>">Purchase Order</a></li>
                    <li class="breadcrumb-item active">Buat Baru</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i><strong>Validation Errors:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-edit mr-2"></i>Form Purchase Order</h3>
                        <div class="card-tools">
                            <span class="badge badge-info">Draft</span>
                        </div>
                    </div>
                    <?= form_open('transaksi/po/store', ['id' => 'form-po', 'autocomplete' => 'off']) ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Supplier -->
                                <div class="form-group">
                                    <label for="supplier_id">Supplier <span class="text-danger">*</span></label>
                                    <select name="supplier_id" id="supplier_id"
                                        class="form-control select2 <?= validation_show_error('supplier_id') ? 'is-invalid' : '' ?>"
                                        data-placeholder="Pilih Supplier..." required>
                                        <option value="">-- Pilih Supplier --</option>
                                        <?php foreach ($suppliers as $supplier): ?>
                                            <option value="<?= $supplier->id ?>" 
                                                data-alamat="<?= esc($supplier->alamat ?? '') ?>"
                                                <?= old('supplier_id') == $supplier->id ? 'selected' : '' ?>>
                                                [<?= esc($supplier->kode) ?>] <?= esc($supplier->nama) ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                    <?php if (validation_show_error('supplier_id')): ?>
                                        <small class="text-danger"><?= validation_show_error('supplier_id') ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Tanggal PO -->
                                <div class="form-group">
                                    <label for="tgl_po">Tanggal PO <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="tgl_po" id="tgl_po"
                                            class="form-control <?= validation_show_error('tgl_po') ? 'is-invalid' : '' ?>"
                                            value="<?= old('tgl_po', date('Y-m-d')) ?>" required>
                                        <?php if (validation_show_error('tgl_po')): ?>
                                            <div class="invalid-feedback"><?= validation_show_error('tgl_po') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control" rows="2"
                                placeholder="Catatan atau keterangan tambahan..."><?= old('keterangan') ?></textarea>
                            <small class="form-text text-muted">Opsional - Informasi tambahan terkait PO ini</small>
                        </div>

                        <!-- Alamat Pengiriman -->
                        <div class="form-group">
                            <label for="alamat_pengiriman">Alamat Pengiriman <span class="text-danger">*</span></label>
                            <textarea name="alamat_pengiriman" id="alamat_pengiriman" 
                                class="form-control <?= validation_show_error('alamat_pengiriman') ? 'is-invalid' : '' ?>" 
                                rows="3" placeholder="Masukkan alamat pengiriman lengkap..." required><?= old('alamat_pengiriman') ?></textarea>
                            <?php if (validation_show_error('alamat_pengiriman')): ?>
                                <small class="text-danger"><?= validation_show_error('alamat_pengiriman') ?></small>
                            <?php endif; ?>
                            <small class="form-text text-muted">Maksimal 160 karakter</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="<?= base_url('transaksi/po') ?>" class="btn btn-default">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary float-right" id="btn-submit">
                            <i class="fas fa-save mr-1"></i> Simpan & Lanjutkan
                        </button>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>

            <!-- Info Panel -->
            <div class="col-lg-4">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Informasi</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-2"><i class="fas fa-lightbulb mr-2 text-warning"></i><strong>Petunjuk:</strong></p>
                        <ol class="pl-3 mb-3 small">
                            <li>Pilih supplier yang akan diajukan PO</li>
                            <li>Tentukan tanggal PO</li>
                            <li>Isi alamat pengiriman</li>
                            <li>Klik <strong>Simpan & Lanjutkan</strong></li>
                            <li>Tambahkan item pada halaman edit PO</li>
                        </ol>
                        <div class="alert alert-warning mb-0 py-2 px-3">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <small>Setelah PO disimpan, Anda dapat menambahkan item pembelian</small>
                        </div>
                    </div>
                </div>

                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list-check mr-2"></i>Status PO</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item"><span class="badge badge-secondary">0</span> Draft</li>
                            <li class="list-group-item"><span class="badge badge-info">1</span> Submitted</li>
                            <li class="list-group-item"><span class="badge badge-warning">2</span> Approved</li>
                            <li class="list-group-item"><span class="badge badge-primary">3</span> Ordered</li>
                            <li class="list-group-item"><span class="badge badge-success">4</span> Processed</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->section('js') ?>
<script>
    $(document).ready(function () {
        // Initialize Select2 with search
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Pilih Supplier...',
            allowClear: true
        });

        // Auto-fill alamat pengiriman from supplier data
        $('#supplier_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const alamatSupplier = selectedOption.data('alamat');
            
            if (alamatSupplier && !$('#alamat_pengiriman').val()) {
                $('#alamat_pengiriman').val(alamatSupplier);
                toastr.info('Alamat supplier telah diisi otomatis. Anda dapat mengubahnya jika perlu.');
            }
        });

        // Form validation
        $('#form-po').on('submit', function(e) {
            let isValid = true;
            let errorMsg = '';

            // Validate supplier
            if (!$('#supplier_id').val()) {
                isValid = false;
                errorMsg += '- Supplier harus dipilih<br>';
                $('#supplier_id').addClass('is-invalid');
            } else {
                $('#supplier_id').removeClass('is-invalid');
            }

            // Validate alamat pengiriman
            if (!$('#alamat_pengiriman').val().trim()) {
                isValid = false;
                errorMsg += '- Alamat pengiriman harus diisi<br>';
                $('#alamat_pengiriman').addClass('is-invalid');
            } else if ($('#alamat_pengiriman').val().length > 160) {
                isValid = false;
                errorMsg += '- Alamat pengiriman maksimal 160 karakter<br>';
                $('#alamat_pengiriman').addClass('is-invalid');
            } else {
                $('#alamat_pengiriman').removeClass('is-invalid');
            }

            if (!isValid) {
                e.preventDefault();
                toastr.error(errorMsg, 'Validasi Gagal');
                return false;
            }

            // Show loading
            $('#btn-submit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');
        });

        // Character counter for alamat pengiriman
        $('#alamat_pengiriman').on('input', function() {
            const length = $(this).val().length;
            const max = 160;
            const remaining = max - length;
            
            if (remaining < 20) {
                $(this).next('.form-text').html(`<span class="text-warning">Sisa ${remaining} karakter</span>`);
            } else {
                $(this).next('.form-text').html('Maksimal 160 karakter');
            }

            if (remaining < 0) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>