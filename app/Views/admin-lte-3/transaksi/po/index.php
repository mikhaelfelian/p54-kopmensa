<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-20
 * 
 * Purchase Order Index View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <a href="<?= base_url('transaksi/po/export') ?>?<?= $_SERVER['QUERY_STRING'] ?>"
                    class="btn btn-sm btn-success rounded-0">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="<?= base_url('transaksi/po/trash') ?>" class="btn btn-sm btn-danger rounded-0">
                    <i class="fas fa-trash"></i> Sampah (<?= $transBeliPOModel->getTrashCount() ?>)
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?= form_open('transaksi/po', ['method' => 'get']) ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>No. PO</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Total Item</th>
                        <th>Status</th>
                        <th width="100">Aksi</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>
                            <?= form_input('no_nota', $filters['no_nota'] ?? '', ['class' => 'form-control form-control-sm rounded-0']) ?>
                        </th>
                        <th>
                            <input type="date" name="date_start" class="form-control form-control-sm rounded-0"
                                value="<?= $filters['date_start'] ?? '' ?>">
                        </th>
                        <th>
                            <select name="kategori" class="form-control form-control-sm rounded-0">
                                <option value="">Semua Supplier</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?= $supplier->id ?>" <?= ($filters['supplier'] ?? '') == $supplier->id ? 'selected' : '' ?>>
                                        <?= esc($supplier->nama) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </th>
                        <th></th>
                        <th></th>
                        <th>
                            <button type="submit" class="btn btn-sm btn-primary rounded-0">
                                <i class="fas fa-filter"></i>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($po_list)): ?>
                        <?php $no = 1 + ($pager->getCurrentPage() - 1) * $pager->getPerPage() ?>
                        <?php foreach ($po_list as $po): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($po->no_nota) ?></td>
                                <td><?= tgl_indo2($po->tgl_masuk) ?></td>
                                <td><?= esc($po->supplier_name) ?></td>
                                <td><?= $po->total_items ?></td>
                                <td>
                                    <?php $status = statusPO($po->status); ?>
                                    <span class="badge badge-<?= $status['badge'] ?>">
                                        <?= $status['label'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url("transaksi/po/detail/{$po->id}") ?>"
                                            class="btn btn-primary btn-sm rounded-0" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($po->status == 0): // Only show edit button for draft POs ?>
                                            <a href="<?= base_url("transaksi/po/edit/{$po->id}") ?>"
                                                class="btn btn-warning btn-sm rounded-0" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
        <?= form_close() ?>
    </div>
    <div class="card-footer">
        <?= $pager->links('po', 'adminlte_pagination') ?>
    </div>
</div>
<?= $this->endSection() ?>