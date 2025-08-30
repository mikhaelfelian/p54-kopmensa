<?= $this->extend(theme_path('main')) ?>
<?= $this->section('content') ?>
<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: <?= date('Y-m-d') ?>

 * Github: github.com/mikhaelfelian
 * description: Create view for Petty Cash transaction
 * This file represents the View.
 */
helper('form');
?>
<div class="card rounded-0">
    <div class="card-header rounded-0">
        <h3 class="card-title">
            <i class="fas fa-plus mr-2"></i>
            Form Petty Cash
        </h3>
    </div>
    <?= form_open('transaksi/petty/store', ['id' => 'pettyCashForm']) ?>
        <div class="card-body rounded-0">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="outlet_id">Outlet <span class="text-danger">*</span></label>
                        <select class="form-control select2 rounded-0" id="outlet_id" name="outlet_id" required>
                            <option value="">Pilih Outlet</option>
                            <?php foreach ($outlets as $outlet): ?>
                                <option value="<?= $outlet->id ?>" <?= session()->get('kasir_outlet') == $outlet->id ? 'selected' : '' ?>>
                                    <?= $outlet->nama ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Kategori</label>
                        <select class="form-control select2 rounded-0" id="category_id" name="category_id">
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category->id ?>" <?= old('category_id') == $category->id ? 'selected' : '' ?>>
                                    <?= $category->kode ?> - <?= $category->nama ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tgl_transaksi">Tanggal Transaksi <span class="text-danger">*</span></label>
                        <input type="date" class="form-control rounded-0" id="tgl_transaksi" name="tgl_transaksi" 
                               value="<?= old('tgl_transaksi', date('Y-m-d')) ?>" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="direction">Jenis Transaksi <span class="text-danger">*</span></label>
                        <select class="form-control rounded-0" id="direction" name="direction" required>
                            <option value="">Pilih Jenis</option>
                            <option value="IN" <?= old('direction') == 'IN' ? 'selected' : '' ?>>Masuk</option>
                            <option value="OUT" <?= old('direction') == 'OUT' ? 'selected' : '' ?>>Keluar</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="amount">Nominal <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-0" id="amount" name="amount" 
                               placeholder="0" required>
                        <small class="form-text text-muted">Minimal Rp 1.000</small>
                    </div>

                    <div class="form-group">
                        <label for="reason">Keterangan <span class="text-danger">*</span></label>
                        <textarea class="form-control rounded-0" id="reason" name="reason" rows="3" 
                                  placeholder="Jelaskan detail transaksi..." required><?= old('reason', '') ?></textarea>
                        <small class="form-text text-muted">Minimal 10 karakter</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer rounded-0">
            <div class="row">
                <div class="col-md-6">
                    <a href="<?= base_url('transaksi/petty') ?>" class="btn btn-secondary rounded-0">
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
<?= $this->endSection() ?>
<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    // Initialize AutoNumeric for amount input
    $('#amount').autoNumeric('init', {
        aSep: '.',
        aDec: ',',
    });

    // Form validation
    $('#pettyCashForm').on('submit', function(e) {
        var amount = $('#amount').autoNumeric('get');
        
        if (amount < 1000) {
            e.preventDefault();
            alert('Nominal minimal Rp 1.000');
            $('#amount').focus();
            return false;
        }

        if ($('#reason').val().length < 10) {
            e.preventDefault();
            alert('Keterangan minimal 10 karakter');
            $('#reason').focus();
            return false;
        }
    });

    // Auto-fill today's date if empty
    if (!$('#tgl_transaksi').val()) {
        $('#tgl_transaksi').val(new Date().toISOString().split('T')[0]);
    }
});
</script>
<?= $this->endSection() ?>
