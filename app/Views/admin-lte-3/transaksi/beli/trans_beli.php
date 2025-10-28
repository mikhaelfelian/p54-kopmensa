<?php
/**
 * Created by: Mikhael Felian Waskito
 * Modified: 2025-01-29
 * 
 * Purchase Transaction Create View (Faktur Pembelian)
 * Modern POS-Style with AdminLTE 3
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-receipt"></i> Buat Faktur Pembelian</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('transaksi/beli') ?>">Faktur Pembelian</a></li>
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

        <?= form_open('transaksi/beli/store', ['id' => 'form-faktur', 'autocomplete' => 'off']) ?>
        <div class="row">
            <!-- Left Panel - Form Input -->
            <div class="col-lg-8">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-edit mr-2"></i>Informasi Faktur</h3>
                        <div class="card-tools">
                            <span class="badge badge-success">Draft</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Purchase Order Reference (Optional) -->
                        <div class="alert alert-info py-2 px-3 mb-3">
                            <i class="fas fa-info-circle mr-1"></i>
                            <small><strong>Opsional:</strong> Pilih PO jika faktur ini merupakan lanjutan dari Purchase Order</small>
                        </div>

                        <div class="form-group">
                            <label for="id_po">Referensi Purchase Order</label>
                            <select name="id_po" id="id_po" class="form-control select2">
                                <option value="">-- Tidak ada / Buat faktur langsung --</option>
                                <?php foreach ($po_list as $po): ?>
                                    <option value="<?= $po->id ?>" 
                                        data-supplier="<?= $po->id_supplier ?>"
                                        data-supplier-nama="<?= esc($po->supplier) ?>"
                                        data-no-po="<?= $po->no_nota ?>"
                                        <?= (old('id_po') == $po->id || ($selected_po && $selected_po->id == $po->id)) ? 'selected' : '' ?>>
                                        [<?= esc($po->no_nota) ?>] <?= esc($po->supplier) ?> - <?= date('d/m/Y', strtotime($po->tgl_masuk)) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <small class="form-text text-muted">Jika dipilih, supplier dan item akan otomatis terisi</small>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Supplier -->
                                <div class="form-group">
                                    <label for="id_supplier">Supplier <span class="text-danger">*</span></label>
                                    <select name="id_supplier" id="id_supplier" class="form-control select2" required>
                                        <option value="">-- Pilih Supplier --</option>
                                        <?php foreach ($suppliers as $supplier): ?>
                                            <option value="<?= $supplier->id ?>" <?= old('id_supplier') == $supplier->id ? 'selected' : '' ?>>
                                                [<?= esc($supplier->kode) ?>] <?= esc($supplier->nama) ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                    <small class="form-text text-danger">* Wajib diisi</small>
                                </div>

                                <!-- Tanggal Faktur -->
                                <div class="form-group">
                                    <label for="tgl_masuk">Tanggal Faktur <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="tgl_masuk" id="tgl_masuk"
                                            class="form-control"
                                            value="<?= old('tgl_masuk', date('Y-m-d')) ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- No. Faktur -->
                                <div class="form-group">
                                    <label for="no_nota">No. Faktur <span class="text-danger">*</span></label>
                                    <input type="text" name="no_nota" id="no_nota"
                                        class="form-control"
                                        placeholder="Masukkan nomor faktur supplier"
                                        value="<?= old('no_nota') ?>" required>
                                    <small class="form-text text-muted">Nomor faktur dari supplier</small>
                                </div>

                                <!-- Tanggal Jatuh Tempo -->
                                <div class="form-group">
                                    <label for="tgl_keluar">Tanggal Jatuh Tempo</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-check"></i></span>
                                        </div>
                                        <input type="date" name="tgl_keluar" id="tgl_keluar"
                                            class="form-control"
                                            value="<?= old('tgl_keluar') ?>">
                                    </div>
                                    <small class="form-text text-muted">Opsional - untuk tracking pembayaran</small>
                                </div>
                            </div>
                        </div>

                        <!-- No. PO Reference (Read-only from selected PO) -->
                        <div class="form-group" id="no-po-group" style="display: none;">
                            <label for="no_po">No. PO Referensi</label>
                            <input type="text" id="no_po" class="form-control bg-light" readonly>
                        </div>

                        <!-- PPN Status -->
                        <div class="form-group">
                            <label>Status PPN <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" name="status_ppn" id="ppn_exclude" value="1"
                                            <?= old('status_ppn', '1') == '1' ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="ppn_exclude">
                                            <i class="fas fa-minus-circle text-warning mr-1"></i>Exclude PPN
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" name="status_ppn" id="ppn_include" value="2"
                                            <?= old('status_ppn') == '2' ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="ppn_include">
                                            <i class="fas fa-check-circle text-success mr-1"></i>Include PPN
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="<?= base_url('transaksi/beli') ?>" class="btn btn-default">
                            <i class="fas fa-arrow-left mr-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-success float-right" id="btn-submit">
                            <i class="fas fa-save mr-1"></i> Simpan & Tambah Item
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Info & Help -->
            <div class="col-lg-4">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Petunjuk Penggunaan</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-2"><i class="fas fa-lightbulb mr-2 text-warning"></i><strong>Langkah-langkah:</strong></p>
                        <ol class="pl-3 mb-3 small">
                            <li>Pilih <strong>Purchase Order</strong> (opsional) atau langsung pilih supplier</li>
                            <li>Isi <strong>No. Faktur</strong> dari supplier</li>
                            <li>Tentukan <strong>Tanggal Faktur</strong></li>
                            <li>Pilih status <strong>PPN</strong></li>
                            <li>Klik <strong>Simpan & Tambah Item</strong></li>
                            <li>Tambahkan detail item pembelian</li>
                        </ol>
                        <div class="alert alert-warning mb-0 py-2 px-3">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <small>Pastikan data sudah benar sebelum menyimpan</small>
                        </div>
                    </div>
                </div>

                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-tag mr-2"></i>Status PPN</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-2">
                                <span class="badge badge-warning">Exclude PPN</span><br>
                                <small class="text-muted">Harga belum termasuk PPN (akan ditambahkan 11%)</small>
                            </li>
                            <li class="mb-0">
                                <span class="badge badge-success">Include PPN</span><br>
                                <small class="text-muted">Harga sudah termasuk PPN (tidak ada penambahan)</small>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</section>

<?= $this->section('js') ?>
<script>
    $(document).ready(function () {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            allowClear: true
        });

        // Handle PO selection
        $('#id_po').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const supplierId = selectedOption.data('supplier');
            const supplierNama = selectedOption.data('supplier-nama');
            const noPO = selectedOption.data('no-po');

            if (supplierId) {
                // Auto-fill supplier
                $('#id_supplier').val(supplierId).trigger('change');
                $('#id_supplier').prop('disabled', true);

                // Show and fill PO number
                $('#no_po').val(noPO);
                $('#no-po-group').slideDown();

                toastr.success(`Supplier "${supplierNama}" telah dipilih otomatis dari PO`, 'PO Dipilih');
            } else {
                // Enable supplier selection
                $('#id_supplier').prop('disabled', false);
                $('#no-po-group').slideUp();
                $('#no_po').val('');
            }
        });

        // Trigger on page load if PO is pre-selected
        if ($('#id_po').val()) {
            $('#id_po').trigger('change');
        }

        // Form validation
        $('#form-faktur').on('submit', function(e) {
            let isValid = true;
            let errorMsg = '';

            // Validate supplier
            if (!$('#id_supplier').val()) {
                isValid = false;
                errorMsg += '- Supplier harus dipilih<br>';
                $('#id_supplier').addClass('is-invalid');
            } else {
                $('#id_supplier').removeClass('is-invalid');
            }

            // Validate no faktur
            if (!$('#no_nota').val().trim()) {
                isValid = false;
                errorMsg += '- No. Faktur harus diisi<br>';
                $('#no_nota').addClass('is-invalid');
            } else {
                $('#no_nota').removeClass('is-invalid');
            }

            if (!isValid) {
                e.preventDefault();
                toastr.error(errorMsg, 'Validasi Gagal');
                return false;
            }

            // Show loading
            $('#btn-submit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');
        });

        // Auto-dismiss alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>

