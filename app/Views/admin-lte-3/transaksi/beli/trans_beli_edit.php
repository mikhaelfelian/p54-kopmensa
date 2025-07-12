<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-05
 * 
 * Purchase Transaction Edit View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-6">
        <?= form_open("transaksi/beli/update/$transaksi->id", ['id' => 'form-transaksi']) ?>
            <div class="card rounded-0">
                <div class="card-body">
                    <!-- PO Selection -->
                    <div class="form-group">
                        <label for="id_po">Kode PO</label>
                        <select name="id_po" id="id_po" class="form-control select2 rounded-0">
                            <option value="">Pilih PO</option>
                            <?php foreach ($po_list as $po): ?>
                                <option value="<?= $po->id ?>" 
                                    data-supplier="<?= $po->id_supplier ?>"
                                    data-no-po="<?= $po->no_nota ?>"
                                    <?= old('id_po', $transaksi->id_po) == $po->id ? 'selected' : '' ?>>
                                    <?= esc($po->no_nota). ' - '.esc($po->supplier) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Supplier -->
                            <div class="form-group">
                                <label for="id_supplier">Supplier <span class="text-danger">*</span></label>
                                <select name="id_supplier" id="id_supplier" class="form-control select2 rounded-0" required>
                                    <option value="">Pilih Supplier</option>
                                    <?php foreach ($suppliers as $supplier): ?>
                                        <option value="<?= $supplier->id ?>" 
                                            <?= old('id_supplier', $transaksi->id_supplier) == $supplier->id ? 'selected' : '' ?>>
                                            <?= esc($supplier->nama) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <!-- Tanggal Faktur -->
                            <div class="form-group">
                                <label for="tgl_masuk">Tanggal Faktur <span class="text-danger">*</span></label>
                                <?= form_input([
                                    'type' => 'date',
                                    'name' => 'tgl_masuk',
                                    'id' => 'tgl_masuk',
                                    'class' => 'form-control rounded-0',
                                    'value' => old('tgl_masuk', $transaksi->tgl_masuk),
                                    'required' => true
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- No PO -->
                            <div class="form-group">
                                <label for="no_po">No. PO</label>
                                <?= form_input([
                                    'type' => 'text',
                                    'name' => 'no_po',
                                    'id' => 'no_po',
                                    'class' => 'form-control rounded-0',
                                    'value' => old('no_po', $transaksi->no_po),
                                    'readonly' => true
                                ]) ?>
                            </div>

                            <!-- Tanggal Tempo -->
                            <div class="form-group">
                                <label for="tgl_keluar">Tanggal Tempo</label>
                                <?= form_input([
                                    'type' => 'date',
                                    'name' => 'tgl_keluar',
                                    'id' => 'tgl_keluar',
                                    'class' => 'form-control rounded-0',
                                    'value' => old('tgl_keluar', $transaksi->tgl_keluar)
                                ]) ?>
                            </div>
                        </div>
                    </div>

                    <!-- No Faktur -->
                    <div class="form-group">
                        <label for="no_nota">No. Faktur <span class="text-danger">*</span></label>
                        <?= form_input([
                            'type' => 'text',
                            'name' => 'no_nota',
                            'id' => 'no_nota',
                            'class' => 'form-control rounded-0',
                            'value' => old('no_nota', $transaksi->no_nota),
                            'required' => true
                        ]) ?>
                    </div>

                    <!-- Status PPN -->
                    <div class="form-group">
                        <label>Status PPN <span class="text-danger">*</span></label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status_ppn" id="ppn_non" value="0"
                                    <?= old('status_ppn', $transaksi->status_ppn) == '0' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ppn_non">Non PPN</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status_ppn" id="ppn_tambah" value="1"
                                    <?= old('status_ppn', $transaksi->status_ppn) == '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ppn_tambah">Tambah PPN</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status_ppn" id="ppn_include" value="2"
                                    <?= old('status_ppn', $transaksi->status_ppn) == '2' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ppn_include">Include PPN</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <a href="<?= base_url('transaksi/beli') ?>" class="btn btn-default float-left rounded-0">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary rounded-0">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </div>
        <?= form_close() ?>
    </div>
    <div class="col-md-6">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shopping-cart mr-1"></i> Item Pembelian
                </h3>
            </div>
            <div class="card-body">
                <?= form_open('', ['id' => 'form-item']) ?>
                    <!-- Item Selection -->
                    <div class="form-group">
                        <label for="id_item">Item <span class="text-danger">*</span></label>
                        <select name="id_item" id="id_item" class="form-control select2 rounded-0" required>
                            <option value="">Pilih Item</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Kode Batch -->
                            <div class="form-group">
                                <label for="kode_batch">Kode Batch</label>
                                <input type="text" name="kode_batch" id="kode_batch" class="form-control rounded-0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Tanggal ED -->
                            <div class="form-group">
                                <label for="tgl_ed">Tanggal ED</label>
                                <input type="date" name="tgl_ed" id="tgl_ed" class="form-control rounded-0">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Jumlah -->
                            <div class="form-group">
                                <label for="jml">Jumlah <span class="text-danger">*</span></label>
                                <input type="number" name="jml" id="jml" class="form-control rounded-0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Satuan -->
                            <div class="form-group">
                                <label for="id_satuan">Satuan <span class="text-danger">*</span></label>
                                <select name="id_satuan" id="id_satuan" class="form-control select2 rounded-0" required>
                                    <option value="">Pilih Satuan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Harga -->
                            <div class="form-group">
                                <label for="harga">Harga <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text rounded-0">Rp</span>
                                    </div>
                                    <input type="text" name="harga" id="harga" class="form-control rounded-0" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Potongan -->
                            <div class="form-group">
                                <label for="potongan">Potongan</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text rounded-0">Rp</span>
                                    </div>
                                    <input type="text" name="potongan" id="potongan" class="form-control rounded-0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Diskon -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="disk1">Diskon 1 (%)</label>
                                <input type="number" name="disk1" id="disk1" class="form-control rounded-0" min="0" max="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="disk2">Diskon 2 (%)</label>
                                <input type="number" name="disk2" id="disk2" class="form-control rounded-0" min="0" max="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="disk3">Diskon 3 (%)</label>
                                <input type="number" name="disk3" id="disk3" class="form-control rounded-0" min="0" max="100">
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary rounded-0">
                            <i class="fas fa-plus mr-1"></i> Tambah
                        </button>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Edit Transaksi Pembelian</h3>
                <span class="badge badge-warning float-right">Draft</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Item</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Diskon</th>
                            <th>Potongan</th>
                            <th>Subtotal</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transaksi->items)): ?>
                            <?php foreach ($transaksi->items as $i => $item): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td>
                                        <?= esc($item->kode) ?><br>
                                        <?= esc($item->item) ?><br>
                                        <small class="text-muted">
                                            Batch: <?= esc($item->kode_batch) ?> | ED: <?= date('d/m/Y', strtotime($item->tgl_ed)) ?>
                                        </small>
                                    </td>
                                    <td><?= number_format($item->jml, 2) ?> <?= esc($item->satuan) ?></td>
                                    <td><?= number_format($item->harga) ?></td>
                                    <td><?= number_format($item->disk1 + $item->disk2 + $item->disk3, 2) ?>%</td>
                                    <td><?= number_format($item->potongan) ?></td>
                                    <td><?= number_format($item->subtotal) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm rounded-0 btn-edit"
                                                data-id="<?= $item->id ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm rounded-0 btn-delete"
                                                data-id="<?= $item->id ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada item</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-right"><strong>Subtotal</strong></td>
                            <td colspan="2"><?= number_format($transaksi->jml_subtotal ?? 0) ?></td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-right"><strong>DPP</strong></td>
                            <td colspan="2"><?= number_format($transaksi->jml_dpp ?? 0) ?></td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-right"><strong>PPN (11%)</strong></td>
                            <td colspan="2"><?= number_format($transaksi->jml_ppn ?? 0) ?></td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-right"><strong>Grand Total</strong></td>
                            <td colspan="2"><?= number_format($transaksi->jml_total ?? 0) ?></td>
                        </tr>
                    </tfoot>
                </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?= base_url('transaksi/beli/proses/' . $transaksi->id) ?>" 
                   class="btn btn-success rounded-0 float-right">
                    <i class="fas fa-check mr-1"></i> Proses
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Handle PO selection
    $('#id_po').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const supplierId = selectedOption.data('supplier');
        const noPo = selectedOption.data('no-po');
        
        // Set supplier dropdown value
        $('#id_supplier').val(supplierId).trigger('change');
        
        // Set No PO field value
        $('#no_po').val(noPo);
    });
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>