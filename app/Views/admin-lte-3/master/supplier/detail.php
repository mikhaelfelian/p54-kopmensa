<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * Supplier Detail View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-4">
        <!-- Profile Image -->
        <div class="card card-primary card-outline rounded-0">
            <div class="card-body box-profile">
                <h3 class="profile-username text-center"><?= esc($supplier->nama) ?></h3>
                <p class="text-muted text-center"><?= $getTipeLabel($supplier->tipe) ?></p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Kode</b> <a class="float-right"><?= esc($supplier->kode) ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>NPWP</b> <a class="float-right"><?= esc($supplier->npwp) ?: '-' ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>Status</b> <a class="float-right"><?= $getStatusLabel($supplier->status) ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card rounded-0">
            <div class="card-header p-2">
                <h3 class="card-title">Detail Informasi</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Alamat</strong>
                        <p class="text-muted">
                            <?= esc($supplier->alamat) ?><br>
                            RT <?= esc($supplier->rt) ?> / RW <?= esc($supplier->rw) ?><br>
                            Kel. <?= esc($supplier->kelurahan) ?><br>
                            Kec. <?= esc($supplier->kecamatan) ?><br>
                            <?= esc($supplier->kota) ?>
                        </p>
                        <hr>
                    </div>
                    <div class="col-md-6">
                        <strong>No. Telepon</strong>
                        <p class="text-muted"><?= esc($supplier->no_tlp) ?: '-' ?></p>
                        <hr>

                        <strong>No. HP</strong>
                        <p class="text-muted"><?= esc($supplier->no_hp) ?></p>
                        <hr>
                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/supplier') ?>" class="btn btn-default rounded-0">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <div class="float-right">
                    <a href="<?= base_url("master/supplier/edit/{$supplier->id}") ?>" 
                       class="btn btn-warning rounded-0">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="<?= base_url("master/supplier/delete/{$supplier->id}") ?>"
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