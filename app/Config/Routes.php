<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Application Routes
 *
 * Defines all application routes, including:
 * - Root (login redirect)
 * - Authentication (login, logout, forgot password)
 * - Dashboard
 *
 * All routes are grouped and use namespaces for clarity.
 *
 * @var RouteCollection $routes
 */

 
 /* API POS */

$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes) {
        // API Authentication routes
        $routes->group('anggota', function ($routes) {
            $routes->post('login', 'Anggota\Auth::login');
            $routes->get('search', 'Anggota\Auth::search');
        });

        // API Authentication routes for cashier
        $routes->group('pos', function ($routes) {
            $routes->post('login', 'Pos\Auth::login');

            // Outlet endpoints
            $routes->get('outlet', 'Pos\Store::getOutlets');
            $routes->get('outlet/detail/(:num)', 'Pos\Store::getOutlets/$1');
        });

        // Protected API routes (require JWT authentication)
        $routes->group('anggota', ['filter' => 'jwtauth'], function ($routes) {
            $routes->get('profile', 'Anggota\\Auth::profile');
            $routes->get('logout', 'Anggota\\Auth::logout');
            
            // PIN Management routes
            $routes->post('set-pin', 'Anggota\\Auth::setPin');
            $routes->post('validate-pin', 'Anggota\\Auth::validatePin');
            $routes->post('change-pin', 'Anggota\\Auth::changePin');
            $routes->get('pin-status', 'Anggota\\Auth::pinStatus');
            $routes->post('reset-pin', 'Anggota\\Auth::resetPin');
        });

        // POS API routes (protected by JWT except for /outlets)
        $routes->group('pos', ['filter' => 'jwtauth', 'namespace' => 'App\Controllers\Api\Pos'], function ($routes) {

        /* Produk */
        $routes->get('produk', 'Produk::getAll');
        $routes->get('produk/detail/(:num)', 'Produk::getById/$1');
        $routes->get('produk/variant/(:num)', 'Produk::getVariant/$1');
        $routes->get('produk/category/(:num)', 'Produk::getByCategory/$1');

        // Merge: Kategori endpoints under api/pos
        $routes->get('category', 'Kategori::index');
        $routes->get('category/(:num)', 'Kategori::detail/$1');

        // Transaction endpoints
        $routes->get('transaksi', 'Transaksi::getTransaction');
        $routes->get('transaksi/(:num)', 'Transaksi::getTransaction/$1');
        $routes->post('transaksi/store', 'Transaksi::store');
        $routes->get('transaksi/payments', 'Transaksi::getPaymentMethods');
        $routes->post('transaksi/validate/voucher', 'Transaksi::validateVoucher');
        $routes->post('transaksi/validate/customer', 'Transaksi::validateCustomer');
    });
});

// ── Root: direct to login page
$routes->get('/', 'Auth::login', [
    'namespace' => 'App\Controllers',
    'as'        => 'root',
]);

/**
 * Authentication Routes
 *
 * Handles all authentication processes:
 * - Login (regular and cashier)
 * - Logout
 * - Forgot Password
 */
$routes->group('auth', ['namespace' => 'App\Controllers'], static function ($routes) {
    // Index / auth landing page
    $routes->get('/', 'Auth::index', ['as' => 'auth.index']);

    // Login form
    $routes->get('login', 'Auth::login', ['as' => 'auth.login']);

    // Login cashier (new URL structure)
    $routes->get('login/cashier', 'Auth::login_kasir', ['as' => 'auth.login.cashier']);

    // Legacy alias (temporary support)
    $routes->get('login-kasir', 'Auth::login_kasir', ['as' => 'auth.login.cashier.legacy']);

    // Login processing
    $routes->post('cek_login', 'Auth::cek_login', ['as' => 'auth.login.attempt']);
    $routes->post('login/cashier', 'Auth::cek_login_kasir', ['as' => 'auth.login.cashier.attempt']);

    // Logout (POST preferred for security)
    $routes->post('logout', 'Auth::logout', ['as' => 'auth.logout']);
    $routes->post('logout/cashier', 'Auth::logout_kasir', ['as' => 'auth.logout.cashier']);

    // Legacy GET logout (temporary; can be removed later)
    $routes->get('logout', 'Auth::logout');
    $routes->get('logout-kasir', 'Auth::logout_kasir');

    // Forgot password (form and submit)
    $routes->get('forgot-password', 'Auth::forgot_password', ['as' => 'auth.forgot']);
    $routes->post('forgot-password', 'Auth::forgot_password', ['as' => 'auth.forgot.submit']);
});

/**
 * Dashboard Routes
 * Accessible only for authenticated users.
 */
