<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-eye"></i> Detail Petty Cash
        </h3>
        <div class="card-tools">
            <a href="<?= base_url('transaksi/petty') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-2"></i>
                            Informasi Transaksi
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>ID Transaksi</strong></td>
                                        <td width="60%">: <?= $pettyEntry->id ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Outlet</strong></td>
                                        <td>: <?= $pettyEntry->outlet_name ?? 'N/A' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kategori</strong></td>
                                        <td>: <?= $pettyEntry->kategori_nama ?? 'N/A' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jenis</strong></td>
                                        <td>: 
                                            <?php if ($pettyEntry->direction === 'IN'): ?>
                                                <span class="badge badge-success">Masuk</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Keluar</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Nominal</strong></td>
                                        <td width="60%">: <strong class="text-primary">Rp <?= format_angka($pettyEntry->amount, 0) ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status</strong></td>
                                        <td>: 
                                            <?php
                                            $statusClass = [
                                                'draft' => 'badge badge-warning',
                                                'posted' => 'badge badge-success',
                                                'void' => 'badge badge-secondary'
                                            ];
                                            $statusLabel = [
                                                'draft' => 'Draft',
                                                'posted' => 'Posted',
                                                'void' => 'Void'
                                            ];
                                            ?>
                                            <span class="<?= $statusClass[$pettyEntry->status] ?? 'badge badge-secondary' ?>">
                                                <?= $statusLabel[$pettyEntry->status] ?? ucfirst($pettyEntry->status) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Referensi</strong></td>
                                        <td>: <?= $pettyEntry->ref_no ?: 'N/A' ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kasir</strong></td>
                                        <td>: <?= ($pettyEntry->user_name ?? '') . ' ' . ($pettyEntry->user_lastname ?? '') ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><strong>Keterangan:</strong></label>
                                    <div class="border rounded p-3 bg-light">
                                        <?= $pettyEntry->reason ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clock mr-2"></i>
                            Timeline
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="time-label">
                                <span class="bg-blue"><?= date('d M Y', strtotime($pettyEntry->created_at)) ?></span>
                            </div>
                            <div>
                                <i class="fas fa-plus bg-blue"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i> <?= date('H:i', strtotime($pettyEntry->created_at)) ?></span>
                                    <h3 class="timeline-header">Transaksi Dibuat</h3>
                                    <div class="timeline-body">
                                        Oleh: <?= ($pettyEntry->user_name ?? '') . ' ' . ($pettyEntry->user_lastname ?? '') ?>
                                    </div>
                                </div>
                            </div>

                            <?php if ($pettyEntry->approved_by): ?>
                                <div class="time-label">
                                    <span class="bg-green"><?= date('d M Y', strtotime($pettyEntry->approved_at)) ?></span>
                                </div>
                                <div>
                                    <i class="fas fa-check bg-green"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i> <?= date('H:i', strtotime($pettyEntry->approved_at)) ?></span>
                                        <h3 class="timeline-header">Transaksi Disetujui</h3>
                                        <div class="timeline-body">
                                            Oleh: <?= $pettyEntry->approved_by ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($pettyEntry->status === 'void'): ?>
                                <div class="time-label">
                                    <span class="bg-red"><?= date('d M Y', strtotime($pettyEntry->updated_at)) ?></span>
                                </div>
                                <div>
                                    <i class="fas fa-ban bg-red"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i> <?= date('H:i', strtotime($pettyEntry->updated_at)) ?></span>
                                        <h3 class="timeline-header">Transaksi Dibatalkan</h3>
                                        <div class="timeline-body">
                                            Status diubah menjadi void
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if ($pettyEntry->status === 'draft'): ?>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cogs mr-2"></i>
                                Aksi
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="btn-group-vertical w-100">
                                <a href="<?= base_url('transaksi/petty/edit/' . $pettyEntry->id) ?>" 
                                   class="btn btn-primary mb-2">
                                    <i class="fas fa-edit mr-2"></i>Edit
                                </a>
                                <a href="<?= base_url('transaksi/petty/approve/' . $pettyEntry->id) ?>" 
                                   class="btn btn-success mb-2"
                                   onclick="return confirm('Apakah Anda yakin ingin approve petty cash ini?')">
                                    <i class="fas fa-check mr-2"></i>Approve
                                </a>
                                <a href="<?= base_url('transaksi/petty/delete/' . $pettyEntry->id) ?>" 
                                   class="btn btn-danger"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus petty cash ini?')">
                                    <i class="fas fa-trash mr-2"></i>Hapus
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Any additional JavaScript can be added here
});
</script>
<?= $this->endSection() ?>
