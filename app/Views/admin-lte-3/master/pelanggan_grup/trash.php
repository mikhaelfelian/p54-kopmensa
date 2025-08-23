<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-08-23
 * 
 * Pelanggan Grup Trash View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <a href="<?= base_url('master/customer-group') ?>" class="btn btn-sm btn-primary rounded-0">
                    <i class="fas fa-arrow-left"></i> Kembali ke List
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?= form_open('master/customer-group/trash', ['method' => 'get']) ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th class="text-left">Nama Grup</th>
                        <th class="text-left">Nama Pelanggan</th>
                        <th class="text-left">Telepon</th>
                        <th class="text-left">Deskripsi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>
                            <?= form_input([
                                'name' => 'keyword',
                                'value' => $keyword ?? '',
                                'class' => 'form-control form-control-sm rounded-0',
                                'placeholder' => 'Cari grup, deskripsi, atau nama pelanggan...'
                            ]) ?>
                        </th>
                        <th></th>
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
                    <?php if (!empty($grup_list)): ?>
                        <?php
                        $no = ($perPage * ($currentPage - 1)) + 1;
                        foreach ($grup_list as $grup):
                            ?>
                            <tr>
                                <td class="text-center" width="3%"><?= $no++ ?>.</td>
                                <td width="20%">
                                    <span class="badge badge-secondary"><?= esc($grup->grup) ?></span>
                                </td>
                                <td width="25%"><?= esc($grup->nama_pelanggan ?? '-') ?></td>
                                <td width="20%"><?= esc($grup->telepon_pelanggan ?? '-') ?></td>
                                <td width="25%"><?= esc($grup->deskripsi ?? '-') ?></td>
                                <td class="text-center" width="7%">
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/customer-group/restore/{$grup->id}") ?>"
                                            class="btn btn-success btn-sm rounded-0"
                                            onclick="return confirm('Apakah anda yakin ingin mengembalikan data ini?')">
                                            <i class="fas fa-undo"></i>
                                        </a>
                                        <a href="<?= base_url("master/customer-group/delete_permanent/{$grup->id}") ?>"
                                            class="btn btn-danger btn-sm rounded-0"
                                            onclick="return confirm('Apakah anda yakin ingin menghapus data ini secara permanen? Data tidak dapat dikembalikan!')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?= form_close() ?>
        </div>
    </div>
    <div class="card-footer">
        <?= $pager->links('adminlte_pagination') ?>
    </div>
</div>
<?= $this->endSection() ?>
