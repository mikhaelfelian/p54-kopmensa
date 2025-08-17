<?= $this->extend('admin-lte-3/layout/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?= isset($title) ? $title : 'PU Menu' ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('pengaturan/pu-menu') ?>">PU Menu</a></li>
                        <li class="breadcrumb-item active"><?= isset($title) ? $title : 'Form' ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?= isset($title) ? $title : 'PU Menu Form' ?></h3>
                            <div class="card-tools">
                                <a href="<?= base_url('pengaturan/pu-menu') ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <form action="<?= base_url('pengaturan/pu-menu/store') ?>" method="post">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama_menu">Nama Menu <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nama_menu" name="nama_menu" 
                                                   placeholder="Masukkan nama menu" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="route">Route <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="route" name="route" 
                                                   placeholder="Contoh: menu/example" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="icon">Icon <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="icon" name="icon" 
                                                   placeholder="Contoh: fas fa-home" required>
                                            <small class="form-text text-muted">Gunakan FontAwesome icon classes</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="urutan">Urutan</label>
                                            <input type="number" class="form-control" id="urutan" name="urutan" 
                                                   placeholder="1" min="1" value="1">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="1">Aktif</option>
                                                <option value="0">Tidak Aktif</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="parent_id">Menu Parent</label>
                                            <select class="form-control" id="parent_id" name="parent_id">
                                                <option value="0">Menu Utama</option>
                                                <!-- TODO: Load parent menus dynamically -->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi</label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" 
                                              placeholder="Deskripsi menu (opsional)"></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <a href="<?= base_url('pengaturan/pu-menu') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
