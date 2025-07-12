<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', to: 'Auth::login');

// Debug route (remove in production)
$routes->get('debug/jwt', 'Debug::jwt');

// Auth routes
$routes->group('auth', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('/', 'Auth::index');
    $routes->get('login', 'Auth::login');
    $routes->post('cek_login', 'Auth::cek_login');
    $routes->get('logout', 'Auth::logout');
    $routes->get('forgot-password', 'Auth::forgot_password');
    $routes->post('forgot-password', 'Auth::forgot_password');
});

$routes->get('/dashboard', 'Dashboard::index', ['namespace' => 'App\Controllers', 'filter' => 'auth']);


/*****
 * MASTER ROUTES
 * These routes handle all master data operations including:
 * - Gudang (Warehouse management)
 * - Satuan (Units of measurement)
 * - Kategori (Categories)
 * - Merk (Brands)
 * All routes are protected by auth filter
 ****/

// Gudang routes
$routes->group('master', ['namespace' => 'App\Controllers\Master', 'filter' => 'auth'], function ($routes) {
    $routes->get('gudang', 'Gudang::index');
    $routes->get('gudang/create', 'Gudang::create');
    $routes->post('gudang/store', 'Gudang::store');
    $routes->get('gudang/edit/(:num)', 'Gudang::edit/$1');
    $routes->post('gudang/update/(:num)', 'Gudang::update/$1');
    $routes->get('gudang/delete/(:num)', 'Gudang::delete/$1');
});

// Satuan routes
$routes->group('master', ['namespace' => 'App\Controllers\Master', 'filter' => 'auth'], function ($routes) {
    $routes->get('satuan', 'Satuan::index');
    $routes->get('satuan/create', 'Satuan::create');
    $routes->post('satuan/store', 'Satuan::store');
    $routes->get('satuan/edit/(:num)', 'Satuan::edit/$1');
    $routes->post('satuan/update/(:num)', 'Satuan::update/$1');
    $routes->get('satuan/delete/(:num)', 'Satuan::delete/$1');
});

// Kategori routes
$routes->group('master', ['namespace' => 'App\Controllers\Master', 'filter' => 'auth'], function ($routes) {
    $routes->get('kategori', 'Kategori::index');
    $routes->get('kategori/create', 'Kategori::create');
    $routes->post('kategori/store', 'Kategori::store');
    $routes->get('kategori/edit/(:num)', 'Kategori::edit/$1');
    $routes->post('kategori/update/(:num)', 'Kategori::update/$1');
    $routes->get('kategori/delete/(:num)', 'Kategori::delete/$1');
});


// Merk routes
$routes->group('master', ['namespace' => 'App\Controllers\Master', 'filter' => 'auth'], function ($routes) {
    $routes->get('merk', 'Merk::index');
    $routes->get('merk/create', 'Merk::create');
    $routes->post('merk/store', 'Merk::store');
    $routes->get('merk/edit/(:num)', 'Merk::edit/$1');
    $routes->post('merk/update/(:num)', 'Merk::update/$1');
    $routes->get('merk/delete/(:num)', 'Merk::delete/$1');
});


// Karyawan Routes
$routes->group('master', ['namespace' => 'App\Controllers\Master', 'filter' => 'auth'], function($routes) {
    $routes->get('karyawan', 'Karyawan::index');
    $routes->get('karyawan/create', 'Karyawan::create');
    $routes->post('karyawan/store', 'Karyawan::store');
    $routes->get('karyawan/edit/(:num)', 'Karyawan::edit/$1');
    $routes->post('karyawan/update/(:num)', 'Karyawan::update/$1');
    $routes->get('karyawan/delete/(:num)', 'Karyawan::delete/$1');
    $routes->get('karyawan/detail/(:num)', 'Karyawan::detail/$1');
});

// Supplier Routes
$routes->group('master', ['namespace' => 'App\Controllers\Master', 'filter' => 'auth'], function($routes) {
    $routes->get('supplier', 'Supplier::index');
    $routes->get('supplier/create', 'Supplier::create');
    $routes->post('supplier/store', 'Supplier::store');
    $routes->get('supplier/edit/(:num)', 'Supplier::edit/$1');
    $routes->post('supplier/update/(:num)', 'Supplier::update/$1');
    $routes->get('supplier/delete/(:num)', 'Supplier::delete/$1');
    $routes->get('supplier/detail/(:num)', 'Supplier::detail/$1');
    $routes->get('supplier/trash', 'Supplier::trash');
});

