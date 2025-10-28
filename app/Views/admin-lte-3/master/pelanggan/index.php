<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-12
 * Github : github.com/mikhaelfelian
 * description : View for displaying pelanggan/customer data
 * This file represents the Pelanggan Index View.
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('master/customer/create') ?>" class="btn btn-sm btn-primary rounded-0">
                            <i class="fas fa-plus"></i> Tambah Data
                        </a>
                        <a href="<?= base_url('master/customer/import') ?>" class="btn btn-sm btn-success rounded-0">
                            <i class="fas fa-file-import"></i> IMPORT
                        </a>
                        <a href="<?= base_url('master/customer/export') ?>" class="btn btn-sm btn-warning rounded-0">
                            <i class="fas fa-file-export"></i> EXPORT
                        </a>
                        <a href="<?= base_url('master/customer/template') ?>" class="btn btn-sm btn-info rounded-0">
                            <i class="fas fa-download"></i> Template
                        </a>
                        <button type="button" id="bulk-delete-btn" class="btn btn-sm btn-danger rounded-0" style="display: none;">
                            <i class="fas fa-trash-alt"></i> Hapus <span id="selected-count">0</span> Terpilih
                        </button>
                    </div>
                    <div class="col-md-6">
                        <?= form_open('', ['method' => 'get', 'class' => 'float-right']) ?>
                        <div class="input-group input-group-sm">
                            <?= form_input([
                                'name'        => 'keyword',
                                'class'       => 'form-control rounded-0',
                                'value'       => $keyword ?? '',
                                'placeholder' => 'Cari...',
                            ]) ?>
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-primary rounded-0" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="select-all">
                            </th>
                            <th width="50">No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>No. Telp</th>
                            <th>Alamat</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pelanggan)): ?>
                            <?php foreach ($pelanggan as $key => $row): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="select-item" value="<?= $row->id ?>">
                                    </td>
                                    <td><?= (($currentPage - 1) * $perPage) + $key + 1 ?></td>
                                    <td><?= esc($row->kode) ?></td>
                                    <td><?= esc($row->nama) ?></td>
                                    <td><?= esc($row->no_telp) ?></td>
                                    <td><?= esc($row->alamat) ?></td>
                                    <td>
                                        <?php
                                        $tipeLabels = [
                                            '0' => '-',
                                            '1' => 'Anggota',
                                            '2' => 'Pelanggan'
                                        ];
                                        echo $tipeLabels[$row->tipe] ?? '-';
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= ($row->status == '1') ? 'success' : 'danger' ?>">
                                            <?= ($row->status == '1') ? 'Aktif' : 'Tidak Aktif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= base_url("master/customer/detail/$row->id") ?>"
                                                class="btn btn-info btn-sm rounded-0">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url("master/customer/edit/$row->id") ?>"
                                                class="btn btn-warning btn-sm rounded-0">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url("master/customer/delete/$row->id") ?>"
                                                class="btn btn-danger btn-sm rounded-0"
                                                onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>

                </div>
            <?php if ($pager): ?>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        <?= $pager->links('pelanggan', 'adminlte_pagination') ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<!-- User Management Modal -->
