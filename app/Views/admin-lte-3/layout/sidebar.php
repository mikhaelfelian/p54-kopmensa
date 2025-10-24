<aside class="main-sidebar sidebar-light-primary elevation-0">
    <!-- Brand Logo -->
    <a href="<?= base_url() ?>" class="brand-link">
        <img src="<?= $Pengaturan->logo ? base_url($Pengaturan->logo) : base_url('public/assets/theme/admin-lte-3/dist/img/AdminLTELogo.png') ?>"
            alt="AdminLTE Logo" class="brand-image img-circle elevation-0" style="opacity: .8">
        <span class="brand-text font-weight-light"><?= $Pengaturan ? $Pengaturan->judul_app : env('app.name') ?></span>
    </a>

    <!-- Sidebar -->
    <?php if (akses_kasir() == TRUE): ?>
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="<?php echo base_url((!empty($Pengaturan->logo) ? $Pengaturan->logo_header : 'public/assets/theme/admin-lte-3/dist/img/AdminLTELogo.png')); ?>"
                            class="brand-image img-rounded-0 elevation-0"
                            style="width: 209px; height: 85px; background-color: transparent;" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block"></a>
                    </div>
                </div>
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="<?= base_url('dashboard') ?>"
                            class="nav-link <?= isMenuActive('dashboard') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <!-- Transaksi -->
                    <li class="nav-header">TRANSAKSI</li>
                    <?php
                    // Integrate isMenuActive with all penjualan menu routes
                    $penjualanMenus = [
                        'transaksi/jual',
                        'transaksi/jual/create',
                        'transaksi/jual/cashier',
                        'transaksi/jual/detail',
                        'transaksi/jual/edit',
                        // Add all retur jual routes for active state
                        'transaksi/retur/jual',
                        'transaksi/retur/jual/refund',
                        'transaksi/retur/jual/exchange'
                    ];
                    $isPenjualanActive = isMenuActive($penjualanMenus);

                    // For Retur Penjualan submenu, check if any of the retur/jual routes are active
                    $returJualMenus = [
                        'transaksi/retur/jual',
                        'transaksi/retur/jual/refund',
                        'transaksi/retur/jual/exchange'
                    ];
                    $isReturJualActive = isMenuActive($returJualMenus);

                    // Check if shift management is active
                    $isShiftActive = isMenuActive(['transaksi/shift', 'transaksi/petty', 'transaksi/petty-category']);
                    ?>
                    <li class="nav-item has-treeview <?= $isPenjualanActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isPenjualanActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-cash-register"></i>
                            <p>
                                Penjualan
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" <?= $isPenjualanActive ? 'style="display: block;"' : 'style="display: none;"' ?>>
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/jual/cashier') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/jual/cashier') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-cash-register nav-icon"></i>
                                    <p>Kasir</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/jual/create') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/jual/create') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-plus nav-icon"></i>
                                    <p>Input Penjualan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/jual') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/jual') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-list nav-icon"></i>
                                    <p>Data Penjualan Kasir</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/jual') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/jual') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-list nav-icon"></i>
                                    <p>Data Penjualan</p>
                                </a>
                            </li>
                            <li class="nav-item <?= $isReturJualActive ? 'menu-open' : '' ?>">
                                <a href="#" class="nav-link <?= $isReturJualActive ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-undo nav-icon"></i>
                                    <p>
                                        Retur Penjualan
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview" <?= $isReturJualActive ? 'style="display: block;"' : 'style="display: none;"' ?>>
                                    <li class="nav-item">
                                        <a href="<?= base_url('transaksi/retur/jual') ?>"
                                            class="nav-link <?= isMenuActive('transaksi/retur/jual') && !isMenuActive('transaksi/retur/jual/refund') && !isMenuActive('transaksi/retur/jual/exchange') ? 'active' : '' ?>">
                                            <?= nbs(4) ?>
                                            <i class="fas fa-list nav-icon"></i>
                                            <p>Data Retur</p>
                                        </a>
                                    </li>
                                    <!-- <li class="nav-item">
                                        <a href="<?= base_url('transaksi/retur/jual/refund') ?>"
                                            class="nav-link <?= isMenuActive('transaksi/retur/jual/refund') ? 'active' : '' ?>">
                                            <?= nbs(4) ?>
                                            <i class="fas fa-money-bill-wave nav-icon"></i>
                                            <p>Retur Refund</p>
                                        </a>
                                    </li> -->
                                    <li class="nav-item">
                                        <a href="<?= base_url('transaksi/retur/jual/exchange') ?>"
                                            class="nav-link <?= isMenuActive('transaksi/retur/jual/exchange') ? 'active' : '' ?>">
                                            <?= nbs(4) ?>
                                            <i class="fas fa-exchange-alt nav-icon"></i>
                                            <p>Tukar Barang</p>
                                        </a>
                                    </li>

                                    <!-- Refund Requests Menu -->
                                    <li class="nav-item">
                                        <a href="<?= base_url('transaksi/refund') ?>"
                                            class="nav-link <?= isMenuActive('transaksi/refund') ? 'active' : '' ?>">
                                            <?= nbs(4) ?>
                                            <i class="fas fa-money-bill-wave nav-icon"></i>
                                            <p>Pengembalian Dana</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- Refund Requests Menu -->
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/refund') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/refund') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-money-bill-wave nav-icon"></i>
                                    <p>Permintaan Refund</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Shift Management -->
                    <li class="nav-item has-treeview <?= $isShiftActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isShiftActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-clock"></i>
                            <p>
                                Shift Management
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/shift') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/shift') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-clock nav-icon"></i>
                                    <p>Data Shift</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/shift/open') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/shift/open') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-play nav-icon"></i>
                                    <p>Buka Shift</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/petty') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/petty') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-money-bill-wave nav-icon"></i>
                                    <p>Petty Cash</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Warehouse -->
                    <li class="nav-header">GUDANG</li>
                    <?php
                    // Integrate isMenuActive with all Gudang menu routes
                    $gudangMenus = [
                        'gudang/transfer',
                        'gudang/penerimaan',
                        'gudang/input_stok',
                        'gudang/stok',
                        'gudang/opname'
                    ];
                    $isGudangActive = isMenuActive($gudangMenus);
                    ?>
                    <li class="nav-item has-treeview <?= $isGudangActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isGudangActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-warehouse"></i>
                            <p>
                                Gudang
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('gudang/input_stok') ?>"
                                    class="nav-link <?= isMenuActive('gudang/input_stok') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-box-open nav-icon"></i>
                                    <p>Input Penerimaan</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
    <?php else: ?>
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2"> 
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="<?php echo base_url((!empty($Pengaturan->logo) ? $Pengaturan->logo_header : 'public/assets/theme/admin-lte-3/dist/img/AdminLTELogo.png')); ?>"
                            class="brand-image img-rounded-0 elevation-0"
                            style="width: 209px; height: 85px; background-color: transparent;" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block"></a>
                    </div>
                </div>
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="<?= base_url('dashboard') ?>"
                            class="nav-link <?= isMenuActive('dashboard') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <!-- Master Data Katalog -->
                    <li class="nav-header">MASTER DATA</li>
                    <li
                        class="nav-item has-treeview <?= isMenuActive(['master/merk', 'master/kategori', 'master/varian', 'master/item', 'master/satuan']) ? 'menu-open' : '' ?>">
                        <a href="#"
                            class="nav-link <?= isMenuActive(['master/merk', 'master/kategori', 'master/varian', 'master/item', 'master/satuan']) ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-briefcase"></i>
                            <p>
                                Katalog
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('master/merk') ?>"
                                    class="nav-link <?= isMenuActive('master/merk') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-tag nav-icon"></i>
                                    <p>Merk</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('master/kategori') ?>"
                                    class="nav-link <?= isMenuActive('master/kategori') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-list nav-icon"></i>
                                    <p>Kategori</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('master/varian') ?>"
                                    class="nav-link <?= isMenuActive('master/varian') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-palette nav-icon"></i>
                                    <p>Varian</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('master/item') ?>"
                                    class="nav-link <?= isMenuActive('master/item') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-box nav-icon"></i>
                                    <p>Item</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('master/satuan') ?>"
                                    class="nav-link <?= isMenuActive('master/satuan') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-ruler nav-icon"></i>
                                    <p>Satuan</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Outlet -->
                    <li
                        class="nav-item has-treeview <?= isMenuActive(['master/outlet', 'master/gudang']) ? 'menu-open' : '' ?>">
                        <a href="#"
                            class="nav-link <?= isMenuActive(['master/outlet', 'master/gudang']) ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-building"></i>
                            <p>
                                Outlet
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('master/outlet') ?>"
                                    class="nav-link <?= isMenuActive('master/outlet') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-store nav-icon"></i>
                                    <p>Data Outlet</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('master/gudang') ?>"
                                    class="nav-link <?= isMenuActive('master/gudang') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-map-marker-alt nav-icon"></i>
                                    <p>Data Gudang</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Contact -->
                    <?php
                    $kontakMenus = [
                        'master/supplier',
                        'master/customer',
                        'master/customer-group',
                        'master/karyawan'
                    ];
                    $isKontakActive = isMenuActive($kontakMenus);
                    ?>
                    <li class="nav-item has-treeview <?= $isKontakActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isKontakActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Kontak
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('master/supplier') ?>"
                                    class="nav-link <?= isMenuActive('master/supplier') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-truck nav-icon"></i>
                                    <p>Supplier</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('master/customer') ?>"
                                    class="nav-link <?= isMenuActive('master/customer') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-user-friends nav-icon"></i>
                                    <p>Pelanggan / Anggota</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('master/customer-group') ?>"
                                    class="nav-link <?= isMenuActive('master/customer-group') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-users-cog nav-icon"></i>
                                    <p>Grup Pelanggan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('master/karyawan') ?>"
                                    class="nav-link <?= isMenuActive('master/karyawan') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-user-tie nav-icon"></i>
                                    <p>Karyawan</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Payment -->
                    <li
                        class="nav-item has-treeview <?= isMenuActive(['master/platform', 'master/bank']) ? 'menu-open' : '' ?>">
                        <a href="#"
                            class="nav-link <?= isMenuActive(['master/platform', 'master/bank']) ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-money-bill-wave"></i>
                            <p>
                                Pembayaran
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('master/voucher') ?>"
                                    class="nav-link <?= isMenuActive('master/voucher') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-ticket-alt nav-icon"></i>
                                    <p>Voucher</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('master/platform') ?>"
                                    class="nav-link <?= isMenuActive('master/platform') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-credit-card nav-icon"></i>
                                    <p>Metode Pembayaran</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Transaksi -->
                    <li class="nav-header">TRANSAKSI</li>
                    <?php
                    // Integrate isMenuActive with all transaksi menu routes (Pembelian)
                    $transaksiMenus = [
                        'transaksi/po',
                        'transaksi/po/create',
                        'transaksi/beli',
                        'transaksi/beli/create',
                        'transaksi/retur/beli'
                    ];
                    $isTransaksiActive = isMenuActive($transaksiMenus);

                    // Integrate isMenuActive with all penjualan menu routes
                    $penjualanMenus = [
                        'transaksi/jual',
                        'transaksi/jual/create',
                        'transaksi/jual/cashier',
                        'transaksi/jual/detail',
                        'transaksi/jual/edit',
                        // Add all retur jual routes for active state
                        'transaksi/retur/jual',
                        'transaksi/retur/jual/refund',
                        'transaksi/retur/jual/exchange'
                    ];
                    $isPenjualanActive = isMenuActive($penjualanMenus);

                    // For Retur Penjualan submenu, check if any of the retur/jual routes are active
                    $returJualMenus = [
                        'transaksi/retur/jual',
                        'transaksi/retur/jual/refund',
                        'transaksi/retur/jual/exchange'
                    ];
                    $isReturJualActive = isMenuActive($returJualMenus);
                    // Check if shift management is active
                    $isShiftActive = isMenuActive(['transaksi/shift', 'transaksi/petty', 'transaksi/petty-category']);
                    ?>
                    <li class="nav-item has-treeview <?= $isTransaksiActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isTransaksiActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-exchange-alt"></i>
                            <p>
                                Pembelian
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" <?= $isTransaksiActive ? 'style="display: block;"' : 'style="display: none;"' ?>>
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/po/create') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/po/create') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-shopping-cart nav-icon"></i>
                                    <p>Purchase Order</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/beli/create') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/beli/create') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-cart-plus nav-icon"></i>
                                    <p>Faktur</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/po') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/po') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-list nav-icon"></i>
                                    <p>Data Purchase Order</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/beli') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/beli') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-list nav-icon"></i>
                                    <p>Data Pembelian</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/retur/beli') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/retur/beli') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-undo nav-icon"></i>
                                    <p>Retur Pembelian</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-treeview <?= $isPenjualanActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isPenjualanActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-cash-register"></i>
                            <p>
                                Penjualan
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" <?= $isPenjualanActive ? 'style="display: block;"' : 'style="display: none;"' ?>>
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/jual/cashier') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/jual/cashier') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-cash-register nav-icon"></i>
                                    <p>Kasir</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/jual/create') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/jual/create') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-plus nav-icon"></i>
                                    <p>Input Penjualan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/jual') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/jual') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-list nav-icon"></i>
                                    <p>Data Penjualan Kasir</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/jual') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/jual') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-list nav-icon"></i>
                                    <p>Data Penjualan</p>
                                </a>
                            </li>
                            <li class="nav-item <?= $isReturJualActive ? 'menu-open' : '' ?>">
                                <a href="#" class="nav-link <?= $isReturJualActive ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-undo nav-icon"></i>
                                    <p>
                                        Retur Penjualan
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview" <?= $isReturJualActive ? 'style="display: block;"' : 'style="display: none;"' ?>>
                                    <li class="nav-item">
                                        <a href="<?= base_url('transaksi/retur/jual') ?>"
                                            class="nav-link <?= isMenuActive('transaksi/retur/jual') && !isMenuActive('transaksi/retur/jual/refund') && !isMenuActive('transaksi/retur/jual/exchange') ? 'active' : '' ?>">
                                            <?= nbs(4) ?>
                                            <i class="fas fa-list nav-icon"></i>
                                            <p>Data Retur</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= base_url('transaksi/retur/jual/refund') ?>"
                                            class="nav-link <?= isMenuActive('transaksi/retur/jual/refund') ? 'active' : '' ?>">
                                            <?= nbs(4) ?>
                                            <i class="fas fa-money-bill-wave nav-icon"></i>
                                            <p>Retur Refund</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= base_url('transaksi/retur/jual/exchange') ?>"
                                            class="nav-link <?= isMenuActive('transaksi/retur/jual/exchange') ? 'active' : '' ?>">
                                            <?= nbs(4) ?>
                                            <i class="fas fa-exchange-alt nav-icon"></i>
                                            <p>Retur Tukar Barang</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- Refund Requests Menu -->
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/refund') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/refund') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-money-bill-wave nav-icon"></i>
                                    <p>Permintaan Refund</p>
                                </a>
                            </li>

                            <!-- Refund Approval Menu (for Superadmin) -->
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/refund/approval') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/refund/approval') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-gavel nav-icon"></i>
                                    <p>Persetujuan Refund</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Shift Management -->
                    <li class="nav-item has-treeview <?= $isShiftActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isShiftActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-clock"></i>
                            <p>
                                Shift Management
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/shift') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/shift') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-clock nav-icon"></i>
                                    <p>Data Shift</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/shift/open') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/shift/open') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-play nav-icon"></i>
                                    <p>Buka Shift</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('transaksi/petty') ?>"
                                    class="nav-link <?= isMenuActive('transaksi/petty') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-money-bill-wave nav-icon"></i>
                                    <p>Uang Kas</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Warehouse -->
                    <li class="nav-header">GUDANG</li>
                    <?php
                    // Integrate isMenuActive with all Gudang menu routes
                    $gudangMenus = [
                        'gudang/transfer',
                        'gudang/penerimaan',
                        'gudang/input_stok',
                        'gudang/stok',
                        'gudang/opname'
                    ];

                    $isGudangActive = isMenuActive($gudangMenus);
                    ?>
                    <li class="nav-item has-treeview <?= $isGudangActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isGudangActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-warehouse"></i>
                            <p>
                                Gudang
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('gudang/transfer') ?>"
                                    class="nav-link <?= isMenuActive('gudang/transfer') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-exchange-alt nav-icon"></i>
                                    <p>Transfer</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('gudang/penerimaan') ?>"
                                    class="nav-link <?= isMenuActive('gudang/penerimaan') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-truck-loading nav-icon"></i>
                                    <p>Penerimaan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('gudang/stok') ?>"
                                    class="nav-link <?= isMenuActive('gudang/stok') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-clipboard-list nav-icon"></i>
                                    <p>Inventori</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('gudang/opname') ?>"
                                    class="nav-link <?= isMenuActive('gudang/opname') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-boxes nav-icon"></i>
                                    <p>Stock Opname</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('gudang/input_stok') ?>"
                                    class="nav-link <?= isMenuActive('gudang/input_stok') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-box-open nav-icon"></i>
                                    <p>Input Penerimaan</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Reports -->
                    <li class="nav-header">LAPORAN</li>
                    <?php
                    // Integrate isMenuActive with all Laporan menu routes (Enhanced with new reports)
                    $laporanMenus = [
                        'laporan/sale',
                        'laporan/purchase',
                        'laporan/stock',
                        'laporan/outlet',
                        'laporan/sales-turnover',
                        'laporan/product-sales',
                        'laporan/order',
                        'laporan/all-in-one-turnover',
                        'laporan/profit-loss',
                        'laporan/best-selling',
                        'laporan/cutoff'
                    ];
                    $isLaporanActive = isMenuActive($laporanMenus);
                    ?>
                    <li class="nav-item has-treeview <?= $isLaporanActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isLaporanActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>
                                Laporan
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <!-- Laporan Standar -->
                            <li class="nav-item">
                                <a href="<?= base_url('laporan/sale') ?>"
                                    class="nav-link <?= isMenuActive('laporan/sale') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-shopping-cart nav-icon"></i>
                                    <p>Laporan Penjualan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('laporan/purchase') ?>"
                                    class="nav-link <?= isMenuActive('laporan/purchase') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-shopping-bag nav-icon"></i>
                                    <p>Laporan Pembelian</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('laporan/stock') ?>"
                                    class="nav-link <?= isMenuActive('laporan/stock') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-boxes nav-icon"></i>
                                    <p>Laporan Stok</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('laporan/outlet') ?>"
                                    class="nav-link <?= isMenuActive('laporan/outlet') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-store nav-icon"></i>
                                    <p>Laporan Outlet</p>
                                </a>
                            </li>

                            <!-- Pemisah -->
                            <li class="nav-item">
                                <div class="nav-link"
                                    style="color: #6c757d; font-size: 0.8rem; font-weight: bold; padding-top: 10px;">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-star nav-icon"></i>
                                    <span>LAPORAN LANJUTAN</span>
                                </div>
                            </li>

                            <!-- Laporan Lanjutan -->
                            <li class="nav-item">
                                <a href="<?= base_url('laporan/sales-turnover') ?>"
                                    class="nav-link <?= isMenuActive('laporan/sales-turnover') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-chart-line nav-icon"></i>
                                    <p>Omzet Penjualan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('laporan/product-sales') ?>"
                                    class="nav-link <?= isMenuActive('laporan/product-sales') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-box nav-icon"></i>
                                    <p>Penjualan Produk</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('laporan/order') ?>"
                                    class="nav-link <?= isMenuActive('laporan/order') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-file-invoice nav-icon"></i>
                                    <p>Laporan Pesanan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('laporan/all-in-one-turnover') ?>"
                                    class="nav-link <?= isMenuActive('laporan/all-in-one-turnover') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-chart-pie nav-icon"></i>
                                    <p>Omzet All-in-One</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('laporan/profit-loss') ?>"
                                    class="nav-link <?= isMenuActive('laporan/profit-loss') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-calculator nav-icon"></i>
                                    <p>Laba & Rugi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('laporan/best-selling') ?>"
                                    class="nav-link <?= isMenuActive('laporan/best-selling') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-trophy nav-icon"></i>
                                    <p>Produk Terlaris</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('laporan/cutoff') ?>"
                                    class="nav-link <?= isMenuActive('laporan/cutoff') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-cut nav-icon"></i>
                                    <p>Laporan Cut-off</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Settings -->
                    <li class="nav-header">PENGATURAN</li>
                    <?php
                    // Enhanced settings menu with cut-off
                    $pengaturanMenus = [
                        'pengaturan/app',
                        'pengaturan/api-tokens',
                        'pengaturan/printer',
                        'master/cutoff'
                    ];
                    $isPengaturanActive = isMenuActive($pengaturanMenus);
                    ?>
                    <li class="nav-item has-treeview <?= $isPengaturanActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isPengaturanActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>
                                Pengaturan
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('pengaturan/app') ?>"
                                    class="nav-link <?= isMenuActive('pengaturan/app') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-cogs nav-icon"></i>
                                    <p>Aplikasi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('pengaturan/api-tokens') ?>"
                                    class="nav-link <?= isMenuActive('pengaturan/api-tokens') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-key nav-icon"></i>
                                    <p>API Tokens</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('pengaturan/printer') ?>"
                                    class="nav-link <?= isMenuActive('pengaturan/printer') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-print nav-icon"></i>
                                    <p>Printer</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('master/cutoff') ?>"
                                    class="nav-link <?= isMenuActive('master/cutoff') ? 'active' : '' ?>">
                                    <?= nbs(3) ?>
                                    <i class="fas fa-cut nav-icon"></i>
                                    <p>Cut-off Management</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Enhanced Features Dashboard -->
                    <li class="nav-item">
                        <a href="<?= base_url('dashboard/system-overview') ?>"
                            class="nav-link <?= isMenuActive('dashboard/system-overview') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-rocket"></i>
                            <p>Ikhtisar Sistem</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('dashboard/enhanced-features') ?>"
                            class="nav-link <?= isMenuActive('dashboard/enhanced-features') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-star"></i>
                            <p>Fitur Unggulan</p>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
    <?php endif; ?>

    <!-- /.sidebar -->
</aside>