// Pelanggan Routes
$routes->group('master', ['namespace' => 'App\Controllers\Master', 'filter' => 'auth'], function($routes) {
    $routes->get('customer', 'Pelanggan::index');
    $routes->get('customer/create', 'Pelanggan::create');
    $routes->post('customer/store', 'Pelanggan::store');
    $routes->get('customer/edit/(:num)', 'Pelanggan::edit/$1');
    $routes->post('customer/update/(:num)', 'Pelanggan::update/$1');
    $routes->get('customer/delete/(:num)', 'Pelanggan::delete/$1');
    $routes->get('customer/detail/(:num)', 'Pelanggan::detail/$1');
    $routes->get('customer/trash', 'Pelanggan::trash');
    $routes->get('customer/restore/(:num)', 'Pelanggan::restore/$1');
    $routes->get('customer/delete_permanent/(:num)', 'Pelanggan::delete_permanent/$1');
});

// Platform Routes
$routes->group('master', ['namespace' => 'App\Controllers\Master', 'filter' => 'auth'], function($routes) {
    $routes->get('platform', 'Platform::index');
    $routes->get('platform/create', 'Platform::create');
    $routes->post('platform/store', 'Platform::store');
    $routes->get('platform/edit/(:num)', 'Platform::edit/$1');
    $routes->post('platform/update/(:num)', 'Platform::update/$1');
    $routes->get('platform/delete/(:num)', 'Platform::delete/$1');
    $routes->get('platform/detail/(:num)', 'Platform::detail/$1');
});

// Outlet routes
$routes->group('master', ['namespace' => 'App\Controllers\Master', 'filter' => 'auth'], function($routes) {
    $routes->get('outlet', 'Outlet::index');
    $routes->get('outlet/create', 'Outlet::create');
    $routes->post('outlet/store', 'Outlet::store');
    $routes->get('outlet/edit/(:num)', 'Outlet::edit/$1');
    $routes->post('outlet/update/(:num)', 'Outlet::update/$1');
    $routes->get('outlet/delete/(:num)', 'Outlet::delete/$1');
    $routes->get('outlet/trash', 'Outlet::trash');
    $routes->get('outlet/restore/(:num)', 'Outlet::restore/$1');
    $routes->get('outlet/delete_permanent/(:num)', 'Outlet::delete_permanent/$1');
});

// Items routes
$routes->group('master', ['namespace' => 'App\Controllers\Master', 'filter' => 'auth'], function($routes) {
    $routes->get('item', 'Item::index');
    $routes->get('item/create', 'Item::create');
    $routes->post('item/store', 'Item::store');
    $routes->get('item/edit/(:num)', 'Item::edit/$1');
    $routes->get('item/upload/(:num)', 'Item::edit_upload/$1');
    $routes->post('item/update/(:num)', 'Item::update/$1');
    $routes->get('item/delete/(:num)', 'Item::delete/$1');
    $routes->get('item/trash', 'Item::trash');
    $routes->get('item/restore/(:num)', 'Item::restore/$1');
    $routes->get('item/delete_permanent/(:num)', 'Item::delete_permanent/$1');
    $routes->post('item/upload_image', 'Item::upload_image');
    $routes->post('item/delete_image', 'Item::delete_image');
    $routes->post('item/store_price/(:num)', 'Item::store_price/$1');
    $routes->post('item/delete_price/(:num)', 'Item::delete_price/$1');
    $routes->post('item/bulk_delete', 'Item::bulk_delete');
    $routes->get('item/export_excel', 'Item::export_to_excel');
    $routes->post('item/store_variant/(:num)', 'Item::store_variant/$1');
    $routes->get('item/get_variants/(:num)', 'Item::get_variants/$1');
    $routes->post('item/delete_variant/(:num)', 'Item::delete_variant/$1');
});

// User Module Routes
$routes->group('users/modules', ['namespace' => 'App\Controllers\Pengaturan', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Modules::index');
});

