<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>

<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-clock"></i> Detail Shift: <?= is_array($shift) ? ($shift['shift_code'] ?? 'N/A') : ($shift->shift_code ?? 'N/A') ?>
        </h3>
        <div class="card-tools">
            <a href="<?= base_url('transaksi/shift') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Error!</h5>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <!-- Shift Information Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-clock mr-2"></i>
                                Informasi Shift
                            </h3>
                            <div class="card-tools">
                                <?php if (is_array($shift) && isset($shift['status']) && $shift['status'] === 'open'): ?>
                                    <span class="badge badge-success">Shift Aktif</span>
                                <?php elseif (is_array($shift) && isset($shift['status']) && $shift['status'] === 'closed'): ?>
                                    <span class="badge badge-secondary">Shift Ditutup</span>
                                <?php elseif (is_object($shift) && $shift->status === 'open'): ?>
                                    <span class="badge badge-success">Shift Aktif</span>
                                <?php elseif (is_object($shift) && $shift->status === 'closed'): ?>
                                    <span class="badge badge-secondary">Shift Ditutup</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Status Tidak Diketahui</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                                                                 <tr>
                                             <td width="150"><strong>Kasir:</strong></td>
                                             <td><?= is_array($shift) ? ($shift['kasir_name'] ?? 'N/A') : ($shift->kasir_name ?? 'N/A') ?></td>
                                         </tr>
                                         <tr>
                                             <td><strong>Outlet:</strong></td>
                                             <td><?= is_array($shift) ? ($shift['outlet_name'] ?? 'N/A') : ($shift->outlet_name ?? 'N/A') ?></td>
                                         </tr>
                                         <tr>
                                             <td><strong>Tanggal:</strong></td>
                                             <td><?= date('d/m/Y', strtotime(is_array($shift) ? ($shift['tanggal'] ?? date('Y-m-d')) : ($shift->tanggal ?? date('Y-m-d')))) ?></td>
                                         </tr>
                                         <tr>
                                             <td><strong>Jam Buka:</strong></td>
                                             <td><?= (is_array($shift) ? ($shift['jam_buka'] ?? null) : ($shift->jam_buka ?? null)) ? date('H:i', strtotime(is_array($shift) ? $shift['jam_buka'] : $shift->jam_buka)) : 'N/A' ?></td>
                                         </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Status:</strong></td>
                                            <td>
                                                <?php 
                                                $status = is_array($shift) ? ($shift['status'] ?? 'unknown') : ($shift->status ?? 'unknown');
                                                if ($status === 'open'): ?>
                                                    <span class="badge badge-success">Aktif</span>
                                                <?php elseif ($status === 'closed'): ?>
                                                    <span class="badge badge-secondary">Ditutup</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning"><?= ucfirst($status) ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jam Tutup:</strong></td>
                                                                                         <td><?= (is_array($shift) ? ($shift['jam_tutup'] ?? null) : ($shift->jam_tutup ?? null)) ? date('H:i', strtotime(is_array($shift) ? $shift['jam_tutup'] : $shift->jam_tutup)) : 'N/A' ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Saldo Awal:</strong></td>
                                            <td><?= format_angka((is_array($shift) ? ($shift['saldo_awal'] ?? 0) : ($shift->saldo_awal ?? 0)), 0) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Saldo Akhir:</strong></td>
                                            <td><?= format_angka((is_array($shift) ? ($shift['saldo_akhir'] ?? 0) : ($shift->saldo_akhir ?? 0)), 0) ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Summary Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar mr-2"></i>
                                Ringkasan Transaksi
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-shopping-cart"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Transaksi</span>
                                            <span class="info-box-number"><?= $transactionCount ?? 0 ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Penjualan</span>
                                            <span class="info-box-number"><?= format_angka($totalSales ?? 0, 0) ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-warning"><i class="fas fa-credit-card"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Pembayaran</span>
                                            <span class="info-box-number"><?= format_angka($totalPayment ?? 0, 0) ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-danger"><i class="fas fa-exchange-alt"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Selisih</span>
                                            <span class="info-box-number"><?= format_angka(($totalPayment ?? 0) - ($totalSales ?? 0), 0) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Action Buttons Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cogs mr-2"></i>
                                Aksi
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php 
                            $status = is_array($shift) ? ($shift['status'] ?? 'unknown') : ($shift->status ?? 'unknown');
                            if ($status === 'open'): ?>
                                <button type="button" class="btn btn-warning btn-block mb-2" onclick="closeShift()">
                                    <i class="fas fa-lock mr-2"></i>
                                    Tutup Shift
                                </button>
                            <?php endif; ?>
                            
                                                         <a href="<?= base_url('transaksi/shift/print/' . (is_array($shift) ? $shift['id'] : $shift->id)) ?>" class="btn btn-info btn-block mb-2" target="_blank">
                                 <i class="fas fa-print mr-2"></i>
                                 Cetak Laporan
                             </a>
                             
                                                           <a href="<?= base_url('transaksi/shift/edit/' . (is_array($shift) ? $shift['id'] : $shift->id)) ?>" class="btn btn-primary btn-block mb-2">
                                 <i class="fas fa-edit mr-2"></i>
                                 Edit Shift
                             </a>
                            
                            <a href="<?= base_url('transaksi/shift') ?>" class="btn btn-secondary btn-block">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali
                            </a>
                        </div>
                    </div>

                    <!-- Quick Stats Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-2"></i>
                                Statistik Cepat
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center">
                                        <h4 class="text-success"><?= $cashTransactions ?? 0 ?></h4>
                                        <small class="text-muted">Tunai</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <h4 class="text-info"><?= $cardTransactions ?? 0 ?></h4>
                                        <small class="text-muted">Kartu</small>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center">
                                        <h4 class="text-warning"><?= $qrisTransactions ?? 0 ?></h4>
                                        <small class="text-muted">QRIS</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <h4 class="text-danger"><?= $otherTransactions ?? 0 ?></h4>
                                        <small class="text-muted">Lainnya</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-2"></i>
                        Transaksi Terbaru
                    </h3>
                    <div class="card-tools">
                                                 <a href="<?= base_url('transaksi?shift_id=' . (is_array($shift) ? $shift['id'] : $shift->id)) ?>" class="btn btn-sm btn-primary">
                             Lihat Semua Transaksi
                         </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Waktu</th>
                                    <th>No Transaksi</th>
                                    <th>Total</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentTransactions)): ?>
                                    <?php foreach ($recentTransactions as $index => $transaction): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= date('H:i', strtotime($transaction->created_at)) ?></td>
                                            <td><?= $transaction->no_transaksi ?? 'N/A' ?></td>
                                            <td><?= format_angka($transaction->total ?? 0, 0) ?></td>
                                            <td>
                                                <?php
                                                $paymentMethod = $transaction->metode_pembayaran ?? 'tunai';
                                                $methodClass = [
                                                    'tunai' => 'badge badge-success',
                                                    'kartu' => 'badge badge-info',
                                                    'qris' => 'badge badge-warning',
                                                    'transfer' => 'badge badge-primary'
                                                ];
                                                $methodLabel = [
                                                    'tunai' => 'Tunai',
                                                    'kartu' => 'Kartu',
                                                    'qris' => 'QRIS',
                                                    'transfer' => 'Transfer'
                                                ];
                                                ?>
                                                <span class="<?= $methodClass[$paymentMethod] ?? 'badge badge-secondary' ?>">
                                                    <?= $methodLabel[$paymentMethod] ?? ucfirst($paymentMethod) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $status = $transaction->status ?? 'pending';
                                                $statusClass = [
                                                    'selesai' => 'badge badge-success',
                                                    'pending' => 'badge badge-warning',
                                                    'batal' => 'badge badge-danger'
                                                ];
                                                ?>
                                                <span class="<?= $statusClass[$status] ?? 'badge badge-secondary' ?>">
                                                    <?= ucfirst($status) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            Tidak ada transaksi untuk shift ini
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Close Shift Modal -->
<div class="modal fade" id="closeShiftModal" tabindex="-1" role="dialog" aria-labelledby="closeShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="closeShiftModalLabel">Tutup Shift</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="closeShiftForm" action="<?= base_url('transaksi/shift/close/' . (is_array($shift) ? $shift['id'] : $shift->id)) ?>" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="saldo_akhir">Saldo Akhir</label>
                        <input type="text" class="form-control" id="saldo_akhir" name="saldo_akhir" 
                               value="<?= (is_array($shift) ? ($shift['saldo_akhir'] ?? 0) : ($shift->saldo_akhir ?? 0)) ?>" required>
                        <small class="form-text text-muted">Masukkan saldo akhir yang tersisa di kasir</small>
                    </div>
                    <div class="form-group">
                        <label for="catatan">Catatan</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="3" 
                                  placeholder="Catatan tambahan (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Tutup Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
function closeShift() {
    $('#closeShiftModal').modal('show');
}

// AutoNumeric for saldo akhir input
$(document).ready(function() {
    $('#saldo_akhir').autoNumeric('init', {
        aSep: ',',
        aDec: '.',
        aSign: '',
        pSign: 's',
        aPad: false,
        nBracket: null,
        vMin: '0',
        vMax: '999999999999'
    });
});
</script>
<?= $this->endSection() ?>
