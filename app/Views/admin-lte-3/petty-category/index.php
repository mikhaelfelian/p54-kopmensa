<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-tags"></i> Kategori Petty Cash
        </h3>
        <div class="card-tools">
            <a href="<?= base_url('petty-category/create') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Kategori
            </a>
        </div>
    </div>
    <div class="card-body">


        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)) : ?>
                        <?php foreach ($categories as $category) : ?>
                            <tr>
                                <td><?= $category['name'] ?></td>
                                <td><?= $category['description'] ?? '-' ?></td>
                                <td>
                                    <?php if ($category['status'] === 'active') : ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else : ?>
                                        <span class="badge badge-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url('petty-category/edit/' . $category['id']) ?>" 
                                           class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('petty-category/toggle-status/' . $category['id']) ?>" 
                                           class="btn btn-info btn-sm" title="Toggle Status"
                                           onclick="return confirm('Are you sure you want to change the status?')">
                                            <i class="fas fa-toggle-on"></i>
                                        </a>
                                        <?php if ($category['can_delete']) : ?>
                                            <a href="<?= base_url('petty-category/delete/' . $category['id']) ?>" 
                                               class="btn btn-danger btn-sm" title="Delete"
                                               onclick="return confirm('Are you sure you want to delete this category?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" class="text-center">No categories found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize DataTable if available
    if ($.fn.DataTable) {
        $('.table').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print"]
        }).buttons().container().appendTo('.card-header .card-tools');
    }
});
</script>
<?= $this->endSection() ?>
