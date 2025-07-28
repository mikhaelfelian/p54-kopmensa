<?php
// // Ensure required variables are set
// if (!isset($user)) {
//     throw new \RuntimeException('User data not passed to view');
// }
if (!isset($Pengaturan)) {
    throw new \RuntimeException('Settings data not passed to view');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'Dashboard' ?> | <?= $Pengaturan->judul_app ?? env('app.name') ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url($Pengaturan->favicon) ?>">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/admin-lte-3/plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/admin-lte-3/dist/css/adminlte.min.css') ?>">
    
    <!-- Custom Sidebar Toggle Styles -->
    <style>
        /* Sidebar toggle animations */
        .main-sidebar {
            transition: margin-left 0.3s ease-in-out;
        }
        
        .sidebar-collapse .main-sidebar {
            margin-left: -250px;
        }
        
        .content-wrapper {
            transition: margin-left 0.3s ease-in-out;
        }
        
        .sidebar-collapse .content-wrapper {
            margin-left: 0;
        }
        
        /* Sidebar toggle button hover effect */
        .navbar-nav .nav-link[data-widget="pushmenu"]:hover {
            background-color: rgba(0,0,0,0.1);
            border-radius: 3px;
        }
        
        /* Mini sidebar styles */
        @media (min-width: 992px) {
            .sidebar-mini.sidebar-collapse .main-sidebar {
                margin-left: 0;
                width: 4.6rem;
            }
            
            .sidebar-mini.sidebar-collapse .content-wrapper {
                margin-left: 4.6rem;
            }
            
            .sidebar-mini.sidebar-collapse .main-sidebar .nav-link p {
                display: none;
            }
            
            .sidebar-mini.sidebar-collapse .main-sidebar .nav-header {
                display: none;
            }
            
            .sidebar-mini.sidebar-collapse .main-sidebar .brand-text {
                display: none;
            }
        }
        
        /* Mobile responsive */
        @media (max-width: 991px) {
            .sidebar-collapse .main-sidebar {
                margin-left: -250px;
            }
            
            .sidebar-collapse .content-wrapper {
                margin-left: 0;
            }
        }
    </style>
    
    <?= $this->renderSection('css') ?>

    <!-- Core Scripts -->
    <script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/jquery-ui/jquery-ui.min.js') ?>"></script>
    <script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/moment/moment.min.js') ?>"></script>
    <script src="<?= base_url('public/assets/theme/admin-lte-3/dist/js/adminlte.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/admin-lte-3/plugins/jquery-ui/jquery-ui.min.css') ?>">

    <!-- Datepicker -->
    <script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') ?>"></script>
    <script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/bootstrap-datepicker/bootstrap-datepicker.id.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/admin-lte-3/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') ?>">

    <!-- Select2 -->
    <script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/select2/js/select2.full.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/admin-lte-3/plugins/select2/css/select2.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/admin-lte-3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">

    <!-- AutoNumeric -->
    <script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/JAutoNumber/autonumeric.js') ?>"></script>
    <?= csrf_meta() ?>

    <!-- Toastr -->
    <script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/toastr/toastr.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/admin-lte-3/plugins/toastr/toastr.min.css') ?>">

    <!-- iCheck Bootstrap -->
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/admin-lte-3/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">

    <!-- jQuery UI -->
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/admin-lte-3/plugins/jquery-ui/jquery-ui.min.css') ?>">

    <script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/JAutonumber/autonumeric.js') ?>"></script>
    <script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/jquery-ui/jquery-ui.js') ?>"></script>
    <script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/moment/moment.min.js') ?>"></script>
    <link href="<?= base_url('public/assets/theme/admin-lte-3/plugins/jquery-ui/jquery-ui.min.css') ?>" rel="stylesheet">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?= $this->include('admin-lte-3/layout/navbar') ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?= $this->include('admin-lte-3/layout/sidebar') ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"><?= $title ?? 'Dashboard' ?></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <?= $breadcrumbs ?? '<li class="breadcrumb-item active">Dashboard</li>' ?>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <?= $this->include('admin-lte-3/layout/footer') ?>
    </div>
    <!-- ./wrapper -->
    <?= $this->renderSection('js') ?>

    <!-- Add this script for toastr notifications -->
    <script>
        $(document).ready(function () {
            // Toastr options
            toastr.options = {
                closeButton: true,
                debug: false,
                newestOnTop: true,
                progressBar: true,
                positionClass: "toast-top-right",
                preventDuplicates: false,
                onclick: null,
                showDuration: "300",
                hideDuration: "1000",
                timeOut: "5000",
                extendedTimeOut: "1000",
                showEasing: "swing",
                hideEasing: "linear",
                showMethod: "fadeIn",
                hideMethod: "fadeOut"
            };

            // Flash messages
            <?php if (session()->getFlashdata('success')): ?>
                toastr.success('<?= session()->getFlashdata('success') ?>');
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                toastr.error('<?= session()->getFlashdata('error') ?>');
            <?php endif; ?>

            <?php if (session()->getFlashdata('warning')): ?>
                toastr.warning('<?= session()->getFlashdata('warning') ?>');
            <?php endif; ?>

            <?php if (session()->getFlashdata('info')): ?>
                toastr.info('<?= session()->getFlashdata('info') ?>');
            <?php endif; ?>
        });
    </script>

    <!-- jQuery UI -->
    <script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/jquery-ui/jquery-ui.min.js') ?>"></script>

    <!-- Select2 -->
    <script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/select2/js/select2.full.min.js') ?>"></script>

    <!-- Sidebar toggle initialization -->
    <script>
        $(document).ready(function() {
            // Ensure AdminLTE sidebar functionality is properly initialized
            if (typeof $.AdminLTE !== 'undefined') {
                $.AdminLTE.init();
            }
            
            // Manual sidebar toggle handler (backup)
            $('[data-widget="pushmenu"]').on('click', function(e) {
                e.preventDefault();
                
                if ($('body').hasClass('sidebar-collapse')) {
                    $('body').removeClass('sidebar-collapse');
                    localStorage.setItem('sidebar-collapsed', 'false');
                } else {
                    $('body').addClass('sidebar-collapse');
                    localStorage.setItem('sidebar-collapsed', 'true');
                }
            });
            
            // Remember sidebar state
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                $('body').addClass('sidebar-collapse');
            }
            
            // Handle sidebar resize
            $(window).on('resize', function() {
                if ($(window).width() <= 991) {
                    $('body').addClass('sidebar-collapse');
                } else {
                    if (localStorage.getItem('sidebar-collapsed') !== 'true') {
                        $('body').removeClass('sidebar-collapse');
                    }
                }
            });
        });
    </script>
</body>
</html>