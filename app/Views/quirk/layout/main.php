<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="<?= base_url('public/file/app/' . $Pengaturan->favicon) ?>" type="image/png">

    <title>Quirk Responsive Admin Templates</title>

    <link rel="stylesheet" href="<?= base_url('public/assets/theme/quirk/lib/Hover/hover.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/quirk/lib/fontawesome/css/font-awesome.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/quirk/lib/weather-icons/css/weather-icons.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/quirk/lib/ionicons/css/ionicons.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/quirk/lib/jquery-toggles/toggles-full.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/quirk/lib/morrisjs/morris.css') ?>">

    <link rel="stylesheet" href="<?= base_url('public/assets/theme/quirk/css/quirk.css') ?>">

    <script src="<?= base_url('public/assets/theme/quirk/lib/modernizr/modernizr.js') ?>"></script>
    
    
    <!-- Core JavaScript Libraries -->
    <script src="<?= base_url('public/assets/theme/quirk/lib/jquery/jquery.js') ?>"></script> <!-- jQuery core library for DOM manipulation and AJAX -->
    <script src="<?= base_url('public/assets/theme/quirk/lib/jquery-ui/jquery-ui.js') ?>"></script> <!-- jQuery UI for enhanced user interface components -->
    <script src="<?= base_url('public/assets/theme/quirk/lib/bootstrap/js/bootstrap.js') ?>"></script> <!-- Bootstrap JavaScript for responsive layout and components -->
    <script src="<?= base_url('public/assets/theme/quirk/lib/jquery-toggles/toggles.js') ?>"></script> <!-- Toggle switches for UI elements -->

    <!-- Chart Libraries -->
    <script src="<?= base_url('public/assets/theme/quirk/lib/morrisjs/morris.js') ?>"></script> <!-- Morris.js for creating charts and graphs -->
    <script src="<?= base_url('public/assets/theme/quirk/lib/raphael/raphael.js') ?>"></script> <!-- Raphael.js required by Morris.js for vector graphics -->

    <!-- Flot Chart Libraries -->
    <script src="<?= base_url('public/assets/theme/quirk/lib/flot/jquery.flot.js') ?>"></script> <!-- Flot core library for plotting charts -->
    <script src="<?= base_url('public/assets/theme/quirk/lib/flot/jquery.flot.resize.js') ?>"></script> <!-- Flot plugin for responsive chart resizing -->
    <script src="<?= base_url('public/assets/theme/quirk/lib/flot-spline/jquery.flot.spline.js') ?>"></script> <!-- Flot plugin for smooth curve interpolation -->

    <!-- UI Components -->
    <script src="<?= base_url('public/assets/theme/quirk/lib/jquery-knob/jquery.knob.js') ?>"></script> <!-- jQuery Knob for circular dial inputs -->

    <!-- Theme Specific Scripts -->
    <script src="<?= base_url('public/assets/theme/quirk/js/quirk.js') ?>"></script> <!-- Main theme JavaScript file -->
    <script src="<?= base_url('public/assets/theme/quirk/js/dashboard.js') ?>"></script> <!-- Dashboard specific JavaScript -->

    <!-- Toastr -->
    <link rel="stylesheet" href="<?= base_url('/public/assets/plugins/toastr/toastr.min.css') ?>">
    <script src="<?= base_url('/public/assets/plugins/toastr/toastr.min.js') ?>"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
  <script src="../lib/html5shiv/html5shiv.js"></script>
  <script src="../lib/respond/respond.src.js"></script>
  <![endif]-->
</head>

<body>
    <header>
        <!-- header -->
        <?= $this->include('quirk/layout/header') ?>
        <!-- header-->
    </header>

    <section>
        <!-- leftpanel -->
        <?= $this->include('quirk/layout/sidebar') ?>
        <!-- leftpanel -->

        <!-- mainpanel -->
        <?= $this->renderSection('content') ?>
        <!-- mainpanel -->
    </section>

    <?php
    // Show toastr notification from flashdata if available
    if ($flash = session()->getFlashdata('toastr')) {
        echo toast_show($flash['message'], $flash['type'], $flash['title'] ?? '');
    }
    ?>
</body>

</html>