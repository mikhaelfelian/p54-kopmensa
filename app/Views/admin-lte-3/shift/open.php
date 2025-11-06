<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-play"></i> Buka Shift Baru
        </h3>
        <div class="card-tools">
            <a href="<?= base_url('transaksi/shift') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($activeShift)): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <h5><i class="fas fa-exclamation-triangle"></i> Anda Memiliki Shift Aktif!</h5>
                <p class="mb-2">
                    <strong>Kode Shift:</strong> <?= esc($activeShift['shift_code']) ?><br>
                    <strong>Waktu Buka:</strong> <?= date('d/m/Y H:i', strtotime($activeShift['start_at'])) ?>
                </p>
                <p class="mb-2"><strong>Anda tidak dapat membuka shift baru sampai shift aktif ditutup atau disetujui.</strong></p>
                <div class="mt-2">
                    <a href="<?= base_url('transaksi/shift/continue/' . $activeShift['id']) ?>" 
                       class="btn btn-success btn-sm rounded-0">
                        <i class="fas fa-play"></i> Lanjutkan Shift
                    </a>
                    <a href="<?= base_url('transaksi/shift/close/' . $activeShift['id']) ?>" 
                       class="btn btn-warning btn-sm rounded-0">
                        <i class="fas fa-stop"></i> Tutup Shift
                    </a>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-6">
                <?= form_open('transaksi/shift/open', ['id' => 'openShiftForm']) ?>
                    <div class="form-group">
                        <label for="outlet_id">Outlet <span class="text-danger">*</span></label>
                        <select name="outlet_id" id="outlet_id" class="form-control" required>
                            <option value="">Pilih Outlet</option>
                            <?php 
                                $selectedOutlet = session('kasir_outlet');
                                foreach ($outlets as $id => $name) : 
                            ?>
                                <option value="<?= $id ?>" <?= ($id == $selectedOutlet) ? 'selected' : '' ?>><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="open_float">Uang Modal (Rp) <span class="text-danger">*</span></label>
                        <input type="text" name="open_float" id="open_float" class="form-control autonumber" 
                               required placeholder="5.000">
                        <small class="form-text text-muted">Jumlah uang yang tersedia di kasir saat shift dibuka</small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" <?= !empty($activeShift) ? 'disabled' : '' ?>>
                            <i class="fas fa-play"></i> Buka Shift
                        </button>
                        <?php if (!empty($activeShift)): ?>
                            <small class="form-text text-danger">
                                <i class="fas fa-info-circle"></i> Tutup shift aktif terlebih dahulu untuk membuka shift baru
                            </small>
                        <?php endif; ?>
                    </div>
                <?= form_close() ?>
            </div>

            <div class="col-md-6">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Petunjuk</h5>
                    <ul class="mb-0">
                        <li>Shift harus dibuka sebelum melakukan transaksi</li>
                        <li>Opening float adalah uang yang tersedia di kasir</li>
                        <li>Setelah shift dibuka, semua transaksi akan tercatat</li>
                        <li>Shift dapat ditutup dan disetujui oleh manager</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize autonumber for currency input with 5.000 format
    $('#open_float').autoNumeric('init', {
        aSep: '.',
        aDec: ',',
        mDec: '0'
    });

    // Handle form submission to convert autonumber format to decimal
    $('#openShiftForm').on('submit', function(e) {
        var openFloatInput = $('#open_float');
        var rawValue = openFloatInput.autoNumeric('get');
        // Convert the formatted value to a decimal number for database
        var decimalValue = parseFloat(rawValue.replace(/\./g, '').replace(',', '.'));
        // Update the input value before submission
        openFloatInput.val(decimalValue);
        // Let the form submit with the converted value
        return true;
    });
});
</script>
<?= $this->endSection() ?>
