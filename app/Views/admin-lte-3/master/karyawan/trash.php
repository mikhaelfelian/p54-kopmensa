<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-17
 * 
 * Karyawan Trash View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('master/karyawan') ?>" class="btn btn-sm btn-secondary rounded-0">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="button" class="btn btn-sm btn-success rounded-0" onclick="restoreAll()">
                            <i class="fas fa-trash-restore"></i> Pulihkan Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-danger rounded-0" onclick="deleteAllPermanent()">
                            <i class="fas fa-trash"></i> Hapus Permanen Semua
                        </button>
                    </div>
                    <div class="col-md-6">
                        <?= form_open('master/karyawan/trash', ['method' => 'get', 'class' => 'float-right']) ?>
                        <div class="input-group input-group-sm">
                            <?= form_input([
                                'name'        => 'search',
                                'class'       => 'form-control rounded-0',
                                'value'       => $search ?? '',
                                'placeholder' => 'Cari...',
                            ]) ?>
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-primary rounded-0" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>L/P</th>
                            <th>Jabatan</th>
                            <th>Tanggal Hapus</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($karyawans)): ?>
                            <?php
                            $no = ($perPage * ($currentPage - 1)) + 1;
                            foreach ($karyawans as $karyawan):
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($karyawan->kode) ?></td>
                                    <td><?= esc($karyawan->nama) ?></td>
                                    <td><?= esc($karyawan->nik) ?></td>
                                    <td><?= jns_klm($karyawan->jns_klm) ?></td>
                                    <td><?= esc($karyawan->jabatan) ?></td>
                                    <td><?= tgl_indo($karyawan->deleted_at ?? '') ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= base_url("master/karyawan/restore/{$karyawan->id}") ?>"
                                                class="btn btn-success btn-sm rounded-0"
                                                onclick="return confirm('Apakah anda yakin ingin memulihkan data ini?')">
                                                <i class="fas fa-undo"></i> Pulihkan
                                            </a>
                                            <a href="<?= base_url("master/karyawan/delete_permanent/{$karyawan->id}") ?>"
                                                class="btn btn-danger btn-sm rounded-0"
                                                onclick="return confirm('Apakah anda yakin ingin menghapus permanen data ini?')">
                                                <i class="fas fa-trash-alt"></i> Hapus Permanen
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($pager): ?>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        <?= $pager->links('karyawan', 'adminlte_pagination') ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<script>
function restoreAll() {
    if (confirm('Apakah Anda yakin ingin memulihkan semua data?')) {
        // TODO: Implement restore all functionality
        alert('Fitur restore all belum diimplementasikan');
    }
}

function deleteAllPermanent() {
    if (confirm('Semua data akan dihapus secara permanen dan tidak dapat dikembalikan. Lanjutkan?')) {
        // TODO: Implement delete all permanent functionality
        alert('Fitur hapus permanen semua belum diimplementasikan');
    }
}
</script>

<?= $this->endSection() ?>

