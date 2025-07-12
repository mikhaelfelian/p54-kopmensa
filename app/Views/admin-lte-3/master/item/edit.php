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
                                <option value="1" <?= old('tipe', $item->tipe) == '1' ? 'selected' : '' ?>>Item</option>
                                <option value="2" <?= old('tipe', $item->tipe) == '2' ? 'selected' : '' ?>>Jasa</option>
                                <option value="3" <?= old('tipe', $item->tipe) == '3' ? 'selected' : '' ?>>Paket</option>
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
                                <button type="button" class="btn btn-success btn-sm rounded-0" onclick="addPriceRow()">
                                    <i class="fa fa-plus"></i> Tambah
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped" id="price-table">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Jml Min</th>
                                            <th>Harga</th>
                                            <th>Ket</th>
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
                                                        <input type="text" name="prices[<?= $priceIndex ?>][nama]"
                                                            value="<?= $price->nama ?>" class="form-control rounded-0"
                                                            placeholder="Contoh: Ecer, Grosir, Distributor" required>
                                                        <div class="invalid-feedback">Nama level harga wajib diisi.</div>
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
                                                        <button type="button" class="btn btn-danger btn-sm rounded-0"
                                                            onclick="return confirm('Hapus data ini?') && deletePriceRow(this, <?= $price->id ?>)"
                                                            title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php $priceIndex++; ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr class="price-row" data-index="0">
                                                <td class="align-middle">
                                                    <input type="text" name="prices[0][nama]" class="form-control rounded-0"
                                                        placeholder="Harga Anggota ..." required>
                                                    <div class="invalid-feedback">Nama level harga wajib diisi.</div>
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
                                                    <button type="button" class="btn btn-danger btn-sm rounded-0"
                                                        onclick="removePriceRow(this)" title="Delete">
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
                                <button type="button" class="btn btn-success btn-sm rounded-0" onclick="addVarianRow()">
                                    <i class="fa fa-plus"></i> Tambah Varian
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped" id="varian-table">
                                    <thead>
                                        <tr>
                                            <th>Kode (SKU)</th>
                                            <th>Nama Varian</th>
                                            <th>Harga Beli</th>
                                            <th>Harga Jual</th>
                                            <th>Barcode</th>
                                            <th style="width:120px;">ACTIONS</th>
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

