<aside class="main-sidebar sidebar-light-primary elevation-0">
    <!-- Brand Logo -->
    <a href="<?= base_url() ?>" class="brand-link">
        <img src="<?= $Pengaturan->logo ? base_url($Pengaturan->logo) : base_url('public/assets/theme/admin-lte-3/dist/img/AdminLTELogo.png') ?>"
            alt="AdminLTE Logo" class="brand-image img-circle elevation-0" style="opacity: .8">
        <span class="brand-text font-weight-light"><?= $Pengaturan ? $Pengaturan->judul_app : env('app.name') ?></span>
    </a>

    <!-- Sidebar -->
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
                <li class="nav-item has-treeview <?= isMenuActive(['master/merk', 'master/kategori', 'master/item', 'master/satuan']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= isMenuActive(['master/merk', 'master/kategori', 'master/item', 'master/satuan']) ? 'active' : '' ?>">
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
                <li class="nav-item has-treeview <?= isMenuActive(['master/outlet', 'master/gudang']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= isMenuActive(['master/outlet', 'master/gudang']) ? 'active' : '' ?>">
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
                <li class="nav-item has-treeview <?= isMenuActive(['master/supplier', 'master/customer', 'master/karyawan']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= isMenuActive(['master/supplier', 'master/customer', 'master/karyawan']) ? 'active' : '' ?>">
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
                <li class="nav-item has-treeview <?= isMenuActive(['master/platform', 'master/bank']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= isMenuActive(['master/platform', 'master/bank']) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>
                            Pembayaran
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('master/platform') ?>"
                                class="nav-link <?= isMenuActive('master/platform') ? 'active' : '' ?>">
                                <?= nbs(3) ?>
                                <i class="fas fa-credit-card nav-icon"></i>
                                <p>Platform</p>
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a href="<?= base_url('master/bank') ?>"
                                class="nav-link <?= isMenuActive('master/bank') ? 'active' : '' ?>">
                                <?= nbs(3) ?>
                                <i class="fas fa-university nav-icon"></i>
                                <p>Bank</p>
                            </a>
                        </li> -->
                    </ul>
                </li>
                
                <!-- Transaksi -->
                <li class="nav-header">TRANSAKSI</li>
                <?php
                    // Integrate isMenuActive with all transaksi menu routes
                    $transaksiMenus = [
                        'transaksi/po',
                        'transaksi/po/create',
                        'transaksi/beli',
                        'transaksi/beli/create'
                    ];
                    $isTransaksiActive = isMenuActive($transaksiMenus);
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
                            <a href="<?= base_url('transaksi/po/create') ?>" class="nav-link <?= isMenuActive('transaksi/po/create') ? 'active' : '' ?>">
                                <?= nbs(3) ?>
                                <i class="fas fa-shopping-cart nav-icon"></i>
                                <p>Purchase Order</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('transaksi/beli/create') ?>" class="nav-link <?= isMenuActive('transaksi/beli/create') ? 'active' : '' ?>">
                                <?= nbs(3) ?>
                                <i class="fas fa-cart-plus nav-icon"></i>
                                <p>Faktur</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('transaksi/po') ?>" class="nav-link <?= isMenuActive('transaksi/po') ? 'active' : '' ?>">
                                <?= nbs(3) ?>
                                <i class="fas fa-list nav-icon"></i>
                                <p>Data Purchase Order</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('transaksi/beli') ?>" class="nav-link <?= isMenuActive('transaksi/beli') ? 'active' : '' ?>">
                                <?= nbs(3) ?>
                                <i class="fas fa-list nav-icon"></i>
                                <p>Data Pembelian</p>
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
                    </ul>
                </li>

                <!-- Settings -->
                <li class="nav-header">PENGATURAN</li>
                <li class="nav-item has-treeview <?= isMenuActive('pengaturan') ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= isMenuActive('pengaturan') ? 'active' : '' ?>">
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
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>