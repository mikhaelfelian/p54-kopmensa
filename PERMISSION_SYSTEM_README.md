# Dynamic Access Control System for CodeIgniter 4 with Ion Auth

This system provides a comprehensive, flexible access control mechanism based on Ion Auth tables with the prefix `tbl_ion_`.

## 🏗️ System Architecture

### Tables Created
1. **`tbl_ion_actions`** - Defines available actions (create, read, update, delete, etc.)
2. **`tbl_ion_modules`** - Defines system modules and their routes
3. **`tbl_ion_permissions`** - Links modules, actions, and users/groups with permissions

### Models
- **`IonActionModel`** - Manages available actions
- **`IonModuleModel`** - Manages system modules
- **`IonPermissionModel`** - Manages user and group permissions

### Services
- **`PermissionService`** - Core service for permission checking and management

### Helpers
- **`permission_helper.php`** - Convenient helper functions for views and controllers

## 🚀 Installation & Setup

### 1. Run Migrations
```bash
php spark migrate
```

### 2. Run Seeders
```bash
php spark db:seed IonActionSeeder
php spark db:seed IonModuleSeeder
php spark db:seed IonPermissionSeeder
```

### 3. Verify Configuration
Ensure the following are loaded in `app/Config/Autoload.php`:
```php
public $helpers = [
    // ... other helpers
    'permission',
];
```

## 📖 Usage Examples

### In Controllers

```php
use App\Services\PermissionService;

class ItemController extends BaseController
{
    protected $permissionService;

    public function __construct()
    {
        $this->permissionService = service('permission');
    }

    public function index()
    {
        // Check if user can read items
        if (!$this->permissionService->canRead('Master/Item')) {
            return redirect()->back()->with('error', 'Access denied');
        }

        // Check if user can read all records (not just own)
        $canReadAll = $this->permissionService->canReadAll('Master/Item');
        
        // Get data based on permissions
        if ($canReadAll) {
            $items = $this->itemModel->findAll();
        } else {
            $items = $this->itemModel->where('created_by', $this->ionAuth->user()->row()->id)->findAll();
        }

        return view('item/index', compact('items'));
    }

    public function create()
    {
        if (!$this->permissionService->canCreate('Master/Item')) {
            return redirect()->back()->with('error', 'Access denied');
        }

        return view('item/create');
    }

    public function store()
    {
        if (!$this->permissionService->canCreate('Master/Item')) {
            return redirect()->back()->with('error', 'Access denied');
        }

        // Process form submission
    }
}
```

### In Views

```php
<!-- Show create button only if user has permission -->
<?= showIfCan('create', 'Master/Item', '<a href="create" class="btn btn-primary">Add New Item</a>') ?>

<!-- Hide content if user doesn't have permission -->
<?= hideIfCannot('delete', 'Master/Item', '<button class="btn btn-danger">Delete</button>') ?>

<!-- Generate permission-aware buttons -->
<?= permissionButton('update', 'Master/Item', 'Edit', 'btn btn-warning', 'editItem()') ?>

<!-- Generate permission-aware links -->
<?= permissionLink('export', 'Master/Item', 'Export', 'export', 'btn btn-success') ?>

<!-- Conditional display -->
<?php if (canDelete('Master/Item')): ?>
    <button class="btn btn-danger" onclick="deleteItem()">Delete</button>
<?php endif; ?>

<!-- Check multiple permissions -->
<?php if (canCreate('Master/Item') && canUpdate('Master/Item')): ?>
    <div class="action-buttons">
        <a href="create" class="btn btn-primary">Create</a>
        <a href="edit" class="btn btn-warning">Edit</a>
    </div>
<?php endif; ?>
```

### Using Helper Functions

```php
// Basic permission checks
if (can('create', 'Master/Item')) {
    // User can create items
}

if (canRead('Master/Item')) {
    // User can read items
}

if (canUpdate('Master/Item')) {
    // User can update items
}

if (canDelete('Master/Item')) {
    // User can delete items
}

if (canExport('Master/Item')) {
    // User can export items
}

if (canApprove('Transaksi/Pembelian')) {
    // User can approve purchases
}

// Check if user is admin
if (isAdmin()) {
    // User has admin privileges
}
```

## 🔧 Advanced Usage

### Granting Permissions Programmatically

```php
$permissionService = service('permission');

// Grant CRUD permissions to a group
$permissionService->grantCrudPermissions('Master/Item', $groupId);

// Grant full permissions to a user
$permissionService->grantFullPermissions('Master/Item', null, $userId);

// Grant specific permissions
$permissionService->grantPermission('Master/Item', 'export', null, $userId);

// Grant multiple permissions
$permissionService->grantMultiplePermissions('Master/Item', ['create', 'read', 'update'], $groupId);
```

### Revoking Permissions

