# Item Trash Function Fix Summary

**Date**: January 2025  
**Status**: ✅ FIXED  
**Issue**: Trash view showing empty results after archiving items

---

## Problem

The `trash()` function in `app/Controllers/Master/Item.php` was not properly displaying archived items because:

1. **Soft Delete Filter**: `ItemModel` has `useSoftDeletes = true`, which automatically adds `WHERE deleted_at IS NULL` to all queries
2. **Incomplete Filter**: The function only filtered for `status_hps = '1'` but didn't account for soft-deleted items with `deleted_at IS NOT NULL`

---

## Solution Applied

### Updated `trash()` Method (Lines 584-631)

```php
public function trash()
{
    $currentPage = $this->request->getVar('page_items') ?? 1;
    $perPage = 10;
    $keyword = $this->request->getVar('keyword');

    // Use withDeleted() to include soft-deleted items
    // This overrides the default "WHERE deleted_at IS NULL" filter
    $this->itemModel->withDeleted();

    // Show items where status_hps = '1' OR deleted_at IS NOT NULL
    $this->itemModel->groupStart()
        ->where('status_hps', '1')
        ->orWhere('deleted_at IS NOT NULL')
        ->groupEnd();

    if ($keyword) {
        $this->itemModel->groupStart()
            ->like('item', $keyword)
            ->orLike('kode', $keyword)
            ->orLike('barcode', $keyword)
            ->orLike('deskripsi', $keyword)
            ->groupEnd();
    }

    // Order by deleted_at descending to show recently archived items first
    $this->itemModel->orderBy('deleted_at', 'DESC');

    $data = [
        'title'         => 'Data Item Terhapus',
        'Pengaturan'    => $this->pengaturan,
        'user'          => $this->ionAuth->user()->row(),
        'items'         => $this->itemModel->paginate($perPage, 'items'),
        'pager'         => $this->itemModel->pager,
        'currentPage'   => $currentPage,
        'perPage'       => $perPage,
        'keyword'       => $keyword,
        'breadcrumbs'   => '...',
        'theme'         => $this->theme,
    ];

    return view($this->theme->getThemePath() . '/master/item/trash', $data);
}
```

---

## Key Changes

### 1. Added `withDeleted()` Call (Line 592)
- Overrides the default `WHERE deleted_at IS NULL` filter
- Allows querying soft-deleted items

### 2. Updated Filter Logic (Lines 595-598)
- Shows items where `status_hps = '1'` OR `deleted_at IS NOT NULL`
- Ensures all archived items are displayed

### 3. Added Order By (Line 610)
- Orders by `deleted_at DESC` to show recently archived items first
- Improves user experience

---

## How It Works Now

### Archive Flow
1. User bulk deletes items via `bulk_delete()`
2. Items are archived: `status_hps='1'`, `deleted_at=now()`
3. Items disappear from main list (filtered by `status_hps='0'`)

### Trash View Display
1. User navigates to `/master/item/trash`
2. `trash()` calls `withDeleted()` to include soft-deleted items
3. Filters show: `status_hps='1' OR deleted_at IS NOT NULL`
4. Items appear in trash view ordered by deletion date

### Restore Flow
1. User selects archived items in trash view
2. Clicks "Bulk Restore" button
3. `bulk_restore()` resets: `status_hps='0'`, `deleted_at=null`
4. Items return to main list

---

## Technical Details

### CodeIgniter Soft Deletes
- When `useSoftDeletes = true`, all queries automatically add `WHERE deleted_at IS NULL`
- `withDeleted()` method overrides this behavior
- `onlyDeleted()` can be used to show ONLY deleted items

### Query Logic
```sql
SELECT * FROM tbl_m_item 
WHERE (status_hps = '1' OR deleted_at IS NOT NULL)
  AND (item LIKE '%keyword%' OR kode LIKE '%keyword%' OR ...)
ORDER BY deleted_at DESC
```

---

## Testing Checklist

- [x] Linter errors fixed
- [x] Archive items appear in trash view
- [x] Search works in trash view
- [x] Pagination works correctly
- [x] Restore functionality works
- [x] Recently archived items appear first

---

## Files Modified

1. ✅ `app/Controllers/Master/Item.php` - Fixed trash() function

---

## Status: ✅ COMPLETE

The trash view now properly displays all archived items where either `status_hps='1'` OR `deleted_at IS NOT NULL`.

