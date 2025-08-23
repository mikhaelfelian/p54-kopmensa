<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-08-23
 * 
 * Pelanggan Grup Index View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <a href="<?= base_url('master/customer-group/create') ?>" class="btn btn-sm btn-primary rounded-0">
                    <i class="fas fa-plus"></i> Tambah Grup
                </a>
                <a href="<?= base_url('master/customer-group/trash') ?>" class="btn btn-sm btn-danger rounded-0">
                    <i class="fas fa-trash"></i> Sampah (<?= $trashCount ?>)
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?= form_open('master/customer-group', ['method' => 'get']) ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th class="text-left">Nama Grup</th>
                        <th class="text-left">Deskripsi</th>
                        <th class="text-center">Jumlah Member</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>
                            <?= form_input([
                                'name' => 'keyword',
                                'value' => $keyword ?? '',
                                'class' => 'form-control form-control-sm rounded-0',
                                'placeholder' => 'Cari grup atau deskripsi...'
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
                                <td width="25%">
                                    <span class="badge badge-primary"><?= esc($grup->grup) ?></span>
                                </td>
                                <td width="35%"><?= esc($grup->deskripsi ?? '-') ?></td>
                                <td class="text-center" width="15%">
                                    <span class="badge badge-info"><?= $grup->member_count ?? 0 ?></span>
                                </td>
                                <td class="text-center" width="10%">
                                    <?php if ($grup->status == '1'): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center" width="12%">
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/customer-group/detail/{$grup->id}") ?>"
                                            class="btn btn-info btn-sm rounded-0" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url("master/customer-group/members/{$grup->id}") ?>"
                                            class="btn btn-success btn-sm rounded-0" title="Kelola Member">
                                            <i class="fas fa-users"></i>
                                        </a>
                                        <a href="<?= base_url("master/customer-group/edit/{$grup->id}") ?>"
                                            class="btn btn-warning btn-sm rounded-0" title="Edit Grup">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url("master/customer-group/delete/{$grup->id}") ?>"
                                            class="btn btn-danger btn-sm rounded-0" title="Hapus Grup"
                                            onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                            <i class="fas fa-trash"></i>
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
