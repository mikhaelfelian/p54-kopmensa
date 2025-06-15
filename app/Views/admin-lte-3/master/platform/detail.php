<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * Platform Detail View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Detail Platform</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Kode</th>
                                <td><?= esc($platform->kode) ?></td>
                            </tr>
                            <tr>
                                <th>Platform</th>
                                <td><?= esc($platform->platform) ?></td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td><?= nl2br(esc($platform->keterangan)) ?: '-' ?></td>
                            </tr>
                            <tr>
                                <th>Persentase</th>
                                <td><?= $platform->persen ? number_format($platform->persen, 1) . '%' : '-' ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td><?= $platform->status === '1' ? 'Aktif' : 'Tidak Aktif' ?></td>
                            </tr>
                            <tr>
                                <th>Dibuat</th>
                                <td><?= $platform->created_at ?></td>
                            </tr>
                            <tr>
                                <th>Diupdate</th>
                                <td><?= $platform->updated_at ?: '-' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/platform') ?>" class="btn btn-default rounded-0">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <div class="float-right">
                    <a href="<?= base_url("master/platform/edit/{$platform->id}") ?>" 
                       class="btn btn-warning rounded-0">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="<?= base_url("master/platform/delete/{$platform->id}") ?>"
                       class="btn btn-danger rounded-0"
                       onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                        <i class="fas fa-trash"></i> Hapus
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 