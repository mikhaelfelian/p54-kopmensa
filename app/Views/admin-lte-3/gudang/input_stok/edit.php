<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>

<form action="<?= base_url('gudang/input_stok/update/' . $inputStok->id) ?>" method="POST">
    <?= csrf_field() ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Input Stok</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>No. Terima <span class="text-danger">*</span></label>
                                <input type="text" name="no_terima" class="form-control" 
                                       value="<?= old('no_terima', $inputStok->no_terima) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Terima <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="tgl_terima" class="form-control" 
                                       value="<?= old('tgl_terima', date('Y-m-d\TH:i', strtotime($inputStok->tgl_terima))) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Supplier <span class="text-danger">*</span></label>
                                <select name="id_supplier" class="form-control rounded-0" required>
                                    <option value="">Pilih Supplier</option>
                                    <?php foreach ($supplierList as $supplier): ?>
                                        <option value="<?= $supplier->id ?>" 
                                                <?= old('id_supplier', $inputStok->id_supplier) == $supplier->id ? 'selected' : '' ?>>
                                            <?= $supplier->nama ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Gudang <span class="text-danger">*</span></label>
                                <select name="id_gudang" class="form-control rounded-0" required>
                                    <option value="">Pilih Gudang</option>
                                    <?php foreach ($gudangList as $gudang): ?>
                                        <option value="<?= $gudang->id ?>" 
                                                <?= old('id_gudang', $inputStok->id_gudang) == $gudang->id ? 'selected' : '' ?>>
                                            <?= $gudang->nama ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Penerima <span class="text-danger">*</span></label>
                                <select name="id_penerima" class="form-control rounded-0" required>
                                    <option value="">Pilih Penerima</option>
                                    <?php foreach ($karyawanList as $karyawan): ?>
                                        <option value="<?= $karyawan->id ?>" 
                                                <?= old('id_penerima', $inputStok->id_penerima) == $karyawan->id ? 'selected' : '' ?>>
                                            <?= $karyawan->nama ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Keterangan (opsional)"><?= old('keterangan', $inputStok->keterangan) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Items (Read Only) -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Item</h3>
                    <small class="text-muted ml-2">(Item tidak dapat diubah setelah input stok dibuat)</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr class="bg-secondary">
                                    <th width="5%">No</th>
                                    <th>Kode Item</th>
                                    <th>Nama Item</th>
                                    <th width="15%">Satuan</th>
                                    <th width="15%">Jumlah</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($items)): ?>
                                    <?php foreach ($items as $index => $item): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= $item->item_kode ?></td>
                                            <td><?= $item->item_nama ?></td>
                                            <td><?= $item->satuan_nama ?></td>
                                            <td class="text-right"><?= number_format($item->jml, 2, ',', '.') ?></td>
                                            <td><?= $item->keterangan ?: '-' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada item</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Aksi</h3>
                </div>
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> Update Input Stok
                    </button>
                    <a href="<?= base_url('gudang/input_stok/detail/' . $inputStok->id) ?>" class="btn btn-info btn-block">
                        <i class="fas fa-eye"></i> Lihat Detail
                    </a>
                    <a href="<?= base_url('gudang/input_stok') ?>" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Summary -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ringkasan</h3>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>Total Item:</strong></td>
                            <td class="text-right"><?= count($items) ?> item</td>
                        </tr>
                        <tr>
                            <td><strong>Dibuat:</strong></td>
                            <td class="text-right"><?= date('d/m/Y H:i', strtotime($inputStok->created_at)) ?></td>
                        </tr>
                        <?php if ($inputStok->updated_at != $inputStok->created_at): ?>
                            <tr>
                                <td><strong>Diperbarui:</strong></td>
                                <td class="text-right"><?= date('d/m/Y H:i', strtotime($inputStok->updated_at)) ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
$(document).ready(function() {
    // No additional initialization needed for regular select elements
});
</script>
<?= $this->endSection() ?>