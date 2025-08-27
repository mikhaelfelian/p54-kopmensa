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
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-ban"></i> Error!</h5>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('errors')) : ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-ban"></i> Error!</h5>
                        <ul>
                            <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('transaksi/refund/store') ?>" method="post" id="refundForm">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_transaction">Pilih Transaksi <span class="text-danger">*</span></label>
                                <select class="form-control select2 rounded-0" id="id_transaction" name="id_transaction" required>
                                    <option value="">Pilih Transaksi</option>
                                    <?php foreach ($salesTransactions as $transaction) : ?>
                                        <option value="<?= $transaction->id ?>" 
                                                data-amount="<?= $transaction->jml_gtotal ?>"
                                                data-customer="<?= $transaction->customer_nama ?>">
                                            <?= $transaction->no_nota ?> - <?= $transaction->customer_nama ?> 
                                            (Rp <?= number_format($transaction->jml_gtotal, 0, ',', '.') ?>)
                                        </option>
                                    <?php endforeach; ?>
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
                        <button type="submit" class="btn btn-primary rounded-0">
                            <i class="fas fa-save"></i> Kirim Permintaan Refund
                        </button>
                        <a href="<?= base_url('transaksi/refund') ?>" class="btn btn-secondary rounded-0">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
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
<script src="<?= base_url('assets/theme/admin-lte-3/plugins/JAutoNumber/autonumeric.js') ?>"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Pilih Transaksi'
    });

    // Initialize AutoNumeric for amount field
    $('#amount').autoNumeric('init', {
        aSep: '.',
        aDec: ','
    });

    // Handle transaction selection
    $('#id_transaction').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const amount = selectedOption.data('amount');
        const customer = selectedOption.data('customer');
        
        if (amount && customer) {
            $('#customer_name').val(customer);
            $('#transaction_amount').val('Rp ' + new Intl.NumberFormat('id-ID').format(amount));
            $('#amount').autoNumeric('set', amount);
        } else {
            $('#customer_name').val('');
            $('#transaction_amount').val('');
            $('#amount').autoNumeric('set', '');
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
    });
});
</script>
<?= $this->endSection() ?>
