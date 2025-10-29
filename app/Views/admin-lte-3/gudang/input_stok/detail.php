<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
                    <!-- Header Info -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Input Stok</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>No. Terima</strong></td>
                                            <td>: <?= $inputStok->no_terima ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Terima</strong></td>
                                            <td>: <?= date('d/m/Y H:i', strtotime($inputStok->tgl_terima)) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Supplier</strong></td>
                                            <td>: <?= $inputStok->supplier_nama ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Gudang</strong></td>
                                            <td>: <?= $inputStok->gudang_nama ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Penerima</strong></td>
                                            <td>: <?= $inputStok->penerima_nama ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status</strong></td>
                                            <td>: 
                                                <?php if ($inputStok->status == '1'): ?>
                                                    <span class="badge badge-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <?php if ($inputStok->keterangan): ?>
                                <div class="row">
                                    <div class="col-12">
                                        <strong>Keterangan:</strong><br>
                                        <?= nl2br(esc($inputStok->keterangan)) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Detail Item</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr class="bg-primary">
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
                            <a href="<?= base_url('gudang/input_stok/edit/' . $inputStok->id) ?>" class="btn btn-warning btn-block">
                                <i class="fas fa-edit"></i> Edit Input Stok
                            </a>
                            <a href="<?= base_url('gudang/input_stok') ?>" class="btn btn-secondary btn-block">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <hr>
                            <a href="<?= base_url('gudang/input_stok/delete/' . $inputStok->id) ?>" 
                               class="btn btn-danger btn-block"
                               onclick="return confirm('Yakin ingin menghapus data ini?')">
                                <i class="fas fa-trash"></i> Hapus Input Stok
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
        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
$(document).ready(function() {
    // Initialize page
});
</script>
<?= $this->endSection() ?>
