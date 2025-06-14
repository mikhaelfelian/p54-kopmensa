<?= $this->extend(theme_path('main')) ?>
<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">          
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">Daftar API Tokens</h3>
                        <div class="card-tools">
                            <a href="<?= base_url('pengaturan/api-tokens/add') ?>" class="btn btn-primary btn-sm rounded-0">
                                <i class="fas fa-plus"></i> Tambah Token
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable rounded-0">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">Provider</th>
                                        <th width="40%">Token</th>
                                        <th width="20%">Description</th>
                                        <th width="10%">Status</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($tokens)) : ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data</td>
                                        </tr>
                                    <?php else : ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($tokens as $token) : ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= esc($token->name) ?></td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control token-input rounded-0" value="<?= esc($token->tokens) ?>" readonly>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-secondary copy-btn rounded-0" type="button" data-toggle="tooltip" title="Copy to clipboard">
                                                                <i class="fas fa-copy"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= esc($token->description ?? '-') ?></td>
                                                <td class="text-center">
                                                    <span class="badge badge-<?= !$token->deleted_at ? 'success' : 'danger' ?> rounded-0">
                                                        <?= !$token->deleted_at ? 'Active' : 'Inactive' ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <a href="<?= base_url('pengaturan/api-tokens/edit/' . $token->id) ?>" class="btn btn-warning btn-sm rounded-0" data-toggle="tooltip" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="<?= base_url('pengaturan/api-tokens/toggle/' . $token->id) ?>" class="btn btn-<?= !$token->deleted_at ? 'secondary' : 'success' ?> btn-sm rounded-0" data-toggle="tooltip" title="<?= !$token->deleted_at ? 'Deactivate' : 'Activate' ?>">
                                                            <i class="fas fa-<?= !$token->deleted_at ? 'times' : 'check' ?>"></i>
                                                        </a>
                                                        <a href="<?= base_url('pengaturan/api-tokens/delete/' . $token->id) ?>" class="btn btn-danger btn-sm rounded-0 delete-confirm" data-toggle="tooltip" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
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
        // Initialize DataTable
        $('.datatable').DataTable();
        
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Copy token to clipboard
        $('.copy-btn').on('click', function() {
            var $input = $(this).closest('.input-group').find('.token-input');
            $input.select();
            document.execCommand('copy');
            
            // Show tooltip
            $(this).attr('data-original-title', 'Copied!').tooltip('show');
            
            // Reset tooltip after 2 seconds
            setTimeout(() => {
                $(this).attr('data-original-title', 'Copy to clipboard').tooltip('hide');
            }, 2000);
        });
        
        // Confirm delete
        $('.delete-confirm').on('click', function(e) {
            e.preventDefault();
            
            var href = $(this).attr('href');
            
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Token yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });
</script>
<?= $this->endSection() ?> 