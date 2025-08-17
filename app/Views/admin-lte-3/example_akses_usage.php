<?php
/**
 * Example Usage of Access Control Helper
 * 
 * This file demonstrates how to use the akses_helper functions
 * in your views and controllers.
 */

// In Controllers:
// ===============

// 1. Check access in controller methods
// public function adminOnly()
// {
//     // Require admin access, redirect if not authorized
//     require_akses('admin');
//     
//     // Your admin-only code here
// }
// 
// public function managerOrHigher()
// {
//     // Check if user has manager access
//     if (!akses_manager()) {
//         return redirect()->back()->with('error', 'Access denied');
//     }
//     
//     // Your manager code here
// }
// 
// // 2. Pass access data to views
// public function dashboard()
// {
//     $data = [
//         'can_edit_users' => akses_admin(),
//         'can_view_reports' => akses_manager(),
//         'can_manage_system' => akses_root(),
//         'user_role' => get_user_role()
//     ];
//     
//     return view('dashboard', $data);
// }

// In Views:
// =========

// 1. Conditional rendering based on access
?>

<!-- Show admin panel only to admins -->
<?php if (akses_admin()): ?>
    <div class="admin-panel">
        <h3>Admin Panel</h3>
        <a href="<?= base_url('admin/users') ?>" class="btn btn-primary">Manage Users</a>
    </div>
<?php endif; ?>

<!-- Show manager features to managers and above -->
<?php if (akses_manager()): ?>
    <div class="manager-panel">
        <h3>Manager Panel</h3>
        <a href="<?= base_url('reports') ?>" class="btn btn-info">View Reports</a>
    </div>
<?php endif; ?>

<!-- Show cashier features to kasir and above -->
<?php if (akses_kasir()): ?>
    <div class="cashier-panel">
        <h3>Cashier Panel</h3>
        <a href="<?= base_url('cashier') ?>" class="btn btn-success">Open Cashier</a>
    </div>
<?php endif; ?>

<!-- 2. Dynamic menu rendering -->
<nav class="navbar">
    <ul class="nav-menu">
        <!-- Always show dashboard -->
        <li><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
        
        <!-- Show based on access -->
        <?php if (akses_kasir()): ?>
            <li><a href="<?= base_url('transaksi') ?>">Transaksi</a></li>
        <?php endif; ?>
        
        <?php if (akses_admin()): ?>
            <li><a href="<?= base_url('master') ?>">Master Data</a></li>
        <?php endif; ?>
        
        <?php if (akses_manager()): ?>
            <li><a href="<?= base_url('laporan') ?>">Laporan</a></li>
        <?php endif; ?>
        
        <?php if (akses_superadmin()): ?>
            <li><a href="<?= base_url('pengaturan') ?>">Pengaturan</a></li>
        <?php endif; ?>
        
        <?php if (akses_root()): ?>
            <li><a href="<?= base_url('system') ?>">System</a></li>
        <?php endif; ?>
    </ul>
</nav>

<!-- 3. Table actions based on access -->
<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user->name ?></td>
            <td><?= $user->email ?></td>
            <td>
                <!-- View button - available to all authenticated users -->
                <a href="<?= base_url('users/view/' . $user->id) ?>" class="btn btn-sm btn-info">
                    <i class="fas fa-eye"></i>
                </a>
                
                <!-- Edit button - admin only -->
                <?php if (akses_admin()): ?>
                    <a href="<?= base_url('users/edit/' . $user->id) ?>" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                <?php endif; ?>
                
                <!-- Delete button - superadmin only -->
                <?php if (akses_superadmin()): ?>
                    <a href="<?= base_url('users/delete/' . $user->id) ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Yakin ingin menghapus?')">
                        <i class="fas fa-trash"></i>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- 4. Form fields based on access -->
<form method="post" action="<?= base_url('users/save') ?>">
    <div class="form-group">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    
    <!-- Role field - admin only -->
    <?php if (akses_admin()): ?>
    <div class="form-group">
        <label>Role</label>
        <select name="role" class="form-control">
            <option value="kasir">Kasir</option>
            <option value="admin">Admin</option>
            <?php if (akses_superadmin()): ?>
                <option value="manager">Manager</option>
                <option value="superadmin">Super Admin</option>
            <?php endif; ?>
            <?php if (akses_root()): ?>
                <option value="root">Root</option>
            <?php endif; ?>
        </select>
    </div>
    <?php endif; ?>
    
    <button type="submit" class="btn btn-primary">Save</button>
</form>

<!-- 5. Display current user role -->
<div class="user-info">
    <p>Welcome, <?= $ionAuth->user()->row()->first_name ?>!</p>
    <p>Your role: <strong><?= ucfirst(get_user_role()) ?></strong></p>
    
    <?php if (akses_root()): ?>
        <span class="badge badge-danger">Root Access</span>
    <?php elseif (akses_superadmin()): ?>
        <span class="badge badge-warning">Super Admin</span>
    <?php elseif (akses_manager()): ?>
        <span class="badge badge-info">Manager</span>
    <?php elseif (akses_admin()): ?>
        <span class="badge badge-primary">Admin</span>
    <?php elseif (akses_kasir()): ?>
        <span class="badge badge-success">Kasir</span>
    <?php else: ?>
        <span class="badge badge-secondary">User</span>
    <?php endif; ?>
</div>

<!-- 6. JavaScript access control -->
<script>
// You can also use PHP to pass access data to JavaScript
const userAccess = {
    isRoot: <?= akses_root() ? 'true' : 'false' ?>,
    isSuperAdmin: <?= akses_superadmin() ? 'true' : 'false' ?>,
    isManager: <?= akses_manager() ? 'true' : 'false' ?>,
    isAdmin: <?= akses_admin() ? 'true' : 'false' ?>,
    isKasir: <?= akses_kasir() ? 'true' : 'false' ?>,
    userRole: '<?= get_user_role() ?>'
};

// Use in JavaScript
if (userAccess.isAdmin) {
    // Show admin features
    document.getElementById('adminPanel').style.display = 'block';
}

// Check access before AJAX calls
function deleteUser(userId) {
    if (!userAccess.isSuperAdmin) {
        alert('You do not have permission to delete users');
        return;
    }
    
    // Proceed with deletion
    if (confirm('Are you sure you want to delete this user?')) {
        // AJAX call here
    }
}
</script>

<?php
// 7. Helper functions for common patterns

// Function to render action buttons based on access
function renderActionButtons($itemId, $viewUrl, $editUrl = null, $deleteUrl = null) {
    $buttons = [];
    
    // View button - always available
    $buttons[] = "<a href=\"{$viewUrl}\" class=\"btn btn-sm btn-info\"><i class=\"fas fa-eye\"></i></a>";
    
    // Edit button - admin only
    if (akses_admin() && $editUrl) {
        $buttons[] = "<a href=\"{$editUrl}\" class=\"btn btn-sm btn-warning\"><i class=\"fas fa-edit\"></i></a>";
    }
    
    // Delete button - superadmin only
    if (akses_superadmin() && $deleteUrl) {
        $buttons[] = "<a href=\"{$deleteUrl}\" class=\"btn btn-sm btn-danger\" onclick=\"return confirm('Yakin ingin menghapus?')\"><i class=\"fas fa-trash\"></i></a>";
    }
    
    return implode(' ', $buttons);
}

// Usage:
echo renderActionButtons(
    $user->id,
    base_url('users/view/' . $user->id),
    akses_admin() ? base_url('users/edit/' . $user->id) : null,
    akses_superadmin() ? base_url('users/delete/' . $user->id) : null
);
?>
