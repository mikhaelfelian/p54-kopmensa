<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-18
 * Github : github.com/mikhaelfelian
 * description : View for editing item data
 * This file represents the View for editing items.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <?= form_open('master/item/update/' . $item->id, ['accept-charset' => 'utf-8']) ?>
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Data Item</h3>
                <div class="card-tools"></div>
            </div>
            <div class="card-body">

                <?= form_hidden('id', $item->id) ?>
                <?= form_hidden('route', '') ?>
                <?= form_hidden('id_item', $item->id) ?>
                <?= form_hidden('status_item', $item->status) ?>
                <?= form_hidden(['name' => 'foto', 'value' => old('foto', $item->foto ?? ''), 'id' => 'foto_input']) ?>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="control-label">Supplier</label>
                            <select name="id_supplier" id="id_supplier" class="form-control rounded-0 select2" required>
                                <option value="">Pilih Supplier</option>
                                <?php foreach ($supplier as $sup): ?>
                                    <option value="<?= $sup->id ?>" <?= old('id_supplier', $item->id_supplier ?? '') == $sup->id ? 'selected' : '' ?>>
                                        <?= $sup->nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Kategori</label>
                            <select name="id_kategori" class="form-control rounded-0">
                                <option value="">-[Kategori]-</option>
                                <?php foreach ($kategori as $k): ?>
                                    <option value="<?= $k->id ?>" <?= old('id_kategori', $item->id_kategori) == $k->id ? 'selected' : '' ?>><?= $k->kategori ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Merk</label>
                            <select name="id_merk" class="form-control rounded-0">
                                <option value="">-[Merk]-</option>
                                <?php foreach ($merk as $m): ?>
                                    <option value="<?= $m->id ?>" <?= old('id_merk', $item->id_merk) == $m->id ? 'selected' : '' ?>><?= $m->merk ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">SKU</label>
                            <?= form_input(['name' => 'kode', 'value' => old('kode', $item->kode ?? ''), 'id' => 'kode', 'class' => 'form-control rounded-0', 'placeholder' => 'Isikan SKU ...', 'readonly' => 'readonly']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Barcode</label>
                            <?= form_input(['name' => 'barcode', 'value' => old('barcode', $item->barcode ?? ''), 'id' => 'barcode', 'class' => 'form-control rounded-0', 'placeholder' => 'Isikan barcode ...']) ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Item*</label>
                    <?= form_input(['name' => 'item', 'value' => old('item', $item->item ?? ''), 'id' => 'item', 'class' => 'form-control rounded-0 ' . ($validation->hasError('item') ? 'is-invalid' : ''), 'placeholder' => 'Isikan nama item / produk ...', 'required' => 'required']) ?>
                    <?php if ($validation->hasError('item')): ?>
                        <div class="invalid-feedback">
                            <?= $validation->getError('item') ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="harga_beli">Harga Beli</label>
                            <div class="input-group mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Rp. </span>
                                </div>
                                <?= form_input(['name' => 'harga_beli', 'value' => old('harga_beli', (float) $item->harga_beli ?? ''), 'id' => 'harga', 'class' => 'form-control rounded-0']) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="harga_jual">Harga Jual</label>
                            <div class="input-group mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Rp. </span>
                                </div>
                                <?= form_input(['name' => 'harga_jual', 'value' => old('harga_jual', (float) $item->harga_jual ?? ''), 'id' => 'harga', 'class' => 'form-control rounded-0']) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Stok Minimum</label>
                            <?= form_input(['type' => 'number', 'name' => 'jml_min', 'value' => old('jml_min', $item->jml_min ?? ''), 'id' => 'jml_min', 'class' => 'form-control rounded-0', 'placeholder' => 'Stok minimum ...']) ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Tipe</label>
                            <select name="tipe" class="form-control rounded-0">
                                <option value="1" <?= old('tipe', $item->tipe ?? '1') == '1' ? 'selected' : '' ?>>Item
                                </option>
                                <option value="2" <?= old('tipe', $item->tipe ?? '1') == '2' ? 'selected' : '' ?>>Jasa
                                </option>
                                <option value="3" <?= old('tipe', $item->tipe ?? '1') == '3' ? 'selected' : '' ?>>Paket
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Satuan</label>
                            <select name="satuan" class="form-control rounded-0">
                                <option value="">-[Pilih Satuan]-</option>
                                <?php foreach ($satuan as $s): ?>
                                    <option value="<?= $s->id ?>" <?= old('satuan', $item->id_satuan) == $s->id ? 'selected' : '' ?>><?= $s->satuanBesar ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Deskripsi</label>
                    <?= form_textarea(['name' => 'deskripsi', 'cols' => '40', 'rows' => '3', 'id' => 'deskripsi', 'class' => 'form-control rounded-0', 'placeholder' => 'Isikan deskripsi item / spek produk / dll ...'], old('deskripsi', $item->deskripsi ?? '')) ?>
                </div>
                <div class="form-group">
                    <label class="control-label">Stockable*</label>
                    <div class="custom-control custom-radio">
                        <input type="radio" name="status_stok" value="1" id="statusStokAktif"
                            class="custom-control-input" <?= old('status_stok', $item->status_stok) == '1' ? 'checked' : '' ?>>
                        <label for="statusStokAktif" class="custom-control-label">Stockable</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" name="status_stok" value="0" id="statusStokNonAktif"
                            class="custom-control-input custom-control-input-danger" <?= old('status_stok', $item->status_stok) == '0' ? 'checked' : '' ?>>
                        <label for="statusStokNonAktif" class="custom-control-label">Non Stockable</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Status*</label>
                    <div class="custom-control custom-radio">
                        <input type="radio" name="status" value="1" id="statusAktif" class="custom-control-input"
                            <?= old('status', $item->status) == '1' ? 'checked' : '' ?>>
                        <label for="statusAktif" class="custom-control-label">Aktif</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" name="status" value="0" id="statusNonAktif"
                            class="custom-control-input custom-control-input-danger" <?= old('status', $item->status) == '0' ? 'checked' : '' ?>>
                        <label for="statusNonAktif" class="custom-control-label">Non - Aktif</label>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-lg-6">
                        <button type="button" onclick="window.location.href = '<?= base_url('master/item') ?>'"
                            class="btn btn-primary btn-flat">« Kembali</button>
                    </div>
                    <div class="col-lg-6 text-right">
                        <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-save"
                                aria-hidden="true"></i> Simpan</button>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-header d-flex align-items-center justify-content-between">
                <ul class="nav nav-tabs card-header-tabs" id="itemTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-harga" data-toggle="tab" href="#tabHarga" role="tab"
                            aria-controls="tabHarga" aria-selected="true">
                            Harga
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-varian" data-toggle="tab" href="#tabVarian" role="tab"
                            aria-controls="tabVarian" aria-selected="false">
                            Varian
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="itemTabsContent">
                    <div class="tab-pane fade show active" id="tabHarga" role="tabpanel" aria-labelledby="tab-harga">
                        <form id="price-form">
                            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                            <div class="mb-3">
                                <button type="button" class="btn btn-success btn-sm rounded-0" id="add-price-btn">
                                    <i class="fa fa-plus"></i> Tambah
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped" id="price-table">
                                    <thead>
                                        <tr>
                                            <th>Customer Group</th>
                                            <th>Min Qty</th>
                                            <th>Price</th>
                                            <th>Notes</th>
                                            <th style="width:120px;">ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody id="price-container">
                                        <?php
                                        $itemHargaModel = new \App\Models\ItemHargaModel();
                                        $existingPrices = $itemHargaModel->getPricesByItemId($item->id);
                                        $priceIndex = 0;
                                        ?>
                                        <?php if (!empty($existingPrices)): ?>
                                            <?php foreach ($existingPrices as $price): ?>
                                                <tr class="price-row" data-index="<?= $priceIndex ?>">
                                                    <td class="align-middle">
                                                        <select name="prices[<?= $priceIndex ?>][nama]" class="form-control rounded-0" required>
                                                            <option value="">Select Customer Group</option>
                                                            <?php foreach ($customer_groups as $group): ?>
                                                                <option value="<?= $group->grup ?>" <?= ($price->nama == $group->grup) ? 'selected' : '' ?>>
                                                                    <?= $group->grup ?> <?= $group->deskripsi ? '(' . $group->deskripsi . ')' : '' ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <div class="invalid-feedback">Customer group wajib dipilih.</div>
                                                    </td>
                                                    <td class="align-middle">
                                                        <input type="number" name="prices[<?= $priceIndex ?>][jml_min]"
                                                            value="<?= $price->jml_min ?>" class="form-control rounded-0"
                                                            placeholder="Minimal beli" min="1" required>
                                                        <div class="invalid-feedback">Jumlah minimal wajib diisi.</div>
                                                    </td>
                                                    <td class="align-middle">
                                                        <input type="text" name="prices[<?= $priceIndex ?>][harga]"
                                                            value="<?= (float) $price->harga ?>"
                                                            class="form-control rounded-0 price-input"
                                                            placeholder="Harga Anggota ..." required>
                                                        <div class="invalid-feedback">Harga wajib diisi.</div>
                                                    </td>
                                                    <td class="align-middle">
                                                        <input type="text" name="prices[<?= $priceIndex ?>][keterangan]"
                                                            value="<?= $price->keterangan ?>" class="form-control rounded-0"
                                                            placeholder="Keterangan tambahan (opsional)">
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm rounded-0 delete-price-btn"
                                                            data-price-id="<?= $price->id ?>" data-toggle="tooltip"
                                                            title="Hapus data ini">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php $priceIndex++; ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr class="price-row" data-index="0">
                                                <td class="align-middle">
                                                    <select name="prices[0][nama]" class="form-control rounded-0" required>
                                                        <option value="">Select Customer Group</option>
                                                        <?php foreach ($customer_groups as $group): ?>
                                                            <option value="<?= $group->grup ?>">
                                                                <?= $group->grup ?> <?= $group->deskripsi ? '(' . $group->deskripsi . ')' : '' ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <div class="invalid-feedback">Customer group wajib dipilih.</div>
                                                </td>
                                                <td class="align-middle">
                                                    <input type="number" name="prices[0][jml_min]"
                                                        class="form-control rounded-0" placeholder="Minimal beli" min="1"
                                                        value="1" required>
                                                    <div class="invalid-feedback">Jumlah minimal wajib diisi.</div>
                                                </td>
                                                <td class="align-middle">
                                                    <input type="text" name="prices[0][harga]"
                                                        class="form-control rounded-0 price-input"
                                                        placeholder="Harga Anggota ..." required>
                                                    <div class="invalid-feedback">Harga wajib diisi.</div>
                                                </td>
                                                <td class="align-middle">
                                                    <input type="text" name="prices[0][keterangan]"
                                                        class="form-control rounded-0"
                                                        placeholder="Keterangan tambahan (opsional)">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm rounded-0 remove-price-btn"
                                                        data-toggle="tooltip" title="Hapus baris ini">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-lg-6">
                                    </div>
                                    <div class="col-lg-6 text-right">
                                        <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-save"
                                                aria-hidden="true"></i> Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                                        <!-- Tab Varian -->
                    <div class="tab-pane fade" id="tabVarian" role="tabpanel" aria-labelledby="tab-varian">                        
                        <form id="varian-form">
                            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                            <div class="mb-3">
                                <button type="button" class="btn btn-success btn-sm rounded-0" id="add-varian-btn">
                                    <i class="fa fa-plus"></i> Tambah Varian
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped varian-table" id="varian-table">
                                    <thead>
                                        <tr>
                                            <th>Varian</th>
                                            <th>Barcode</th>
                                            <th>Harga Beli</th>
                                            <th>Harga Jual</th>
                                            <th>Foto Varian</th>
                                            <th>Status</th>
                                            <th style="width:120px;">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody id="varian-tbody">
                                        <!-- Varian rows will be added here -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-lg-6">
                                    </div>
                                    <div class="col-lg-6 text-right">
                                        <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-save"
                                                aria-hidden="true"></i> Simpan Varian</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pastikan Bootstrap JS dan jQuery sudah di-load agar tab berfungsi -->
        <script>
            $(function () {
                $('#itemTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                    // Tab event handler jika perlu
                });
            });
        </script>
    </div>
