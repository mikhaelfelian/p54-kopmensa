<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h3 class="card-title">Item Settings - <?= esc($supplier->nama) ?></h3>
            </div>
            <div class="col-md-6 text-right">
                <a href="<?= base_url('master/supplier/items/' . $supplier->id . '/add') ?>" class="btn btn-sm btn-primary rounded-0">
                    <i class="fas fa-plus"></i> Tambah Item
                </a>
                <a href="<?= base_url('master/supplier') ?>" class="btn btn-sm btn-secondary rounded-0">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Filter Form -->
        <div class="row mb-3">
            <div class="col-md-12">
                <form method="GET" class="form-inline">
                    <div class="form-group mr-3">
                        <input type="text" name="keyword" class="form-control form-control-sm" 
                               placeholder="Cari item..." value="<?= esc($keyword) ?>">
                    </div>
                    <div class="form-group mr-3">
                        <select name="kategori" class="form-control form-control-sm">
                            <option value="">- Semua Kategori -</option>
                            <?php foreach ($kategoriList as $kat): ?>
                                <option value="<?= $kat->id ?>" <?= $kategori == $kat->id ? 'selected' : '' ?>>
                                    <?= esc($kat->kategori) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mr-3">
                        <select name="merk" class="form-control form-control-sm">
                            <option value="">- Semua Merk -</option>
                            <?php foreach ($merkList as $m): ?>
                                <option value="<?= $m->id ?>" <?= $merk == $m->id ? 'selected' : '' ?>>
                                    <?= esc($m->merk) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mr-3">
                        <select name="per_page" class="form-control form-control-sm">
                            <option value="20" <?= $perPage == 20 ? 'selected' : '' ?>>20</option>
                            <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
                            <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-info">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="<?= base_url('master/supplier/items/' . $supplier->id) ?>" class="btn btn-sm btn-secondary ml-2">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </form>
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm">
                <thead>
                    <tr class="bg-primary">
                        <th width="5%">No</th>
                        <th width="10%">Kode</th>
                        <th>Item</th>
                        <th width="10%">Kategori</th>
                        <th width="10%">Merk</th>
                        <th width="8%">Satuan</th>
                        <th width="10%" class="text-right">Harga Beli</th>
                        <th width="10%" class="text-right">Harga Jual</th>
                        <th width="8%" class="text-center">Min Stok</th>
                        <th width="12%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php
                        $no = 1;
                        foreach ($items as $item):
                        ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?>.</td>
                                <td><?= esc($item->kode) ?></td>
                                <td>
                                    <strong><?= esc($item->item) ?></strong>
                                    <?php if (!empty($item->barcode)): ?>
                                        <br><small class="text-muted">Barcode: <?= esc($item->barcode) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($item->kategori_nama ?? '-') ?></td>
                                <td><?= esc($item->merk_nama ?? '-') ?></td>
                                <td><?= esc($item->satuan_nama ?? '-') ?></td>
                                <td class="text-right"><?= number_format($item->harga_beli, 0, ',', '.') ?></td>
                                <td class="text-right"><?= number_format($item->harga_jual, 0, ',', '.') ?></td>
                                <td class="text-center"><?= $item->min_stok ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url("master/item/detail/{$item->id}") ?>"
                                            class="btn btn-info btn-xs" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url("master/item/edit/{$item->id}") ?>"
                                            class="btn btn-warning btn-xs" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">
                                <div class="py-3">
                                    <i class="fas fa-box fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">Belum ada item untuk supplier ini</p>
                                    <a href="<?= base_url('master/supplier/items/' . $supplier->id . '/add') ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah Item Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if (!empty($items) && $pager): ?>
        <div class="card-footer">
            <?= $pager->links('items', 'default_full') ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
