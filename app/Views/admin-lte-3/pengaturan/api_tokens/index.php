<?= $this->extend('admin-lte-3/layout/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">API Tokens</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Pengaturan</a></li>
                        <li class="breadcrumb-item active">API Tokens</li>
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
                            <h3 class="card-title">API Token Management</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#generateTokenModal">
                                    <i class="fas fa-plus"></i> Generate New Token
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="20%">Token Name</th>
                                            <th width="35%">Token</th>
                                            <th width="15%">Created</th>
                                            <th width="15%">Last Used</th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                <i class="fas fa-info-circle"></i> No API tokens generated yet
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Generate Token Modal -->
<div class="modal fade" id="generateTokenModal" tabindex="-1" role="dialog" aria-labelledby="generateTokenModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateTokenModalLabel">Generate New API Token</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tokenName">Token Name</label>
                        <input type="text" class="form-control" id="tokenName" placeholder="Enter token name" required>
                        <small class="form-text text-muted">Give your token a descriptive name for easy identification.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate Token</button>
                </div>
            </form>
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