</div>

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .varian-table th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
    }

    .varian-table td {
        vertical-align: middle;
    }

    .photo-preview {
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .varian-row:hover {
        background-color: #f8f9fa;
    }

    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>

<script type="text/javascript">
    $(document).ready(function () {
        // Initialize Select2 for supplier dropdown
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        // AutoNumeric for price inputs
        $("input[id=harga]").autoNumeric({ aSep: '.', aDec: ',', aPad: false });

        // Initialize existing price inputs with AutoNumeric
        $('.price-input').autoNumeric({ aSep: '.', aDec: ',', aPad: false });

        // Set initial price index
        window.priceIndex = $('.price-row').length;

        // Add price row button event
        $('#add-price-btn').on('click', function () {
            addPriceRow();
        });

        // Add variant row button event
        $('#add-varian-btn').on('click', function () {
            addVarianRow();
        });
    });

    // Dynamic Price Row Management
    function addPriceRow() {
        const $container = $('#price-container');
        const currentIndex = window.priceIndex || 0;

        const newRowHtml = `
            <tr class="price-row" data-index="${currentIndex}" style="display: none;">
            <td class="align-middle">
                    <select name="prices[${currentIndex}][nama]" class="form-control rounded-0" required>
                        <option value="">Select Customer Group</option>
                        <?php foreach ($customer_groups as $group): ?>
                            <option value="<?= $group->grup ?>">
                                <?= $group->grup ?> <?= $group->deskripsi ? '(' . $group->deskripsi . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <div class="invalid-feedback">Customer group wajib dipilih.</div>
            </td>
            <td class="align-middle">
                    <input type="number" 
                           name="prices[${currentIndex}][jml_min]" 
                           class="form-control rounded-0" 
                           placeholder="Minimal beli" 
                           min="1" 
                           value="1" 
                           required>
                <div class="invalid-feedback">Jumlah minimal wajib diisi.</div>
            </td>
            <td class="align-middle">
                    <input type="text" 
                           name="prices[${currentIndex}][harga]" 
                           class="form-control rounded-0 price-input" 
                           placeholder="0" 
                           required>
                <div class="invalid-feedback">Harga wajib diisi.</div>
            </td>
            <td class="align-middle">
                    <input type="text" 
                           name="prices[${currentIndex}][keterangan]" 
                           class="form-control rounded-0" 
                           placeholder="Keterangan tambahan (opsional)">
            </td>
            <td class="align-middle text-center">
                    <button type="button" 
                            class="btn btn-danger btn-sm rounded-0 remove-price-btn" 
                            data-toggle="tooltip" 
                            title="Hapus baris ini">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
            </tr>
        `;

        const $newRow = $(newRowHtml);
        $container.append($newRow);

        // Show with animation
        $newRow.fadeIn(300);

        // Initialize AutoNumeric for new price input
        $newRow.find('.price-input').autoNumeric({
            aSep: '.',
            aDec: ',',
            aPad: false,
            vMin: '0'
        });

        // Initialize tooltip
        $newRow.find('[data-toggle="tooltip"]').tooltip();

        // Focus on first select
        setTimeout(() => {
            $newRow.find('select[name*="[nama]"]').focus();
        }, 300);

        window.priceIndex++;
    }

    // Event delegation for remove buttons
    $(document).on('click', '.remove-price-btn', function () {
        removePriceRow(this);
    });

    // Event delegation for delete buttons (existing records)
    $(document).on('click', '.delete-price-btn', function () {
        const priceId = $(this).data('price-id');
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            deletePriceRow(this, priceId);
        }
    });

    function removePriceRow(button) {
        const $priceRows = $('.price-row');
        const $currentRow = $(button).closest('.price-row');

        if ($priceRows.length > 1) {
            // Confirm deletion
            if (confirm('Apakah Anda yakin ingin menghapus baris ini?')) {
                $currentRow.fadeOut(300, function () {
                    $(this).remove();
                    // Re-index remaining rows
                    reindexPriceRows();
                });
            }
        } else {
            toastr.warning('Minimal harus ada satu level harga!');
        }
    }

    function deletePriceRow(btn, priceId) {
        if (!priceId) {
            removePriceRow(btn);
            return;
        }

        // Show loading state
        const $btn = $(btn);
        const originalHtml = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

        $.ajax({
            url: '<?= base_url('master/item/delete_price/') ?>' + priceId,
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': $('input[name="<?= csrf_token() ?>"]').val()
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    $btn.closest('.price-row').fadeOut(300, function () {
                        $(this).remove();
                        reindexPriceRows();
                    });
                } else {
                    toastr.error(response.message || 'Gagal menghapus harga!');
                    $btn.html(originalHtml).prop('disabled', false);
                }

                if (response.csrfHash) {
                    $('input[name="<?= csrf_token() ?>"]').val(response.csrfHash);
                }
            },
            error: function () {
                toastr.error('Terjadi kesalahan server!');
                $btn.html(originalHtml).prop('disabled', false);
            }
        });
    }

    // Re-index price rows after deletion
    function reindexPriceRows() {
        $('.price-row').each(function (index) {
            const $row = $(this);
            $row.attr('data-index', index);

            // Update input names
            $row.find('input[name*="[nama]"]').attr('name', `prices[${index}][nama]`);
            $row.find('input[name*="[jml_min]"]').attr('name', `prices[${index}][jml_min]`);
            $row.find('input[name*="[harga]"]').attr('name', `prices[${index}][harga]`);
            $row.find('input[name*="[keterangan]"]').attr('name', `prices[${index}][keterangan]`);
        });

        window.priceIndex = $('.price-row').length;
    }

    // Add keyboard shortcuts
    $(document).on('keydown', '.price-row input', function (e) {
        // Enter key to add new row
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            const $currentRow = $(this).closest('.price-row');
            if ($currentRow.is(':last-child')) {
                addPriceRow();
            } else {
                // Move to next input or next row
                const $nextInput = $(this).closest('td').next().find('input');
                if ($nextInput.length) {
                    $nextInput.focus();
                } else {
                    $currentRow.next('.price-row').find('input:first').focus();
                }
            }
        }
    });

    $(function () {
        $('#price-form').on('submit', function (e) {
            e.preventDefault();
            var $form = $(this);
            var url = '<?= base_url('master/item/store_price/' . $item->id) ?>';
            var data = $form.serializeArray();
            // Add CSRF token
            data.push({ name: '<?= csrf_token() ?>', value: $('input[name=<?= csrf_token() ?>]').val() });
            $.post(url, data, function (response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message || 'Gagal menyimpan harga!');
                }
                // Update CSRF token after each request
                if (response.csrfHash) {
                    $('input[name=<?= csrf_token() ?>]').val(response.csrfHash);
                }
            }, 'json').fail(function (xhr) {
                toastr.error('Terjadi kesalahan server!');
            });
        });

        // Variant form submission
        $('#varian-form').on('submit', function (e) {
            e.preventDefault();
            var $form = $(this);
            var url = '<?= base_url('master/item/store_variant/' . $item->id) ?>';

            // Generate kode for variants that don't have one
            $('.varian-row').each(function (index) {
                var $kodeInput = $(this).find('input[name*="[kode]"]');
                var $namaInput = $(this).find('select[name*="[nama]"]');

                if (!$kodeInput.val() && $namaInput.val()) {
                    // Generate base SKU: Convert to uppercase, remove special chars, limit to 8 chars
                    let baseSku = $namaInput.val().toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 8);

                    // Check if SKU already exists and make it unique
                    let finalSku = baseSku;
                    let counter = 1;

                    while (skuExists(finalSku)) {
                        finalSku = baseSku + counter.toString().padStart(2, '0');
                        counter++;
                    }

                    $kodeInput.val(finalSku);
                }
            });

            // Only add CSRF token if not already present in the serialized data
            var data = $form.serializeArray();
            var csrfName = '<?= csrf_token() ?>';
            var csrfValue = $('input[name=<?= csrf_token() ?>]').val();
            var hasCsrf = data.some(function (field) {
                return field.name === csrfName;
            });
            if (!hasCsrf) {
                data.push({ name: csrfName, value: csrfValue });
            }

            // Debug: Log the data being sent
            console.log('Form data being sent:', data);

            $.post(url, data, function (response) {
                console.log('Server response:', response);
                if (response.success) {
                    toastr.success(response.message);
                    loadVariants(); // Reload variants after saving
                } else {
                    toastr.error(response.message || 'Gagal menyimpan varian!');
                }
                // Update CSRF token after each request
                if (response.csrfHash) {
                    $('input[name=<?= csrf_token() ?>]').val(response.csrfHash);
                }
            }, 'json').fail(function (xhr) {
                console.error('AJAX request failed:', xhr);
                toastr.error('Terjadi kesalahan server!');
            });
        });
    });

    // Load existing variants on page load
    loadVariants();

    let varianIndex = 0;

    function addVarianRow() {
        const tbody = document.getElementById('varian-tbody');
        const newRow = document.createElement('tr');
        newRow.className = 'varian-row';
        newRow.setAttribute('data-index', varianIndex);

        newRow.innerHTML = `
            <td class="align-middle">
                <input type="hidden" name="variants[${varianIndex}][id]" value="">
                <input type="hidden" name="variants[${varianIndex}][kode]" value="">
                <select name="variants[${varianIndex}][nama]" class="form-control rounded-0" required>
                    <option value="">- Pilih -</option>
                    <?php if (!empty($sql_varian)): ?>
                        <?php foreach ($sql_varian as $variant): ?>
                            <option value="<?= $variant->nama ?>"><?= $variant->nama ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No variants available</option>
                    <?php endif; ?>
                </select>
                <div class="invalid-feedback">Variant selection is required.</div>
            </td>
            <td class="align-middle">
                <input type="text" name="variants[${varianIndex}][barcode]" class="form-control rounded-0" placeholder="Barcode (optional)">
            </td>
            <td class="align-middle">
                <input type="text" name="variants[${varianIndex}][harga_beli]" class="form-control rounded-0 varian-price-input" placeholder="Purchase price (optional)">
            </td>
            <td class="align-middle">
                <input type="text" name="variants[${varianIndex}][harga_dasar]" class="form-control rounded-0 varian-base-price-input" placeholder="Base price fallback (optional)">
            </td>
            <td class="align-middle">
                <input type="file" name="variants[${varianIndex}][foto]" class="form-control rounded-0" accept="image/*">
            </td>
            <td class="align-middle">
                <select name="variants[${varianIndex}][status]" class="form-control rounded-0">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </td>
            <td class="align-middle text-center">
                <button type="button" class="btn btn-danger btn-sm rounded-0" onclick="removeVarianRow(this)" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(newRow);

        // Initialize autoNumeric for the new price inputs
        $(newRow).find('.varian-price-input').autoNumeric({ aSep: '.', aDec: ',', aPad: false });
        $(newRow).find('.varian-base-price-input').autoNumeric({ aSep: '.', aDec: ',', aPad: false });

        // Auto-generate SKU when variant name is selected
        const kodeInput = $(newRow).find('input[name*="[kode]"]');
        const namaSelect = $(newRow).find('select[name*="[nama]"]');

        namaSelect.on('change', function () {
            const nama = $(this).val();
            if (nama) {
                // Generate base SKU: Convert to uppercase, remove special chars, limit to 8 chars
                let baseSku = nama.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 8);

                // Check if SKU already exists and make it unique
                let finalSku = baseSku;
                let counter = 1;

                while (skuExists(finalSku)) {
                    finalSku = baseSku + counter.toString().padStart(2, '0');
                    counter++;
                }

                kodeInput.val(finalSku);
            }
        });

        varianIndex++;
    }

    function removeVarianRow(button) {
        const varianRows = document.querySelectorAll('.varian-row');
        if (varianRows.length > 0) {
            button.closest('.varian-row').remove();
        } else {
            toastr.warning('Tidak ada varian untuk dihapus!');
        }
    }

    function deleteVarianRow(btn, varianId) {
        if (!varianId) {
            removeVarianRow(btn);
            return;
        }
        $.post('<?= base_url('master/item/delete_variant/') ?>' + varianId, {
            '<?= csrf_token() ?>': $('input[name=<?= csrf_token() ?>]').val()
        }, function (response) {
            if (response.success) {
                toastr.success(response.message);
                $(btn).closest('.varian-row').remove();
            } else {
                toastr.error(response.message || 'Gagal menghapus varian!');
            }
            if (response.csrfHash) {
                $('input[name=<?= csrf_token() ?>]').val(response.csrfHash);
            }
        }, 'json').fail(function () {
            toastr.error('Terjadi kesalahan server!');
        });
    }

    function loadVariants() {
        $.get('<?= base_url('master/item/get_variants/' . $item->id) ?>', function (response) {
            if (response.success) {
                const tbody = document.getElementById('varian-tbody');
                tbody.innerHTML = '';

                response.variants.forEach(function (variant, index) {
                    const newRow = document.createElement('tr');
                    newRow.className = 'varian-row';
                    newRow.setAttribute('data-index', index);
                    newRow.setAttribute('data-id', variant.id);

                    newRow.innerHTML = `
                        <td class="align-middle">
                            <input type="hidden" name="variants[${index}][id]" value="${variant.id}">
                            <input type="hidden" name="variants[${index}][kode]" value="${variant.kode}">
                            <select name="variants[${index}][nama]" class="form-control rounded-0" required>
                                <option value="">Select Variant</option>
                                <?php foreach ($sql_varian as $var): ?>
                                    <option value="<?= $var->nama ?>"><?= $var->nama ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Variant selection is required.</div>
                        </td>
                        <td class="align-middle">
                            <input type="text" name="variants[${index}][barcode]" class="form-control rounded-0" value="${variant.barcode || ''}" placeholder="Barcode (optional)">
                        </td>
                        <td class="align-middle">
                            <input type="text" name="variants[${index}][harga_beli]" class="form-control rounded-0 varian-price-input" value="${variant.harga_beli || ''}" placeholder="Purchase price (optional)">
                        </td>
                        <td class="align-middle">
                            <input type="text" name="variants[${index}][harga_dasar]" class="form-control rounded-0 varian-base-price-input" value="${variant.harga_dasar || ''}" placeholder="Base price fallback (optional)">
                        </td>
                        <td class="align-middle">
                            <input type="file" name="variants[${index}][foto]" class="form-control rounded-0" accept="image/*">
                        </td>
                        <td class="align-middle">
                            <select name="variants[${index}][status]" class="form-control rounded-0">
                                <option value="1" ${variant.status == 1 ? 'selected' : ''}>Active</option>
                                <option value="0" ${variant.status == 0 ? 'selected' : ''}>Inactive</option>
                            </select>
                        </td>
                        <td class="align-middle text-center">
                            <button type="button" class="btn btn-danger btn-sm rounded-0" onclick="deleteVarianRow(this, ${variant.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;

                    tbody.appendChild(newRow);
                });

                // Initialize autoNumeric for all price inputs
                $('.varian-price-input').autoNumeric({ aSep: '.', aDec: ',', aPad: false });
                $('.varian-base-price-input').autoNumeric({ aSep: '.', aDec: ',', aPad: false });

                // Add SKU generation event handlers for existing variant dropdowns
                $('select[name*="[nama]"]').on('change', function() {
                    const nama = $(this).val();
                    const row = $(this).closest('.varian-row');
                    const kodeInput = row.find('input[name*="[kode]"]');
                    
                    if (nama && !kodeInput.val()) {
                        // Generate base SKU: Convert to uppercase, remove special chars, limit to 8 chars
                        let baseSku = nama.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 8);

                        // Check if SKU already exists and make it unique
                        let finalSku = baseSku;
                        let counter = 1;

                        while (skuExists(finalSku)) {
                            finalSku = baseSku + counter.toString().padStart(2, '0');
                            counter++;
                        }

                        kodeInput.val(finalSku);
                    }
                });

                // Set selected values for existing variants and generate SKUs
                response.variants.forEach(function (variant, index) {
                    // Set selected value for variant name dropdown
                    const namaSelect = $(`select[name="variants[${index}][nama]"]`);
                    if (namaSelect.length && variant.nama) {
                        namaSelect.val(variant.nama);
                    }
                    
                    if (!variant.kode) {

                        const kodeInput = $(`input[name="variants[${index}][kode]"]`);

                        if (variant.nama) {
                            let baseSku = variant.nama.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 8);
                            let finalSku = baseSku;
                            let counter = 1;

                            while (skuExists(finalSku)) {
                                finalSku = baseSku + counter.toString().padStart(2, '0');
                                counter++;
                            }

                            kodeInput.val(finalSku);
                        }
                    }
                });

                varianIndex = response.variants.length;
            }
        }, 'json');
    }

    // Handle variant photo upload and preview
    function handleVariantPhotoUpload(input) {
        const file = input.files[0];
        if (file) {
            // Validate file type
            if (!file.type.startsWith('image/')) {
                toastr.error('Please select an image file');
                input.value = '';
                return;
            }

            // Validate file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                toastr.error('Image size should be less than 2MB');
                input.value = '';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = input.parentNode.querySelector('.photo-preview');
                if (preview) {
                    preview.remove();
                }

                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'photo-preview mt-2';
                img.style.maxWidth = '100px';
                img.style.maxHeight = '100px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '4px';

                input.parentNode.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    }

    // Add photo preview functionality to existing variant rows
    $(document).on('change', 'input[name*="[foto]"]', function () {
        handleVariantPhotoUpload(this);
    });

    // Function to check if SKU already exists
    function skuExists(sku) {
        const existingSkus = [];
        $('input[name*="[kode]"]').each(function () {
            const existingSku = $(this).val();
            if (existingSku && existingSku !== sku) {
                existingSkus.push(existingSku);
            }
        });
        return existingSkus.includes(sku);
    }
</script>

<?= $this->endSection() ?>