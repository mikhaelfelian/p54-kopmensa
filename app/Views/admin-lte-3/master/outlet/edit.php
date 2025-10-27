<?= $this->extend(theme_path('main')) ?>
<?= $this->section('content') ?>
<div class="row">
    <!-- Form Edit Outlet -->
    <div class="col-md-6">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit"></i> Form Edit Outlet
                </h3>
            </div>
            <?= form_open('master/outlet/update/' . $outlet->id) ?>
            <div class="card-body">
                <div class="form-group">
                    <label>Nama <span class="text-danger">*</span></label>
                    <?= form_input([
                        'type' => 'text',
                        'name' => 'nama',
                        'class' => 'form-control rounded-0',
                        'value' => $outlet->nama,
                        'placeholder' => 'Nama Outlet',
                        'required' => true
                    ]) ?>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <?= form_textarea([
                        'name' => 'deskripsi',
                        'class' => 'form-control rounded-0',
                        'value' => $outlet->deskripsi,
                        'placeholder' => 'Deskripsi Outlet',
                        'rows' => 3
                    ]) ?>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" name="status" value="1" id="statusAktif"
                            <?= ($outlet->status == '1') ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="statusAktif">
                            Aktif
                        </label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" name="status" value="0" id="statusNonaktif"
                            <?= ($outlet->status == '0') ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="statusNonaktif">
                            Tidak Aktif
                        </label>
                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/outlet') ?>" class="btn btn-default rounded-0">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary rounded-0 float-right">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
            <?= form_close() ?>
        </div>
    </div>

    <!-- Platform Management -->
    <div class="col-md-6">
        <div class="card rounded-0">
            <div class="card-header bg-info">
                <h3 class="card-title">
                    <i class="fas fa-credit-card"></i> Platform Pembayaran
                </h3>
            </div>
            <div class="card-body">
                <!-- Assigned Platforms -->
                <h6 class="mb-2"><strong>Platform Aktif</strong></h6>
                <?php if (!empty($assignedPlatforms)): ?>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th width="40">No</th>
                                    <th>Platform</th>
                                    <th width="80">%</th>
                                    <th width="60">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignedPlatforms as $key => $platform): ?>
                                    <tr>
                                        <td><?= $key + 1 ?></td>
                                        <td>
                                            <small class="text-muted"><?= esc($platform->platform_kode) ?></small><br>
                                            <?= esc($platform->platform) ?>
                                        </td>
                                        <td><?= number_format($platform->persen, 1) ?>%</td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-danger btn-xs rounded-0 remove-platform" 
                                                    data-id="<?= $platform->id_platform ?>"
                                                    title="Hapus">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info alert-sm mb-3">
                        <i class="fas fa-info-circle"></i> Belum ada platform
                    </div>
                <?php endif ?>

                <!-- Available Platforms -->
                <h6 class="mb-2"><strong>Tambah Platform</strong></h6>
                <?php if (!empty($availablePlatforms)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th width="40">No</th>
                                    <th>Platform</th>
                                    <th width="80">%</th>
                                    <th width="60">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($availablePlatforms as $key => $platform): ?>
                                    <tr>
                                        <td><?= $key + 1 ?></td>
                                        <td>
                                            <small class="text-muted"><?= esc($platform->kode) ?></small><br>
                                            <?= esc($platform->platform) ?>
                                        </td>
                                        <td><?= number_format($platform->persen, 1) ?>%</td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-success btn-xs rounded-0 add-platform" 
                                                    data-id="<?= $platform->id ?>"
                                                    title="Tambah">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning alert-sm">
                        <i class="fas fa-check-circle"></i> Semua platform sudah ditambahkan
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Add platform
    $('.add-platform').click(function() {
        const platformId = $(this).data('id');
        const btn = $(this);
        
        if (!confirm('Tambahkan platform ini ke outlet?')) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '<?= base_url("master/outlet/assign_platform/{$outlet->id}") ?>',
            type: 'POST',
            data: {
                id_platform: platformId,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message
                    });
                    btn.prop('disabled', false).html('<i class="fas fa-plus"></i>');
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan pada server'
                });
                btn.prop('disabled', false).html('<i class="fas fa-plus"></i>');
            }
        });
    });

    // Remove platform
    $('.remove-platform').click(function() {
        const platformId = $(this).data('id');
        const btn = $(this);
        
        if (!confirm('Hapus platform ini dari outlet?')) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '<?= base_url("master/outlet/remove_platform/{$outlet->id}/") ?>' + platformId,
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message
                    });
                    btn.prop('disabled', false).html('<i class="fas fa-times"></i>');
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan pada server'
                });
                btn.prop('disabled', false).html('<i class="fas fa-times"></i>');
            }
        });
    });
});
</script>

<?= $this->endSection() ?>