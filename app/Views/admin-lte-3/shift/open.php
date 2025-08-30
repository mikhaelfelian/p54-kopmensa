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
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-play"></i> Buka Shift
                        </button>
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
        aDec: ','
    });

    // Handle form submission to convert autonumber format to decimal
    $('#openShiftForm').on('submit', function(e) {
        console.log('Form submitted');
        
        var openFloatInput = $('#open_float');
        var rawValue = openFloatInput.autoNumeric('get');
        
        console.log('Raw value from autonumber:', rawValue);
        
        // Convert the formatted value to a decimal number for database
        var decimalValue = parseFloat(rawValue.replace(/\./g, '').replace(',', '.'));
        
        console.log('Converted decimal value:', decimalValue);
        
        // Update the input value before submission
        openFloatInput.val(decimalValue);
        
        console.log('Final input value:', openFloatInput.val());
        
        // Let the form submit with the converted value
        return true;
    });
});
</script>
<?= $this->endSection() ?>
