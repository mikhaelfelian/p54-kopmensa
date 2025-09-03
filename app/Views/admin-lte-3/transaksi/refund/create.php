<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Description: Create view for Refund Requests
 */

helper('form');
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-plus"></i> <?= $title ?>
                </h3>
                <div class="card-tools">
                                         <a href="<?= base_url('transaksi/refund') ?>" class="btn btn-secondary btn-sm rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?= form_open('transaksi/refund/store', ['id' => 'refundForm']) ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_transaction">Pilih Transaksi <span class="text-danger">*</span></label>
                                <select class="form-control select2 rounded-0" id="id_transaction" name="id_transaction" required>
                                    <option value="">Pilih Transaksi</option>
                                    <?php if (empty($salesTransactions)) : ?>
                                        <option value="" disabled>No transactions available</option>
                                    <?php else : ?>
                                        <?php foreach ($salesTransactions as $transaction) : ?>
                                            <option value="<?= $transaction->id ?>" 
                                                    data-amount="<?= $transaction->jml_gtotal ?>"
                                                    data-customer="<?= $transaction->customer_nama ?>">
                                                <?= $transaction->no_nota ?> - <?= $transaction->customer_nama ?> 
                                                (Rp <?= number_format($transaction->jml_gtotal, 0, ',', '.') ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <small class="form-text text-muted">Pilih transaksi yang akan direfund</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_name">Nama Pelanggan</label>
                                <input type="text" class="form-control rounded-0" id="customer_name" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="transaction_amount">Total Transaksi</label>
                                <input type="text" class="form-control rounded-0" id="transaction_amount" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount">Jumlah Refund <span class="text-danger">*</span></label>
                                <input type="text" class="form-control autonumeric rounded-0" id="amount" name="amount" 
                                       placeholder="0" required>
                                <small class="form-text text-muted">Jumlah refund tidak boleh melebihi total transaksi</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reason">Alasan Refund <span class="text-danger">*</span></label>
                        <textarea class="form-control rounded-0" id="reason" name="reason" rows="4" 
                                  placeholder="Jelaskan alasan refund secara detail (minimal 10 karakter)" required></textarea>
                        <small class="form-text text-muted">Alasan refund wajib diisi dan minimal 10 karakter</small>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                                                 <a href="<?= base_url('transaksi/refund') ?>" class="btn btn-secondary rounded-0">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="submit" class="btn btn-primary rounded-0">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<!-- Select2 is already included in the main layout -->
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- Select2 is already included in the main layout -->
<script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/JAutoNumber/autonumeric.js') ?>"></script>
<script>
$(document).ready(function() {
    console.log('Document ready');
    
    // Check if AutoNumeric is loaded
    if (typeof $.fn.autoNumeric === 'undefined') {
        console.error('AutoNumeric library not loaded!');
    } else {
        console.log('AutoNumeric library loaded successfully');
    }
    
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Pilih Transaksi'
    });

    // Initialize AutoNumeric for amount field
    try {
        $('#amount').autoNumeric('init', {
            aSep: '.',
            aDec: ',',
            mDec: '0'
        });
        console.log('AutoNumeric initialized successfully');
    } catch (error) {
        console.error('Error initializing AutoNumeric:', error);
    }

    // Handle transaction selection
    $('#id_transaction').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const amount = selectedOption.data('amount');
        const customer = selectedOption.data('customer');
        
        console.log('Selected option:', selectedOption);
        console.log('Amount:', amount);
        console.log('Customer:', customer);
        
        if (amount && customer) {
            $('#customer_name').val(customer);
            // Format amount with dot as thousands separator (Indonesian format)
            const formattedAmount = new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
            $('#transaction_amount').val('Rp ' + formattedAmount);
            $('#amount').autoNumeric('set', amount);
            console.log('Fields updated successfully');
        } else {
            $('#customer_name').val('');
            $('#transaction_amount').val('');
            $('#amount').autoNumeric('set', '');
            console.log('Fields cleared');
        }
    });

    // Form validation
    $('#refundForm').on('submit', function(e) {
        const amount = parseFloat($('#amount').autoNumeric('get'));
        const transactionAmount = parseFloat($('#id_transaction option:selected').data('amount'));
        const reason = $('#reason').val().trim();
        
        if (amount > transactionAmount) {
            e.preventDefault();
            alert('Jumlah refund tidak boleh melebihi total transaksi!');
            return false;
        }
        
        if (reason.length < 10) {
            e.preventDefault();
            alert('Alasan refund minimal 10 karakter!');
            return false;
        }
        
        // Log the values for debugging
        console.log('Refund amount:', amount);
        console.log('Transaction amount:', transactionAmount);
        console.log('Reason length:', reason.length);
            });
    });
    
    // Helper function to format numbers in Indonesian format (5.000)
    function formatNumber(number) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(number);
    }
</script>
<?= $this->endSection() ?>
