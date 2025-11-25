<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-1"></i> Laporan Omzet Shift
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('laporan/shift-omzet/export_excel') ?>?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&id_user=<?= $idUser ?? '' ?>" 
                       class="btn btn-success btn-sm rounded-0">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                    <a href="<?= base_url('laporan/shift-omzet/export_pdf') ?>?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&id_user=<?= $idUser ?? '' ?>" 
                       class="btn btn-danger btn-sm rounded-0">
                        <i class="fas fa-file-pdf mr-1"></i> Export PDF
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="get" action="<?= base_url('laporan/shift-omzet') ?>" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $startDate ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $endDate ?>">
                        </div>
                        <div class="col-md-3">
                            <label>User</label>
                            <select name="id_user" class="form-control form-control-sm">
                                <option value="">Semua User</option>
                                <?php foreach ($userList as $userItem): ?>
                                    <?php
                                    $fullName = trim(($userItem->first_name ?? '') . ' ' . ($userItem->last_name ?? ''));
                                    $displayName = $fullName ?: $userItem->username ?? 'User ' . $userItem->id;
                                    ?>
                                    <option value="<?= $userItem->id ?>" <?= ($idUser ?? '') == $userItem->id ? 'selected' : '' ?>>
                                        <?= esc($displayName) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-sm btn-block">
                                <i class="fas fa-search mr-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= format_angka(count($shifts)) ?></h3>
                                <p>Total Shift</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= format_angka($totalTransactions) ?></h3>
                                <p>Total Transaksi</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?= format_angka($totalOmzet) ?></h3>
                                <p>Total Omzet</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Shift Code</th>
                                <th>Nama Shift</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Outlet</th>
                                <th>User</th>
                                <th class="text-right">Total Transaksi</th>
                                <th class="text-right">Total Omzet</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($shifts)): ?>
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data omzet shift</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($shifts as $index => $shift): ?>
                                    <?php
                                    $userName = trim(($shift->user_open_first_name ?? '') . ' ' . ($shift->user_open_last_name ?? ''));
                                    $userName = $userName ?: $shift->user_open_username ?? '-';
                                    ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= esc($shift->shift_code) ?></td>
                                        <td><?= esc($shift->nama_shift ?? '-') ?></td>
                                        <td><?= $shift->start_at ? date('d/m/Y H:i', strtotime($shift->start_at)) : '-' ?></td>
                                        <td><?= $shift->end_at ? date('d/m/Y H:i', strtotime($shift->end_at)) : '-' ?></td>
                                        <td><?= esc($shift->outlet_nama ?? '-') ?></td>
                                        <td><?= esc($userName) ?></td>
                                        <td class="text-right"><?= format_angka($shift->total_transactions ?? 0) ?></td>
                                        <td class="text-right"><?= format_angka($shift->total_omzet ?? 0) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($shifts)): ?>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="7" class="text-right">TOTAL</th>
                                    <th class="text-right"><?= format_angka($totalTransactions) ?></th>
                                    <th class="text-right"><?= format_angka($totalOmzet) ?></th>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

