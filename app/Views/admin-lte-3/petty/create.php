<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-plus"></i> Tambah Petty Cash
        </h3>
        <div class="card-tools">
            <a href="<?= base_url('transaksi/petty') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <form action="<?= base_url('transaksi/petty/create') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label for="type">Tipe <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="">Pilih Tipe</option>
                            <option value="in">Cash In</option>
                            <option value="out">Cash Out</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Kategori <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($categories as $category) : ?>
                                <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="amount">Jumlah (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="amount" class="form-control" 
                               step="0.01" min="0" required placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control" rows="3" 
                                  placeholder="Deskripsi transaksi (opsional)"></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-md-6">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Petunjuk</h5>
                    <ul class="mb-0">
                        <li>Cash In: Uang masuk ke petty cash (contoh: pengembalian, surplus)</li>
                        <li>Cash Out: Uang keluar dari petty cash (contoh: belanja, biaya operasional)</li>
                        <li>Pilih kategori yang sesuai dengan jenis transaksi</li>
                        <li>Entry akan berstatus pending sampai disetujui</li>
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
    // Format currency input
    $('#amount').on('input', function() {
        let value = $(this).val();
        if (value && !isNaN(value)) {
            $(this).val(parseFloat(value).toFixed(2));
        }
    });
});
</script>
<?= $this->endSection() ?>
