<div class="headerpanel">
    <div class="logopanel">
        <h2><a href="<?= base_url() ?>"><?= $Pengaturan->judul_app ?></a></h2>
    </div>
    <!-- logopanel -->

    <div class="headerbar">
        <a id="menuToggle" class="menutoggle"><i class="fa fa-bars"></i></a>

        <div class="searchpanel">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search for...">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                </span>
            </div><!-- input-group -->
        </div>

        <div class="header-right">
            <ul class="headermenu">
                <li>
                    <div id="noticePanel" class="btn-group">
                        <button class="btn btn-notice alert-notice" data-toggle="dropdown">
                            <i class="fa fa-globe"></i>
                        </button>
                        <div id="noticeDropdown" class="dropdown-menu dm-notice pull-right">
                            <div role="tabpanel">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs nav-justified" role="tablist">
                                    <li class="active"><a data-target="#notification" data-toggle="tab">Notifications
                                            (2)</a></li>
                                    <li><a data-target="#reminders" data-toggle="tab">Reminders (4)</a></li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="notification">
                                        <ul class="list-group notice-list">
                                            <li class="list-group-item unread">
                                                <div class="row">
                                                    <div class="col-xs-2">
                                                        <i class="fa fa-envelope"></i>
                                                    </div>
                                                    <div class="col-xs-10">
                                                        <h5><a href="">New message from Weno Carasbong</a></h5>
                                                        <small>June 20, 2015</small>
                                                        <span>Soluta nobis est eligendi optio cumque...</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item unread">
                                                <div class="row">
                                                    <div class="col-xs-2">
                                                        <i class="fa fa-user"></i>
                                                    </div>
                                                    <div class="col-xs-10">
                                                        <h5><a href="">Renov Leonga is now following you!</a>
                                                        </h5>
                                                        <small>June 18, 2015</small>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col-xs-2">
                                                        <i class="fa fa-user"></i>
                                                    </div>
                                                    <div class="col-xs-10">
                                                        <h5><a href="">Zaham Sindil is now following you!</a>
                                                        </h5>
                                                        <small>June 17, 2015</small>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col-xs-2">
                                                        <i class="fa fa-thumbs-up"></i>
                                                    </div>
                                                    <div class="col-xs-10">
                                                        <h5><a href="">Rey Reslaba likes your post!</a></h5>
                                                        <small>June 16, 2015</small>
                                                        <span>HTML5 For Beginners Chapter 1</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col-xs-2">
                                                        <i class="fa fa-comment"></i>
                                                    </div>
                                                    <div class="col-xs-10">
                                                        <h5><a href="">Socrates commented on your post!</a></h5>
                                                        <small>June 16, 2015</small>
                                                        <span>Temporibus autem et aut officiis debitis...</span>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                        <a class="btn-more" href="">View More Notifications <i
                                                class="fa fa-long-arrow-right"></i></a>
                                    </div><!-- tab-pane -->

                                    <div role="tabpanel" class="tab-pane" id="reminders">
                                        <h1 id="todayDay" class="today-day">...</h1>
                                        <h3 id="todayDate" class="today-date">...</h3>

                                        <h5 class="today-weather"><i class="wi wi-hail"></i> Cloudy 77 Degree
                                        </h5>
                                        <p>Thunderstorm in the area this afternoon through this evening</p>

                                        <h4 class="panel-title">Upcoming Events</h4>
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col-xs-2">
                                                        <h4>20</h4>
                                                        <p>Aug</p>
                                                    </div>
                                                    <div class="col-xs-10">
                                                        <h5><a href="">HTML5/CSS3 Live! United States</a></h5>
                                                        <small>San Francisco, CA</small>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col-xs-2">
                                                        <h4>05</h4>
                                                        <p>Sep</p>
                                                    </div>
                                                    <div class="col-xs-10">
                                                        <h5><a href="">Web Technology Summit</a></h5>
                                                        <small>Sydney, Australia</small>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col-xs-2">
                                                        <h4>25</h4>
                                                        <p>Sep</p>
                                                    </div>
                                                    <div class="col-xs-10">
                                                        <h5><a href="">HTML5 Developer Conference 2015</a></h5>
                                                        <small>Los Angeles CA United States</small>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col-xs-2">
                                                        <h4>10</h4>
                                                        <p>Oct</p>
                                                    </div>
                                                    <div class="col-xs-10">
                                                        <h5><a href="">AngularJS Conference 2015</a></h5>
                                                        <small>Silicon Valley CA, United States</small>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                        <a class="btn-more" href="">View More Events <i
                                                class="fa fa-long-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="btn-group">
                        <button type="button" class="btn btn-logged" data-toggle="dropdown">
                            <img src="<?php echo base_url('public/assets/theme/quirk/images/photos/loggeduser.png') ?>"
                                alt="" />
                            Elen Adarna
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="profile.html"><i class="glyphicon glyphicon-user"></i> My Profile</a>
                            </li>
                            <li><a href="#"><i class="glyphicon glyphicon-cog"></i> Account Settings</a></li>
                            <li><a href="#"><i class="glyphicon glyphicon-question-sign"></i> Help</a></li>
                            <li><a href="<?= base_url('auth/logout') ?>" onclick="return confirm('Apakah anda yakin ingin logout?')"><i class="glyphicon glyphicon-log-out"></i> Log Out</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <button id="chatview" class="btn btn-chat alert-notice">
                        <span class="badge-alert"></span>
                        <i class="fa fa-comments-o"></i>
                    </button>
                </li>
            </ul>
        </div><!-- header-right -->
    </div>
    <!-- headerbar -->
</div>