<div class="logopanel">
    <h2><a href="<?= base_url() ?>"><?= $Pengaturan->judul_app ?></a></h2>
</div>

<div class="headerbar">
    <a id="menuToggle" class="menutoggle"><i class="fa fa-bars"></i></a>

    <div class="searchpanel">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Search for...">
            <span class="input-group-btn">
                <button class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
            </span>
        </div>
    </div>

    <div class="header-right">
        <ul class="headermenu">
            <li>
                <div class="btn-group">
                    <button type="button" class="btn btn-logged" data-toggle="dropdown">
                        <img src="<?= base_url('public/assets/theme/quirk/images/photos/loggeduser.png') ?>" alt="" />
                        <?= $user->username ?? 'User' ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="<?= base_url('profile') ?>"><i class="fa fa-user"></i> My Profile</a></li>
                        <li><a href="<?= base_url('settings') ?>"><i class="fa fa-cog"></i> Account Settings</a></li>
                        <li><a href="<?= base_url('help') ?>"><i class="fa fa-question-circle"></i> Help</a></li>
                        <li><a href="<?= base_url('auth/logout') ?>"><i class="fa fa-sign-out"></i> Log Out</a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</div> 