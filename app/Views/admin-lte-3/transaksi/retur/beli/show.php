<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-25
 * Github : github.com/mikhaelfelian
 * description : View for showing detail of purchase return (retur pembelian)
 * This file represents the View.
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <!-- Return Header Information -->
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-undo mr-2"></i>
                    Detail Retur Pembelian - <?= esc($retur->no_nota_retur) ?>
                </h3>
                <div class="card-tools">
                    <?php if ($retur->status_retur == '0'): ?>
                        <a href="<?= base_url('transaksi/retur/beli/edit/' . $retur->id) ?>" class="btn btn-warning btn-sm rounded-0">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                    <?php endif; ?>
                    <a href="<?= base_url('transaksi/retur/beli') ?>" class="btn btn-secondary btn-sm rounded-0">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>No. Nota Retur</strong></td>
                                <td width="5%">:</td>
                                <td><?= esc($retur->no_nota_retur) ?></td>
                            </tr>
                            <tr>
                                <td><strong>No. Nota Asal</strong></td>
                                <td>:</td>
                                <td><?= esc($retur->no_nota_asal ?? $retur->no_nota_pembelian) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Retur</strong></td>
                                <td>:</td>
                                <td><?= date('d/m/Y', strtotime($retur->tgl_retur)) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Supplier</strong></td>
                                <td>:</td>
                                <td><?= esc($retur->supplier_nama) ?></td>
                            </tr>
                            <tr>
                                <td><strong>User Input</strong></td>
                                <td>:</td>
                                <td><?= esc($retur->first_name . ' ' . $retur->last_name) ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>User Penerima</strong></td>
                                <td width="5%">:</td>
                                <td><?= esc(($retur->terima_first_name ?? '') . ' ' . ($retur->terima_last_name ?? '')) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status PPN</strong></td>
                                <td>:</td>
                                <td>
                                    <?php
                                    $ppnStatus = [
                                        '0' => '<span class="badge badge-secondary">Non PPN</span>',
                                        '1' => '<span class="badge badge-info">Dengan PPN</span>',
                                        '2' => '<span class="badge badge-primary">PPN Ditangguhkan</span>'
                                    ];
                                    echo $ppnStatus[$retur->status_ppn ?? '0'] ?? '';
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status Retur</strong></td>
                                <td>:</td>
                                <td>
                                    <?php
                                    $returStatus = [
                                        '0' => '<span class="badge badge-warning">Draft</span>',
                                        '1' => '<span class="badge badge-success">Selesai</span>'
                                    ];
                                    echo $returStatus[$retur->status_retur ?? '0'] ?? '';
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Dibuat</strong></td>
                                <td>:</td>
                                <td><?= date('d/m/Y H:i', strtotime($retur->created_at)) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <?php if (!empty($retur->alasan_retur)): ?>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><strong>Alasan Retur:</strong></label>
                            <p class="form-control-static"><?= nl2br(esc($retur->alasan_retur)) ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($retur->catatan)): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><strong>Catatan:</strong></label>
                            <p class="form-control-static"><?= nl2br(esc($retur->catatan)) ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Return Items -->
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-boxes mr-2"></i>
                    Daftar Barang Retur
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Merk</th>
                                <th width="10%" class="text-center">Qty</th>
                                <th width="10%">Satuan</th>
                                <th width="15%" class="text-right">Harga</th>
                                <th width="15%" class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($retur->items)): ?>
                                <?php foreach ($retur->items as $index => $item): ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td><?= esc($item->kode ?? $item->item_kode) ?></td>
                                        <td><?= esc($item->item ?? $item->item_nama) ?></td>
                                        <td><?= esc($item->kategori_nama ?? '-') ?></td>
                                        <td><?= esc($item->merk_nama ?? '-') ?></td>
                                        <td class="text-center"><?= number_format($item->jml, 2, ',', '.') ?></td>
                                        <td><?= esc($item->satuan ?? $item->satuan_nama) ?></td>
                                        <td class="text-right"><?= number_format($item->harga, 2, ',', '.') ?></td>
                                        <td class="text-right"><?= number_format($item->subtotal, 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada item retur</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="8" class="text-right font-weight-bold">Subtotal:</td>
                                <td class="text-right font-weight-bold">
                                    <?= number_format($retur->jml_subtotal ?? 0, 2, ',', '.') ?>
                                </td>
                            </tr>
                            <?php if (($retur->jml_ppn ?? 0) > 0): ?>
                            <tr>
                                <td colspan="8" class="text-right font-weight-bold">PPN (11%):</td>
                                <td class="text-right font-weight-bold">
                                    <?= number_format($retur->jml_ppn, 2, ',', '.') ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <tr class="bg-primary text-white">
                                <td colspan="8" class="text-right font-weight-bold">Total Retur:</td>
                                <td class="text-right font-weight-bold">
                                    <?= number_format($retur->jml_total ?? 0, 2, ',', '.') ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 