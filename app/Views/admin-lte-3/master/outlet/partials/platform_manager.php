<?php
/**
 * Shared component for managing outlet payment platforms.
 * Expected variables:
 * - $outlet
 * - $assignedPlatforms
 * - $availablePlatforms
 * - $embedded (optional) -> when true, hides the back button and tweaks heading
 */

$embedded = $embedded ?? false;
?>

<div class="card rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            <i class="fas fa-credit-card"></i>
            <?= $embedded ? 'Metode Pembayaran Outlet' : 'Kelola Platform Pembayaran - ' . esc($outlet->nama) ?>
        </h3>
        <?php if ($embedded): ?>
            <span class="text-muted small">Tambah atau hapus metode bayar per outlet</span>
        <?php else: ?>
            <div class="card-tools">
                <a href="<?= base_url('master/outlet') ?>" class="btn btn-sm btn-secondary rounded-0">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Assigned Platforms -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Platform Aktif</h5>
                    <span class="badge badge-info"><?= count($assignedPlatforms ?? []) ?></span>
                </div>
                <?php if (!empty($assignedPlatforms)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="40">No</th>
                                    <th>Kode</th>
                                    <th>Platform</th>
                                    <th width="70" class="text-center">Fee (%)</th>
                                    <th width="70" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignedPlatforms as $key => $platform): ?>
                                    <tr>
                                        <td><?= $key + 1 ?></td>
                                        <td><?= esc($platform->platform_kode) ?></td>
                                        <td><?= esc($platform->platform) ?></td>
                                        <td class="text-center">
                                            <?= $platform->persen !== null ? number_format($platform->persen, 1) : '0.0' ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-danger btn-xs rounded-0 remove-platform"
                                                data-id="<?= $platform->id_platform ?>"
                                                title="Hapus Platform">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i> Belum ada platform aktif untuk outlet ini.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Available Platforms -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Platform Tersedia</h5>
                    <span class="badge badge-secondary"><?= count($availablePlatforms ?? []) ?></span>
                </div>
                <?php if (!empty($availablePlatforms)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="40">No</th>
                                    <th>Kode</th>
                                    <th>Platform</th>
                                    <th width="70" class="text-center">Fee (%)</th>
                                    <th width="70" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($availablePlatforms as $key => $platform): ?>
                                    <tr>
                                        <td><?= $key + 1 ?></td>
                                        <td><?= esc($platform->kode) ?></td>
                                        <td><?= esc($platform->platform) ?></td>
                                        <td class="text-center">
                                            <?= $platform->persen !== null ? number_format($platform->persen, 1) : '0.0' ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-success btn-xs rounded-0 add-platform"
                                                data-id="<?= $platform->id ?>"
                                                title="Tambah Platform">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Semua platform sudah ditambahkan ke outlet ini.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const assignUrl = '<?= base_url("master/outlet/assign_platform/{$outlet->id}") ?>';
    const removeUrlBase = '<?= base_url("master/outlet/remove_platform/{$outlet->id}/") ?>';
    const csrfToken = '<?= csrf_token() ?>';
    const csrfHash = '<?= csrf_hash() ?>';

    function handleResponse(btn, defaultIcon) {
        return function(response) {
            if (response.success) {
                alert(response.message);
                window.location.reload();
            } else {
                alert(response.message || 'Operasi gagal');
                btn.prop('disabled', false).html(defaultIcon);
            }
        };
    }

    $('.add-platform').on('click', function() {
        const btn = $(this);
        const platformId = btn.data('id');

        if (!confirm('Tambahkan platform ini ke outlet?')) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: assignUrl,
            type: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            data: {
                id_platform: platformId,
                [csrfToken]: csrfHash
            },
            dataType: 'json',
            success: handleResponse(btn, '<i class="fas fa-plus"></i>'),
            error: function(xhr) {
                alert((xhr.responseJSON && xhr.responseJSON.message) || 'Terjadi kesalahan pada server');
                btn.prop('disabled', false).html('<i class="fas fa-plus"></i>');
            }
        });
    });

    $('.remove-platform').on('click', function() {
        const btn = $(this);
        const platformId = btn.data('id');

        if (!confirm('Hapus platform ini dari outlet?')) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: removeUrlBase + platformId,
            type: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            data: {
                [csrfToken]: csrfHash
            },
            dataType: 'json',
            success: handleResponse(btn, '<i class="fas fa-times"></i>'),
            error: function(xhr) {
                alert((xhr.responseJSON && xhr.responseJSON.message) || 'Terjadi kesalahan pada server');
                btn.prop('disabled', false).html('<i class="fas fa-times"></i>');
            }
        });
    });
});
</script>

