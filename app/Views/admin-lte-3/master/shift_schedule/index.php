<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <a href="<?= base_url('master/shift-schedule/create') ?>" class="btn btn-sm btn-primary rounded-0">
                    <i class="fas fa-plus"></i> Tambah Jadwal
                </a>
                <a href="<?= base_url('transaksi/shift') ?>" class="btn btn-sm btn-secondary rounded-0">
                    <i class="fas fa-clock"></i> Manajemen Shift
                </a>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Outlet</th>
                            <th>Hari</th>
                            <th>Jam Buka</th>
                            <th>Jam Tutup</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $days = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
                        ?>
                        <?php if (empty($rows)): ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">Belum ada jadwal</td></tr>
                        <?php else: ?>
                            <?php foreach ($rows as $i => $r): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= esc($r->outlet_nama ?? '-') ?></td>
                                    <td><?= $days[(int) ($r->day_of_week ?? 0)] ?? '-' ?></td>
                                    <td><?= $r->jam_buka ? esc(substr($r->jam_buka, 0, 5)) : '-' ?></td>
                                    <td><?= $r->jam_tutup ? esc(substr($r->jam_tutup, 0, 5)) : '-' ?></td>
                                    <td><?= esc($r->keterangan ?? '') ?></td>
                                    <td>
                                        <span class="badge badge-<?= ($r->status ?? '1') === '1' ? 'success' : 'secondary' ?>">
                                            <?= ($r->status ?? '1') === '1' ? 'Aktif' : 'Nonaktif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('master/shift-schedule/edit/' . $r->id) ?>" class="btn btn-xs btn-warning rounded-0"><i class="fas fa-edit"></i></a>
                                        <form action="<?= base_url('master/shift-schedule/delete/' . $r->id) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus jadwal ini?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-xs btn-danger rounded-0" title="Hapus"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (!empty($pager)): ?>
                <div class="card-footer clearfix"><?= $pager->links('shift_schedule', 'adminlte_pagination') ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
