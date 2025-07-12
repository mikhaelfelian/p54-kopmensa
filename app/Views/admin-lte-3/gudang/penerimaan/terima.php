<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-29
 * 
 * Purchase Receiving Form View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-truck-loading mr-1"></i> Terima Barang
                </h3>
                <div class="card-tools">
                    <a href="<?= base_url('gudang/penerimaan') ?>" class="btn btn-default btn-sm rounded-0">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Transaction Information -->
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
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Supplier</strong></td>
                                <td>: <?= esc($transaksi->supplier_nama ?? $transaksi->supplier) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Alamat</strong></td>
                                <td>: <?= esc($transaksi->supplier_alamat ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: 
                                    <span class="badge badge-success">Siap Diterima</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Terima</strong></td>
                                <td>: <?= date('d/m/Y') ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <!-- Receiving Form -->
                <?= form_open('gudang/terima/save/' . $transaksi->id, ['id' => 'form-terima']) ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Kode</th>
                                <th width="25%">Item</th>
                                <th width="10%" class="text-center">Ordered</th>
                                <th width="10%" class="text-center">Received</th>
                                <th width="10%" class="text-center">Satuan</th>
                                <th width="15%" class="text-center">Gudang</th>
                                <th width="15%">Keterangan</th>
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
                                            <br>
                                            <small class="text-muted"><?= esc($item->item_name ?? $item->item) ?></small>
                                        </td>
                                        <td class="text-center">
                                            <strong><?= number_format($item->jml, 2) ?></strong>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" 
                                                   name="jml_diterima[<?= $item->id ?>]" 
                                                   value="<?= $item->jml ?>" 
                                                   min="0" 
                                                   max="<?= $item->jml * 1.1 ?>" 
                                                   step="0.01"
                                                   class="form-control form-control-sm text-center"
                                                   required>
                                        </td>
                                        <td class="text-center"><?= esc($item->satuan) ?></td>
                                        <td class="text-center">
                                            <select name="id_gudang[<?= $item->id ?>]" class="form-control form-control-sm" required>
                                                <option value="">Pilih Gudang</option>
                                                <?php foreach ($gudang as $g): ?>
                                                    <option value="<?= $g->id ?>"><?= esc($g->gudang) ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </td>
                                        <td>
                                            <textarea name="keterangan[<?= $item->id ?>]" 
                                                      class="form-control form-control-sm" 
                                                      rows="2" 
                                                      placeholder="Keterangan (opsional)"><?= esc($item->keterangan ?? '') ?></textarea>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada item</td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>

                <!-- Additional Information -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="catatan_umum"><strong>Catatan Umum:</strong></label>
                            <textarea name="catatan_umum" id="catatan_umum" class="form-control" rows="3" 
                                      placeholder="Catatan umum untuk penerimaan barang ini..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-3">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-secondary rounded-0" onclick="history.back()">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-success rounded-0">
                            <i class="fas fa-check mr-1"></i> Terima Barang
                        </button>
                    </div>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Auto-calculate received quantity based on status
    // (status dropdown removed, so this part can be skipped)

    // Form validation
    $('#form-terima').on('submit', function(e) {
        let isValid = true;
        let errorMessages = [];
        
        // Check if all warehouses are selected
        $('select[name^="id_gudang"]').each(function() {
            if (!$(this).val()) {
                errorMessages.push('Gudang harus dipilih untuk semua item');
                isValid = false;
                return false;
            }
        });
        
        // Check if all received quantities are valid
        $('input[name^="jml_diterima"]').each(function() {
            const received = parseFloat($(this).val()) || 0;
            const ordered = parseFloat($(this).closest('tr').find('td:eq(3) strong').text().replace(',', '')) || 0;
            
            if (received < 0) {
                errorMessages.push('Jumlah diterima tidak boleh negatif');
                isValid = false;
                return false;
            }
            
            if (received > ordered * 1.1) {
                errorMessages.push('Jumlah diterima tidak boleh melebihi 110% dari jumlah order');
                isValid = false;
                return false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            toastr.error(errorMessages.join('<br>'), 'Validasi Gagal');
            return false;
        }
        
        // Confirmation with JS confirm
        if (!confirm('Apakah anda yakin ingin menerima barang sesuai data di atas?')) {
            e.preventDefault();
            return false;
        }
        // else, allow submit
    });
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?> 