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

                <!-- Analyzed data -->
                <li class="nav-item has-treeview <?= isMenuActive(['serp', 'google', 'instagram', 'maps', 'sentimentanalysis', 'xosint']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= isMenuActive(['serp', 'google', 'instagram', 'maps', 'sentimentanalysis', 'xosint']) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>
                            Modul Analisis Data
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('serp/google') ?>"
                                class="nav-link <?= isMenuActive('serp/google') ? 'active' : '' ?>">
                                <?= nbs(3) ?>
                                <i class="fas fa-search nav-icon"></i>
                                <p>Mesin Pencari</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('serp/instagram') ?>"
                                class="nav-link <?= isMenuActive('serp/instagram') ? 'active' : '' ?>">
                                <?= nbs(3) ?>
                                <i class="fab fa-instagram nav-icon"></i>
                                <p>Instagram OSINT</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('serp/xosint') ?>"
                                class="nav-link <?= isMenuActive('serp/xosint') ? 'active' : '' ?>">
                                <?= nbs(3) ?>
                                <i class="fab fa-twitter nav-icon"></i>
                                <p>X.com OSINT</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('serp/maps') ?>"
                                class="nav-link <?= isMenuActive('serp/maps') ? 'active' : '' ?>">
                                <?= nbs(3) ?>
                                <i class="fas fa-map-marked-alt nav-icon"></i>
                                <p>Lokasi Peta</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('serp/sentiment') ?>"
                                class="nav-link <?= isMenuActive('serp/sentiment') ? 'active' : '' ?>">
                                <?= nbs(3) ?>
                                <i class="fas fa-comment-dots nav-icon"></i>
                                <p>Analisis Sentimen</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview <?= isMenuActive(['master', 'satuan']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= isMenuActive(['master', 'satuan']) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-map-marker-alt"></i>
                        <p>
                            Modul Tracking
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('serp') ?>"
                                class="nav-link <?= isMenuActive('serp') ? 'active' : '' ?>">
                                <?= nbs(3) ?>
                                <i class="fas fa-search nav-icon"></i>
                                <p>Lokasi Peta</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('serp') ?>"
                                class="nav-link <?= isMenuActive('serp') ? 'active' : '' ?>">
                                <?= nbs(3) ?>
                                <i class="fas fa-search nav-icon"></i>
                                <p>Lokasi Ponsel</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Settings -->
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