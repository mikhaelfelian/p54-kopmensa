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
                                <i class="fas fa-plus mr-2"></i>
                                Form Petty Cash
                            </h3>
                        </div>
                        <form action="<?= base_url('transaksi/petty/store') ?>" method="POST" id="pettyCashForm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_outlet">Outlet <span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="id_outlet" name="id_outlet" required>
                                                <option value="">Pilih Outlet</option>
                                                <?php foreach ($outlets as $outlet): ?>
                                                    <option value="<?= $outlet->id ?>" <?= old('id_outlet') == $outlet->id ? 'selected' : '' ?>>
                                                        <?= $outlet->nama ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="id_kategori">Kategori <span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="id_kategori" name="id_kategori" required>
                                                <option value="">Pilih Kategori</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?= $category->id ?>" <?= old('id_kategori') == $category->id ? 'selected' : '' ?>>
                                                        <?= $category->kode ?> - <?= $category->nama ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="tgl_transaksi">Tanggal Transaksi <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="tgl_transaksi" name="tgl_transaksi" 
                                                   value="<?= old('tgl_transaksi', date('Y-m-d')) ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jenis">Jenis Transaksi <span class="text-danger">*</span></label>
                                            <select class="form-control" id="jenis" name="jenis" required>
                                                <option value="">Pilih Jenis</option>
                                                <option value="masuk" <?= old('jenis') == 'masuk' ? 'selected' : '' ?>>Masuk</option>
                                                <option value="keluar" <?= old('jenis') == 'keluar' ? 'selected' : '' ?>>Keluar</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="nominal">Nominal <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nominal" name="nominal" 
                                                   placeholder="0" required>
                                            <small class="form-text text-muted">Minimal Rp 1.000</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="keterangan">Keterangan <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                                                      placeholder="Jelaskan detail transaksi..." required><?= old('keterangan') ?></textarea>
                                            <small class="form-text text-muted">Minimal 10 karakter</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="<?= base_url('petty') ?>" class="btn btn-secondary">
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
                                            Simpan
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
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    // Initialize AutoNumeric for nominal input
    $('#nominal').autoNumeric('init', {
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
        var nominal = $('#nominal').autoNumeric('get');
        
        if (nominal < 1000) {
            e.preventDefault();
            alert('Nominal minimal Rp 1.000');
            $('#nominal').focus();
            return false;
        }

        if ($('#keterangan').val().length < 10) {
            e.preventDefault();
            alert('Keterangan minimal 10 karakter');
            $('#keterangan').focus();
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