```php
$permissionService = service('permission');

// Revoke specific permission
$permissionService->revokePermission('Master/Item', 'delete', $groupId);

// Revoke multiple permissions
$permissionService->revokeMultiplePermissions('Master/Item', ['create', 'update'], $groupId);
```

### Getting User Permissions

```php
$permissionService = service('permission');

// Get all permissions for current user
$permissions = $permissionService->getUserPermissions();

// Get accessible modules for user
$modules = $permissionService->getUserAccessibleModules();

// Get permission summary
$summary = $permissionService->getPermissionSummary();
```

## 🎯 Available Actions

The system comes with these predefined actions:

- **`create`** - Create new records
- **`read`** - Read/view records
- **`read_all`** - Read all records (not just own)
- **`update`** - Update records
- **`update_all`** - Update all records (not just own)
- **`delete`** - Delete records
- **`delete_all`** - Delete all records (not just own)
- **`export`** - Export data
- **`import`** - Import data
- **`approve`** - Approve records
- **`reject`** - Reject records

## 🏷️ Module Structure

Modules are organized hierarchically:

```
Master Data (parent)
├── Item/Barang
├── Kategori
├── Supplier
├── Pelanggan
├── Karyawan
└── Gudang

Transaksi (parent)
├── Pembelian
└── Penjualan

Gudang (parent)
├── Input Stok
└── Inventori

Laporan (parent)
├── Laporan Outlet
└── Laporan Penjualan

Pengaturan (parent)
├── Modul
└── Printer
```

## 🔒 Permission Inheritance

The system follows this permission hierarchy:

1. **User-specific permissions** (highest priority)
2. **Group permissions** (inherited from user's groups)
3. **Default module permissions** (lowest priority)

## 🚨 Security Features

- **Explicit Denial**: Permissions are explicitly denied by default
- **Route-based**: Permissions are tied to specific module routes
- **Action Granularity**: Fine-grained control over specific actions
- **Group Support**: Permissions can be assigned to groups or individual users
- **Audit Trail**: All permission changes are timestamped

## 🧪 Testing Permissions

```php
// In your test files
public function testUserPermissions()
{
    $permissionService = service('permission');
    
    // Test if user can create items
    $this->assertTrue($permissionService->canCreate('Master/Item'));
    
    // Test if user can delete items
    $this->assertFalse($permissionService->canDelete('Master/Item'));
}
```

## 🔄 Updating Permissions

### Adding New Actions
1. Add the action to `tbl_ion_actions`
2. Update the permission seeder if needed
3. Use the new action in your code

### Adding New Modules
1. Add the module to `tbl_ion_modules`
2. Set appropriate default permissions
3. Run the permission seeder to create group permissions

## 📝 Best Practices

1. **Always check permissions** before performing actions
2. **Use helper functions** in views for cleaner code
3. **Check permissions early** in controller methods
4. **Provide meaningful error messages** when access is denied
5. **Use route-based permissions** for consistency
6. **Test permission logic** thoroughly

## 🐛 Troubleshooting

### Common Issues

1. **Permissions not working**: Ensure the permission helper is loaded
2. **Service not found**: Check if PermissionService is registered in Services.php
3. **Database errors**: Verify migrations and seeders have been run
4. **Cache issues**: Clear application cache if using caching

### Debug Permissions

```php
// Enable debug logging
log_message('debug', 'User permissions: ' . json_encode(service('permission')->getUserPermissions()));

// Check specific permission
log_message('debug', 'Can create: ' . (canCreate('Master/Item') ? 'Yes' : 'No'));
```

## 📚 API Reference

### PermissionService Methods

- `can($action, $moduleRoute)` - Check if user has permission
- `canCreate($moduleRoute)` - Check create permission
- `canRead($moduleRoute)` - Check read permission
- `canUpdate($moduleRoute)` - Check update permission
- `canDelete($moduleRoute)` - Check delete permission
- `grantPermission($moduleRoute, $action, $groupId, $userId)` - Grant permission
- `revokePermission($moduleRoute, $action, $groupId, $userId)` - Revoke permission
- `isAdmin($userId)` - Check if user is admin

### Helper Functions

- `can($action, $moduleRoute)` - Permission check
- `showIfCan($action, $moduleRoute, $html)` - Conditional display
- `hideIfCannot($action, $moduleRoute, $html)` - Conditional hiding
- `permissionButton($action, $moduleRoute, $text, $class, $onclick)` - Permission-aware button
- `permissionLink($action, $moduleRoute, $text, $url, $class)` - Permission-aware link

## 🤝 Contributing

When adding new features to the permission system:

1. Follow the existing naming conventions
2. Add appropriate validation rules
3. Update the documentation
4. Include tests for new functionality
5. Maintain backward compatibility

## 📄 License

This system is part of the KopMensa application and follows the same licensing terms.
