<?= $this->extend(theme_path('main')) ?>

<?php
// Load shift helper for transaction counting
helper('shift');
?>

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
                                <input type="text" name="counted_cash" id="counted_cash" class="form-control autonumber" 
                                       required placeholder="5.000">
                                <small class="form-text text-muted">Jumlah uang yang sebenarnya ada di kasir</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expected_cash">Uang yang Diharapkan (Rp)</label>
                                <input type="text" id="expected_cash" class="form-control" 
                                       value="<?= format_angka($shift['expected_cash'], 0) ?>" readonly>
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
                        <td>Kode Shift:</td>
                        <td><strong><?= $shift['shift_code'] ?></strong></td>
                    </tr>
                    <tr>
                        <td>Outlet:</td>
                        <td><?= $shift['outlet_name'] ?? 'N/A' ?></td>
                    </tr>
                    <tr>
                        <td>Dibuka oleh:</td>
                        <td><?= ($shift['user_open_name'] ?? '') . ' ' . ($shift['user_open_lastname'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td>Waktu Buka:</td>
                        <td><?= date('d/m/Y H:i', strtotime($shift['start_at'])) ?></td>
                    </tr>
                    <tr>
                        <td>Uang Modal:</td>
                        <td class="text-right"><strong><?= format_angka($shift['open_float'], 0) ?></strong></td>
                    </tr>
                    <tr>
                        <td>Total Transaksi:</td>
                        <td class="text-right"><strong><?= get_shift_transaction_count() ?></strong></td>
                    </tr>
                    <tr>
                        <td>Pendapatan:</td>
                        <td class="text-right"><?= format_angka($salesSummary['total_sales'], 0) ?></td>
                    </tr>
                    <?php if (!empty($salesSummary['payment_methods'])): ?>
                    <tr>
                        <td colspan="2" class="text-center"><strong>Metode Pembayaran:</strong></td>
                    </tr>
                    <?php foreach ($salesSummary['payment_methods'] as $payment): ?>
                    <tr>
                        <td class="pl-4"><?= esc($payment['method']) ?>:</td>
                        <td class="text-right"><?= format_angka($payment['amount'], 0) ?> (<?= $payment['count'] ?>x)</td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <tr>
                        <td>Kas Kecil Masuk:</td>
                        <td class="text-right text-success"><?= format_angka($shift['petty_in_total'], 0) ?></td>
                    </tr>
                    <tr>
                        <td>Kas Kecil Keluar:</td>
                        <td class="text-right text-danger"><?= format_angka($shift['petty_out_total'], 0) ?></td>
                    </tr>
                    <tr class="table-info">
                        <td><strong>Total Diharapkan:</strong></td>
                        <td class="text-right"><strong><?= format_angka($shift['expected_cash'], 0) ?></strong></td>
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
    // Initialize autoNumeric for counted_cash: only allow thousands separator (dot), no decimals, no decimals shown
    $('#counted_cash').autoNumeric('init', {
        aSep: '.',
        aDec: ',',
        mDec: 0, // No decimals
        aPad: false // Do not pad decimals
    });

    // Calculate difference when counted cash changes
    $('#counted_cash').on('change', function() {
        // Get value as integer using autoNumeric
        let counted = parseInt($(this).autoNumeric('get')) || 0;
        let expected = parseInt('<?= (int)$shift['expected_cash'] ?>') || 0;
        let difference = counted - expected;
        // Show difference alert
        if (Math.abs(difference) > 0) {
            let alertClass = difference > 0 ? 'alert-success' : 'alert-danger';
            let alertText = difference > 0 ? 'Lebih' : 'Kurang';
            
            if (!$('#difference-alert').length) {
                $('#counted_cash').after(`
                    <div id="difference-alert" class="alert ${alertClass} alert-sm mt-2">
                        <i class="icon fas fa-info"></i> ${alertText}: Rp ${Math.abs(difference).toLocaleString('id-ID')}
                    </div>
                `);
            } else {
                $('#difference-alert').removeClass('alert-success alert-danger')
                    .addClass(alertClass)
                    .html(`<i class="icon fas fa-info"></i> ${alertText}: Rp ${Math.abs(difference).toLocaleString('id-ID')}`);
            }
        } else {
            $('#difference-alert').remove();
        }
    });

    // Form submission - convert autoNumeric formatted value to integer
    $('#closeShiftForm').on('submit', function(e) {
        var countedCashInput = $('#counted_cash');
        var rawValue = countedCashInput.autoNumeric('get');
        var intValue = parseInt(rawValue.replace(/\./g, '')) || 0;
        countedCashInput.val(intValue);
        return true;
    });
});
</script>
<?= $this->endSection() ?>
