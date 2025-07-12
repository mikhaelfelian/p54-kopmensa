<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-06-17
 * 
 * Pelanggan Index View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <a href="<?= base_url('master/customer/create') ?>" class="btn btn-sm btn-primary rounded-0">
                    <i class="fas fa-plus"></i> Tambah Data
                </a>
                <a href="<?= base_url('master/customer/trash') ?>" class="btn btn-sm btn-danger rounded-0">
                    <i class="fas fa-trash"></i> Sampah (<?= $trashCount ?>)
                </a>
                <a href="<?= base_url('master/customer/export') ?>?<?= $_SERVER['QUERY_STRING'] ?>"
                    class="btn btn-sm btn-success rounded-0">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?= form_open('master/customer', ['method' => 'get']) ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th class="text-left">Kode</th>
                        <th class="text-left">Nama</th>
                        <th class="text-left">Alamat</th>
                        <th class="text-right">Limit Saldo</th>
                        <th class="text-center">Aksi</th>
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
                    <?php if (!empty($pelanggans)): ?>
                        <?php
                        $no = ($perPage * ($currentPage - 1)) + 1;
                        foreach ($pelanggans as $pelanggan):
                            ?>
                            <tr>
                                <td class="text-center" width="3%"><?= $no++ ?>.</td>
                                <td width="15%"><?= esc($pelanggan->kode) ?></td>
                                <td width="30%"><?= esc($pelanggan->nama) ?></td>
                                <td width="25%"><?= esc($pelanggan->alamat) ?></td>
                                <td class="text-right" width="15%">Rp <?= number_format($pelanggan->limit ?? 0, 0, ',', '.') ?></td>
                                <td class="text-center" width="12%">
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/customer/detail/{$pelanggan->id}") ?>"
                                            class="btn btn-info btn-sm rounded-0">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url("master/customer/edit/{$pelanggan->id}") ?>"
                                            class="btn btn-warning btn-sm rounded-0">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url("master/customer/delete/{$pelanggan->id}") ?>"
                                            class="btn btn-danger btn-sm rounded-0"
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
        <?= $pager->links('pelanggan', 'adminlte_pagination') ?>
    </div>
</div>
<?= $this->endSection() ?>