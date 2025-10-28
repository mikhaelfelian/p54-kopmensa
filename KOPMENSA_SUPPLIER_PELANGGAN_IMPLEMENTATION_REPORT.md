# Kopmensa Supplier & Pelanggan Module Implementation Report

**Date**: January 2025  
**Framework**: CodeIgniter 4.6.3, AdminLTE 3  
**Status**: 📋 IMPLEMENTATION PLAN

---

## Executive Summary

This report documents the required implementations for **Supplier** and **Pelanggan (Anggota)** modules to complete all missing/broken features. The analysis shows that both modules have basic functionality but need enhancements for full compliance.

### Current State Analysis

#### Supplier Module ✅ Mostly Complete
- ✅ CRUD operations working
- ✅ Import functionality exists
- ✅ Export functionality exists  
- ✅ Checklist UI exists
- ❌ Bulk delete needs FormData fix (same as Outlet/Gudang)
- ❌ Missing `kategori` field (perorangan/pabrikan)
- ✅ Detail view shows related items

#### Pelanggan Module ⚠️ Partially Complete
- ✅ CRUD operations working
- ✅ Import functionality exists
- ❌ Export functionality missing
- ✅ Checklist UI exists
- ❌ Bulk delete needs FormData fix
- ❌ Missing `is_blocked` field
- ❌ Missing `limit_belanja` field
- ❌ Transaction history needs enhancement
- ✅ Soft delete working

---

## 1. Critical Fixes Required

### 1.1 Fix Bulk Delete for Both Modules

**Problem**: Same AJAX issue as Outlet/Gudang - sending `item_ids` incorrectly.

**Files to Fix**:
- `app/Views/admin-lte-3/master/supplier/index.php`
- `app/Views/admin-lte-3/master/pelanggan/index.php`

**Current Code** (Broken):
```javascript
body: new URLSearchParams({
    'item_ids': itemIds,  // ❌ Becomes string
    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
})
```

**Fix Required**:
```javascript
// Use FormData for proper array handling
const formData = new FormData();
itemIds.forEach(id => {
    formData.append('item_ids[]', id);
});
formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

fetch('<?= base_url('master/supplier/bulk_delete') ?>', {
    method: 'POST',
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    },
    body: formData
})
```

**Apply to**: Both Supplier and Pelanggan index views (lines ~190-210)

---

### 1.2 Add Pelanggan Export Functionality

**File**: `app/Controllers/Master/Pelanggan.php`

**Add after line 1295** (`downloadTemplate` method):

```php
/**
 * Export pelanggan data to Excel
 */
public function exportExcel()
{
    $keyword = $this->request->getVar('keyword');
    
    // Build query - same filters as index
    $query = $this->pelangganModel;
    $query->where('status_hps', '0');
    
    if ($keyword) {
        $query->groupStart()
            ->like('nama', $keyword)
            ->orLike('kode', $keyword)
            ->orLike('no_telp', $keyword)
            ->orLike('alamat', $keyword)
            ->groupEnd();
    }
    
    // Get all data (no pagination for export)
    $pelanggans = $query->orderBy('id', 'DESC')->findAll();
    
    // Prepare Excel data
    $headers = ['Kode', 'Nama', 'No. Telp', 'Email', 'Alamat', 'Kota', 'Provinsi', 'Tipe', 'Status'];
    $excelData = [];
    
    foreach ($pelanggans as $pelanggan) {
        $tipeLabel = [
            '0' => '-',
            '1' => 'Anggota',
            '2' => 'Pelanggan'
        ];
        
        $excelData[] = [
            $pelanggan->kode,
            $pelanggan->nama,
            $pelanggan->no_telp ?? '',
            $pelanggan->email ?? '',
            $pelanggan->alamat ?? '',
            $pelanggan->kota ?? '',
            $pelanggan->provinsi ?? '',
            $tipeLabel[$pelanggan->tipe] ?? '-',
            ($pelanggan->status == '1') ? 'Aktif' : 'Tidak Aktif'
        ];
    }
    
    $filename = 'export_pelanggan_' . date('Y-m-d_His') . '.xlsx';
    $filepath = createExcelTemplate($headers, $excelData, $filename);
    
    return $this->response->download($filepath, null);
}
```

**Also Add Export Button** to `app/Views/admin-lte-3/master/pelanggan/index.php`:

After line 27 (after IMPORT button):
```php
<a href="<?= base_url('master/pelanggan/export') ?>" class="btn btn-sm btn-warning rounded-0">
    <i class="fas fa-file-export"></i> EXPORT
</a>
```

---

### 1.3 Add Kategori Field to Supplier

**Create Migration**: `app/Database/Migrations/2025_01_28_000001_add_kategori_to_supplier.php`

```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKategoriToSupplier extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_m_supplier', [
            'kategori' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'tipe',
                'comment' => 'perorangan/pabrikan'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_m_supplier', 'kategori');
    }
}
```

**Update Model**: `app/Models/SupplierModel.php`

Add to `$allowedFields` array (line 24):
```php
protected $allowedFields = [
    'kode', 'nama', 'npwp', 'alamat', 'rt', 'rw', 
    'kecamatan', 'kelurahan', 'kota', 'no_tlp', 'no_hp',
    'tipe', 'kategori', 'status', 'status_hps'  // ← Add kategori here
];
```

**Update Views**: Add kategori field to create/edit forms:
- `app/Views/admin-lte-3/master/supplier/create.php`
- `app/Views/admin-lte-3/master/supplier/edit.php`

Add after tipe field:
```html
<div class="form-group">
    <label>Kategori</label>
    <?= form_dropdown('kategori', [
        'perorangan' => 'Perorangan',
        'pabrikan' => 'Pabrikan'
    ], set_value('kategori', $supplier->kategori ?? ''), ['class' => 'form-control rounded-0']) ?>
</div>
```

