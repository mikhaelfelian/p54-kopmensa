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
            <?= form_open('master/customer/trash', ['method' => 'get']) ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Kode</th>
                        <th width="35%">Nama</th>
                        <th width="15%" class="text-center">Tipe</th>
                        <th width="15%" class="text-center">Status</th>
                        <th width="20%" class="text-center">Aksi</th>
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
                        <th class="text-center">
                            <?= form_dropdown(
                                'tipe',
                                [
                                    '' => '- Semua -',
                                    '1' => 'Anggota',
                                    '2' => 'Pelanggan'
                                ],
                                $selectedTipe ?? '',
                                'class="form-control form-control-sm rounded-0"'
                            ) ?>
                        </th>
                        <th></th>
                        <th class="text-center">
                            <button type="submit" class="btn btn-sm btn-primary rounded-0">
                                <i class="fas fa-search"></i>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pelanggans)): ?>
                        <?php $no = 1; foreach ($pelanggans as $pelanggan): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($pelanggan->kode) ?></td>
                                <td><?= esc($pelanggan->nama) ?></td>
                                <td class="text-center"><?= $getTipeLabel($pelanggan->tipe) ?></td>
                                <td class="text-center"><?= $getStatusLabel($pelanggan->status) ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/customer/restore/{$pelanggan->id}") ?>"
                                            class="btn btn-success btn-sm rounded-0" title="Pulihkan">
                                            <i class="fas fa-trash-restore"></i>
                                        </a>
                                        <a href="<?= base_url("master/customer/delete_permanent/{$pelanggan->id}") ?>"
                                            class="btn btn-danger btn-sm rounded-0" title="Hapus Permanen"
                                            onclick="return confirm('Data akan dihapus secara permanen. Lanjutkan?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif ?>
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