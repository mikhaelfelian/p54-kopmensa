<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= base_url() ?>" class="brand-link">
        <img src="<?= base_url($Pengaturan->logo) ?>" alt="<?= $Pengaturan->judul_app ?>" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"><?= $Pengaturan->judul_app ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- ################## LEFT PANEL PROFILE ################## -->
        <div class="media leftpanel-profile">
            <div class="media-left">
                <a href="#">
                    <img src="<?= base_url('public/assets/theme/quirk/images/photos/loggeduser.png') ?>" alt="" class="media-object img-circle">
                </a>
            </div>
            <div class="media-body">
                <h4 class="media-heading"><?= $user->username ?? 'User' ?> <a data-toggle="collapse" data-target="#loguserinfo" class="pull-right"><i class="fa fa-angle-down"></i></a></h4>
                <span><?= $user->role ?? 'User' ?></span>
            </div>
        </div>

        <div class="leftpanel-userinfo collapse" id="loguserinfo">
            <h5 class="sidebar-title">Contact</h5>
            <ul class="list-group">
                <li class="list-group-item">
                    <label class="pull-left">Email</label>
                    <span class="pull-right"><?= $user->email ?? '-' ?></span>
                </li>
                <li class="list-group-item">
                    <label class="pull-left">Role</label>
                    <span class="pull-right"><?= $user->role ?? '-' ?></span>
                </li>
            </ul>
        </div>

        <ul class="nav nav-tabs nav-justified nav-sidebar">
            <li class="tooltips active" data-toggle="tooltip" title="Main Menu"><a data-toggle="tab" data-target="#mainmenu"><i class="fa fa-ellipsis-h"></i></a></li>
            <li class="tooltips" data-toggle="tooltip" title="Settings"><a data-toggle="tab" data-target="#settings"><i class="fa fa-cog"></i></a></li>
            <li class="tooltips" data-toggle="tooltip" title="Log Out"><a href="<?= base_url('auth/logout') ?>"><i class="fa fa-sign-out"></i></a></li>
        </ul>

        <div class="tab-content">
            <!-- ################# MAIN MENU ################### -->
            <div class="tab-pane active" id="mainmenu">
                <h5 class="sidebar-title">Main Menu</h5>
                <ul class="nav nav-pills nav-stacked nav-quirk">
                    <li class="<?= current_url() == base_url('dashboard') ? 'active' : '' ?>">
                        <a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> <span>Dashboard</span></a>
                    </li>
                    <!-- Add more menu items here -->
                </ul>
            </div>

            <!-- #################### SETTINGS ################### -->
            <div class="tab-pane" id="settings">
                <h5 class="sidebar-title">General Settings</h5>
                <ul class="list-group list-group-settings">
                    <li class="list-group-item">
                        <h5>Notifications</h5>
                        <small>Get notified about important updates.</small>
                        <div class="toggle-wrapper">
                            <div class="leftpanel-toggle toggle-light success"></div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</aside> 