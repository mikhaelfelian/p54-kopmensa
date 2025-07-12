<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-23
 * 
 * Purchase Order Edit View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-6">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Edit Purchase Order</h3>
            </div>
            <?= form_open('transaksi/po/update/' . $po->id, ['id' => 'form-po']) ?>
            <div class="card-body">
                <!-- No PO -->
                <div class="form-group">
                    <label>No PO</label>
                    <?= form_input([
                        'type' => 'text',
                        'class' => 'form-control rounded-0',
                        'value' => esc($po->no_nota),
                        'readonly' => true
                    ]) ?>
                </div>

                <!-- Supplier -->
                <div class="form-group">
                    <label>Nama Supplier<span class="text-danger">*</span></label>
                    <select name="supplier_id"
                        class="form-control rounded-0 select2 <?= validation_show_error('supplier_id') ? 'is-invalid' : '' ?>">
                        <option value="">Pilih Nama Supplier</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= $supplier->id ?>" <?= old('supplier_id', $po->id_supplier) == $supplier->id ? 'selected' : '' ?>>
                                <?= esc($supplier->nama) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                    <div class="invalid-feedback">
                        <?= validation_show_error('supplier_id') ?>
                    </div>
                </div>

                <!-- Tanggal PO -->
                <div class="form-group">
                    <label>Tgl PO</label>
                    <?= form_input([
                        'type' => 'date',
                        'name' => 'tgl_po',
                        'class' => 'form-control rounded-0' . (validation_show_error('tgl_po') ? ' is-invalid' : ''),
                        'value' => old('tgl_po', $po->tgl_masuk)
                    ]) ?>
                    <div class="invalid-feedback">
                        <?= validation_show_error('tgl_po') ?>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="form-group">
                    <label>Keterangan</label>
                    <?= form_textarea([
                        'name' => 'keterangan',
                        'class' => 'form-control rounded-0',
                        'rows' => '3',
                        'placeholder' => 'Masukkan keterangan...',
                        'value' => old('keterangan', $po->keterangan)
                    ]) ?>
                </div>

                <!-- Alamat Pengiriman -->
                <div class="form-group">
                    <label>Alamat Pengiriman<span class="text-danger">*</span></label>
                    <?= form_textarea([
                        'name' => 'alamat_pengiriman',
                        'class' => 'form-control rounded-0' . (validation_show_error('alamat_pengiriman') ? ' is-invalid' : ''),
                        'rows' => '3',
                        'placeholder' => 'Masukkan alamat pengiriman...',
                        'value' => old('alamat_pengiriman', $po->pengiriman)
                    ]) ?>
                    <div class="invalid-feedback">
                        <?= validation_show_error('alamat_pengiriman') ?>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?= base_url('transaksi/po') ?>" class="btn btn-default rounded-0">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <?= form_open('transaksi/po/proses/' . $po->id, ['id' => 'form-proses']) ?>
                <button type="submit" class="btn btn-success float-right rounded-0" id="btn-proses"
                    onclick="return confirm('Apakah anda yakin ingin memproses PO ini?')">
                    <i class="fas fa-check"></i> Proses &raquo;
                </button>
                <?= form_close() ?>
            </div>
            <?= form_close() ?>
        </div>
    </div>
    <div class="col-md-6">
        <?= form_open('transaksi/po/cart_add/' . $po->id, ['id' => 'form-add-item']) ?>
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Detail Item</h3>
            </div>
            <div class="card-body">
                <!-- Item -->
                <div class="form-group">
                    <label>Item <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <?= form_input([
                            'type' => 'text',
                            'id' => 'item',
                            'name' => 'item',
                            'class' => 'form-control rounded-0' . (session('errors.id_item') ? ' is-invalid' : ''),
                            'placeholder' => 'Cari item...',
                            'value' => old('item')
                        ]) ?>
                        <input type="hidden" id="id_item" name="id_item" value="<?= old('id_item') ?>">
                    </div>
                    <?php if (session('errors.id_item')): ?>
                        <div class="invalid-feedback">
                            <?= session('errors.id_item') ?>
                        </div>
                    <?php endif ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <!-- Jumlah -->
                        <div class="form-group">
                            <label>Jml <span class="text-danger">*</span></label>
                            <?= form_input([
                                'type' => 'number',
                                'name' => 'jumlah',
                                'id' => 'jumlah',
                                'class' => 'form-control rounded-0' . (session('errors.jumlah') ? ' is-invalid' : ''),
                                'value' => old('jumlah', '1'),
                                'min' => '1'
                            ]) ?>
                            <?php if (session('errors.jumlah')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.jumlah') ?>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Satuan -->
                        <div class="form-group">
                            <label>Satuan <span class="text-danger">*</span></label>
                            <select name="satuan" id="satuan"
                                class="form-control rounded-0 <?= session('errors.satuan') ? 'is-invalid' : '' ?>">
                                <option value="">- Pilih -</option>
                                <?php foreach ($satuans as $satuan): ?>
                                    <option value="<?= $satuan->id ?>" <?= old('satuan') == $satuan->id ? 'selected' : '' ?>>
                                        <?= esc($satuan->satuanBesar) . ($satuan->jml != 1 ? ' (' . $satuan->jml . ' ' . esc($satuan->satuanKecil) . ')' : '') ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <?php if (session('errors.satuan')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.satuan') ?>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="form-group">
                    <label>Keterangan</label>
                    <?= form_textarea([
                        'name' => 'keterangan',
                        'id' => 'keterangan',
                        'class' => 'form-control rounded-0',
                        'rows' => '3',
                        'placeholder' => 'Isikan keterangan ...',
                        'value' => old('keterangan')
                    ]) ?>
                </div>
            </div>
            <div class="card-footer text-left">
                <button type="submit" class="btn btn-primary float-right rounded-0">
                    <i class="fas fa-plus"></i> Tambah
                </button>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shopping-cart"></i> Item Pembelian
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Item</th>
                                <th>Jml</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($po_details)): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($po_details as $detail): ?>
                                    <tr>
                                        <td width="4%" class="text-center"><?= $no++ ?></td>
                                        <td width="50%" class="text-left">
                                            <small><i><?= $detail->kode ?></i></small><?= br() ?>
                                            <?= $detail->item ?>
                                        </td>
                                        <td width="10%" class="text-left">
                                            <?= $detail->jml . ' ' . $detail->satuan ?>
                                        </td>
                                        <td width="20%" class="text-left"><?= $detail->keterangan_itm ?></td>
                                        <td width="5%" class="text-center">
                                            <a href="<?= base_url("transaksi/po/cart_delete/{$detail->id}?id={$po->id}") ?>"
                                                class="btn btn-danger btn-sm rounded-0"
                                                onclick="return confirm('Apakah anda yakin ingin menghapus item ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <?php if (!empty($po_details)): ?>
                    <?= form_open('transaksi/po/proses/' . $po->id, ['id' => 'form-proses']) ?>
                    <button type="submit" class="btn btn-success float-right rounded-0" id="btn-proses"
                        onclick="return confirm('Apakah anda yakin ingin memproses PO ini?')">
                        <i class="fas fa-check"></i> Proses &raquo;
                    </button>
                    <?= form_close() ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?= $this->section('js') ?>
<script>
    $(document).ready(function () {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        // Initialize jQuery UI Autocomplete
        $('#item').autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: '<?= base_url('publik/items_stock') ?>',
                    dataType: 'json',
                    data: {
                        term: request.term
                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },
            minLength: 1,
            select: function (event, ui) {
                $('#id_item').val(ui.item.id);
                $('#item').val(ui.item.item);
                return false;
            }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                .append("<div>" + item.kode + " - " + item.item + "</div>")
                .appendTo(ul);
        };
    });

    function updateStatus(id) {
        if (confirm('Apakah anda yakin ingin memproses PO ini?')) {
            $.ajax({
                url: '<?= base_url('transaksi/po/update-status') ?>/' + id,
                type: 'POST',
                data: {
                    status: 1, // Status 1 = Diproses
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success('PO berhasil diproses');
                        window.location.href = '<?= base_url('transaksi/po') ?>';
                    } else {
                        toastr.error(response.message || 'Gagal memproses PO');
                    }
                },
                error: function () {
                    toastr.error('Terjadi kesalahan sistem');
                }
            });
        }
    }
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>