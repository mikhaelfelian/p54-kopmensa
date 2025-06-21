<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-18
 * Github : github.com/mikhaelfelian
 * description : View for displaying item/product data
 * This file represents the Items Index View.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('master/item/create') ?>" class="btn btn-sm btn-primary rounded-0">
                            <i class="fas fa-plus"></i> Tambah Data
                        </a>
                        <?php if ($trashCount > 0): ?>
                            <a href="<?= base_url('master/item/trash') ?>" class="btn btn-sm btn-danger rounded-0">
                                <i class="fas fa-trash"></i> Arsip (<?= $trashCount ?>)
                            </a>
                        <?php endif ?>
                    </div>
                    <div class="col-md-6">
                        <form action="<?= base_url('master/item') ?>" method="get" class="float-right">
                            <div class="input-group input-group-sm">
                                <input type="text" name="keyword" class="form-control rounded-0" value="<?= $keyword ?? '' ?>" placeholder="Cari...">
                                <div class="input-group-append">
                                    <button class="btn btn-sm btn-primary rounded-0" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th width="80">Foto</th>
                            <th>Kode</th>
                            <th>Barcode</th>
                            <th>Nama Item</th>
                            <th>Kategori</th>
                            <th>Merk</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Stok Min</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="12" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $key => $row): ?>
                                <tr>
                                    <td><?= (($currentPage - 1) * $perPage) + $key + 1 ?></td>
                                    <td>
                                        <?php if (!empty($row->foto)): ?>
                                            <img src="<?= base_url($row->foto) ?>" 
                                                 alt="<?= $row->item ?>" 
                                                 class="img-thumbnail" 
                                                 style="width: 50px; height: 50px; object-fit: cover;"
                                                 data-toggle="tooltip" 
                                                 title="<?= $row->item ?>">
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $row->kode ?></td>
                                    <td><?= $row->barcode ?></td>
                                    <td><?= $row->item ?></td>
                                    <td><?= $row->id_kategori ?></td>
                                    <td><?= $row->id_merk ?></td>
                                    <td><?= format_angka($row->harga_beli) ?></td>
                                    <td><?= number_format($row->harga_jual, 0, ',', '.') ?></td>
                                    <td><?= $row->jml_min ?></td>
                                    <td>
                                        <span class="badge badge-<?= ($row->status == '1') ? 'success' : 'danger' ?>">
                                            <?= ($row->status == '1') ? 'Aktif' : 'Tidak Aktif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= base_url("master/item/edit/{$row->id}") ?>"
                                                class="btn btn-warning btn-sm rounded-0">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url("master/item/upload/{$row->id}") ?>"
                                                class="btn btn-info btn-sm rounded-0">
                                                <i class="fas fa-upload"></i>
                                            </a>
                                            <a href="<?= base_url("master/item/delete/{$row->id}") ?>"
                                                class="btn btn-danger btn-sm rounded-0"
                                                onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
            <?php if ($pager): ?>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        <?= $pager->links('items', 'adminlte_pagination') ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
<?= $this->endSection() ?> 