$routes->group('gudang', ['namespace' => 'App\Controllers\Gudang', 'filter' => 'auth'], static function ($routes) {
    // Inventori / Stok
    $routes->get('stok', 'Inventori::index');
    $routes->get('stok/detail/(:num)', 'Inventori::detail/$1');
    $routes->get('stok/export_excel', 'Inventori::export_to_excel');
    
    // Transfer / Mutasi
    $routes->get('transfer', 'Transfer::index');
    $routes->get('transfer/create', 'Transfer::create');
    $routes->post('transfer/store', 'Transfer::store');
    $routes->get('transfer/input/(:num)', 'Transfer::inputItem/$1');
    
    // Opname / Stock Opname
    $routes->get('opname', 'Opname::index');
    $routes->get('opname/create', 'Opname::create');
    $routes->post('opname/store', 'Opname::store');
    
    // Penerimaan / Receiving
    $routes->get('penerimaan', 'TransBeli::index');
    $routes->get('terima/(:num)', 'TransBeli::terima/$1');
    $routes->post('terima/save/(:num)', 'TransBeli::save/$1');
});

$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes) {
    // API Authentication routes
    $routes->group('anggota', function ($routes) {
        $routes->post('login', 'Anggota\Auth::login');
    });
    
    // Protected API routes (require JWT authentication)
    $routes->group('anggota', ['filter' => 'jwtauth'], function ($routes) {
        $routes->get('profile', 'Anggota\\Auth::profile');
        $routes->get('logout', 'Anggota\\Auth::logout');
    });

    $routes->group('pos', ['filter' => 'jwtauth', 'namespace' => 'App\Controllers\Api\Pos'], function ($routes) {
        $routes->get('produk', 'Produk::index');
        $routes->get('produk/detail/(:num)', 'Produk::detail/$1');
        $routes->get('produk/category', 'Produk::getCategory');
        
        // Merge: Kategori endpoints under api/pos
        $routes->get('category', 'Kategori::index');
        $routes->get('category/(:num)', 'Kategori::detail/$1');
        
        // Outlet endpoints
        $routes->get('outlet', 'Store::index');
        $routes->get('outlet/detail/(:num)', 'Store::detail/$1');
    });
});

/*
 * TRANSAKSI ROUTES
 */
// Purchase Order Routes
$routes->group('transaksi', ['namespace' => 'App\Controllers\Transaksi', 'filter' => 'auth'], function($routes) {
    $routes->get('po', 'TransBeliPO::index');
    $routes->get('po/create', 'TransBeliPO::create');
    $routes->post('po/store', 'TransBeliPO::store');
    $routes->get('po/detail/(:num)', 'TransBeliPO::detail/$1');
    $routes->get('po/edit/(:num)', 'TransBeliPO::edit/$1');
    $routes->post('po/update/(:num)', 'TransBeliPO::update/$1');
    $routes->get('po/print/(:num)', 'TransBeliPO::print/$1');
    $routes->post('po/cart_add/(:num)', 'TransBeliPO::cart_add/$1');
    $routes->get('po/cart_delete/(:num)', 'TransBeliPO::cart_delete/$1');
    $routes->post('po/proses/(:num)', 'TransBeliPO::proses/$1');
    $routes->get('po/delete/(:num)', 'TransBeliPO::delete/$1');
    $routes->get('po/buat-faktur/(:num)', 'TransBeliPO::buatFaktur/$1');
    $routes->get('po/approve/(:num)/(:any)', 'TransBeliPO::approve/$1/$2');
    $routes->get('po/stats', 'TransBeliPO::getStats');
    $routes->post('po/bulk-delete', 'TransBeliPO::bulkDelete');
});

// Purchase Transaction Routes
$routes->group('transaksi', ['namespace' => 'App\Controllers\Transaksi', 'filter' => 'auth'], function($routes) {
    $routes->get('beli', 'TransBeli::index');
    $routes->get('beli/create', 'TransBeli::create');
    $routes->post('beli/store', 'TransBeli::store');
    $routes->get('beli/detail/(:num)', 'TransBeli::detail/$1');
    $routes->get('beli/edit/(:num)', 'TransBeli::edit/$1');
    $routes->post('beli/update/(:num)', 'TransBeli::update/$1');
    $routes->get('beli/get-items/(:num)', 'TransBeli::getItems/$1');
    $routes->get('beli/proses/(:num)', 'TransBeli::proses/$1');
});

// Public API routes
$routes->group('publik', function ($routes) {
    $routes->get('items', 'Publik::getItems');
    $routes->get('items_stock', 'Publik::getItemsStock');
});

// untuk test
$routes->get('home/test', 'Home::test');
$routes->get('home/test2', 'Home::test2');




