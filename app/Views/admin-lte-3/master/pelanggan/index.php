<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-12
 * Github : github.com/mikhaelfelian
 * description : View for displaying pelanggan/customer data
 * This file represents the Pelanggan Index View.
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('master/pelanggan/create') ?>" class="btn btn-sm btn-primary rounded-0">
                            <i class="fas fa-plus"></i> Tambah Data
                        </a>
                    </div>
                    <div class="col-md-6">
                        <?= form_open('', ['method' => 'get', 'class' => 'float-right']) ?>
                        <div class="input-group input-group-sm">
                            <?= form_input([
                                'name' => 'keyword',
                                'class' => 'form-control rounded-0',
                                'value' => $keyword ?? '',
                                'placeholder' => 'Cari...'
                            ]) ?>
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-primary rounded-0" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>No. Telp</th>
                            <th>Alamat</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pelanggan)): ?>
                            <?php foreach ($pelanggan as $key => $row): ?>
                                <tr>
                                    <td><?= (($currentPage - 1) * $perPage) + $key + 1 ?></td>
                                    <td><?= esc($row->kode) ?></td>
                                    <td><?= esc($row->nama) ?></td>
                                    <td><?= esc($row->no_telp) ?></td>
                                    <td><?= esc($row->alamat) ?></td>
                                    <td>
                                        <?php
                                        $tipeLabels = [
                                            '0' => '-',
                                            '1' => 'Anggota',
                                            '2' => 'Pelanggan'
                                        ];
                                        echo $tipeLabels[$row->tipe] ?? '-';
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= ($row->status == '1') ? 'success' : 'danger' ?>">
                                            <?= ($row->status == '1') ? 'Aktif' : 'Tidak Aktif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= base_url("master/pelanggan/view/$row->id") ?>"
                                                class="btn btn-info btn-sm rounded-0">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url("master/pelanggan/edit/$row->id") ?>"
                                                class="btn btn-warning btn-sm rounded-0">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url("master/pelanggan/delete/$row->id") ?>"
                                                class="btn btn-danger btn-sm rounded-0"
                                                onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>

                </div>
            <?php if ($pager): ?>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        <?= $pager->links('pelanggan', 'adminlte_pagination') ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
<?= $this->endSection() ?>