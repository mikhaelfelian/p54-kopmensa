<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card rounded-0">
                <div class="card-header bg-warning">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle"></i> Shift Aktif Ditemukan
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Anda masih memiliki shift aktif!</h5>
                        <p>Shift berikut masih terbuka dan belum ditutup:</p>
                    </div>

                    <table class="table table-bordered">
                        <tr>
                            <td width="40%"><strong>Kode Shift:</strong></td>
                            <td><?= esc($shift['shift_code']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Outlet:</strong></td>
                            <td><?= esc($shift['outlet_name'] ?? 'N/A') ?></td>
                        </tr>
                        <tr>
                            <td><strong>Waktu Buka:</strong></td>
                            <td><?= date('d/m/Y H:i', strtotime($shift['start_at'])) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td><span class="badge badge-warning">OPEN</span></td>
                        </tr>
                    </table>

                    <div class="alert alert-warning">
                        <strong>Peringatan:</strong> Anda tidak dapat membuka shift baru sampai shift ini ditutup atau disetujui.
                    </div>

                    <div class="text-center mt-4">
                        <a href="<?= base_url('transaksi/shift/continue/' . $shift['id']) ?>" 
                           class="btn btn-success btn-lg rounded-0">
                            <i class="fas fa-play"></i> Lanjutkan Shift
                        </a>
                        <a href="<?= base_url('transaksi/shift/close/' . $shift['id']) ?>" 
                           class="btn btn-warning btn-lg rounded-0">
                            <i class="fas fa-stop"></i> Tutup Shift
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

