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
    <div class="col-md-6">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Data Item</h3>
                <div class="card-tools"></div>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Kategori</label>
                            <select class="form-control rounded-0" disabled>
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
                            <select class="form-control rounded-0" disabled>
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
                            <input type="text" value="<?= old('kode', $item->kode ?? '') ?>" class="form-control rounded-0" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Barcode</label>
                            <input type="text" value="<?= old('barcode', $item->barcode ?? '') ?>" class="form-control rounded-0" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Item*</label>
                    <input type="text" value="<?= old('item', $item->item ?? '') ?>" class="form-control rounded-0" readonly>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="inputEmail3">Harga Beli</label>
                            <div class="input-group mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Rp. </span>
                                </div>
                                <input type="text" value="<?= old('harga_beli', $item->harga_beli ?? '') ?>" class="form-control rounded-0" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="inputEmail3">Harga Jual</label>
                            <div class="input-group mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text">Rp. </span>
                                </div>
                                <input type="text" value="<?= old('harga_jual', $item->harga_jual ?? '') ?>" class="form-control rounded-0" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Stok Minimum</label>
                            <input type="number" value="<?= old('jml_min', $item->jml_min ?? '') ?>" class="form-control rounded-0" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Tipe</label>
                            <select class="form-control rounded-0" disabled>
                                <option value="1" <?= old('tipe', $item->tipe) == '1' ? 'selected' : '' ?>>Item</option>
                                <option value="2" <?= old('tipe', $item->tipe) == '2' ? 'selected' : '' ?>>Jasa</option>
                                <option value="3" <?= old('tipe', $item->tipe) == '3' ? 'selected' : '' ?>>Paket</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Deskripsi</label>
                    <textarea cols="40" rows="3" class="form-control rounded-0" readonly><?= old('deskripsi', $item->deskripsi ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label class="control-label">Stockable*</label>
                    <div class="custom-control custom-radio">
                        <input type="radio" value="1" id="statusStokAktif" class="custom-control-input" disabled
                            <?= old('status_stok', $item->status_stok) == '1' ? 'checked' : '' ?>>
                        <label for="statusStokAktif" class="custom-control-label">Stockable</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" value="0" id="statusStokNonAktif" class="custom-control-input custom-control-input-danger" disabled <?= old('status_stok', $item->status_stok) == '0' ? 'checked' : '' ?>>
                        <label for="statusStokNonAktif" class="custom-control-label">Non Stockable</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Status*</label>
                    <div class="custom-control custom-radio">
                        <input type="radio" value="1" id="statusAktif" class="custom-control-input" disabled
                            <?= old('status', $item->status) == '1' ? 'checked' : '' ?>>
                        <label for="statusAktif" class="custom-control-label">Aktif</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" value="0" id="statusNonAktif" class="custom-control-input custom-control-input-danger" disabled <?= old('status', $item->status) == '0' ? 'checked' : '' ?>>
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
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-default rounded-0">
            <div class="card-header">
                <h3 class="card-title">Foto Produk</h3>
            </div>
            <div class="card-body">
                <?php if (empty($item->foto)): ?>
                    <div id="dropzone" class="dropzone-custom">
                        <div class="dz-message" data-dz-message>
                            <div>
                                <i class="fa fa-cloud-upload-alt fa-3x mb-2" style="color:#888;"></i>
                                <div>Seret dan lepas file di sini atau klik<br>untuk mengunggah</div>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">
                        * File yang diijinkan: jpg|png|jpeg|gif|webp (Maks. 5MB)
                    </small>
                <?php else: ?>
                    <div class="text-center">
                        <img src="<?= base_url($item->foto) ?>" alt="Foto Produk" class="img-fluid" style="max-width: 200px; max-height: 200px;">
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeImage()">
                                <i class="fa fa-trash"></i> Hapus Foto
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Dropzone CSS -->
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">

<!-- Dropzone JS -->
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<style>
    .dropzone-custom {
        border: 2px dashed #20b2aa !important;
        border-radius: 12px;
        background: #fff;
        min-height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 24px 0;
    }

    .dropzone-custom .dz-message {
        margin: 0;
        color: #888;
        font-size: 18px;
    }

    .dropzone-custom .fa-cloud-upload-alt {
        display: block;
        margin: 0 auto 8px auto;
    }

    /* Force thumbnail size to 120px */
    .dropzone-custom .dz-preview .dz-image img {
        width: 120px !important;
        height: 120px !important;
        object-fit: cover;
    }

    .dropzone-custom .dz-preview .dz-image {
        width: 120px !important;
        height: 120px !important;
    }
</style>

<script>    
    Dropzone.autoDiscover = false;

    // Function to remove image using jQuery AJAX
    function removeImage() {
        if (confirm('Apakah Anda yakin ingin menghapus foto ini?')) {
            $.ajax({
                url: "<?= base_url('master/item/delete_image') ?>",
                type: "POST",
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                },
                data: {
                    filename: "<?= $item->foto ?? '' ?>",
                    item_id: <?= $item->id ?>,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function (response) {
                    if (response.success) {
                        // Clear the hidden input
                        $('#foto_input').val('');
                        
                        // Show success message using toastr
                        toastr.success('Foto berhasil dihapus!');
                        
                        // Refresh page after a short delay
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Gagal menghapus foto!');
                    }
                },
                error: function (xhr, status, error) {
                    toastr.error('Terjadi kesalahan saat menghapus foto!');
                    console.error('Error:', error);
                }
            });
        }
    }

    // Function to handle form submission with CSRF token refresh
    function submitForm() {
        // Ensure foto_input is properly set
        var fotoValue = $('#foto_input').val();
        if (!fotoValue) {
            $('#foto_input').val('');
        }
        
        // Submit the form directly
        $('form').submit();
    }

    $(document).ready(function () {
        $("input[id=harga]").autoNumeric({aSep: '.', aDec: ',', aPad: false});

        var myDropzone = new Dropzone("#dropzone", {
            dictDefaultMessage: "",
            url: "<?= base_url('master/item/upload_image') ?>",
            paramName: "file",
            maxFilesize: 2, // MB
            acceptedFiles: "image/*",
            maxFiles: 1,
            addRemoveLinks: true,
            dictRemoveFile: "Hapus",
            dictFileTooBig: "File terlalu besar ({{filesize}}MB). Maksimal: {{maxFilesize}}MB.",
            dictInvalidFileType: "Tipe file tidak diizinkan.",
            thumbnailWidth: 120,  // Set thumbnail width to 120px
            thumbnailHeight: 120, // Set thumbnail height to 120px
            params: {
                item_id: <?= $item->id ?>,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            init: function () {
                // Show existing image if available
                <?php if (!empty($item->foto)): ?>
                    var mockFile = {
                        name: "<?= $item->foto ?>",
                        size: 0,
                        serverFileName: "<?= $item->foto ?>"
                    };
                    this.emit("addedfile", mockFile);
                    this.emit("thumbnail", mockFile, "<?= base_url($item->foto) ?>");
                    this.emit("complete", mockFile);
                    this.emit("success", mockFile);
                    this.files.push(mockFile);
                <?php endif; ?>

                this.on("success", function (file, response) {
                    if (response.success) {
                        file.serverFileName = response.filename;
                        $('#foto_input').val(response.filename);
                        // Show success message
                        $(file.previewElement).find('.dz-success-mark').show();
                    } else {
                        this.removeFile(file);
                        alert(response.message);
                    }
                });

                this.on("removedfile", function (file) {
                    if (file.serverFileName) {
                        // Delete file from server
                        $.ajax({
                            url: "<?= base_url('master/item/delete_image') ?>",
                            type: "POST",
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                            },
                            data: {
                                filename: file.serverFileName,
                                item_id: <?= $item->id ?>,
                                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                            },
                            success: function (response) {
                                if (response.success) {
                                    $('#foto_input').val('');
                                }
                            }
                        });
                    }
                });

                this.on("error", function (file, errorMessage) {
                    alert(errorMessage);
                });
            }
        });
    });
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<?= $this->endSection() ?>