<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-credit-card"></i> Kelola Platform Pembayaran - <?= esc($outlet->nama) ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('master/outlet') ?>" class="btn btn-sm btn-secondary rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Assigned Platforms -->
                    <div class="col-md-6">
                        <h5 class="mb-3">Platform Aktif</h5>
                        <?php if (!empty($assignedPlatforms)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="50">No</th>
                                            <th>Kode</th>
                                            <th>Platform</th>
                                            <th>Persen (%)</th>
                                            <th width="80">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($assignedPlatforms as $key => $platform): ?>
                                            <tr>
                                                <td><?= $key + 1 ?></td>
                                                <td><?= esc($platform->platform_kode) ?></td>
                                                <td><?= esc($platform->platform) ?></td>
                                                <td><?= number_format($platform->persen, 1) ?>%</td>
                                                <td>
                                                    <button type="button" 
                                                            class="btn btn-danger btn-xs rounded-0 remove-platform" 
                                                            data-id="<?= $platform->id_platform ?>"
                                                            title="Hapus Platform">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Belum ada platform yang ditambahkan
                            </div>
                        <?php endif ?>
                    </div>

                    <!-- Available Platforms -->
                    <div class="col-md-6">
                        <h5 class="mb-3">Platform Tersedia</h5>
                        <?php if (!empty($availablePlatforms)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="50">No</th>
                                            <th>Kode</th>
                                            <th>Platform</th>
                                            <th>Persen (%)</th>
                                            <th width="80">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($availablePlatforms as $key => $platform): ?>
                                            <tr>
                                                <td><?= $key + 1 ?></td>
                                                <td><?= esc($platform->kode) ?></td>
                                                <td><?= esc($platform->platform) ?></td>
                                                <td><?= number_format($platform->persen, 1) ?>%</td>
                                                <td>
                                                    <button type="button" 
                                                            class="btn btn-success btn-xs rounded-0 add-platform" 
                                                            data-id="<?= $platform->id ?>"
                                                            title="Tambah Platform">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Semua platform sudah ditambahkan
                            </div>
                        <?php endif ?>
                    </div>
                </div>
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
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                id_platform: platformId,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                    btn.prop('disabled', false).html('<i class="fas fa-plus"></i>');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan pada server';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
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
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                    btn.prop('disabled', false).html('<i class="fas fa-times"></i>');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan pada server';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
                btn.prop('disabled', false).html('<i class="fas fa-times"></i>');
            }
        });
    });
});
</script>

<?= $this->endSection() ?>

