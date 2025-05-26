<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-05-26
 * This file represents the sidebar component for the Quirk theme.
 */
?>
<div class="leftpanel">
    <div class="leftpanelinner">
        <!-- ################## LEFT PANEL PROFILE ################## -->
        <div class="media leftpanel-profile">
            <div class="media-left">
                <a href="#">
                    <img src="<?php echo base_url('/public/assets/theme/quirk/images/photos/loggeduser.png')?>" alt="" class="media-object img-circle">
                </a>
            </div>
            <div class="media-body">
                <h4 class="media-heading">Elen Adarna <a data-toggle="collapse" data-target="#loguserinfo" class="pull-right"><i class="fa fa-angle-down"></i></a></h4>
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
            <li class="tooltips active" data-toggle="tooltip" title="Main Menu"><a data-toggle="tab" data-target="#mainmenu"><i class="tooltips fa fa-ellipsis-h"></i></a></li>
            <li class="tooltips unread" data-toggle="tooltip" title="Check Mail"><a data-toggle="tab" data-target="#emailmenu"><i class="tooltips fa fa-envelope"></i></a></li>
            <li class="tooltips" data-toggle="tooltip" title="Contacts"><a data-toggle="tab" data-target="#contactmenu"><i class="fa fa-user"></i></a></li>
            <li class="tooltips" data-toggle="tooltip" title="Settings"><a data-toggle="tab" data-target="#settings"><i class="fa fa-cog"></i></a></li>
            <li class="tooltips" data-toggle="tooltip" title="Log Out"><a href="signin.html"><i class="fa fa-sign-out"></i></a></li>
        </ul>

        <div class="tab-content">
            <!-- Main Menu Content -->
            <div class="tab-pane active" id="mainmenu">
                <h5 class="sidebar-title">Favorites</h5>
                <ul class="nav nav-pills nav-stacked nav-quirk">
                    <li class="active"><a href="index.html"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>
                    <li><a href="widgets.html"><span class="badge pull-right">10+</span><i class="fa fa-cube"></i> <span>Widgets</span></a></li>
                    <li><a href="maps.html"><i class="fa fa-map-marker"></i> <span>Maps</span></a></li>
                </ul>

                <h5 class="sidebar-title">Main Menu</h5>
                <ul class="nav nav-pills nav-stacked nav-quirk">
                    <!-- Main menu items -->
                </ul>
            </div>
        </div><!-- tab-content -->
    </div><!-- leftpanelinner -->
</div><!-- leftpanel --> 