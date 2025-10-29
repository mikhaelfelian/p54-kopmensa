<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-17
 * 
 * Karyawan Detail View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-3">
        <!-- Profile Image -->
        <div class="card card-primary card-outline rounded-0">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle"
                         src="<?= base_url($karyawan->file_foto ?: 'public/assets/theme/admin-lte-3/dist/img/icon_putra.png') ?>"
                         alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?= esc($karyawan->nama_pgl) ?></h3>

                <p class="text-muted text-center"><?= esc($karyawan->jabatan) ?></p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Kode</b> <a class="float-right"><?= esc($karyawan->kode) ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>NIP</b> <a class="float-right"><?= esc($karyawan->nik) ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card rounded-0">
            <div class="card-header p-2">
                <h3 class="card-title">Detail Informasi</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Nama Lengkap</strong>
                        <p class="text-muted">
                            <?= esc($karyawan->nama_dpn ? $karyawan->nama_dpn . '. ' : '') ?>
                            <?= esc($karyawan->nama) ?>
                            <?= esc($karyawan->nama_blk ? ', ' . $karyawan->nama_blk : '') ?>
                        </p>
                        <hr>

                        <strong>Tempat, Tanggal Lahir</strong>
                        <p class="text-muted">
                            <?= esc($karyawan->tmp_lahir) ?>, 
                            <?= tgl_indo($karyawan->tgl_lahir) ?>
                            (<?= usia_lkp($karyawan->tgl_lahir) ?>)
                        </p>
                        <hr>

                        <strong>Jenis Kelamin</strong>
                        <p class="text-muted"><?= jns_klm($karyawan->jns_klm) ?></p>
                        <hr>

                        <strong>Alamat KTP</strong>
                        <p class="text-muted">
                            <?= esc($karyawan->alamat) ?><br>
                            RT <?= esc($karyawan->rt) ?> / RW <?= esc($karyawan->rw) ?><br>
                            Kel. <?= esc($karyawan->kelurahan) ?><br>
                            Kec. <?= esc($karyawan->kecamatan) ?><br>
                            <?= esc($karyawan->kota) ?>
                        </p>
                        <hr>
                    </div>
                    <div class="col-md-6">
                        <strong>Alamat Domisili</strong>
                        <p class="text-muted"><?= nl2br(esc($karyawan->alamat_domisili)) ?: '-' ?></p>
                        <hr>

                        <strong>No. HP</strong>
                        <p class="text-muted"><?= esc($karyawan->no_hp) ?></p>
                        <hr>

                        <strong>Jabatan</strong>
                        <p class="text-muted"><?= esc($karyawan->jabatan) ?: '-' ?></p>
                        <hr>
                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/karyawan') ?>" class="btn btn-default rounded-0">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <div class="float-right">
                    <a href="<?= base_url("master/karyawan/edit/{$karyawan->id}") ?>" 
                       class="btn btn-warning rounded-0">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="<?= base_url("master/karyawan/delete/{$karyawan->id}") ?>"
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