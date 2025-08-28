<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-plus"></i> Tambah Kategori Petty Cash
        </h3>
        <div class="card-tools">
            <a href="<?= base_url('petty-category') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <form action="<?= base_url('petty-category/create') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label for="name">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" 
                               required placeholder="Masukkan nama kategori">
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control" rows="3" 
                                  placeholder="Deskripsi kategori (opsional)"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-md-6">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Petunjuk</h5>
                    <ul class="mb-0">
                        <li>Nama kategori harus unik dan mudah dipahami</li>
                        <li>Deskripsi membantu menjelaskan tujuan kategori</li>
                        <li>Status active memungkinkan kategori digunakan</li>
                        <li>Status inactive mencegah penggunaan kategori</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Auto-focus on name field
    $('#name').focus();
});
</script>
<?= $this->endSection() ?>
