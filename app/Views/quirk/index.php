<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <!--<link rel="shortcut icon" href="../images/favicon.png" type="image/png">-->

    <title>Quirk Responsive Admin Templates</title>

    <link rel="stylesheet" href="<?= base_url('public/assets/theme/quirk/lib/Hover/hover.css')?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/quirk/lib/fontawesome/css/font-awesome.css')?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/quirk/lib/weather-icons/css/weather-icons.css')?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/quirk/lib/ionicons/css/ionicons.css')?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/quirk/lib/jquery-toggles/toggles-full.css')?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/quirk/lib/morrisjs/morris.css')?>">

    <link rel="stylesheet" href="<?= base_url('public/assets/theme/quirk/css/quirk.css')?>">

    <script src="<?= base_url('public/assets/theme/quirk/lib/modernizr/modernizr.js')?>"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
  <script src="../lib/html5shiv/html5shiv.js"></script>
  <script src="../lib/respond/respond.src.js"></script>
  <![endif]-->
</head>

<body>

    <header>
        <div class="headerpanel">

            <div class="logopanel">
                <h2><a href="index.html">Quirk</a></h2>
            </div><!-- logopanel -->

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
                                            <li class="active"><a data-target="#notification"
                                                    data-toggle="tab">Notifications (2)</a></li>
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
                                    <img src="<?php echo base_url('public/assets/theme/quirk/images/photos/loggeduser.png') ?>" alt="" />
                                    Elen Adarna
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                    <li><a href="profile.html"><i class="glyphicon glyphicon-user"></i> My Profile</a>
                                    </li>
                                    <li><a href="#"><i class="glyphicon glyphicon-cog"></i> Account Settings</a></li>
                                    <li><a href="#"><i class="glyphicon glyphicon-question-sign"></i> Help</a></li>
                                    <li><a href="signin.html"><i class="glyphicon glyphicon-log-out"></i> Log Out</a>
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
            </div><!-- headerbar -->
        </div><!-- header-->
    </header>

    <section>

        <div class="leftpanel">
            <div class="leftpanelinner">

                <!-- ################## LEFT PANEL PROFILE ################## -->

                <div class="media leftpanel-profile">
                    <div class="media-left">
                        <a href="#">
                            <img src="<?php echo base_url('public/assets/theme/quirk/images/photos/loggeduser.png') ?>" alt="" class="media-object img-circle">
                        </a>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">Elen Adarna <a data-toggle="collapse" data-target="#loguserinfo"
                                class="pull-right"><i class="fa fa-angle-down"></i></a></h4>
                        <span>Software Engineer</span>
                    </div>
                </div><!-- leftpanel-profile -->

                <div class="leftpanel-userinfo collapse" id="loguserinfo">
                    <h5 class="sidebar-title">Address</h5>
                    <address>
                        4975 Cambridge Road
                        Miami Gardens, FL 33056
                    </address>
                    <h5 class="sidebar-title">Contact</h5>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <label class="pull-left">Email</label>
                            <span class="pull-right">me@themepixels.com</span>
                        </li>
                        <li class="list-group-item">
                            <label class="pull-left">Home</label>
                            <span class="pull-right">(032) 1234 567</span>
                        </li>
                        <li class="list-group-item">
                            <label class="pull-left">Mobile</label>
                            <span class="pull-right">+63012 3456 789</span>
                        </li>
                        <li class="list-group-item">
                            <label class="pull-left">Social</label>
                            <div class="social-icons pull-right">
                                <a href="#"><i class="fa fa-facebook-official"></i></a>
                                <a href="#"><i class="fa fa-twitter"></i></a>
                                <a href="#"><i class="fa fa-pinterest"></i></a>
                            </div>
                        </li>
                    </ul>
                </div><!-- leftpanel-userinfo -->

                <ul class="nav nav-tabs nav-justified nav-sidebar">
                    <li class="tooltips active" data-toggle="tooltip" title="Main Menu"><a data-toggle="tab"
                            data-target="#mainmenu"><i class="tooltips fa fa-ellipsis-h"></i></a></li>
                    <li class="tooltips unread" data-toggle="tooltip" title="Check Mail"><a data-toggle="tab"
                            data-target="#emailmenu"><i class="tooltips fa fa-envelope"></i></a></li>
                    <li class="tooltips" data-toggle="tooltip" title="Contacts"><a data-toggle="tab"
                            data-target="#contactmenu"><i class="fa fa-user"></i></a></li>
                    <li class="tooltips" data-toggle="tooltip" title="Settings"><a data-toggle="tab"
                            data-target="#settings"><i class="fa fa-cog"></i></a></li>
                    <li class="tooltips" data-toggle="tooltip" title="Log Out"><a href="signin.html"><i
                                class="fa fa-sign-out"></i></a></li>
                </ul>

                <div class="tab-content">

                    <!-- ################# MAIN MENU ################### -->

                    <div class="tab-pane active" id="mainmenu">
                        <h5 class="sidebar-title">Favorites</h5>
                        <ul class="nav nav-pills nav-stacked nav-quirk">
                            <li class="active"><a href="index.html"><i class="fa fa-home"></i>
                                    <span>Dashboard</span></a></li>
                            <li><a href="widgets.html"><span class="badge pull-right">10+</span><i
                                        class="fa fa-cube"></i> <span>Widgets</span></a></li>
                            <li><a href="maps.html"><i class="fa fa-map-marker"></i> <span>Maps</span></a></li>
                        </ul>

                        <h5 class="sidebar-title">Main Menu</h5>
                        <ul class="nav nav-pills nav-stacked nav-quirk">
                            <li class="nav-parent">
                                <a href=""><i class="fa fa-check-square"></i> <span>Forms</span></a>
                                <ul class="children">
                                    <li><a href="general-forms.html">Form Elements</a></li>
                                    <li><a href="form-validation.html">Form Validation</a></li>
                                    <li><a href="form-wizards.html">Form Wizards</a></li>
                                    <li><a href="wysiwyg.html">Text Editor</a></li>
                                </ul>
                            </li>
                            <li class="nav-parent"><a href=""><i class="fa fa-suitcase"></i> <span>UI
                                        Elements</span></a>
                                <ul class="children">
                                    <li><a href="buttons.html">Buttons</a></li>
                                    <li><a href="icons.html">Icons</a></li>
                                    <li><a href="typography.html">Typography</a></li>
                                    <li><a href="alerts.html">Alerts &amp; Notifications</a></li>
                                    <li><a href="tabs-accordions.html">Tabs &amp; Accordions</a></li>
                                    <li><a href="sliders.html">Sliders</a></li>
                                    <li><a href="graphs.html">Graphs &amp; Charts</a></li>
                                    <li><a href="panels.html">Panels</a></li>
                                    <li><a href="extras.html">Extras</a></li>
                                </ul>
                            </li>
                            <li class="nav-parent"><a href=""><i class="fa fa-th-list"></i> <span>Tables</span></a>
                                <ul class="children">
                                    <li><a href="basic-tables.html">Basic Tables</a></li>
                                    <li><a href="data-tables.html">Data Tables</a></li>
                                </ul>
                            </li>
                            <li class="nav-parent"><a href=""><i class="fa fa-file-text"></i> <span>Pages</span></a>
                                <ul class="children">
                                    <li><a href="asset-manager.html">Asset Manager</a></li>
                                    <li><a href="people-directory.html">People Directory</a></li>
                                    <li><a href="timeline.html">Timeline</a></li>
                                    <li><a href="profile.html">Profile</a></li>
                                    <li><a href="blank.html">Blank Page</a></li>
                                    <li><a href="notfound.html">404 Page</a></li>
                                    <li><a href="signin.html">Sign In</a></li>
                                    <li><a href="signup.html">Sign Up</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div><!-- tab-pane -->

                    <!-- ######################## EMAIL MENU ##################### -->

                    <div class="tab-pane" id="emailmenu">
                        <div class="sidebar-btn-wrapper">
                            <a href="compose.html" class="btn btn-danger btn-block">Compose</a>
                        </div>

                        <h5 class="sidebar-title">Mailboxes</h5>
                        <ul class="nav nav-pills nav-stacked nav-quirk nav-mail">
                            <li><a href="email.html"><i class="fa fa-inbox"></i> <span>Inbox (3)</span></a></li>
                            <li><a href="email.html"><i class="fa fa-pencil"></i> <span>Draft (2)</span></a></li>
                            <li><a href="email.html"><i class="fa fa-paper-plane"></i> <span>Sent</span></a></li>
                        </ul>

                        <h5 class="sidebar-title">Tags</h5>
                        <ul class="nav nav-pills nav-stacked nav-quirk nav-label">
                            <li><a href="#"><i class="fa fa-tags primary"></i> <span>Communication</span></a></li>
                            <li><a href="#"><i class="fa fa-tags success"></i> <span>Updates</span></a></li>
                            <li><a href="#"><i class="fa fa-tags warning"></i> <span>Promotions</span></a></li>
                            <li><a href="#"><i class="fa fa-tags danger"></i> <span>Social</span></a></li>
                        </ul>
                    </div><!-- tab-pane -->

                    <!-- ################### CONTACT LIST ################### -->

                    <div class="tab-pane" id="contactmenu">
                        <div class="input-group input-search-contact">
                            <input type="text" class="form-control" placeholder="Search contact">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                        <h5 class="sidebar-title">My Contacts</h5>
                        <ul class="media-list media-list-contacts">
                            <li class="media">
                                <a href="#">
                                    <div class="media-left">
                                        <img class="media-object img-circle" src="<?php echo base_url('public/assets/theme/quirk/images/photos/user1.png') ?>" alt="">
                                    </div>
                                    <div class="media-body">
                                        <h4 class="media-heading">Christina R. Hill</h4>
                                        <span><i class="fa fa-phone"></i> 386-752-1860</span>
                                    </div>
                                </a>
                            </li>
                            <li class="media">
                                <a href="#">
                                    <div class="media-left">
                                        <img class="media-object img-circle" src="<?php echo base_url('public/assets/theme/quirk/images/photos/user2.png') ?>" alt="">
                                    </div>
                                    <div class="media-body">
                                        <h4 class="media-heading">Floyd M. Romero</h4>
                                        <span><i class="fa fa-mobile"></i> +1614-650-8281</span>
                                    </div>
                                </a>
                            </li>
                            <li class="media">
                                <a href="#">
                                    <div class="media-left">
                                        <img class="media-object img-circle" src="<?php echo base_url('public/assets/theme/quirk/images/photos/user3.png') ?>" alt="">
                                    </div>
                                    <div class="media-body">
                                        <h4 class="media-heading">Jennie S. Gray</h4>
                                        <span><i class="fa fa-phone"></i> 310-757-8444</span>
                                    </div>
                                </a>
                            </li>
                            <li class="media">
                                <a href="#">
                                    <div class="media-left">
                                        <img class="media-object img-circle" src="<?php echo base_url('public/assets/theme/quirk/images/photos/user4.png') ?>" alt="">
                                    </div>
                                    <div class="media-body">
                                        <h4 class="media-heading">Alia J. Locher</h4>
                                        <span><i class="fa fa-mobile"></i> +1517-386-0059</span>
                                    </div>
                                </a>
                            </li>
                            <li class="media">
                                <a href="#">
                                    <div class="media-left">
                                        <img class="media-object img-circle" src="<?php echo base_url('public/assets/theme/quirk/images/photos/user5.png') ?>" alt="">
                                    </div>
                                    <div class="media-body">
                                        <h4 class="media-heading">Nicholas T. Hinkle</h4>
                                        <span><i class="fa fa-skype"></i> nicholas.hinkle</span>
                                    </div>
                                </a>
                            </li>
                            <li class="media">
                                <a href="#">
                                    <div class="media-left">
                                        <img class="media-object img-circle" src="<?php echo base_url('public/assets/theme/quirk/images/photos/user6.png') ?>" alt="">
                                    </div>
                                    <div class="media-body">
                                        <h4 class="media-heading">Jamie W. Bradford</h4>
                                        <span><i class="fa fa-phone"></i> 225-270-2425</span>
                                    </div>
                                </a>
                            </li>
                            <li class="media">
                                <a href="#">
                                    <div class="media-left">
                                        <img class="media-object img-circle" src="<?php echo base_url('public/assets/theme/quirk/images/photos/user7.png') ?>" alt="">
                                    </div>
                                    <div class="media-body">
                                        <h4 class="media-heading">Pamela J. Stump</h4>
                                        <span><i class="fa fa-mobile"></i> +1773-879-2491</span>
                                    </div>
                                </a>
                            </li>
                            <li class="media">
                                <a href="#">
                                    <div class="media-left">
                                        <img class="media-object img-circle" src="<?php echo base_url('public/assets/theme/quirk/images/photos/user8.png') ?>" alt="">
                                    </div>
                                    <div class="media-body">
                                        <h4 class="media-heading">Refugio C. Burgess</h4>
                                        <span><i class="fa fa-mobile"></i> +1660-627-7184</span>
                                    </div>
                                </a>
                            </li>
                            <li class="media">
                                <a href="#">
                                    <div class="media-left">
                                        <img class="media-object img-circle" src="<?php echo base_url('public/assets/theme/quirk/images/photos/user9.png') ?>" alt="">
                                    </div>
                                    <div class="media-body">
                                        <h4 class="media-heading">Ashley T. Brewington</h4>
                                        <span><i class="fa fa-skype"></i> ashley.brewington</span>
                                    </div>
                                </a>
                            </li>
                            <li class="media">
                                <a href="#">
                                    <div class="media-left">
                                        <img class="media-object img-circle" src="<?php echo base_url('public/assets/theme/quirk/images/photos/user10.png') ?>" alt="">
                                    </div>
                                    <div class="media-body">
                                        <h4 class="media-heading">Roberta F. Horn</h4>
                                        <span><i class="fa fa-phone"></i> 716-630-0132</span>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div><!-- tab-pane -->

                    <!-- #################### SETTINGS ################### -->

                    <div class="tab-pane" id="settings">
                        <h5 class="sidebar-title">General Settings</h5>
                        <ul class="list-group list-group-settings">
                            <li class="list-group-item">
                                <h5>Daily Newsletter</h5>
                                <small>Get notified when someone else is trying to access your account.</small>
                                <div class="toggle-wrapper">
                                    <div class="leftpanel-toggle toggle-light success"></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <h5>Call Phones</h5>
                                <small>Make calls to friends and family right from your account.</small>
                                <div class="toggle-wrapper">
                                    <div class="leftpanel-toggle-off toggle-light success"></div>
                                </div>
                            </li>
                        </ul>
                        <h5 class="sidebar-title">Security Settings</h5>
                        <ul class="list-group list-group-settings">
                            <li class="list-group-item">
                                <h5>Login Notifications</h5>
                                <small>Get notified when someone else is trying to access your account.</small>
                                <div class="toggle-wrapper">
                                    <div class="leftpanel-toggle toggle-light success"></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <h5>Phone Approvals</h5>
                                <small>Use your phone when login as an extra layer of security.</small>
                                <div class="toggle-wrapper">
                                    <div class="leftpanel-toggle toggle-light success"></div>
                                </div>
                            </li>
                        </ul>
                    </div><!-- tab-pane -->


                </div><!-- tab-content -->

            </div><!-- leftpanelinner -->
        </div><!-- leftpanel -->

        <div class="mainpanel">

            <!--<div class="pageheader">
      <h2><i class="fa fa-home"></i> Dashboard</h2>
    </div>-->

            <div class="contentpanel">
                <div class="row">
                    <div class="col-md-9 col-lg-8 dash-left">
                        <div class="panel panel-announcement">
                            <ul class="panel-options">
                                <li><a><i class="fa fa-refresh"></i></a></li>
                                <li><a class="panel-remove"><i class="fa fa-remove"></i></a></li>
                            </ul>
                            <div class="panel-heading">
                                <h4 class="panel-title">Latest Announcement</h4>
                            </div>
                            <div class="panel-body">
                                <h2>A new admin template has been released by <span
                                        class="text-primary">ThemePixels</span> with a name <span
                                        class="text-success">Quirk</span> is now live and available for purchase!</h2>
                                <h4>Explore this new template and see the beauty of Quirk! <a href="">Take a Tour!</a>
                                </h4>
                            </div>
                        </div><!-- panel -->

                        <div class="panel panel-site-traffic">
                            <div class="panel-heading">
                                <ul class="panel-options">
                                    <li><a><i class="fa fa-refresh"></i></a></li>
                                </ul>
                                <h4 class="panel-title text-success">How Engaged Our Users Daily</h4>
                                <p class="nomargin">Past 30 Days — Last Updated July 14, 2015</p>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-6 col-sm-4">
                                        <div class="pull-left">
                                            <div class="icon icon ion-stats-bars"></div>
                                        </div>
                                        <div class="pull-left">
                                            <h4 class="panel-title">Bounce Rate</h4>
                                            <h3>23.30%</h3>
                                            <h5 class="text-success">2.00% increased</h5>
                                        </div>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <div class="pull-left">
                                            <div class="icon icon ion-eye"></div>
                                        </div>
                                        <h4 class="panel-title">Pageviews / Visitor</h4>
                                        <h3>38.10</h3>
                                        <h5 class="text-danger">5.70% decreased</h5>
                                    </div>
                                    <div class="col-xs-6 col-sm-4">
                                        <div class="pull-left">
                                            <div class="icon icon ion-clock"></div>
                                        </div>
                                        <h4 class="panel-title">Time on Site</h4>
                                        <h3>4:45</h3>
                                        <h5 class="text-success">5.00% increased</h5>
                                    </div>
                                </div><!-- row -->

                                <div class="mb20"></div>

                                <div id="basicflot" style="height: 263px"></div>

                            </div><!-- panel-body -->

                            <div class="table-responsive">
                                <table class="table table-bordered table-default table-striped nomargin">
                                    <thead class="success">
                                        <tr>
                                            <th>Country</th>
                                            <th class="text-right">% of Visitors</th>
                                            <th class="text-right">Bounce Rate</th>
                                            <th class="text-right">Page View</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>United States</td>
                                            <td class="text-right">61%</td>
                                            <td class="text-right">25.87%</td>
                                            <td class="text-right">55.23</td>
                                        </tr>
                                        <tr>
                                            <td>Canada</td>
                                            <td class="text-right">13%</td>
                                            <td class="text-right">23.12%</td>
                                            <td class="text-right">65.00</td>
                                        </tr>
                                        <tr>
                                            <td>Great Britain</td>
                                            <td class="text-right">10%</td>
                                            <td class="text-right">20.43%</td>
                                            <td class="text-right">67.99</td>
                                        </tr>
                                        <tr>
                                            <td>Philippines</td>
                                            <td class="text-right">7%</td>
                                            <td class="text-right">18.17%</td>
                                            <td class="text-right">55.13</td>
                                        </tr>
                                        <tr>
                                            <td>Australia</td>
                                            <td class="text-right">6.03%</td>
                                            <td class="text-right">17.67%</td>
                                            <td class="text-right">67.05</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div><!-- table-responsive -->

                        </div><!-- panel -->

                        <div class="row panel-statistics">
                            <div class="col-sm-6">
                                <div class="panel panel-updates">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-7 col-lg-8">
                                                <h4 class="panel-title text-success">Products Added</h4>
                                                <h3>75.7%</h3>
                                                <div class="progress">
                                                    <div style="width: 75.7%" aria-valuemax="100" aria-valuemin="0"
                                                        aria-valuenow="75.7" role="progressbar"
                                                        class="progress-bar progress-bar-success">
                                                        <span class="sr-only">75.7% Complete (success)</span>
                                                    </div>
                                                </div>
                                                <p>Added products for this month: 75</p>
                                            </div>
                                            <div class="col-xs-5 col-lg-4 text-right">
                                                <input type="text" value="75" class="dial-success">
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- panel -->
                            </div><!-- col-sm-6 -->

                            <div class="col-sm-6">
                                <div class="panel panel-danger-full panel-updates">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-7 col-lg-8">
                                                <h4 class="panel-title text-warning">Products Rejected</h4>
                                                <h3>39.9%</h3>
                                                <div class="progress">
                                                    <div style="width: 39.9%" aria-valuemax="100" aria-valuemin="0"
                                                        aria-valuenow="39.9" role="progressbar"
                                                        class="progress-bar progress-bar-warning">
                                                        <span class="sr-only">39.9% Complete (success)</span>
                                                    </div>
                                                </div>
                                                <p>Rejected products for this month: 45</p>
                                            </div>
                                            <div class="col-xs-5 col-lg-4 text-right">
                                                <input type="text" value="45" class="dial-warning">
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- panel -->
                            </div><!-- col-sm-6 -->

                            <div class="col-sm-6">
                                <div class="panel panel-success-full panel-updates">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-7 col-lg-8">
                                                <h4 class="panel-title text-success">Products Sold</h4>
                                                <h3>55.4%</h3>
                                                <div class="progress">
                                                    <div style="width: 55.4%" aria-valuemax="100" aria-valuemin="0"
                                                        aria-valuenow="55.4" role="progressbar"
                                                        class="progress-bar progress-bar-info">
                                                        <span class="sr-only">55.4% Complete (success)</span>
                                                    </div>
                                                </div>
                                                <p>Sold products for this month: 1,203</p>
                                            </div>
                                            <div class="col-xs-5 col-lg-4 text-right">
                                                <input type="text" value="55" class="dial-info">
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- panel -->
                            </div><!-- col-sm-6 -->

                            <div class="col-sm-6">
                                <div class="panel panel-updates">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-7 col-lg-8">
                                                <h4 class="panel-title text-danger">Products Returned</h4>
                                                <h3>22.1%</h3>
                                                <div class="progress">
                                                    <div style="width: 22.1%" aria-valuemax="100" aria-valuemin="0"
                                                        aria-valuenow="22.1" role="progressbar"
                                                        class="progress-bar progress-bar-danger">
                                                        <span class="sr-only">22.1% Complete (success)</span>
                                                    </div>
                                                </div>
                                                <p>Returned products this month: 22</p>
                                            </div>
                                            <div class="col-xs-5 col-lg-4 text-right">
                                                <input type="text" value="22" class="dial-danger">
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- panel -->
                            </div><!-- col-sm-6 -->

                        </div><!-- row -->

                        <div class="row row-col-join panel-earnings">
                            <div class="col-xs-3 col-sm-4 col-lg-3">
                                <div class="panel">
                                    <ul class="panel-options">
                                        <li><a><i class="glyphicon glyphicon-option-vertical"></i></a></li>
                                    </ul>
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Total Earnings</h4>
                                    </div>
                                    <div class="panel-body">
                                        <h3 class="earning-amount">$1,543.03</h3>
                                        <h4 class="earning-today">Today's Earnings</h4>

                                        <ul class="list-group">
                                            <li class="list-group-item">This Week <span
                                                    class="pull-right">$12,320.34</span></li>
                                            <li class="list-group-item">This Month <span
                                                    class="pull-right">$37,520.34</span></li>
                                        </ul>
                                        <hr class="invisible">
                                        <p>Total items sold this month: 325</p>
                                    </div>
                                </div><!-- panel -->
                            </div>
                            <div class="col-xs-9 col-sm-8 col-lg-9">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Earnings Graph Overview</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div id="line-chart" class="body-chart"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row panel-quick-page">
                            <div class="col-xs-4 col-sm-5 col-md-4 page-user">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Manage Users</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="page-icon"><i class="icon ion-person-stalker"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-4 col-sm-4 col-md-4 page-products">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Manage Products</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="page-icon"><i class="fa fa-shopping-cart"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-4 col-sm-3 col-md-2 page-events">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Events</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="page-icon"><i class="icon ion-ios-calendar-outline"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-4 col-sm-3 col-md-2 page-messages">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Messages</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="page-icon"><i class="icon ion-email"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-4 col-sm-5 col-md-2 page-reports">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Reports</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="page-icon"><i class="icon ion-arrow-graph-up-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-4 col-sm-4 col-md-2 page-statistics">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Statistics</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="page-icon"><i class="icon ion-ios-pulse-strong"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-4 col-sm-4 col-md-4 page-support">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Manage Support</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="page-icon"><i class="icon ion-help-buoy"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-4 col-sm-4 col-md-2 page-privacy">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Privacy</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="page-icon"><i class="icon ion-android-lock"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-4 col-sm-4 col-md-2 page-settings">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Settings</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="page-icon"><i class="icon ion-gear-a"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- row -->

                    </div><!-- col-md-9 -->
                    <div class="col-md-3 col-lg-4 dash-right">
                        <div class="row">
                            <div class="col-sm-5 col-md-12 col-lg-6">
                                <div class="panel panel-danger panel-weather">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Weather Forecast</h4>
                                    </div>
                                    <div class="panel-body inverse">
                                        <div class="row mb10">
                                            <div class="col-xs-6">
                                                <h2 class="today-day">Monday</h2>
                                                <h3 class="today-date">July 13, 2015</h3>
                                            </div>
                                            <div class="col-xs-6">
                                                <i class="wi wi-hail today-cloud"></i>
                                            </div>
                                        </div>
                                        <p class="nomargin">Thunderstorm in the area of responsibility this afternoon
                                            through this evening.</p>
                                        <div class="row mt10">
                                            <div class="col-xs-7">
                                                <strong>Temperature:</strong> (Celcius) 19
                                            </div>
                                            <div class="col-xs-5">
                                                <strong>Wind:</strong> 30+ mph
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- col-md-12 -->
                            <div class="col-sm-5 col-md-12 col-lg-6">
                                <div class="panel panel-primary list-announcement">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Previous Announcements</h4>
                                    </div>
                                    <div class="panel-body">
                                        <ul class="list-unstyled mb20">
                                            <li>
                                                <a href="">Testing Credit Card Payments on...</a>
                                                <small>June 30, 2015 <a href="">7 shares</a></small>
                                            </li>
                                            <li>
                                                <a href="">A Shopping Cart for New and...</a>
                                                <small>June 15, 2015 &nbsp; <a href="">11 shares</a></small>
                                            </li>
                                            <li>
                                                <a href="">A Shopping Cart for New and...</a>
                                                <small>June 15, 2015 &nbsp; <a href="">2 shares</a></small>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="panel-footer">
                                        <button class="btn btn-primary btn-block">View More Announcements <i
                                                class="fa fa-arrow-right"></i></button>
                                    </div>
                                </div>
                            </div><!-- col-md-12 -->
                        </div><!-- row -->

                        <div class="row">
                            <div class="col-sm-5 col-md-12 col-lg-6">
                                <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Recent User Activity</h4>
                                    </div>
                                    <div class="panel-body">
                                        <ul class="media-list user-list">
                                            <li class="media">
                                                <div class="media-left">
                                                    <a href="#">
                                                        <img class="media-object img-circle"
                                                            src="<?php echo base_url('public/assets/theme/quirk/images/photos/user2.png') ?>" alt="">
                                                    </a>
                                                </div>
                                                <div class="media-body">
                                                    <h4 class="media-heading nomargin"><a href="">Floyd M. Romero</a>
                                                    </h4>
                                                    is now following <a href="">Christina R. Hill</a>
                                                    <small class="date"><i class="glyphicon glyphicon-time"></i> Just
                                                        now</small>
                                                </div>
                                            </li>
                                            <li class="media">
                                                <div class="media-left">
                                                    <a href="#">
                                                        <img class="media-object img-circle"
                                                            src="<?php echo base_url('public/assets/theme/quirk/images/photos/user10.png') ?>" alt="">
                                                    </a>
                                                </div>
                                                <div class="media-body">
                                                    <h4 class="media-heading nomargin"><a href="">Roberta F. Horn</a>
                                                    </h4>
                                                    commented on <a href="">HTML5 Tutorial</a>
                                                    <small class="date"><i class="glyphicon glyphicon-time"></i>
                                                        Yesterday</small>
                                                </div>
                                            </li>
                                            <li class="media">
                                                <div class="media-left">
                                                    <a href="#">
                                                        <img class="media-object img-circle"
                                                            src="<?php echo base_url('public/assets/theme/quirk/images/photos/user3.png') ?>" alt="">
                                                    </a>
                                                </div>
                                                <div class="media-body">
                                                    <h4 class="media-heading nomargin"><a href="">Jennie S. Gray</a>
                                                    </h4>
                                                    posted a video on <a href="">The Discovery</a>
                                                    <small class="date"><i class="glyphicon glyphicon-time"></i> June
                                                        25, 2015</small>
                                                </div>
                                            </li>
                                            <li class="media">
                                                <div class="media-left">
                                                    <a href="#">
                                                        <img class="media-object img-circle"
                                                            src="<?php echo base_url('public/assets/theme/quirk/images/photos/user5.png') ?>" alt="">
                                                    </a>
                                                </div>
                                                <div class="media-body">
                                                    <h4 class="media-heading nomargin"><a href="">Nicholas T. Hinkle</a>
                                                    </h4>
                                                    liked your video on <a href="">The Discovery</a>
                                                    <small class="date"><i class="glyphicon glyphicon-time"></i> June
                                                        24, 2015</small>
                                                </div>
                                            </li>
                                            <li class="media">
                                                <div class="media-left">
                                                    <a href="#">
                                                        <img class="media-object img-circle"
                                                            src="<?php echo base_url('public/assets/theme/quirk/images/photos/user2.png') ?>" alt="">
                                                    </a>
                                                </div>
                                                <div class="media-body">
                                                    <h4 class="media-heading nomargin"><a href="">Floyd M. Romero</a>
                                                    </h4>
                                                    liked your photo on <a href="">My Life Adventure</a>
                                                    <small class="date"><i class="glyphicon glyphicon-time"></i> June
                                                        24, 2015</small>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div><!-- panel -->
                            </div>

                            <div class="col-sm-5 col-md-12 col-lg-6">
                                <div class="panel panel-inverse">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Most Followed Users</h4>
                                    </div>
                                    <div class="panel-body">
                                        <ul class="media-list user-list">
                                            <li class="media">
                                                <div class="media-left">
                                                    <a href="#">
                                                        <img class="media-object img-circle"
                                                            src="<?php echo base_url('public/assets/theme/quirk/images/photos/user9.png') ?>" alt="">
                                                    </a>
                                                </div>
                                                <div class="media-body">
                                                    <h4 class="media-heading"><a href="">Ashley T. Brewington</a></h4>
                                                    <span>5,323</span> Followers
                                                </div>
                                            </li>
                                            <li class="media">
                                                <div class="media-left">
                                                    <a href="#">
                                                        <img class="media-object img-circle"
                                                            src="<?php echo base_url('public/assets/theme/quirk/images/photos/user10.png') ?>" alt="">
                                                    </a>
                                                </div>
                                                <div class="media-body">
                                                    <h4 class="media-heading"><a href="">Roberta F. Horn</a></h4>
                                                    <span>4,100</span> Followers
                                                </div>
                                            </li>
                                            <li class="media">
                                                <div class="media-left">
                                                    <a href="#">
                                                        <img class="media-object img-circle"
                                                            src="<?php echo base_url('public/assets/theme/quirk/images/photos/user3.png') ?>" alt="">
                                                    </a>
                                                </div>
                                                <div class="media-body">
                                                    <h4 class="media-heading"><a href="">Jennie S. Gray</a></h4>
                                                    <span>3,508</span> Followers
                                                </div>
                                            </li>
                                            <li class="media">
                                                <div class="media-left">
                                                    <a href="#">
                                                        <img class="media-object img-circle"
                                                            src="<?php echo base_url('public/assets/theme/quirk/images/photos/user4.png') ?>" alt="">
                                                    </a>
                                                </div>
                                                <div class="media-body">
                                                    <h4 class="media-heading"><a href="">Alia J. Locher</a></h4>
                                                    <span>3,508</span> Followers
                                                </div>
                                            </li>
                                            <li class="media">
                                                <div class="media-left">
                                                    <a href="#">
                                                        <img class="media-object img-circle"
                                                            src="<?php echo base_url('public/assets/theme/quirk/images/photos/user6.png') ?>" alt="">
                                                    </a>
                                                </div>
                                                <div class="media-body">
                                                    <h4 class="media-heading"><a href="">Jamie W. Bradford</a></h4>
                                                    <span>2,001</span> Followers
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div><!-- row -->

                    </div><!-- col-md-3 -->
                </div><!-- row -->

            </div>
            <!-- contentpanel -->

        </div>
        <!-- mainpanel -->

    </section>

    <script src="<?= base_url('public/assets/theme/quirk/lib/jquery/jquery.js')?>"></script>
    <script src="<?= base_url('public/assets/theme/quirk/lib/jquery-ui/jquery-ui.js')?>"></script>
    <script src="<?= base_url('public/assets/theme/quirk/lib/bootstrap/js/bootstrap.js')?>"></script>
    <script src="<?= base_url('public/assets/theme/quirk/lib/jquery-toggles/toggles.js')?>"></script>

    <script src="<?= base_url('public/assets/theme/quirk/lib/morrisjs/morris.js')?>"></script>
    <script src="<?= base_url('public/assets/theme/quirk/lib/raphael/raphael.js')?>"></script>

    <script src="<?= base_url('public/assets/theme/quirk/lib/flot/jquery.flot.js')?>"></script>
    <script src="<?= base_url('public/assets/theme/quirk/lib/flot/jquery.flot.resize.js')?>"></script>
    <script src="<?= base_url('public/assets/theme/quirk/lib/flot-spline/jquery.flot.spline.js')?>"></script>

    <script src="<?= base_url('public/assets/theme/quirk/lib/jquery-knob/jquery.knob.js')?>"></script>

    <script src="<?= base_url('public/assets/theme/quirk/js/quirk.js')?>"></script>
    <script src="<?= base_url('public/assets/theme/quirk/js/dashboard.js')?>"></script>

</body>

</html>