<div class="modal fade" id="userManagementModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Management - <span id="customerName"></span></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Account Actions</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Reset Password</label>
                                    <button type="button" class="btn btn-warning btn-sm btn-block" id="resetPasswordBtn">
                                        <i class="fas fa-key"></i> Reset Password
                                    </button>
                                </div>
                                <div class="form-group">
                                    <label>Generate New Username</label>
                                    <button type="button" class="btn btn-primary btn-sm btn-block" id="generateUsernameBtn">
                                        <i class="fas fa-user-edit"></i> Generate Username
                                    </button>
                                </div>
                                <div class="form-group">
                                    <label>Block/Unblock Account</label>
                                    <button type="button" class="btn btn-danger btn-sm btn-block" id="toggleBlockBtn">
                                        <i class="fas fa-ban"></i> <span id="blockBtnText">Block Account</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Account Information</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Username:</strong> <span id="currentUsername">-</span></p>
                                <p><strong>Email:</strong> <span id="currentEmail">-</span></p>
                                <p><strong>Account Status:</strong> <span id="accountStatus" class="badge badge-success">Active</span></p>
                                <p><strong>Last Login:</strong> <span id="lastLogin">-</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- User Logs Section -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">User Activity Logs</h6>
                            </div>
                            <div class="card-body">
                                <div id="userLogs">
                                    <div class="text-center">
                                        <i class="fas fa-spinner fa-spin"></i> Loading logs...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Purchase History Section -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Purchase History</h6>
                            </div>
                            <div class="card-body">
                                <div id="purchaseHistory">
                                    <div class="text-center">
                                        <i class="fas fa-spinner fa-spin"></i> Loading purchase history...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let currentCustomerId = 0;
    let currentUserId = 0;
    
    $('#userManagementModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        currentCustomerId = button.data('customer-id');
        currentUserId = button.data('user-id');
        const customerName = button.data('customer-name');
        
        $('#customerName').text(customerName);
        
        // Load user information
        loadUserInfo(currentUserId);
        
        // Load user logs
        loadUserLogs(currentUserId);
        
        // Load purchase history
        loadPurchaseHistory(currentCustomerId);
    });
    
    // Reset password
    $('#resetPasswordBtn').click(function() {
        if (confirm('Are you sure you want to reset the password?')) {
            $.post('<?= base_url("master/customer/reset_password") ?>', {
                user_id: currentUserId
            }, function(response) {
                if (response.success) {
                    alert('Password reset successfully. New password: ' + response.new_password);
                } else {
                    alert('Error: ' + response.message);
                }
            }, 'json');
        }
    });
    
    // Generate new username
    $('#generateUsernameBtn').click(function() {
        if (confirm('Are you sure you want to generate a new username?')) {
            $.post('<?= base_url("master/customer/generate_username") ?>', {
                user_id: currentUserId
            }, function(response) {
                if (response.success) {
                    $('#currentUsername').text(response.new_username);
                    alert('New username generated: ' + response.new_username);
                } else {
                    alert('Error: ' + response.message);
                }
            }, 'json');
        }
    });
    
    // Toggle block status
    $('#toggleBlockBtn').click(function() {
        const isBlocked = $(this).data('blocked') || false;
        const action = isBlocked ? 'unblock' : 'block';
        const message = `Are you sure you want to ${action} this account?`;
        
        if (confirm(message)) {
            $.post('<?= base_url("master/customer/toggle_block") ?>', {
                user_id: currentUserId,
                action: action
            }, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            }, 'json');
        }
    });
    
    function loadUserInfo(userId) {
        $.get('<?= base_url("master/customer/get_user_info") ?>/' + userId, function(response) {
            if (response.success) {
                $('#currentUsername').text(response.data.username);
                $('#currentEmail').text(response.data.email);
                $('#lastLogin').text(response.data.last_login);
                
                const statusBadge = response.data.active ? 'badge-success' : 'badge-danger';
                const statusText = response.data.active ? 'Active' : 'Blocked';
                $('#accountStatus').removeClass('badge-success badge-danger').addClass(statusBadge).text(statusText);
                
                $('#toggleBlockBtn').data('blocked', !response.data.active);
                $('#blockBtnText').text(response.data.active ? 'Block Account' : 'Unblock Account');
            }
        }, 'json');
    }
    
    function loadUserLogs(userId) {
        $.get('<?= base_url("master/customer/get_user_logs") ?>/' + userId, function(response) {
            let logsHtml = '';
            if (response.success && response.data.length > 0) {
                logsHtml = '<div class="table-responsive"><table class="table table-sm table-striped">';
                logsHtml += '<thead><tr><th>Date</th><th>Action</th><th>IP Address</th></tr></thead><tbody>';
                response.data.forEach(function(log) {
                    logsHtml += `<tr><td>${log.created_at}</td><td>${log.action}</td><td>${log.ip_address}</td></tr>`;
                });
                logsHtml += '</tbody></table></div>';
            } else {
                logsHtml = '<p class="text-muted">No activity logs found.</p>';
            }
            $('#userLogs').html(logsHtml);
        }, 'json');
    }
    
    function loadPurchaseHistory(customerId) {
        $.get('<?= base_url("master/customer/get_purchase_history") ?>/' + customerId, function(response) {
            let historyHtml = '';
            if (response.success && response.data.length > 0) {
                historyHtml = '<div class="table-responsive"><table class="table table-sm table-striped">';
                historyHtml += '<thead><tr><th>Date</th><th>Invoice</th><th>Total</th><th>Status</th></tr></thead><tbody>';
                response.data.forEach(function(history) {
                    historyHtml += `<tr><td>${history.tanggal}</td><td>${history.no_invoice}</td><td>${history.total}</td><td><span class="badge badge-success">${history.status}</span></td></tr>`;
                });
                historyHtml += '</tbody></table></div>';
            } else {
                historyHtml = '<p class="text-muted">No purchase history found.</p>';
            }
            $('#purchaseHistory').html(historyHtml);
        }, 'json');
    }
});

