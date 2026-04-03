<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<?php
$t = (string) ($transfer->tipe ?? '');
$defGdAsal = (int) ($transfer->id_gd_asal ?? 0);
$defGdTuj  = (int) ($transfer->id_gd_tujuan ?? 0);
?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Edit Transfer/Mutasi</h3>
                <div class="card-tools">
                    <a href="<?= base_url('gudang/transfer') ?>" class="btn btn-sm btn-secondary rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <form action="<?= base_url('gudang/transfer/update/' . $transfer->id) ?>" method="post" id="transferForm">
                <?= csrf_field() ?>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tgl_masuk">Tanggal Transfer <span class="text-danger">*</span></label>
                                <input type="date" class="form-control rounded-0" id="tgl_masuk" name="tgl_masuk"
                                       value="<?= esc(old('tgl_masuk', date('Y-m-d', strtotime($transfer->tgl_masuk)))) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipe">Tipe Transfer <span class="text-danger">*</span></label>
                                <select class="form-control rounded-0" id="tipe" name="tipe" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="1" <?= $t === '1' ? 'selected' : '' ?>>Pindah Gudang</option>
                                    <option value="2" <?= $t === '2' ? 'selected' : '' ?>>Stok Masuk</option>
                                    <option value="3" <?= $t === '3' ? 'selected' : '' ?>>Stok Keluar</option>
                                    <option value="4" <?= $t === '4' ? 'selected' : '' ?>>Pindah Outlet</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6" id="gudang-asal-section">
                            <div class="form-group">
                                <label for="id_gd_asal">Gudang Asal <span class="text-danger" id="gudang-asal-required">*</span></label>
                                <select class="form-control rounded-0" id="id_gd_asal" name="id_gd_asal">
                                    <option value="">Pilih Gudang Asal</option>
                                    <?php foreach ($gudang as $gd): ?>
                                        <option value="<?= $gd->id ?>" <?= (string) old('id_gd_asal', $transfer->id_gd_asal) === (string) $gd->id ? 'selected' : '' ?>>
                                            <?= esc($gd->nama) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="gudang-tujuan-section">
                            <div class="form-group">
                                <label for="id_gd_tujuan">Gudang Tujuan <span class="text-danger" id="gudang-tujuan-required">*</span></label>
                                <select class="form-control rounded-0" id="id_gd_tujuan" name="id_gd_tujuan">
                                    <option value="">Pilih Gudang Tujuan</option>
                                    <?php foreach ($gudang as $gd): ?>
                                        <option value="<?= $gd->id ?>" <?= (string) old('id_gd_tujuan', $transfer->id_gd_tujuan) === (string) $gd->id ? 'selected' : '' ?>>
                                            <?= esc($gd->nama) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="outlet-pair-section" style="display:none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_outlet_asal">Outlet Asal <span class="text-danger" id="outlet-asal-required">*</span></label>
                                <select class="form-control rounded-0" id="id_outlet_asal" name="id_outlet_asal">
                                    <option value="">Pilih outlet asal</option>
                                    <?php foreach ($outlet as $ot): ?>
                                        <option value="<?= $ot->id ?>" <?= (string) old('id_outlet_asal', $t === '4' ? (string) $defGdAsal : '') === (string) $ot->id ? 'selected' : '' ?>><?= esc($ot->nama) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_outlet_tujuan">Outlet Tujuan <span class="text-danger" id="outlet-tujuan-required">*</span></label>
                                <select class="form-control rounded-0" id="id_outlet_tujuan" name="id_outlet_tujuan">
                                    <option value="">Pilih outlet tujuan</option>
                                    <?php foreach ($outlet as $ot): ?>
                                        <option value="<?= $ot->id ?>" <?= (string) old('id_outlet_tujuan', $t === '4' ? (string) $defGdTuj : '') === (string) $ot->id ? 'selected' : '' ?>><?= esc($ot->nama) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted small" id="transfer-help-text" style="display:none;">
                        <strong>Pindah Gudang:</strong> mutasi antar gudang pusat.
                        <strong>Stok Masuk:</strong> penyesuaian masuk ke gudang tujuan.
                        <strong>Stok Keluar:</strong> penyesuaian keluar dari gudang asal.
                        <strong>Pindah Outlet:</strong> transfer stok antar outlet (asal &amp; tujuan wajib berbeda).
                    </p>

                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control rounded-0" id="keterangan" name="keterangan" rows="3"><?= esc(old('keterangan', $transfer->keterangan)) ?></textarea>
                    </div>

                    <div class="alert alert-info">
                        <p class="mb-0">Status nota:
                            <?php
                            $statusNotaLabels = ['0' => 'Draft', '1' => 'Pending', '2' => 'Diproses', '3' => 'Selesai', '4' => 'Dibatalkan'];
                            echo '<strong>' . ($statusNotaLabels[$transfer->status_nota] ?? 'Unknown') . '</strong>';
                            ?>
                        </p>
                        <?php if ($transfer->status_nota == '3'): ?>
                            <p class="text-warning mb-0">Transfer yang sudah selesai tidak dapat diedit.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer">
                    <?php if ($transfer->status_nota != '3'): ?>
                        <button type="submit" class="btn btn-primary rounded-0">
                            <i class="fas fa-save"></i> Update
                        </button>
                    <?php endif; ?>
                    <a href="<?= base_url('gudang/transfer') ?>" class="btn btn-secondary rounded-0">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    function applyTipe(tipe) {
        $('#gudang-asal-section').hide();
        $('#gudang-tujuan-section').hide();
        $('#outlet-pair-section').hide();
        $('#transfer-help-text').hide();
        $('#id_gd_asal').prop('disabled', false).prop('required', false);
        $('#id_gd_tujuan').prop('disabled', false).prop('required', false);
        $('#id_outlet_asal').prop('required', false);
        $('#id_outlet_tujuan').prop('required', false);
        $('#gudang-asal-required').hide();
        $('#gudang-tujuan-required').hide();
        $('#outlet-asal-required').hide();
        $('#outlet-tujuan-required').hide();

        if (tipe === '1') {
            $('#gudang-asal-section').show();
            $('#gudang-tujuan-section').show();
            $('#id_gd_asal').prop('required', true);
            $('#id_gd_tujuan').prop('required', true);
            $('#gudang-asal-required').show();
            $('#gudang-tujuan-required').show();
        } else if (tipe === '2') {
            $('#gudang-asal-section').hide();
            $('#gudang-tujuan-section').show();
            $('#id_gd_asal').val('0');
            $('#id_gd_tujuan').prop('required', true);
            $('#gudang-tujuan-required').show();
        } else if (tipe === '3') {
            $('#gudang-asal-section').show();
            $('#gudang-tujuan-section').hide();
            $('#id_gd_tujuan').val('0');
            $('#id_gd_asal').prop('required', true);
            $('#gudang-asal-required').show();
        } else if (tipe === '4') {
            $('#outlet-pair-section').show();
            $('#transfer-help-text').show();
            $('#id_outlet_asal').prop('required', true);
            $('#id_outlet_tujuan').prop('required', true);
            $('#outlet-asal-required').show();
            $('#outlet-tujuan-required').show();
        }
    }

    $('#transferForm').on('submit', function(e) {
        var tipe = $('#tipe').val();
        var gdAsal = $('#id_gd_asal').val();
        var gdTujuan = $('#id_gd_tujuan').val();
        var oa = $('#id_outlet_asal').val();
        var ot = $('#id_outlet_tujuan').val();
        $('.is-invalid').removeClass('is-invalid');
        if (tipe === '1') {
            if (!gdAsal || !gdTujuan || gdAsal === gdTujuan) {
                e.preventDefault();
                alert('Gudang asal dan tujuan harus dipilih dan tidak boleh sama.');
                return false;
            }
        } else if (tipe === '2') {
            if (!gdTujuan) { e.preventDefault(); alert('Gudang tujuan wajib.'); return false; }
        } else if (tipe === '3') {
            if (!gdAsal) { e.preventDefault(); alert('Gudang asal wajib.'); return false; }
        } else if (tipe === '4') {
            if (!oa || !ot) {
                e.preventDefault();
                alert('Outlet asal dan tujuan wajib diisi.');
                return false;
            }
            if (oa === ot) {
                e.preventDefault();
                alert('Outlet asal dan tujuan tidak boleh sama.');
                return false;
            }
        }
    });

    $('#tipe').on('change', function() {
        applyTipe($(this).val());
    });

    // Restore values after generic apply (edit initial)
    var initialTipe = '<?= esc($t, 'js') ?>';
    applyTipe(initialTipe);
    <?php if ($t === '1' || $t === '2' || $t === '3'): ?>
    $('#id_gd_asal').val('<?= (int) $defGdAsal ?>');
    $('#id_gd_tujuan').val('<?= (int) $defGdTuj ?>');
    <?php endif; ?>
    <?php if ($t === '4'): ?>
    $('#id_outlet_asal').val('<?= (int) $defGdAsal ?>');
    $('#id_outlet_tujuan').val('<?= (int) $defGdTuj ?>');
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
