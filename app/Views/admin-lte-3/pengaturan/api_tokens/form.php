<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?= $title ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>">Dashboard</a></li>
                    <li class="breadcrumb-item">Pengaturan</li>
                    <li class="breadcrumb-item"><a href="<?= base_url('pengaturan/api-tokens') ?>">API Tokens</a></li>
                    <li class="breadcrumb-item active"><?= $title ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php if (session()->has('success')) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session('success') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->has('error')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session('error') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <?= isset($token) ? 'Edit API Token' : 'Tambah API Token Baru' ?>
                        </h3>
                    </div>
                    <div class="card-body">
                        <form action="<?= isset($token) ? base_url('pengaturan/api-tokens/edit/' . $token->id) : base_url('pengaturan/api-tokens/add') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="form-group">
                                <label for="provider">Provider <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= $validation->hasError('provider') ? 'is-invalid' : '' ?>" id="provider" name="provider" value="<?= set_value('provider', isset($token) ? $token->name : '') ?>" placeholder="Masukkan nama provider (contoh: apify, openai)" required>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('provider') ?>
                                </div>
                                <small class="text-muted">Nama provider harus unik dan hanya boleh mengandung huruf, angka, dan tanda "-" atau "_".</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="token">Token <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control <?= $validation->hasError('token') ? 'is-invalid' : '' ?>" id="token" name="token" value="<?= set_value('token', isset($token) ? $token->tokens : '') ?>" placeholder="Masukkan token API" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="showHideToken">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('token') ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea class="form-control <?= $validation->hasError('description') ? 'is-invalid' : '' ?>" id="description" name="description" rows="3" placeholder="Deskripsi singkat tentang token ini (opsional)"><?= set_value('description', '') ?></textarea>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('description') ?>
                                </div>
                            </div>
                            
                            <?php if (isset($token)) : ?>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" <?= !$token->deleted_at ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="is_active">Status Aktif</label>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <a href="<?= base_url('pengaturan/api-tokens') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Show/hide token
        $('#showHideToken').on('click', function() {
            var $input = $('#token');
            var $icon = $(this).find('i');
            
            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                $input.attr('type', 'password');
                $icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
        
        // Initialize with password type
        $('#token').attr('type', 'password');
    });
</script>
<?= $this->endSection() ?> 