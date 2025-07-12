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
<div class="row">
    <div class="col-md-6">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Purchase Order</h3>
            </div>
            <?= form_open('transaksi/po/store', ['id' => 'form-po']) ?>
            <div class="card-body">
                <!-- Supplier -->
                <div class="form-group">
                    <label>Nama Supplier<span class="text-danger">*</span></label>
                    <select name="supplier_id"
                        class="form-control rounded-0 select2 <?= validation_show_error('supplier_id') ? 'is-invalid' : '' ?>">
                        <option value="">Pilih Nama Supplier</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= $supplier->id ?>" <?= old('supplier_id') == $supplier->id ? 'selected' : '' ?>>
                                <?= esc($supplier->nama) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                    <div class="invalid-feedback">
                        <?= validation_show_error('supplier_id') ?>
                    </div>
                </div>

                <!-- Tanggal PO -->
                <div class="form-group">
                    <label>Tgl PO</label>
                    <input type="date" name="tgl_po"
                        class="form-control rounded-0<?= validation_show_error('tgl_po') ? ' is-invalid' : '' ?>"
                        value="<?= old('tgl_po', date('Y-m-d')) ?>">
                    <div class="invalid-feedback">
                        <?= validation_show_error('tgl_po') ?>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control rounded-0" rows="3"
                        placeholder="Masukkan keterangan..."><?= old('keterangan') ?></textarea>
                </div>

                <!-- Alamat Pengiriman -->
                <div class="form-group">
                    <label>Alamat Pengiriman<span class="text-danger">*</span></label>
                    <?= form_textarea(['name' => 'alamat_pengiriman', 'class' => 'form-control rounded-0' . (validation_show_error('alamat_pengiriman') ? ' is-invalid' : ''), 'rows' => 3, 'placeholder' => 'Masukkan alamat pengiriman...', 'value' => old('alamat_pengiriman')]) ?>
                    <div class="invalid-feedback">
                        <?= validation_show_error('alamat_pengiriman') ?>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?= base_url('transaksi/po') ?>" class="btn btn-default rounded-0">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary rounded-0 float-right">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<?= $this->section('js') ?>
<script>
    $(document).ready(function () {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    });
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>