<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-20
 * Github : github.com/mikhaelfelian
 * description : Trash view for customer (pelanggan) data
 * This file represents the Pelanggan Trash View.
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <a href="<?= base_url('master/customer') ?>" class="btn btn-sm btn-secondary rounded-0">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="button" class="btn btn-sm btn-danger rounded-0" onclick="restoreAll()">
                    <i class="fas fa-trash-restore"></i> Pulihkan Semua
                </button>
                <button type="button" class="btn btn-sm btn-danger rounded-0" onclick="deleteAllPermanent()">
                    <i class="fas fa-trash"></i> Hapus Permanen Semua
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
        <?= form_open('master/customer', ['method' => 'get']) ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th class="text-left">Kode</th>
                        <th class="text-left">Nama</th>
                        <th class="text-left">Alamat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>
                            <?= form_input([
                                'name' => 'search',
                                'value' => $search,
                                'class' => 'form-control form-control-sm rounded-0',
                                'placeholder' => 'Cari...'
                            ]) ?>
                        </th>
                        <th></th>
                        <th></th>
                        <th class="text-center">
                            <button type="submit" class="btn btn-sm btn-primary rounded-0">
                                <i class="fas fa-filter"></i>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pelanggans)): ?>
                        <?php
                        $no = ($perPage * ($currentPage - 1)) + 1;
                        foreach ($pelanggans as $pelanggan):
                            ?>
                            <tr>
                                <td class="text-center" width="3%"><?= $no++ ?>.</td>
                                <td width="15%"><?= esc($pelanggan->kode) ?></td>
                                <td width="40%"><?= esc($pelanggan->nama) ?></td>
                                <td width="30%"><?= esc($pelanggan->alamat) ?></td>
                                <td class="text-center" width="12%">
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/customer/detail/{$pelanggan->id}") ?>"
                                            class="btn btn-info btn-sm rounded-0">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url("master/customer/edit/{$pelanggan->id}") ?>"
                                            class="btn btn-warning btn-sm rounded-0">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url("master/customer/delete/{$pelanggan->id}") ?>"
                                            class="btn btn-danger btn-sm rounded-0"
                                            onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?= form_close() ?>
        </div>
    </div>
    <div class="card-footer">
        <?= $pager->links('pelanggan', 'adminlte_pagination') ?>
    </div>
</div>

<?= $this->section('js') ?>
<script>
function restoreAll() {
    if (confirm('Pulihkan semua data?')) {
        window.location.href = '<?= base_url("master/customer/restore-all") ?>';
    }
}

function deleteAllPermanent() {
    if (confirm('Semua data akan dihapus secara permanen. Lanjutkan?')) {
        window.location.href = '<?= base_url("master/customer/delete-all-permanent") ?>';
    }
}
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?> 