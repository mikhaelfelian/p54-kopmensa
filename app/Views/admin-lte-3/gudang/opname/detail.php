<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-12
 * Github : github.com/mikhaelfelian
 * description : View for displaying stock opname detail.
 * This file represents the opname detail view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Detail Stok Opname</h3>
                <div class="card-tools">
                    <a href="<?= base_url('gudang/opname') ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <?php if ($opname->status == '0'): ?>
                        <a href="<?= base_url("gudang/opname/edit/{$opname->id}") ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="<?= base_url("gudang/opname/input/{$opname->id}") ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Input Item
                        </a>
                    <?php endif ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>ID Opname</strong></td>
                                <td>: <?= $opname->id ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Opname</strong></td>
                                <td>: <?= isset($opname->tgl_masuk) ? date('d/m/Y', strtotime($opname->tgl_masuk)) : date('d/m/Y', strtotime($opname->created_at)) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Gudang</strong></td>
                                <td>: <?= $gudang ? $gudang->nama : '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: 
                                    <?php if ($opname->status == '0'): ?>
                                        <span class="badge badge-warning">Draft</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Selesai</span>
                                    <?php endif ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Dibuat Oleh</strong></td>
                                <td>: <?= $opname->user_name ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Dibuat</strong></td>
                                <td>: <?= date('d/m/Y H:i', strtotime($opname->created_at)) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Keterangan</strong></td>
                                <td>: <?= $opname->keterangan ?: '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Reset</strong></td>
                                <td>: 
                                    <?php if ($opname->reset == '0'): ?>
                                        <span class="badge badge-info">Belum Diproses</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Sudah Diproses</span>
                                    <?php endif ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <?php if (!empty($details)): ?>
                    <hr>
                    <h5>Detail Item Opname</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Item</th>
                                    <th>Satuan</th>
                                    <th>Stok Sistem</th>
                                    <th>Stok Fisik</th>
                                    <th>Selisih</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($details as $index => $detail): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= $detail->kode ?></td>
                                        <td><?= $detail->item ?></td>
                                        <td><?= $detail->satuan ?></td>
                                        <td><?= number_format($detail->jml_sys, 2) ?></td>
                                        <td><?= number_format($detail->jml_so, 2) ?></td>
                                        <td>
                                            <?php 
                                            $selisih = $detail->jml_sls;
                                            $class = $selisih > 0 ? 'text-success' : ($selisih < 0 ? 'text-danger' : 'text-muted');
                                            ?>
                                            <span class="<?= $class ?>">
                                                <?= number_format($selisih, 2) ?>
                                            </span>
                                        </td>
                                        <td><?= $detail->keterangan ?: '-' ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <hr>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Belum ada item yang diinput untuk opname ini.
                        <?php if ($opname->status == '0'): ?>
                            <a href="<?= base_url("gudang/opname/input/{$opname->id}") ?>" class="btn btn-sm btn-primary ml-2">
                                Input Item Sekarang
                            </a>
                        <?php endif ?>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 