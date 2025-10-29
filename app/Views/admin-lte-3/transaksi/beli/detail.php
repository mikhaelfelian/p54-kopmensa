<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-05
 * 
 * Purchase Transaction Detail View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-invoice mr-1"></i> Detail Transaksi Pembelian
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('transaksi/beli') ?>" class="btn btn-default btn-sm rounded-0">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <a href="<?= base_url('transaksi/beli/edit/' . $transaksi->id) ?>" class="btn btn-primary btn-sm rounded-0">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Transaction Header -->
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>No. Faktur</strong></td>
                                <td>: <?= esc($transaksi->no_nota) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Faktur</strong></td>
                                <td>: <?= date('d/m/Y', strtotime($transaksi->tgl_masuk)) ?></td>
                            </tr>
                            <tr>
                                <td><strong>No. PO</strong></td>
                                <td>: <?= esc($transaksi->no_po ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: 
                                    <?php if ($transaksi->status_nota == '0'): ?>
                                        <span class="badge badge-warning">Draft</span>
                                    <?php elseif ($transaksi->status_nota == '1'): ?>
                                        <span class="badge badge-success">Diproses</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><?= $transaksi->status_nota ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Supplier</strong></td>
                                <td>: <?= esc($supplier->nama ?? $transaksi->supplier ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Alamat</strong></td>
                                <td>: <?= esc($supplier->alamat ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Telepon</strong></td>
                                <td>: <?= esc($supplier->telepon ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status PPN</strong></td>
                                <td>: 
                                    <?php 
                                    $ppnStatus = '';
                                    switch ($transaksi->status_ppn) {
                                        case '0': $ppnStatus = 'Non PPN'; break;
                                        case '1': $ppnStatus = 'Tambah PPN'; break;
                                        case '2': $ppnStatus = 'Include PPN'; break;
                                        default: $ppnStatus = 'Tidak Diketahui';
                                    }
                                    echo $ppnStatus;
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <!-- Items Table -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Kode</th>
                                <th width="25%">Item</th>
                                <th width="10%" class="text-center">Jumlah</th>
                                <th width="10%" class="text-center">Satuan</th>
                                <th width="12%" class="text-right">Harga</th>
                                <th width="8%" class="text-center">Diskon</th>
                                <th width="10%" class="text-right">Potongan</th>
                                <th width="12%" class="text-right">Subtotal</th>
                                <th width="12%" class="text-right">PPN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($items)): ?>
                                <?php foreach ($items as $i => $item): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= esc($item->kode) ?></td>
                                        <td>
                                            <?= esc($item->item) ?>
                                            <?php if (!empty($item->keterangan)): ?>
                                                <br><small class="text-muted"><?= esc($item->keterangan) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?= number_format($item->jml, 2) ?></td>
                                        <td class="text-center"><?= esc($item->satuan) ?></td>
                                        <td class="text-right"><?= number_format($item->harga ?? 0) ?></td>
                                        <td class="text-center">
                                            <?php 
                                            $totalDisk = ($item->disk1 ?? 0) + ($item->disk2 ?? 0) + ($item->disk3 ?? 0);
                                            echo $totalDisk > 0 ? number_format($totalDisk, 2) . '%' : '-';
                                            ?>
                                        </td>
                                        <td class="text-right"><?= number_format($item->potongan ?? 0) ?></td>
                                        <td class="text-right"><?= number_format($item->subtotal ?? 0) ?></td>
                                        <td class="text-right"><?= number_format($item->ppn_amount ?? 0) ?></td>
                                    </tr>
                                <?php endforeach ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada item</td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="9" class="text-right"><strong>Subtotal</strong></td>
                                <td class="text-right"><strong><?= number_format($subtotal) ?></strong></td>
                            </tr>
                            <?php if ($total_diskon > 0): ?>
                                <tr>
                                    <td colspan="9" class="text-right"><strong>Total Diskon</strong></td>
                                    <td class="text-right"><strong><?= number_format($total_diskon, 2) ?>%</strong></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($total_potongan > 0): ?>
                                <tr>
                                    <td colspan="9" class="text-right"><strong>Total Potongan</strong></td>
                                    <td class="text-right"><strong><?= number_format($total_potongan) ?></strong></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($ppn > 0): ?>
                                <tr>
                                    <td colspan="9" class="text-right"><strong>DPP</strong></td>
                                    <td class="text-right"><strong><?= number_format($dpp) ?></strong></td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="text-right"><strong>PPN (11%)</strong></td>
                                    <td class="text-right"><strong><?= number_format($ppn) ?></strong></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td colspan="9" class="text-right"><strong>Grand Total</strong></td>
                                <td class="text-right"><strong><?= number_format($total) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Additional Information -->
                <?php if (!empty($transaksi->keterangan)): ?>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong>Keterangan:</strong></label>
                                <p class="form-control-static"><?= esc($transaksi->keterangan) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="row mt-3">
                    <div class="col-md-12 text-right">
                        <?php if ($transaksi->status_nota == '0'): ?>
                            <a href="<?= base_url('transaksi/beli/proses/' . $transaksi->id) ?>" 
                               class="btn btn-success rounded-0"
                               onclick="return confirm('Apakah anda yakin ingin memproses transaksi ini?')">
                                <i class="fas fa-check mr-1"></i> Proses Transaksi
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize any JavaScript functionality here
    console.log('Detail view loaded for transaction: <?= $transaksi->id ?>');
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>