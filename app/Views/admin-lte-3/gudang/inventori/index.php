<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2024-06-21
 * Github : github.com/mikhaelfelian
 * description : View for displaying stockable items.
 * This file represents the inventory index view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Data Item Stockable</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>SKU</th>
                                <th>Barcode</th>
                                <th>Item</th>
                                <th>Kategori</th>
                                <th>Merk</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($items)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($items as $key => $row): ?>
                                    <tr>
                                        <td><?= (($currentPage - 1) * $perPage) + $key + 1 ?></td>
                                        <td>
                                            <?php if (!empty($row->foto)): ?>
                                                <img src="<?= base_url($row->foto) ?>" alt="<?= $row->item ?>" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="fas fa-image text-muted"></i></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $row->kode ?></td>
                                        <td><?= $row->barcode ?></td>
                                        <td><?= $row->item ?></td>
                                        <td><?= $row->id_kategori ?></td>
                                        <td><?= $row->id_merk ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('gudang/stok/' . url_title($row->item, '-', true) . '/' . $row->id) ?>" class="btn btn-sm btn-info" data-toggle="tooltip" title="Lihat Stok"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix">
                <?= $pager->links('items', 'bootstrap_pagination') ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Aksi</h3>
            </div>
            <div class="card-body">
                <form action="<?= base_url('gudang/inventori') ?>" method="get">
                    <div class="input-group">
                        <input type="text" name="keyword" class="form-control" placeholder="Cari item..." value="<?= $keyword ?? '' ?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 