// Bulk delete functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const selectItems = document.querySelectorAll('.select-item');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    const selectedCount = document.getElementById('selected-count');

    // Handle select all checkbox
    selectAll.addEventListener('change', function() {
        selectItems.forEach(item => {
            item.checked = this.checked;
        });
        updateBulkDeleteButton();
    });

    // Handle individual checkboxes
    selectItems.forEach(item => {
        item.addEventListener('change', function() {
            updateSelectAllState();
            updateBulkDeleteButton();
        });
    });

    // Update select-all checkbox state (checked, unchecked, or indeterminate)
    function updateSelectAllState() {
        const totalItems = selectItems.length;
        const checkedItems = document.querySelectorAll('.select-item:checked').length;

        if (checkedItems === 0) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        } else if (checkedItems === totalItems) {
            selectAll.checked = true;
            selectAll.indeterminate = false;
        } else {
            selectAll.checked = false;
            selectAll.indeterminate = true;
        }
    }

    // Show/hide bulk delete button based on selection
    function updateBulkDeleteButton() {
        const checkedItems = document.querySelectorAll('.select-item:checked');
        if (checkedItems.length > 0) {
            bulkDeleteBtn.style.display = 'inline-block';
            selectedCount.textContent = checkedItems.length;
        } else {
            bulkDeleteBtn.style.display = 'none';
        }
    }

    // Handle bulk delete
    bulkDeleteBtn.addEventListener('click', function() {
        const checkedItems = document.querySelectorAll('.select-item:checked');
        const itemIds = Array.from(checkedItems).map(item => item.value);

        if (itemIds.length === 0) {
            alert('Tidak ada item yang dipilih');
            return;
        }

        if (!confirm(`Apakah Anda yakin ingin menghapus ${itemIds.length} pelanggan yang dipilih?`)) {
            return;
        }

        // Show loading state
        bulkDeleteBtn.disabled = true;
        bulkDeleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';

        // Send AJAX request with FormData to properly handle arrays
        const formData = new FormData();
        itemIds.forEach(id => {
            formData.append('item_ids[]', id);
        });
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        
        fetch('<?= base_url('master/customer/bulk_delete') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
                bulkDeleteBtn.disabled = false;
                bulkDeleteBtn.innerHTML = '<i class="fas fa-trash-alt"></i> Hapus <span id="selected-count">' + itemIds.length + '</span> Terpilih';
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
            bulkDeleteBtn.disabled = false;
            bulkDeleteBtn.innerHTML = '<i class="fas fa-trash-alt"></i> Hapus <span id="selected-count">' + itemIds.length + '</span> Terpilih';
        });
    });
});
</script>

<?= $this->endSection() ?>