<?= $this->extend(theme_path('main')) ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Detail Voucher</h3>
                <div class="card-tools">
                    <a href="<?= base_url('master/voucher') ?>" class="btn btn-sm btn-secondary rounded-0">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <a href="<?= base_url('master/voucher/edit/' . $voucher->id) ?>" class="btn btn-sm btn-warning rounded-0">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Informasi Voucher</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Kode Voucher</strong></td>
                                <td>: <span class="badge badge-primary"><?= esc($voucher->kode) ?></span></td>
                            </tr>
                            <tr>
                                <td><strong>Jenis Voucher</strong></td>
                                <td>: 
                                    <?php if ($voucher->jenis_voucher === 'nominal'): ?>
                                        <span class="badge badge-primary">Nominal (Rp)</span>
                                    <?php else: ?>
                                        <span class="badge badge-info">Persentase (%)</span>
                                    <?php endif; ?>
                                </td>
                            </tr> 
                            <tr>
                                <td><strong>Nominal Voucher</strong></td>
                                <td>: 
                                    <?php if ($voucher->jenis_voucher === 'nominal'): ?>
                                        <strong class="text-success">Rp <?= number_format($voucher->nominal) ?></strong>
                                    <?php else: ?>
                                        <strong class="text-info"><?= $voucher->nominal ?>%</strong>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Jumlah</strong></td>
                                <td>: <?= number_format($voucher->jml) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Periode Aktif</strong></td>
                                <td>: <?= date('d/m/Y', strtotime($voucher->tgl_masuk)) ?> - <?= date('d/m/Y', strtotime($voucher->tgl_keluar)) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: 
                                    <?php 
                                    $today = date('Y-m-d');
                                    $isExpired = $voucher->tgl_keluar < $today;
                                    $isNotStarted = $voucher->tgl_masuk > $today;
                                    $isFull = $voucher->jml_keluar >= $voucher->jml_max;
                                    ?>
                                    
                                    <?php if ($voucher->status == '0'): ?>
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    <?php elseif ($isExpired): ?>
                                        <span class="badge badge-danger">Kadaluarsa</span>
                                    <?php elseif ($isNotStarted): ?>
                                        <span class="badge badge-warning">Belum Aktif</span>
                                    <?php elseif ($isFull): ?>
                                        <span class="badge badge-dark">Habis</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Dibuat Oleh</strong></td>
                                <td>: User ID <?= $voucher->id_user ?></td>
                            </tr>
                            <tr>
                                <td><strong>Dibuat Pada</strong></td>
                                <td>: <?= date('d/m/Y H:i:s', strtotime($voucher->created_at)) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Terakhir Update</strong></td>
                                <td>: <?= date('d/m/Y H:i:s', strtotime($voucher->updated_at)) ?></td>
                            </tr>
                        </table>
                        
                        <?php if (!empty($voucher->keterangan)): ?>
                            <h5>Keterangan</h5>
                            <div class="alert alert-info">
                                <?= nl2br(esc($voucher->keterangan)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Statistik Penggunaan</h5>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row text-center mb-3">
                                    <div class="col-4">
                                        <div class="text-success">
                                            <h3><?= number_format($stats['used']) ?></h3>
                                            <small>Terpakai</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-warning">
                                            <h3><?= number_format($stats['remaining']) ?></h3>
                                            <small>Sisa</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-info">
                                            <h3><?= number_format($stats['total']) ?></h3>
                                            <small>Total Limit</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="progress mb-2">
                                    <div class="progress-bar bg-success" 
                                         style="width: <?= $stats['percentage_used'] ?>%"
                                         aria-valuenow="<?= $stats['percentage_used'] ?>"
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <?= number_format($stats['percentage_used'], 1) ?>%
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <small class="text-muted">
                                        Persentase penggunaan voucher
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Cards -->
                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fas fa-calendar-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Durasi</span>
                                        <span class="info-box-number">
                                            <?php
                                            $start = new DateTime($voucher->tgl_masuk);
                                            $end = new DateTime($voucher->tgl_keluar);
                                            $diff = $start->diff($end);
                                            echo $diff->days + 1 . ' hari';
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Sisa Waktu</span>
                                        <span class="info-box-number">
                                            <?php
                                            $today = new DateTime();
                                            $end = new DateTime($voucher->tgl_keluar);
                                            if ($end < $today) {
                                                echo 'Kadaluarsa';
                                            } else {
                                                $diff = $today->diff($end);
                                                echo $diff->days . ' hari';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
<?= $this->endSection() ?>