<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-08-23
 * Github : github.com/mikhaelfelian
 * description : View for viewing customer group details
 * This file represents the View for viewing customer group details.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-6">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Detail Data Grup Pelanggan</h3>
                <div class="card-tools"></div>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>ID</strong></td>
                        <td width="5%">:</td>
                        <td width="65%"><?= $grup->id ?></td>
                    </tr>
                    <tr>
                        <td><strong>Nama Grup</strong></td>
                        <td>:</td>
                        <td><span class="badge badge-primary"><?= esc($grup->grup) ?></span></td>
                    </tr>
                    <tr>
                        <td><strong>Nama Pelanggan</strong></td>
                        <td>:</td>
                        <td><?= esc($grup->nama_pelanggan ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Telepon</strong></td>
                        <td>:</td>
                        <td><?= esc($grup->telepon_pelanggan ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Alamat</strong></td>
                        <td>:</td>
                        <td><?= esc($grup->alamat_pelanggan ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Deskripsi</strong></td>
                        <td>:</td>
                        <td><?= esc($grup->deskripsi ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                        <td>:</td>
                        <td>
                            <?php if ($grup->status == '1'): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Dibuat</strong></td>
                        <td>:</td>
                        <td><?= $grup->created_at ?? '-' ?></td>
                    </tr>
                    <tr>
                        <td><strong>Diupdate</strong></td>
                        <td>:</td>
                        <td><?= $grup->updated_at ?? '-' ?></td>
                    </tr>
                </table>
            </div>
            <div class="card-footer">
                <a href="<?= base_url('master/customer-group/edit/' . $grup->id) ?>" class="btn btn-warning rounded-0">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="<?= base_url('master/customer-group') ?>" class="btn btn-secondary rounded-0">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
