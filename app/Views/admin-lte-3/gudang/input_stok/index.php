<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="card-title">Data Input Stok</h3>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="<?= base_url('gudang/input_stok/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Input Stok
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Filter Form -->
                <form method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control form-control-sm"
                                value="<?= $startDate ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control form-control-sm"
                                value="<?= $endDate ?>">
                        </div>
                        <div class="col-md-2">
                            <label>Supplier</label>
                            <select name="id_supplier" class="form-control form-control-sm">
                                <option value="">Semua Supplier</option>
                                <?php foreach ($supplierList as $supplier): ?>
                                    <option value="<?= $supplier->id ?>" <?= $idSupplier == $supplier->id ? 'selected' : '' ?>>
                                        <?= $supplier->nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Gudang</label>
                            <select name="id_gudang" class="form-control form-control-sm">
                                <option value="">Semua Gudang</option>
                                <?php foreach ($gudangList as $gudang): ?>
                                    <option value="<?= $gudang->id ?>" <?= $idGudang == $gudang->id ? 'selected' : '' ?>>
                                        <?= $gudang->nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-info btn-sm">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="<?= base_url('gudang/input_stok') ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr class="bg-primary">
                                <th width="5%">No</th>
                                <th>No. Terima</th>
                                <th>Tanggal</th>
                                <th>Supplier</th>
                                <th>Gudang</th>
                                <th>Penerima</th>
                                <th>Keterangan</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($inputStoks)): ?>
                                <?php foreach ($inputStoks as $index => $inputStok): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= $inputStok->no_terima ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($inputStok->tgl_terima)) ?></td>
                                        <td><?= $inputStok->supplier_nama ?? '-' ?></td>
                                        <td><?= $inputStok->gudang_nama ?? '-' ?></td>
                                        <td><?= $inputStok->penerima_nama ?? '-' ?></td>
                                        <td><?= $inputStok->keterangan ?: '-' ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('gudang/input_stok/detail/' . $inputStok->id) ?>"
                                                    class="btn btn-info btn-xs" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= base_url('gudang/input_stok/edit/' . $inputStok->id) ?>"
                                                    class="btn btn-warning btn-xs" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('gudang/input_stok/delete/' . $inputStok->id) ?>"
                                                    class="btn btn-danger btn-xs" title="Hapus"
                                                    onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data input stok</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    $(document).ready(function () {
        // Initialize datatable if needed
    });
</script>
<?= $this->endSection() ?>