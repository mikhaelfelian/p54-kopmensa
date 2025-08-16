# V_ITEM_STOK View and Model Documentation

## Overview

The `v_item_stok` view provides a comprehensive view of item stock information by joining multiple tables in the Kopmensa system. This view combines data from:

- `tbl_m_item_stok` - Stock quantities
- `tbl_m_item` - Item details
- `tbl_m_gudang` - Warehouse information
- `tbl_m_outlet` - Outlet information
- `tbl_m_kategori` - Category information
- `tbl_m_merk` - Brand information
- `tbl_m_satuan` - Unit information
- `tbl_m_supplier` - Supplier information
- `users` - User information

## Installation

### 1. Create the Database View

Run the SQL script in `database/create_v_item_stok_view.sql`:

```sql
-- Execute this in your MySQL database
source database/create_v_item_stok_view.sql;
```

### 2. The Model is Ready

The `VItemStokModel` is already created and ready to use.

## Usage Examples

### Basic Usage

```php
use App\Models\VItemStokModel;

$vItemStokModel = new VItemStokModel();

// Get all stock overview
$stockOverview = $vItemStokModel->getStockOverview();

// Get stock by specific warehouse
$warehouseStock = $vItemStokModel->getStockOverview(gudangId: 1);

// Search items
$searchResults = $vItemStokModel->searchItems(['keyword' => 'laptop']);
```

### Advanced Queries

#### Get Stock Summary by Warehouse

```php
$warehouseSummary = $vItemStokModel->getStockSummaryByWarehouse();
// Returns: warehouse name, total items, total stock, items in/out of stock
```

#### Get Low Stock Items

```php
$lowStockItems = $vItemStokModel->getLowStockItems(gudangId: 1, threshold: 5);
// Returns items with stock <= 5
```

#### Get Stock Value Summary

```php
$valueSummary = $vItemStokModel->getStockValueSummary(gudangId: 1);
// Returns: total cost value, total sales value, average prices
```

#### Search with Multiple Criteria

```php
$criteria = [
    'keyword' => 'laptop',
    'gudang_id' => 1,
    'kategori_id' => 'electronics',
    'min_stock' => 10,
    'sort_by' => 'item_nama',
    'sort_order' => 'ASC'
];

$results = $vItemStokModel->searchItems($criteria, perPage: 20, page: 1);
```

## Available Methods

### Core Methods

- `getStockOverview()` - Get comprehensive stock information
- `getStockSummaryByWarehouse()` - Stock summary grouped by warehouse
- `getStockSummaryByCategory()` - Stock summary grouped by category
- `getLowStockItems()` - Items with low stock
- `getOutOfStockItems()` - Items with zero stock
- `getStockMovementSummary()` - Stock movement over time
- `getItemStockHistory()` - Stock history for specific item
- `getStockValueSummary()` - Financial summary of stock
- `searchItems()` - Advanced search with multiple criteria

### Parameters

Most methods accept these common parameters:

- `$gudangId` - Filter by warehouse ID
- `$outletId` - Filter by outlet ID
- `$keyword` - Search keyword
- `$perPage` - Items per page for pagination
- `$page` - Current page number

## View Structure

The view includes these fields:

### Stock Information
- `id`, `id_item`, `id_gudang`, `id_outlet`, `id_user`
- `jml` (quantity), `status`, `created_at`, `updated_at`

### Item Information
- `item_nama`, `item_kode`, `item_barcode`
- `item_harga_beli`, `item_harga_jual`
- `item_min_stok`, `item_max_stok`, `item_status`

### Location Information
- `gudang_nama`, `gudang_alamat`, `gudang_status`
- `outlet_nama`, `outlet_alamat`, `outlet_status`

### Classification Information
- `kategori_nama`, `merk_nama`, `satuan_nama`, `supplier_nama`

### User Information
- `user_nama`, `user_email`

## Performance Considerations

### Indexes

The view performs best when these indexes exist on the underlying tables:

```sql
-- These should already exist in your database
CREATE INDEX idx_item_stok_item ON tbl_m_item_stok(id_item);
CREATE INDEX idx_item_stok_gudang ON tbl_m_item_stok(id_gudang);
CREATE INDEX idx_item_stok_outlet ON tbl_m_item_stok(id_outlet);
CREATE INDEX idx_item_stok_status ON tbl_m_item_stok(status);
CREATE INDEX idx_item_stok_created ON tbl_m_item_stok(created_at);
```

### Filtering

Always use filters when possible to limit the data returned:

```php
// Good - filtered by warehouse
$stock = $vItemStokModel->getStockOverview(gudangId: 1);

// Avoid - returns all data
$stock = $vItemStokModel->getStockOverview();
```

## Integration Examples

### In Controllers

```php
class StockReport extends BaseController
{
    protected $vItemStokModel;

    public function __construct()
    {
        $this->vItemStokModel = new VItemStokModel();
    }

    public function index()
    {
        $gudangId = $this->request->getGet('gudang_id');
        $keyword = $this->request->getGet('keyword');
        
        $data = [
            'stock' => $this->vItemStokModel->getStockOverview(
                gudangId: $gudangId,
                keyword: $keyword,
                perPage: 20
            ),
            'summary' => $this->vItemStokModel->getStockSummaryByWarehouse($gudangId),
            'lowStock' => $this->vItemStokModel->getLowStockItems($gudangId, 10)
        ];

        return view('stock_report/index', $data);
    }
}
```

### In Views

```php
<!-- Display stock overview -->
<?php foreach ($stock as $item): ?>
    <tr>
        <td><?= esc($item->item_nama) ?></td>
        <td><?= esc($item->kategori_nama) ?></td>
        <td><?= esc($item->gudang_nama) ?></td>
        <td><?= number_format($item->jml, 2) ?></td>
        <td><?= number_format($item->item_harga_jual, 0, ',', '.') ?></td>
    </tr>
<?php endforeach; ?>

<!-- Pagination -->
<?= $pager->links('stock') ?>
```

## Troubleshooting

### Common Issues

1. **View not found**: Ensure the SQL script has been executed
2. **Performance issues**: Check if indexes exist on underlying tables
3. **Empty results**: Verify the WHERE conditions in the view match your data

### Debug Queries

To debug, you can check the actual SQL being generated:

```php
$builder = $vItemStokModel->getStockOverview(gudangId: 1);
echo $builder->getLastQuery(); // Shows the actual SQL
```

## Future Enhancements

Consider adding these features:

1. **Caching**: Cache frequently accessed data
2. **Real-time updates**: Use database triggers for live stock updates
3. **Export functionality**: Add methods for Excel/PDF export
4. **Stock alerts**: Integrate with notification system for low stock

## Support

For issues or questions about the `v_item_stok` view or `VItemStokModel`, refer to:

- Database schema documentation
- CodeIgniter 4 documentation
- Existing stock-related controllers and models
