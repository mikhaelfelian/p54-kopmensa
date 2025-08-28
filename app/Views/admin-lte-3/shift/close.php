<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-stop"></i> Tutup Shift: <?= $shift['shift_code'] ?>
        </h3>
        <div class="card-tools">
            <a href="<?= base_url('transaksi/shift') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <form action="<?= base_url('transaksi/shift/close') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="shift_id" value="<?= $shift['id'] ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="counted_cash">Uang yang Dihitung (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="counted_cash" id="counted_cash" class="form-control" 
                                       step="0.01" min="0" required placeholder="0.00">
                                <small class="form-text text-muted">Jumlah uang yang sebenarnya ada di kasir</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expected_cash">Uang yang Diharapkan (Rp)</label>
                                <input type="text" id="expected_cash" class="form-control" 
                                       value="<?= number_format($shift['expected_cash'], 2) ?>" readonly>
                                <small class="form-text text-muted">Opening Float + Sales Cash + Petty Cash</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes">Catatan</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" 
                                  placeholder="Catatan tambahan (opsional)"></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Apakah Anda yakin ingin menutup shift ini?')">
                            <i class="fas fa-stop"></i> Tutup Shift
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-md-4">
                <table class="table table-sm">
                    <tr>
                        <td>Shift Code:</td>
                        <td><strong><?= $shift['shift_code'] ?></strong></td>
                    </tr>
                    <tr>
                        <td>Outlet:</td>
                        <td><?= $shift['outlet_name'] ?? 'N/A' ?></td>
                    </tr>
                    <tr>
                        <td>Dibuka Oleh:</td>
                        <td><?= ($shift['user_open_name'] ?? '') . ' ' . ($shift['user_open_lastname'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td>Waktu Buka:</td>
                        <td><?= date('d/m/Y H:i', strtotime($shift['start_at'])) ?></td>
                    </tr>
                    <tr>
                        <td>Opening Float:</td>
                        <td class="text-right"><strong><?= number_format($shift['open_float'], 2) ?></strong></td>
                    </tr>
                    <tr>
                        <td>Sales Cash:</td>
                        <td class="text-right"><?= number_format($shift['sales_cash_total'], 2) ?></td>
                    </tr>
                    <tr>
                        <td>Petty Cash IN:</td>
                        <td class="text-right text-success"><?= number_format($shift['petty_in_total'], 2) ?></td>
                    </tr>
                    <tr>
                        <td>Petty Cash OUT:</td>
                        <td class="text-right text-danger"><?= number_format($shift['petty_out_total'], 2) ?></td>
                    </tr>
                    <tr class="table-info">
                        <td><strong>Total Diharapkan:</strong></td>
                        <td class="text-right"><strong><?= number_format($shift['expected_cash'], 2) ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Format currency input
    $('#counted_cash').on('input', function() {
        let value = $(this).val();
        if (value && !isNaN(value)) {
            $(this).val(parseFloat(value).toFixed(2));
        }
    });

    // Calculate difference when counted cash changes
    $('#counted_cash').on('change', function() {
        let counted = parseFloat($(this).val()) || 0;
        let expected = parseFloat('<?= $shift['expected_cash'] ?>') || 0;
        let difference = counted - expected;
        
        // Show difference alert
        if (Math.abs(difference) > 0.01) {
            let alertClass = difference > 0 ? 'alert-success' : 'alert-danger';
            let alertText = difference > 0 ? 'Lebih' : 'Kurang';
            
            if (!$('#difference-alert').length) {
                $('#counted_cash').after(`
                    <div id="difference-alert" class="alert ${alertClass} alert-sm mt-2">
                        <i class="icon fas fa-info"></i> ${alertText}: Rp ${Math.abs(difference).toFixed(2)}
                    </div>
                `);
            } else {
                $('#difference-alert').removeClass('alert-success alert-danger')
                    .addClass(alertClass)
                    .html(`<i class="icon fas fa-info"></i> ${alertText}: Rp ${Math.abs(difference).toFixed(2)}`);
            }
        } else {
            $('#difference-alert').remove();
        }
    });
});
</script>
<?= $this->endSection() ?>
