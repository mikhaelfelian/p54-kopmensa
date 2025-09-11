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
                        <a href="<?= base_url('gudang/stok/export_excel') . '?' . http_build_query($_GET) ?>" class="btn btn-sm btn-success rounded-0">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <?php if (isset($trashCount) && $trashCount > 0): ?>
                            <a href="<?= base_url('gudang/stok/trash') ?>" class="btn btn-sm btn-danger rounded-0">
                                <i class="fas fa-trash"></i> Arsip (<?= $trashCount ?>)
                            </a>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Section -->
                <div class="card card-outline card-primary mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-filter"></i> Filter Data
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?= form_open('gudang/stok', ['method' => 'get']) ?>
                        <div class="row">
                            <!-- Keyword Search -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Cari Item</label>
                                    <?= form_input([
                                        'name' => 'keyword',
                                        'class' => 'form-control rounded-0',
                                        'placeholder' => 'Kode / Nama Item / Barcode',
                                        'value' => esc($keyword ?? '')
                                    ]) ?>
                                </div>
                            </div>
                            
                            <!-- Outlet Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Filter Warehouse / Store</label>
                                    <select name="outlet_filter" class="form-control rounded-0">
                                        <option value="">- Semua Warehouse / Store -</option>
                                        <?php if (isset($outlets) && !empty($outlets)): ?>
                                            <?php foreach ($outlets as $outlet): ?>
                                                <option value="<?= $outlet->id ?>" <?= (isset($outlet_filter) && $outlet_filter == $outlet->id) ? 'selected' : '' ?>>
                                                    <?= esc($outlet->nama) ?> (<?= esc($outlet->kode) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Items per page -->
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Items per Page</label>
                                    <select name="per_page" class="form-control rounded-0">
                                        <option value="50" <?= (isset($_GET['per_page']) && $_GET['per_page'] == '50') ? 'selected' : '' ?>>50</option>
                                        <option value="100" <?= (isset($_GET['per_page']) && $_GET['per_page'] == '100') ? 'selected' : 'selected' ?>>100 (Default)</option>
                                        <option value="200" <?= (isset($_GET['per_page']) && $_GET['per_page'] == '200') ? 'selected' : '' ?>>200</option>
                                        <option value="500" <?= (isset($_GET['per_page']) && $_GET['per_page'] == '500') ? 'selected' : '' ?>>500</option>
                                        <option value="1000" <?= (isset($_GET['per_page']) && $_GET['per_page'] == '1000') ? 'selected' : '' ?>>1000</option>
                                        <option value="-1" <?= (isset($_GET['per_page']) && $_GET['per_page'] == '-1') ? 'selected' : '' ?>>All Items</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Stockable Filter -->
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Status Stok</label>
                                    <select name="stok" class="form-control rounded-0">
                                        <option value="">- Semua -</option>
                                        <option value="1" <?= (isset($stok) && $stok == '1') ? 'selected' : '' ?>>Stockable</option>
                                        <option value="0" <?= (isset($stok) && $stok == '0') ? 'selected' : '' ?>>Non Stockable</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Category Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Kategori</label>
                                    <select name="kategori" class="form-control rounded-0">
                                        <option value="">- Semua Kategori -</option>
                                        <?php if (isset($kategori)): ?>
                                            <?php foreach ($kategori as $kat_item): ?>
                                                <option value="<?= $kat_item->id ?>" <?= (isset($kat) && $kat == $kat_item->id) ? 'selected' : '' ?>>
                                                    <?= $kat_item->kategori ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Brand/Merk Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Merk</label>
                                    <select name="merk" class="form-control rounded-0">
                                        <option value="">- Semua Merk -</option>
                                        <?php if (isset($merk_list)): ?>
                                            <?php foreach ($merk_list as $merk_item): ?>
                                                <option value="<?= $merk_item->id ?>" <?= (isset($merk) && $merk == $merk_item->id) ? 'selected' : '' ?>>
                                                    <?= $merk_item->merk ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Min Stock Filter -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Stok Minimum</label>
                                    <div class="input-group">
                                        <select name="min_stok_operator" class="form-control rounded-0" style="max-width: 80px;">
                                            <option value="">-</option>
                                            <option value="=" <?= (isset($min_stok_operator) && $min_stok_operator == '=') ? 'selected' : '' ?>>=</option>
                                            <option value="<=" <?= (isset($min_stok_operator) && $min_stok_operator == '<=') ? 'selected' : '' ?>>≤</option>
                                            <option value=">=" <?= (isset($min_stok_operator) && $min_stok_operator == '>=') ? 'selected' : '' ?>>≥</option>
                                            <option value=">" <?= (isset($min_stok_operator) && $min_stok_operator == '>') ? 'selected' : '' ?>>></option>
                                            <option value="<" <?= (isset($min_stok_operator) && $min_stok_operator == '<') ? 'selected' : '' ?>><</option>
                                        </select>
                                        <input type="number" name="min_stok_value" class="form-control rounded-0" placeholder="Nilai" value="<?= esc($min_stok_value ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Harga Beli Filter -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Harga Beli</label>
                                    <div class="input-group">
                                        <select name="harga_beli_operator" class="form-control rounded-0" style="max-width: 80px;">
                                            <option value="">-</option>
                                            <option value="=" <?= (isset($harga_beli_operator) && $harga_beli_operator == '=') ? 'selected' : '' ?>>=</option>
                                            <option value="<=" <?= (isset($harga_beli_operator) && $harga_beli_operator == '<=') ? 'selected' : '' ?>>≤</option>
                                            <option value=">=" <?= (isset($harga_beli_operator) && $harga_beli_operator == '>=') ? 'selected' : '' ?>>≥</option>
                                            <option value=">" <?= (isset($harga_beli_operator) && $harga_beli_operator == '>') ? 'selected' : '' ?>>></option>
                                            <option value="<" <?= (isset($harga_beli_operator) && $harga_beli_operator == '<') ? 'selected' : '' ?>><</option>
                                        </select>
                                        <input type="text" name="harga_beli_value" class="form-control rounded-0" placeholder="Rp 0" value="<?= esc($harga_beli_value ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Harga Jual Filter -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Harga Jual</label>
                                    <div class="input-group">
                                        <select name="harga_jual_operator" class="form-control rounded-0" style="max-width: 80px;">
                                            <option value="">-</option>
                                            <option value="=" <?= (isset($harga_jual_operator) && $harga_jual_operator == '=') ? 'selected' : '' ?>>=</option>
                                            <option value="<=" <?= (isset($harga_jual_operator) && $harga_jual_operator == '<=') ? 'selected' : '' ?>>≤</option>
                                            <option value=">=" <?= (isset($harga_jual_operator) && $harga_jual_operator == '>=') ? 'selected' : '' ?>>≥</option>
                                            <option value=">" <?= (isset($harga_jual_operator) && $harga_jual_operator == '>') ? 'selected' : '' ?>>></option>
                                            <option value="<" <?= (isset($harga_jual_operator) && $harga_jual_operator == '<') ? 'selected' : '' ?>><</option>
                                        </select>
                                        <input type="text" name="harga_jual_value" class="form-control rounded-0" placeholder="Rp 0" value="<?= esc($harga_jual_value ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary rounded-0">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="<?= base_url('gudang/stok') ?>" class="btn btn-secondary rounded-0">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
                
                <!-- Table Section -->
                <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">No.</th>
                            <th width="80">Foto</th>
                            <th>Kategori</th>
                            <th>Item</th>
                            <th class="text-center">Stok</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
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
                                    <td>
                                        <?php
                                            // Query sum stok for this item
                                            $db = \Config\Database::connect();
                                            $sumStok = $db->table('tbl_m_item_stok')
                                                ->select('SUM(jml) as total_stok')
                                                ->where('id_item', $row->id)
                                                ->get()
                                                ->getRow();
                                            $totalStok = $sumStok && isset($sumStok->total_stok) ? $sumStok->total_stok : 0;
                                        ?>
                                        <?= $totalStok ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('gudang/stok/detail/' . $row->id) ?>" 
                                           class="btn btn-info btn-sm rounded-0" 
                                           title="Lihat Detail Stok">
                                            <i class="fas fa-eye"></i>
                                        </a>
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
                </div>
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
    
    // Price input formatting
    $('input[name="harga_beli_value"], input[name="harga_jual_value"]').on('input', function() {
        var value = $(this).val().replace(/[^\d]/g, '');
        if (value !== '') {
            value = parseInt(value).toLocaleString('id-ID');
            $(this).val(value);
        }
    });
});
</script>
<?= $this->endSection() ?> 