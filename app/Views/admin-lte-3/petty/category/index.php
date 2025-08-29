<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-tags"></i> Kategori Petty Cash
        </h3>
        <div class="card-tools">
            <a href="<?= base_url('transaksi/petty') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-2"></i>
                                Daftar Kategori
                            </h3>
                            <div class="card-tools">
                                <a href="<?= base_url('transaksi/petty/category/create') ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus mr-2"></i>
                                    Tambah Kategori
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="categoryTable">
                                    <thead>
                                        <tr>
                                            <th width="50">No</th>
                                            <th width="100">Kode</th>
                                            <th>Nama Kategori</th>
                                            <th>Deskripsi</th>
                                            <th width="100">Status</th>
                                            <th width="150">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($categories)): ?>
                                            <?php foreach ($categories as $index => $category): ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td>
                                                        <span class="badge badge-info"><?= $category->kode ?></span>
                                                    </td>
                                                    <td><?= $category->nama ?></td>
                                                    <td><?= $category->deskripsi ?: '-' ?></td>
                                                    <td>
                                                        <?php if ($category->status == '1'): ?>
                                                            <span class="badge badge-success">Aktif</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary">Nonaktif</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="<?= base_url('petty/category/edit/' . $category->id) ?>" 
                                                               class="btn btn-sm btn-primary" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            
                                                            <?php if ($category->status == '1'): ?>
                                                                <a href="<?= base_url('petty/category/toggle-status/' . $category->id) ?>" 
                                                                   class="btn btn-sm btn-warning" title="Nonaktifkan"
                                                                   onclick="return confirm('Yakin ingin menonaktifkan kategori ini?')">
                                                                    <i class="fas fa-ban"></i>
                                                                </a>
                                                            <?php else: ?>
                                                                <a href="<?= base_url('petty/category/toggle-status/' . $category->id) ?>" 
                                                                   class="btn btn-sm btn-success" title="Aktifkan"
                                                                   onclick="return confirm('Yakin ingin mengaktifkan kategori ini?')">
                                                                    <i class="fas fa-check"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                            
                                                            <a href="<?= base_url('petty/category/delete/' . $category->id) ?>" 
                                                               class="btn btn-sm btn-danger" title="Hapus"
                                                               onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    Tidak ada data kategori
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="<?= base_url('petty') ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Kembali ke Petty Cash
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <span class="text-muted">
                                        Total: <strong><?= count($categories) ?></strong> kategori
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#categoryTable').DataTable({
        "responsive": true,
        "autoWidth": false,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "pageLength": 25,
        "order": [[1, "asc"]]
    });
});
</script>
<?= $this->endSection() ?>
