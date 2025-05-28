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

    <script src="<?= base_url('public/assets/theme/quirk/lib/jquery/jquery.js') ?>"></script>
    <script src="<?= base_url('public/assets/theme/quirk/lib/jquery-ui/jquery-ui.js') ?>"></script>
    <script src="<?= base_url('public/assets/theme/quirk/lib/bootstrap/js/bootstrap.js') ?>"></script>
    <script src="<?= base_url('public/assets/theme/quirk/lib/jquery-toggles/toggles.js') ?>"></script>

    <script src="<?= base_url('public/assets/theme/quirk/lib/morrisjs/morris.js') ?>"></script>
    <script src="<?= base_url('public/assets/theme/quirk/lib/raphael/raphael.js') ?>"></script>

    <script src="<?= base_url('public/assets/theme/quirk/lib/flot/jquery.flot.js') ?>"></script>
    <script src="<?= base_url('public/assets/theme/quirk/lib/flot/jquery.flot.resize.js') ?>"></script>
    <script src="<?= base_url('public/assets/theme/quirk/lib/flot-spline/jquery.flot.spline.js') ?>"></script>

    <script src="<?= base_url('public/assets/theme/quirk/lib/jquery-knob/jquery.knob.js') ?>"></script>

    <script src="<?= base_url('public/assets/theme/quirk/js/quirk.js') ?>"></script>
    <script src="<?= base_url('public/assets/theme/quirk/js/dashboard.js') ?>"></script>
</body>

</html>