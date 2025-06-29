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
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('gudang/stok/create') ?>" class="btn btn-sm btn-primary rounded-0">
                            <i class="fas fa-plus"></i> Tambah Data
                        </a>
                        <?php if (isset($trashCount) && $trashCount > 0): ?>
                            <a href="<?= base_url('gudang/stok/trash') ?>" class="btn btn-sm btn-danger rounded-0">
                                <i class="fas fa-trash"></i> Arsip (<?= $trashCount ?>)
                            </a>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive">
                <?= form_open('gudang/stok', attributes: ['method' => 'get']) ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">No.</th>
                            <th width="80">Foto</th>
                            <th>Kategori</th>
                            <th>Item</th>
                            <th class="text-center">Stok Min</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>
                                <select name="kategori" class="form-control rounded-0">
                                    <option value="">- Kategori -</option>
                                    <?php if (isset($kategori)): ?>
                                        <?php foreach ($kategori as $kat_item): ?>
                                            <option value="<?= $kat_item->id ?>" <?= (isset($kat) && $kat == $kat_item->id) ? 'selected' : '' ?>>
                                                <?= $kat_item->kategori ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </th>
                            <th>
                                <?= form_input([
                                    'name' => 'keyword',
                                    'class' => 'form-control rounded-0',
                                    'placeholder' => 'Isikan Kode / Nama Item ...',
                                    'value' => esc($keyword ?? '')
                                ]) ?>
                            </th>
                            <th></th>
                            <th>
                                <button type="submit" class="btn btn-primary rounded-0"><i class="fa fa-search"></i>
                                    Filter</button>
                            </th>
                        </tr>
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $key => $row): ?>
                                <tr>
                                    <td class="text-center"><?= (($currentPage - 1) * $perPage) + $key + 1 ?>.</td>
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
                                    <td><?= $row->kategori ?></td>
                                    <td>
                                        <?= $row->kode ?>
                                        <?= br() ?>
                                        <?= $row->item ?>
                                        <?= br() ?>
                                        <small><b>Rp. <?= format_angka($row->harga_jual) ?></b></small>
                                        <?php if (!empty($row->deskripsi)): ?>
                                            <?= br() ?>
                                            <small><i>(<?= strtolower($row->deskripsi) ?>)</i></small>
                                        <?php endif; ?>
                                        <?= br() ?>
                                        <small><i><?= $row->barcode ?></i></small>
                                        <?php if (function_exists('isItemActive')): ?>
                                            <?php $statusInfo = isItemActive($row->status); ?>
                                            <?= br() ?>
                                            <span class="badge badge-<?= $statusInfo['badge'] ?>"><?= $statusInfo['label'] ?></span>
                                        <?php else: ?>
                                            <?= br() ?>
                                            <span class="badge badge-<?= ($row->status == '1') ? 'success' : 'danger' ?>">
                                                <?= ($row->status == '1') ? 'Aktif' : 'Tidak Aktif' ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?= $row->jml_min ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= base_url("gudang/stok/detail/{$row->id}") ?>"
                                                class="btn btn-info btn-sm rounded-0">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="12" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
                <?= form_close() ?>
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