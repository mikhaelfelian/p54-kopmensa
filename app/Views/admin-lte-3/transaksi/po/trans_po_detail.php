<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-27
 * 
 * Purchase Order Detail View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<!-- Informasi PO -->
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">Informasi PO</h3>
        <div class="card-tools">
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <td width="150">No. PO</td>
                        <td width="20">:</td>
                        <td><?= esc($po->no_nota) ?></td>
                    </tr>
                    <tr>
                        <td>Tanggal PO</td>
                        <td>:</td>
                        <td><?= date('d/m/Y', strtotime($po->tgl_masuk)) ?></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <td width="150">Supplier</td>
                        <td width="20">:</td>
                        <td><?= esc($po->supplier_name) ?></td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td><?= esc($po->supplier_address) ?></td>
                    </tr>
                    <tr>
                        <td>Telepon</td>
                        <td>:</td>
                        <td><?= esc($po->supplier_phone) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Item List -->
        <div class="table-responsive mt-4">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Item</th>
                        <th class="text-center">Jumlah</th>
                        <th>Satuan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $i => $item): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= esc($item->kode) ?></td>
                                <td><?= esc($item->item_name) ?></td>
                                <td class="text-center"><?= esc($item->jml) ?></td>
                                <td><?= esc($item->satuan_name) ?></td>
                                <td><?= esc($item->keterangan) ?></td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada item</td>
                        </tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <a href="<?= base_url('transaksi/po') ?>" class="btn btn-default rounded-0">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <?php if ($po->status == 0): ?>
            <a href="<?= base_url("transaksi/po/edit/{$po->id}") ?>" class="btn btn-warning rounded-0">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="<?= base_url("transaksi/po/delete/{$po->id}") ?>" class="btn btn-danger rounded-0"
                onclick="return confirm('Apakah anda yakin ingin menghapus PO ini?')">
                <i class="fas fa-trash"></i> Hapus
            </a>
        <?php else: ?>
            <?php if ($po->status == '4'): ?>
                <a href="<?= base_url("transaksi/beli/create?id_po=$po->id") ?>" class="btn btn-primary rounded-0">
                    <i class="fas fa-file-invoice"></i> Buat Faktur
                </a>
            <?php else: ?>
                <a href="<?= base_url("transaksi/po/proses/{$po->id}") ?>" class="btn btn-success rounded-0">
                    <i class="fas fa-check"></i> Proses
                </a>
            <?php endif; ?>
        <?php endif ?>
    </div>
</div>
<?= $this->endSection() ?>