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
                <li class="nav-item has-treeview <?= isMenuActive(['master/outlet', 'master/lokasi']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= isMenuActive(['master/outlet', 'master/lokasi']) ? 'active' : '' ?>">
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
                        <!-- <li class="nav-item">
                            <a href="<?= base_url('master/lokasi') ?>"
                                class="nav-link <?= isMenuActive('master/lokasi') ? 'active' : '' ?>">
                                <?= nbs(3) ?>
                                <i class="fas fa-map-marker-alt nav-icon"></i>
                                <p>Lokasi</p>
                            </a>
                        </li> -->
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