**Update Export**: Modify `export()` method to include kategori column.

---

### 1.4 Add is_blocked and limit_belanja to Pelanggan

**Create Migration**: `app/Database/Migrations/2025_01_28_000002_add_blocked_fields_to_pelanggan.php`

```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBlockedFieldsToPelanggan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_m_pelanggan', [
            'is_blocked' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'status',
                'comment' => '0=not blocked, 1=blocked'
            ],
            'limit_belanja' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'after' => 'is_blocked',
                'comment' => 'Shopping limit in currency'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_m_pelanggan', ['is_blocked', 'limit_belanja']);
    }
}
```

**Update Model**: `app/Models/PelangganModel.php`

Update `$allowedFields` (line 22):
```php
protected $allowedFields = [
    'id_user', 'kode', 'nama', 'no_telp', 'alamat', 'kota', 
    'provinsi', 'tipe', 'status', 'is_blocked', 'limit_belanja', 'status_hps', 'limit'
];
```

**Update Views**: Add fields to create/edit forms and show in listing.

---

### 1.5 Add Transaction History to Pelanggan Detail

**File**: `app/Controllers/Master/Pelanggan.php`

**In `detail()` method**, add transaction data:

```php
// Get transaction history (last 10)
$transJualModel = new \App\Models\TransJualModel();
$transactions = $transJualModel->where('id_pelanggan', $id)
    ->orderBy('id', 'DESC')
    ->limit(10)
    ->findAll();

$data['transactions'] = $transactions;
```

**Update View**: `app/Views/admin-lte-3/master/pelanggan/detail.php`

Add tabbed interface:
```html
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#info">Info</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#transactions">Transaksi</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#activity">Aktivitas</a>
    </li>
</ul>

<div class="tab-content">
    <div id="info" class="tab-pane active">
        <!-- Existing detail content -->
    </div>
    
    <div id="transactions" class="tab-pane">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $i => $trans): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= $trans->created_at ?></td>
                    <td><?= number_format($trans->total, 0, ',', '.') ?></td>
                    <td><?= $trans->status ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
```

---

## 2. Routes to Add

**File**: `app/Config/Routes.php`

Add to Supplier routes section (around line 480):
```php
$routes->get('supplier/export', 'Supplier::export');  // Already exists
$routes->post('supplier/bulk_delete', 'Supplier::bulk_delete');  // Already exists, verify working
```

Add to Pelanggan routes section (around line 540):
```php
$routes->get('pelanggan/export', 'Pelanggan::exportExcel');  // NEW - Add this
$routes->post('pelanggan/bulk_delete', 'Pelanggan::bulk_delete');  // Verify if exists
```

---

## 3. Database Migrations

Run these commands after creating migration files:

```bash
php spark migrate:add AddKategoriToSupplier
php spark migrate:add AddBlockedFieldsToPelanggan
php spark migrate
```

---

## 4. Files to Modify

### Supplier Module
1. ✅ `app/Controllers/Master/Supplier.php` - Already has import/export
2. ❌ `app/Views/admin-lte-3/master/supplier/index.php` - Fix AJAX bulk delete
3. ❌ `app/Views/admin-lte-3/master/supplier/create.php` - Add kategori field
4. ❌ `app/Views/admin-lte-3/master/supplier/edit.php` - Add kategori field
5. ❌ `app/Models/SupplierModel.php` - Add kategori to allowedFields
6. ❌ `app/Database/Migrations/*` - Create kategori migration

### Pelanggan Module
1. ❌ `app/Controllers/Master/Pelanggan.php` - Add exportExcel()
2. ❌ `app/Views/admin-lte-3/master/pelanggan/index.php` - Fix AJAX, add export button
3. ❌ `app/Views/admin-lte-3/master/pelanggan/detail.php` - Add transaction history tabs
4. ❌ `app/Models/PelangganModel.php` - Add is_blocked, limit_belanja to allowedFields
5. ❌ `app/Database/Migrations/*` - Create blocked fields migration

---

## 5. Testing Checklist

### Supplier Module
- [ ] CRUD operations work
- [ ] Import Excel works with kategori
- [ ] Export Excel includes kategori column
- [ ] Checklist bulk delete works (after fix)
- [ ] Soft delete works (status_hps = 1)
- [ ] Kategori dropdown shows in create/edit
- [ ] Detail view shows related items

### Pelanggan Module
- [ ] CRUD operations work
- [ ] Import Excel works
- [ ] Export Excel works (NEW)
- [ ] Checklist bulk delete works (after fix)
- [ ] Soft delete works
- [ ] Transaction history shows in detail view
- [ ] is_blocked field functional
- [ ] limit_belanja field functional
- [ ] Block/unblock member works

---

## 6. Estimated Implementation Time

- **Quick Fixes** (Bulk delete AJAX): 15 minutes
- **Pelanggan Export**: 30 minutes
- **Database Migrations**: 30 minutes
- **UI Updates** (forms, views): 1 hour
- **Testing**: 1 hour

**Total**: ~3.5 hours

---

## 7. Priority Order

### Priority 1 (Critical)
1. Fix bulk delete AJAX for both modules
2. Add Pelanggan export functionality
3. Run existing imports/exports to verify they work

### Priority 2 (Important)
1. Add kategori field to Supplier
2. Add is_blocked and limit_belanja to Pelanggan
3. Add transaction history to Pelanggan detail

### Priority 3 (Enhancement)
1. Add more robust error handling
2. Add DataTables integration (optional)
3. Add advanced filtering

---

## End of Report

**Next Steps**:
1. Review this report
2. Implement fixes in priority order
3. Test each feature after implementation
4. Update documentation

**Status**: Ready for implementation

