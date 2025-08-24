<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * Platform Index View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <a href="<?= base_url('master/platform/create') ?>" class="btn btn-sm btn-primary rounded-0">
                    <i class="fas fa-plus"></i> Tambah Data
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?= form_open('master/platform', ['method' => 'get']) ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Kode</th>
                        <th width="15%">Platform</th>
                        <th width="25%">Keterangan</th>
                        <th width="10%">Persentase</th>
                        <th width="10%">Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th colspan="4">
                            <?= form_input([
                                'name' => 'search',
                                'value' => $search,
                                'class' => 'form-control form-control-sm rounded-0',
                                'placeholder' => 'Cari data...'
                            ]) ?>
                        </th>
                        <th>
                            <?= form_dropdown(
                                'status',
                                [
                                    '' => 'Semua Status',
                                    '1' => 'Aktif',
                                    '0' => 'Tidak Aktif'
                                ],
                                $status,
                                'class="form-control form-control-sm rounded-0"'
                            ) ?>
                        </th>
                        <th class="text-center">
                            <button type="submit" class="btn btn-sm btn-primary rounded-0">
                                <i class="fas fa-search"></i>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($platforms)): ?>
                        <?php
                        $no = ($perPage * ($currentPage - 1)) + 1;
                        foreach ($platforms as $platform):
                            ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?>.</td>
                                <td><?= esc($platform->kode) ?></td>
                                <td><?= esc($platform->platform) ?></td>
                                <td><?= esc($platform->keterangan) ?></td>
                                <td><?= $platform->persen ? number_format($platform->persen, 1) . '%' : '-' ?></td>
                                <td><?= $getStatusLabel($platform->status) ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/platform/detail/{$platform->id}") ?>"
                                            class="btn btn-info btn-sm rounded-0">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($platform->status_sys != '1'): ?>
                                            <a href="<?= base_url("master/platform/edit/{$platform->id}") ?>"
                                                class="btn btn-warning btn-sm rounded-0">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url("master/platform/delete/{$platform->id}") ?>"
                                                class="btn btn-danger btn-sm rounded-0"
                                                onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?= form_close() ?>
        </div>
    </div>
    <div class="card-footer">
        <?= $pager->links('platform', 'adminlte_pagination') ?>
    </div>
</div>
<?= $this->endSection() ?> 