$routes->get('dashboard', 'Dashboard::index', [
    'namespace' => 'App\Controllers',
    'filter'    => 'auth',
    'as'        => 'dashboard.index',
]);



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
    $routes->get('gudang/trash', 'Gudang::trash');
    $routes->get('gudang/restore/(:num)', 'Gudang::restore/$1');
    $routes->get('gudang/delete_permanent/(:num)', 'Gudang::delete_permanent/$1');
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

// Varian routes
$routes->group('master', ['namespace' => 'App\Controllers\Master', 'filter' => 'auth'], function ($routes) {
    $routes->get('varian', 'Varian::index');
    $routes->get('varian/create', 'Varian::create');
    $routes->post('varian/store', 'Varian::store');
    $routes->get('varian/edit/(:num)', 'Varian::edit/$1');
    $routes->post('varian/update/(:num)', 'Varian::update/$1');
    $routes->get('varian/delete/(:num)', 'Varian::delete/$1');
});

// Voucher routes
$routes->group('master', ['namespace' => 'App\Controllers\Master', 'filter' => 'auth'], function ($routes) {
    $routes->get('voucher', 'Voucher::index');
    $routes->get('voucher/create', 'Voucher::create');
    $routes->post('voucher/store', 'Voucher::store');
    $routes->get('voucher/edit/(:num)', 'Voucher::edit/$1');
    $routes->post('voucher/update/(:num)', 'Voucher::update/$1');
    $routes->get('voucher/delete/(:num)', 'Voucher::delete/$1');
    $routes->get('voucher/detail/(:num)', 'Voucher::detail/$1');
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
$routes->group('master', ['namespace' => 'App\Controllers\Master', 'filter' => 'auth'], function ($routes) {
    $routes->get('karyawan', 'Karyawan::index');
    $routes->get('karyawan/create', 'Karyawan::create');
    $routes->post('karyawan/store', 'Karyawan::store');
    $routes->get('karyawan/edit/(:num)', 'Karyawan::edit/$1');
    $routes->post('karyawan/update/(:num)', 'Karyawan::update/$1');
    $routes->get('karyawan/delete/(:num)', 'Karyawan::delete/$1');
    $routes->get('karyawan/detail/(:num)', 'Karyawan::detail/$1');
});

// Supplier Routes
$routes->group('master', ['namespace' => 'App\Controllers\Master', 'filter' => 'auth'], function ($routes) {
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
$routes->group('master', ['namespace' => 'App\Controllers\Master', 'filter' => 'auth'], function ($routes) {
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
$routes->group('master', ['namespace' => 'App\Controllers\Master', 'filter' => 'auth'], function ($routes) {
    $routes->get('platform', 'Platform::index');
    $routes->get('platform/create', 'Platform::create');
    $routes->post('platform/store', 'Platform::store');
    $routes->get('platform/edit/(:num)', 'Platform::edit/$1');
    $routes->post('platform/update/(:num)', 'Platform::update/$1');
    $routes->get('platform/delete/(:num)', 'Platform::delete/$1');
    $routes->get('platform/detail/(:num)', 'Platform::detail/$1');
});

// Outlet routes
$routes->group('master', ['namespace' => 'App\Controllers\Master', 'filter' => 'auth'], function ($routes) {
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
$routes->group('master', ['namespace' => 'App\Controllers\Master', 'filter' => 'auth'], function ($routes) {
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

// Add route for TransJual get_variants (for cashier)
$routes->get('transaksi/transjual/get_variants/(:num)', 'Transaksi\\TransJual::get_variants/$1');

// User Module Routes
$routes->group('users/modules', ['namespace' => 'App\Controllers\Pengaturan', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Modules::index');
});

$routes->group('gudang', ['namespace' => 'App\Controllers\Gudang', 'filter' => 'auth'], static function ($routes) {
    // Inventori / Stok
    $routes->get('stok', 'Inventori::index');
    $routes->get('stok/detail/(:num)', 'Inventori::detail/$1');
    $routes->post('stok/update/(:num)', 'Inventori::updateStock/$1');
    $routes->get('stok/export_excel', 'Inventori::export_to_excel');

    // Input Stok
    $routes->get('input_stok', 'InputStok::index');
    $routes->get('input_stok/create', 'InputStok::create');
    $routes->post('input_stok/store', 'InputStok::store');
    $routes->get('input_stok/detail/(:num)', 'InputStok::detail/$1');
    $routes->get('input_stok/edit/(:num)', 'InputStok::edit/$1');
    $routes->post('input_stok/update/(:num)', 'InputStok::update/$1');
    $routes->get('input_stok/delete/(:num)', 'InputStok::delete/$1');

    // Transfer / Mutasi
    $routes->get('transfer', 'Transfer::index');
    $routes->get('transfer/create', 'Transfer::create');
    $routes->post('transfer/store', 'Transfer::store');
    $routes->get('transfer/detail/(:num)', 'Transfer::detail/$1');
    $routes->get('transfer/edit/(:num)', 'Transfer::edit/$1');
    $routes->post('transfer/update/(:num)', 'Transfer::update/$1');
    $routes->get('transfer/delete/(:num)', 'Transfer::delete/$1');
    $routes->get('transfer/input/(:num)', 'Transfer::inputItem/$1');
    $routes->post('transfer/process/(:num)', 'Transfer::process/$1');

    // Opname / Stock Opname
    $routes->get('opname', 'Opname::index');
    $routes->get('opname/create', 'Opname::create');
    $routes->post('opname/store', 'Opname::store');
    $routes->get('opname/detail/(:num)', 'Opname::detail/$1');
    $routes->get('opname/edit/(:num)', 'Opname::edit/$1');
    $routes->post('opname/update/(:num)', 'Opname::update/$1');
    $routes->get('opname/input/(:num)', 'Opname::input/$1');
    $routes->match(['get', 'post'], 'opname/process/(:num)', 'Opname::proses/$1');
    $routes->get('opname/delete/(:num)', 'Opname::delete/$1');
    $routes->get('opname/get-stock-outlet', 'Opname::getStockOutletAjax');
    $routes->match(['get', 'post'], 'opname/add-item', 'Opname::addItem');
    $routes->post('opname/delete-item', 'Opname::deleteItem');
    $routes->get('opname/get-table-data/(:num)', 'Opname::getTableData/$1');

    // Penerimaan / Receiving
    $routes->get('penerimaan', 'TransBeli::index');
    $routes->get('terima/(:num)', 'TransBeli::terima/$1');
    $routes->post('terima/save/(:num)', 'TransBeli::save/$1');
});

/*
 * TRANSAKSI ROUTES
 */
// Purchase Order Routes
$routes->group('transaksi', ['namespace' => 'App\Controllers\Transaksi', 'filter' => 'auth'], function ($routes) {
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

    $routes->get('beli', 'TransBeli::index');
    $routes->get('beli/create', 'TransBeli::create');
    $routes->post('beli/store', 'TransBeli::store');
    $routes->get('beli/detail/(:num)', 'TransBeli::detail/$1');
    $routes->get('beli/edit/(:num)', 'TransBeli::edit/$1');
    $routes->post('beli/update/(:num)', 'TransBeli::update/$1');
    $routes->post('beli/cart_add/(:num)', 'TransBeli::cart_add/$1');
    $routes->get('beli/cart_add/(:num)', 'TransBeli::cart_add_redirect/$1');
    $routes->post('beli/cart_update/(:num)', 'TransBeli::cart_update/$1');
    $routes->post('beli/cart_delete/(:num)', 'TransBeli::cart_delete/$1');
    $routes->get('beli/get-items/(:num)', 'TransBeli::getItems/$1');
    $routes->get('beli/proses/(:num)', 'TransBeli::proses/$1');

    // Sales Transaction Routes
    $routes->get('jual', 'TransJual::index');
    $routes->get('jual/cashier-data', 'TransJual::data_penjualan_kasir');
    $routes->get('jual/cashier', 'TransJual::cashier');
    $routes->get('jual/create', 'TransJual::create');
    $routes->post('jual/store', 'TransJual::store');
    $routes->get('jual/get-details/(:num)', 'TransJual::getTransactionDetails/$1');
    $routes->match(['get', 'post'], 'jual/search-items', 'TransJual::searchItems');
    $routes->get('jual/get-customer/(:num)', 'TransJual::getCustomerInfo/$1');
    $routes->get('jual/generate-nota', 'TransJual::generateNotaNumber');
    $routes->post('jual/validate-voucher', 'TransJual::validateVoucher');
    $routes->post('jual/process-transaction', 'TransJual::processTransaction');

    // Add route for get_variants
    $routes->get('jual/get_variants/(:num)', 'TransJual::get_variants/$1');
    
    // Draft management routes
    $routes->get('jual/get-drafts', 'TransJual::getDrafts');
    $routes->get('jual/get-draft/(:num)', 'TransJual::getDraft/$1');
    $routes->post('jual/delete-draft/(:num)', 'TransJual::deleteDraft/$1');
    
    // Print transaction data route
            $routes->get('jual/get-transaction-for-print/(:num)', 'TransJual::getTransactionForPrint/$1');
        $routes->get('jual/print-receipt-view', 'TransJual::printReceiptView');
    
    // Session refresh endpoint
    $routes->get('jual/refresh-session', 'TransJual::refreshSession');
    
    // QR Scanner for Piutang transactions (no CSRF to allow mobile scanning)
    $routes->get('jual/qr-scanner/(:num)', 'TransJual::qrScanner/$1');
    // Moved outside group for CSRF bypass

    // Purchase Return Routes
    $routes->get('retur/beli', 'ReturBeli::index');
    $routes->get('retur/beli/create', 'ReturBeli::create');
    $routes->post('retur/beli/store', 'ReturBeli::store');
    $routes->get('retur/beli/edit/(:num)', 'ReturBeli::edit/$1');
    $routes->post('retur/beli/update/(:num)', 'ReturBeli::update/$1');
    $routes->post('retur/beli/delete/(:num)', 'ReturBeli::delete/$1');
    $routes->get('retur/beli/(:num)', 'ReturBeli::show/$1');

    // Sales Return Routes
    $routes->get('retur/jual', 'ReturJual::index');
    $routes->get('retur/jual/refund', 'ReturJual::refund');
    $routes->get('retur/jual/exchange', 'ReturJual::exchange');
    $routes->post('retur/jual/store', 'ReturJual::store');
    $routes->get('retur/jual/edit/(:num)', 'ReturJual::edit/$1');
    $routes->post('retur/jual/update/(:num)', 'ReturJual::update/$1');
    $routes->post('retur/jual/delete/(:num)', 'ReturJual::delete/$1');
    $routes->get('retur/jual/(:num)', 'ReturJual::show/$1');
    // AJAX routes for sales return
    $routes->get('retur/jual/sales-items/(:num)', 'ReturJual::getSalesItems/$1');
    $routes->get('retur/jual/search-items', 'ReturJual::searchItems');
    $routes->post('retur/jual/search-items', 'ReturJual::searchItems');
    $routes->get('retur/jual/test', 'ReturJual::testEndpoint');
});

// Public API routes
$routes->group('publik', function ($routes) {
    $routes->get('items', 'Publik::getItems');
    $routes->get('items_stock', 'Publik::getItemsStock');
    $routes->get('satuan', 'Publik::getSatuan');
});

/*
 * LAPORAN ROUTES
 */
$routes->group('laporan', ['namespace' => 'App\Controllers\Laporan', 'filter' => 'auth'], function ($routes) {
    // Sales Report Routes
    $routes->get('sale', 'SaleReport::index');
    $routes->get('sale/detail/(:num)', 'SaleReport::detail/$1');
    $routes->get('sale/export_excel', 'SaleReport::export_excel');

    // Purchase Report Routes
    $routes->get('purchase', 'PurchaseReport::index');
    $routes->get('purchase/detail/(:num)', 'PurchaseReport::detail/$1');
    $routes->get('purchase/export_excel', 'PurchaseReport::export_excel');

    // Stock Report Routes
    $routes->get('stock', 'StockReport::index');
    $routes->get('stock/detail/(:num)', 'StockReport::detail/$1');
    $routes->get('stock/export_excel', 'StockReport::export_excel');
    $routes->get('stock/test_data', 'StockReport::test_data');

    // Outlet Report Routes
    $routes->get('outlet', 'OutletReport::index');
    $routes->get('outlet/detail/(:num)', 'OutletReport::detail/$1');
    $routes->get('outlet/export_excel', 'OutletReport::export_excel');
});

$routes->group('pengaturan', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('app', 'Pengaturan::index');
    $routes->post('app/update', 'Pengaturan::update');
    
    // API Tokens route
    $routes->get('api-tokens', 'Pengaturan::apiTokens');
    
    // Printer management routes
    $routes->group('printer', ['namespace' => 'App\Controllers\Pengaturan'], function ($routes) {
        $routes->get('/', 'Printer::index');
        $routes->get('create', 'Printer::create');
        $routes->post('store', 'Printer::store');
        $routes->get('edit/(:num)', 'Printer::edit/$1');
        $routes->post('update/(:num)', 'Printer::update/$1');
        $routes->get('delete/(:num)', 'Printer::delete/$1');
        $routes->get('set-default/(:num)', 'Printer::setDefault/$1');
        $routes->get('test/(:num)', 'Printer::testConnection/$1');
    });
    
    // PU Menu management routes
    $routes->group('pu-menu', ['namespace' => 'App\Controllers\Pengaturan'], function ($routes) {
        $routes->get('/', 'PuMenu::index');
        $routes->get('create', 'PuMenu::create');
        $routes->post('store', 'PuMenu::store');
        $routes->get('edit/(:num)', 'PuMenu::edit/$1');
        $routes->post('update/(:num)', 'PuMenu::update/$1');
        $routes->get('delete/(:num)', 'PuMenu::delete/$1');
    });
});

// QR Scanner Processing Route (no filters to bypass CSRF and auth)
$routes->post('api/qr-scan', 'Transaksi\\TransJual::processQrScan');

// untuk test
$routes->get('home/test', 'Home::test');
$routes->get('home/test2', 'Home::test2');






