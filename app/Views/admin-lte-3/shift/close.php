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
                <form action="<?= base_url('transaksi/shift/close') ?>" method="post" id="closeShiftForm">
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
                        <textarea name="notes" id="notes" class="form-control" rows="2" 
                                  placeholder="Catatan tambahan (opsional)"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="catatan_shift">Catatan Shift</label>
                        <textarea name="catatan_shift" id="catatan_shift" class="form-control" rows="3" 
                                  placeholder="Catatan khusus untuk shift ini (opsional)"></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Apakah Anda yakin ingin menutup shift ini?')">
                            <i class="fas fa-stop"></i> Tutup Shift
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-md-4">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Ringkasan Shift</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped">
                            <tr>
                                <td width="40%">Kode Shift:</td>
                                <td><strong><?= esc($shift['shift_code']) ?></strong></td>
                            </tr>
                            <tr>
                                <td>Outlet:</td>
                                <td><?= esc($shift['outlet_name'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <td>Kasir:</td>
                                <td><?= esc(($shift['user_open_name'] ?? '') . ' ' . ($shift['user_open_lastname'] ?? '')) ?></td>
                            </tr>
                            <tr>
                                <td>Waktu Buka:</td>
                                <td><?= date('d/m/Y H:i', strtotime($shift['start_at'])) ?></td>
                            </tr>
                            <tr class="bg-light">
                                <td colspan="2" class="text-center"><strong>RINGKASAN KEUANGAN</strong></td>
                            </tr>
                            <tr>
                                <td>Uang Modal (Opening Float):</td>
                                <td class="text-right"><strong class="text-primary"><?= format_angka($shift['open_float'], 0) ?></strong></td>
                            </tr>
                            <tr>
                                <td>Total Transaksi:</td>
                                <td class="text-right"><strong><?= $transactionStats['total_transactions'] ?? 0 ?></strong></td>
                            </tr>
                            <tr>
                                <td>Total Pendapatan:</td>
                                <td class="text-right"><strong class="text-success"><?= format_angka($transactionStats['total_revenue'] ?? 0, 0) ?></strong></td>
                            </tr>
                            <tr class="bg-light">
                                <td colspan="2" class="text-center"><strong>RINGKASAN METODE PEMBAYARAN</strong></td>
                            </tr>
                            <?php if (!empty($paymentBreakdown['payment_methods'])): ?>
                                <?php 
                                $totalPaymentAmount = 0;
                                $totalPaymentTransactions = 0;
                                foreach ($paymentBreakdown['payment_methods'] as $payment): 
                                    $totalPaymentAmount += (float)($payment['total_amount'] ?? 0);
                                    $totalPaymentTransactions += (int)($payment['transaction_count'] ?? 0);
                                ?>
                                <tr>
                                    <td class="pl-3">
                                        <i class="fas fa-circle" style="font-size: 6px;"></i> 
                                        <?= esc($payment['payment_method_name'] ?? $payment['payment_method_type'] ?? 'Unknown') ?>
                                    </td>
                                    <td class="text-right">
                                        <strong><?= format_angka($payment['total_amount'], 0) ?></strong> 
                                        <br>
                                        <small class="text-muted"><?= $payment['transaction_count'] ?> transaksi</small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="table-info">
                                    <td><strong>Total Semua Pembayaran:</strong></td>
                                    <td class="text-right">
                                        <strong><?= format_angka($totalPaymentAmount, 0) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= $totalPaymentTransactions ?> transaksi</small>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted">- Tidak ada transaksi -</td>
                                </tr>
                            <?php endif; ?>
                            <?php if (($paymentBreakdown['total_refund'] ?? 0) > 0): ?>
                            <tr class="text-danger">
                                <td>Total Refund:</td>
                                <td class="text-right"><strong>-<?= format_angka($paymentBreakdown['total_refund'], 0) ?></strong></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="bg-light">
                                <td colspan="2" class="text-center"><strong>KAS KECIL</strong></td>
                            </tr>
                            <tr>
                                <td>Kas Kecil Masuk:</td>
                                <td class="text-right text-success">+<?= format_angka($shift['petty_in_total'], 0) ?></td>
                            </tr>
                            <tr>
                                <td>Kas Kecil Keluar:</td>
                                <td class="text-right text-danger">-<?= format_angka($shift['petty_out_total'], 0) ?></td>
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
