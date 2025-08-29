<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-edit"></i> Edit Petty Cash
        </h3>
        <div class="card-tools">
            <a href="<?= base_url('transaksi/petty') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-ban"></i> Error!</h5>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-ban"></i> Validation Error!</h5>
                <ul>
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-edit mr-2"></i>
                            Form Edit Petty Cash
                        </h3>
                    </div>
                    <form action="<?= base_url('transaksi/petty/update/' . $pettyEntry->id) ?>" method="POST" id="pettyCashForm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="outlet_id">Outlet <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="outlet_id" name="outlet_id" required>
                                            <option value="">Pilih Outlet</option>
                                            <?php foreach ($outlets as $outlet): ?>
                                                <option value="<?= $outlet->id ?>" <?= (old('outlet_id', $pettyEntry->outlet_id) == $outlet->id) ? 'selected' : '' ?>>
                                                    <?= $outlet->nama ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="category_id">Kategori</label>
                                        <select class="form-control select2" id="category_id" name="category_id">
                                            <option value="">Pilih Kategori</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?= $category->id ?>" <?= (old('category_id', $pettyEntry->category_id) == $category->id) ? 'selected' : '' ?>>
                                                    <?= $category->name ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="direction">Jenis Transaksi <span class="text-danger">*</span></label>
                                        <select class="form-control" id="direction" name="direction" required>
                                            <option value="">Pilih Jenis</option>
                                            <option value="IN" <?= (old('direction', $pettyEntry->direction) == 'IN') ? 'selected' : '' ?>>Masuk</option>
                                            <option value="OUT" <?= (old('direction', $pettyEntry->direction) == 'OUT') ? 'selected' : '' ?>>Keluar</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount">Nominal <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="amount" name="amount" 
                                               value="<?= old('amount', format_angka($pettyEntry->amount, 0)) ?>" 
                                               placeholder="0" required>
                                        <small class="form-text text-muted">Minimal Rp 1.000</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="reason">Keterangan <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="reason" name="reason" rows="3" 
                                                  placeholder="Jelaskan detail transaksi..." required><?= old('reason', $pettyEntry->reason) ?></textarea>
                                        <small class="form-text text-muted">Minimal 10 karakter</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="ref_no">Referensi</label>
                                        <input type="text" class="form-control" id="ref_no" name="ref_no" 
                                               value="<?= old('ref_no', $pettyEntry->ref_no) ?>" 
                                               placeholder="Nomor referensi (opsional)">
                                        <small class="form-text text-muted">Nomor referensi atau bukti transaksi</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="<?= base_url('transaksi/petty') ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Kembali
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="reset" class="btn btn-warning">
                                        <i class="fas fa-undo mr-2"></i>
                                        Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-2"></i>
                                        Update
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
        aSep: ',',
        aDec: '.',
        aSign: '',
        pSign: 's',
        aPad: false,
        nBracket: null,
        vMin: '1000',
        vMax: '999999999999'
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
});
</script>
<?= $this->endSection() ?>