<script type="text/javascript">
    $(document).ready(function () {
        // Initialize Select2 for supplier dropdown
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',

        });

        // AutoNumeric for price inputs
        $("input[id=harga]").autoNumeric({ aSep: '.', aDec: ',', aPad: false });
    });

    let priceIndex = 0;

    function addPriceRow() {
        const container = document.getElementById('price-container');
        const newRow = document.createElement('tr');
        newRow.className = 'price-row';
        newRow.setAttribute('data-index', priceIndex);

        newRow.innerHTML = `
            <td class="align-middle">
                <input type="text" name="prices[${priceIndex}][nama]" class="form-control rounded-0" placeholder="Contoh: Ecer, Grosir, Distributor" required>
                <div class="invalid-feedback">Nama level harga wajib diisi.</div>
            </td>
            <td class="align-middle">
                <input type="number" name="prices[${priceIndex}][jml_min]" class="form-control rounded-0" placeholder="Minimal beli" min="1" value="1" required>
                <div class="invalid-feedback">Jumlah minimal wajib diisi.</div>
            </td>
            <td class="align-middle">
                <input type="text" name="prices[${priceIndex}][harga]" class="form-control rounded-0 price-input" placeholder="Harga Anggota ..." required>
                <div class="invalid-feedback">Harga wajib diisi.</div>
            </td>
            <td class="align-middle">
                <input type="text" name="prices[${priceIndex}][keterangan]" class="form-control rounded-0" placeholder="Keterangan tambahan (opsional)">
            </td>
            <td class="align-middle text-center">
                <button type="button" class="btn btn-danger btn-sm rounded-0" onclick="removePriceRow(this)" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        container.appendChild(newRow);

        // Initialize autoNumeric for the new price input
        $(newRow).find('.price-input').autoNumeric({ aSep: '.', aDec: ',', aPad: false });

        priceIndex++;
    }

    function removePriceRow(button) {
        const priceRows = document.querySelectorAll('.price-row');
        if (priceRows.length > 1) {
            button.closest('.price-row').remove();
        } else {
            toastr.warning('Minimal harus ada satu level harga!');
        }
    }

    function deletePriceRow(btn, priceId) {
        if (!priceId) {
            removePriceRow(btn);
            return;
        }
        $.post('<?= base_url('master/item/delete_price/') ?>' + priceId, {
            '<?= csrf_token() ?>': $('input[name=<?= csrf_token() ?>]').val()
        }, function (response) {
            if (response.success) {
                toastr.success(response.message);
                $(btn).closest('.price-row').remove();
            } else {
                toastr.error(response.message || 'Gagal menghapus harga!');
            }
            if (response.csrfHash) {
                $('input[name=<?= csrf_token() ?>]').val(response.csrfHash);
            }
        }, 'json').fail(function () {
            toastr.error('Terjadi kesalahan server!');
        });
    }

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
            var data = $form.serializeArray();
            // Add CSRF token
            data.push({ name: '<?= csrf_token() ?>', value: $('input[name=<?= csrf_token() ?>]').val() });
            $.post(url, data, function (response) {
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
                toastr.error('Terjadi kesalahan server!');
            });
        });

        // Load existing variants on page load
        loadVariants();
    });

    let varianIndex = 0;

    function addVarianRow() {
        const tbody = document.getElementById('varian-tbody');
        const newRow = document.createElement('tr');
        newRow.className = 'varian-row';
        newRow.setAttribute('data-index', varianIndex);

        newRow.innerHTML = `
            <td class="align-middle">
                <input type="text" name="variants[${varianIndex}][kode]" class="form-control rounded-0" placeholder="Kode SKU" required>
                <div class="invalid-feedback">Kode SKU wajib diisi.</div>
            </td>
            <td class="align-middle">
                <input type="text" name="variants[${varianIndex}][nama]" class="form-control rounded-0" placeholder="Nama Varian" required>
                <div class="invalid-feedback">Nama varian wajib diisi.</div>
            </td>
            <td class="align-middle">
                <input type="text" name="variants[${varianIndex}][harga_beli]" class="form-control rounded-0 varian-price-input" placeholder="Harga Beli" required>
                <div class="invalid-feedback">Harga beli wajib diisi.</div>
            </td>
            <td class="align-middle">
                <select name="variants[${varianIndex}][id_item_harga]" class="form-control rounded-0" required>
                    <option value="">Pilih Harga Jual</option>
                    <?php foreach ($item_harga_list ?? [] as $harga): ?>
                    <option value="<?= $harga->id ?>"><?= $harga->nama ?> - <?= number_format($harga->harga, 0, ',', '.') ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Harga jual wajib dipilih.</div>
            </td>
            <td class="align-middle">
                <input type="text" name="variants[${varianIndex}][barcode]" class="form-control rounded-0" placeholder="Barcode (opsional)">
            </td>
            <td class="align-middle text-center">
                <button type="button" class="btn btn-danger btn-sm rounded-0" onclick="removeVarianRow(this)" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(newRow);

        // Initialize autoNumeric for the new price input
        $(newRow).find('.varian-price-input').autoNumeric({ aSep: '.', aDec: ',', aPad: false });

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
                            <input type="text" name="variants[${index}][kode]" class="form-control rounded-0" value="${variant.kode}" required>
                            <div class="invalid-feedback">Kode SKU wajib diisi.</div>
                        </td>
                        <td class="align-middle">
                            <input type="text" name="variants[${index}][nama]" class="form-control rounded-0" value="${variant.nama}" required>
                            <div class="invalid-feedback">Nama varian wajib diisi.</div>
                        </td>
                        <td class="align-middle">
                            <input type="text" name="variants[${index}][harga_beli]" class="form-control rounded-0 varian-price-input" value="${variant.harga_beli}" required>
                            <div class="invalid-feedback">Harga beli wajib diisi.</div>
                        </td>
                        <td class="align-middle">
                            <select name="variants[${index}][id_item_harga]" class="form-control rounded-0" required>
                                <option value="">Pilih Harga Jual</option>
                                <?php foreach ($item_harga_list ?? [] as $harga): ?>
                                <option value="<?= $harga->id ?>" ${variant.id_item_harga == <?= $harga->id ?> ? 'selected' : ''}>
                                    <?= $harga->nama ?> - <?= number_format($harga->harga, 0, ',', '.') ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Harga jual wajib dipilih.</div>
                        </td>
                        <td class="align-middle">
                            <input type="text" name="variants[${index}][barcode]" class="form-control rounded-0" value="${variant.barcode || ''}" placeholder="Barcode (opsional)">
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
                varianIndex = response.variants.length;
            }
        }, 'json');
    }
</script>

<?= $this->endSection() ?>