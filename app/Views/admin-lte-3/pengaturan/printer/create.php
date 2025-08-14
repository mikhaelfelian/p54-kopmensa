<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Tambah Printer</h3>
                <div class="card-tools">
                    <a href="<?= base_url('pengaturan/printer') ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <form action="<?= base_url('pengaturan/printer/store') ?>" method="post">
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_printer">Nama Printer <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_printer" name="nama_printer"
                                    value="<?= old('nama_printer') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipe_printer">Tipe Printer <span class="text-danger">*</span></label>
                                <select class="form-control" id="tipe_printer" name="tipe_printer" required>
                                    <option value="">Pilih Tipe</option>
                                    <?php foreach ($printerTypes as $value => $label): ?>
                                        <option value="<?= $value ?>" <?= old('tipe_printer') == $value ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Network Printer Fields -->
                    <div id="network-fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ip_address">IP Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="ip_address" name="ip_address"
                                        value="<?= old('ip_address') ?>" placeholder="192.168.1.100">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="port">Port <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="port" name="port"
                                        value="<?= old('port', '9100') ?>" min="1" max="65535">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- USB/File Printer Fields -->
                    <div id="path-fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="path">Path/Device <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="path" name="path"
                                        value="<?= old('path') ?>"
                                        placeholder="/dev/usb/lp0 atau C:\printer\receipt.txt">
                                    <small class="form-text text-muted">
                                        Untuk USB: /dev/usb/lp0, /dev/ttyUSB0<br>
                                        Untuk File: /path/to/file.txt atau C:\path\to\file.txt
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Windows Printer Fields -->
                    <div id="windows-fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="path">Nama Printer Windows <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="path" name="path"
                                        value="<?= old('path') ?>"
                                        placeholder="POS-58 Printer atau Microsoft Print to PDF">
                                    <small class="form-text text-muted">
                                        Masukkan nama printer yang terdaftar di Windows
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="driver">Driver <span class="text-danger">*</span></label>
                                <select class="form-control" id="driver" name="driver" required>
                                    <option value="">Pilih Driver</option>
                                    <?php foreach ($driverOptions as $value => $label): ?>
                                        <option value="<?= $value ?>" <?= old('driver') == $value ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="width_paper">Lebar Kertas (mm) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="width_paper" name="width_paper"
                                    value="<?= old('width_paper', '58') ?>" min="1" max="200" required>
                                <small class="form-text text-muted">
                                    Umum: 58mm (POS58), 80mm, 112mm
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1" <?= old('status', '1') == '1' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="0" <?= old('status') == '0' ? 'selected' : '' ?>>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox mt-4">
                                    <input type="checkbox" class="custom-control-input" id="is_default"
                                        name="is_default" value="1">
                                    <label class="custom-control-label" for="is_default">
                                        Jadikan sebagai printer default
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3"
                            placeholder="Keterangan tambahan tentang printer ini"><?= old('keterangan') ?></textarea>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="<?= base_url('pengaturan/printer') ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->section('js') ?>
<script>
    $(document).ready(function () {
        // Show/hide fields based on printer type
        $('#tipe_printer').on('change', function () {
            const selectedType = $(this).val();

            // Hide all type-specific fields
            $('#network-fields, #path-fields, #windows-fields').hide();

            // Show relevant fields
            if (selectedType === 'network') {
                $('#network-fields').show();
            } else if (selectedType === 'usb' || selectedType === 'file') {
                $('#path-fields').show();
            } else if (selectedType === 'windows') {
                $('#windows-fields').show();
            }
        });

        // Trigger change event on page load
        $('#tipe_printer').trigger('change');
    });
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>