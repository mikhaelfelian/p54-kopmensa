<?= $this->extend(theme_path('main')) ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Printer</h3>
                <div class="card-tools">
                    <a href="<?= base_url('pengaturan/printer/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Printer
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Printer</th>
                                <th>Tipe</th>
                                <th>Driver</th>
                                <th>Koneksi</th>
                                <th>Status</th>
                                <th>Default</th>
                                <th width="200">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($printers)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada printer yang dikonfigurasi</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($printers as $index => $printer): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <strong><?= esc($printer->nama_printer) ?></strong>
                                            <?php if ($printer->keterangan): ?>
                                                <br><small class="text-muted"><?= esc($printer->keterangan) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $typeLabels = [
                                                'network' => '<span class="badge badge-info">Network</span>',
                                                'usb' => '<span class="badge badge-success">USB</span>',
                                                'file' => '<span class="badge badge-warning">File</span>',
                                                'windows' => '<span class="badge badge-secondary">Windows</span>'
                                            ];
                                            echo $typeLabels[$printer->tipe_printer] ?? $printer->tipe_printer;
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary"><?= strtoupper($printer->driver) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($printer->tipe_printer === 'network'): ?>
                                                <?= $printer->ip_address ?>:<?= $printer->port ?>
                                            <?php elseif (in_array($printer->tipe_printer, ['usb', 'file'])): ?>
                                                <?= esc($printer->path) ?>
                                            <?php else: ?>
                                                <?= esc($printer->path) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($printer->status === '1'): ?>
                                                <span class="badge badge-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($printer->is_default === '1'): ?>
                                                <span class="badge badge-success">Default</span>
                                            <?php else: ?>
                                                <a href="<?= base_url('pengaturan/printer/set-default/' . $printer->id) ?>"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    onclick="return confirm('Set printer ini sebagai default?')">
                                                    Set Default
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= base_url('pengaturan/printer/test/' . $printer->id) ?>"
                                                    class="btn btn-sm btn-info" title="Test Koneksi">
                                                    <i class="fas fa-plug"></i>
                                                </a>
                                                <a href="<?= base_url('pengaturan/printer/edit/' . $printer->id) ?>"
                                                    class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if ($printer->is_default !== '1'): ?>
                                                    <a href="<?= base_url('pengaturan/printer/delete/' . $printer->id) ?>"
                                                        class="btn btn-sm btn-danger" title="Hapus"
                                                        onclick="return confirm('Yakin ingin menghapus printer ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>