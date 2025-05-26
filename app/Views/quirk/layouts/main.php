<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-05-26
 * This file represents the main layout template for the Quirk theme.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">
    <link rel="shortcut icon" href="#" type="image/png">

    <title><?php echo $Pengaturan->judul_app ?? 'Kopmensa POS' ?><?php echo ' | '.$Pengaturan->judul ?? '' ?></title>

    <!--common style-->
    <link href="<?php echo base_url('/public/assets/theme/quirk/lib/bootstrap/css/bootstrap.css')?>" rel="stylesheet">
    <link href="<?php echo base_url('/public/assets/theme/quirk/lib/font-awesome/css/font-awesome.css')?>" rel="stylesheet">
    <link href="<?php echo base_url('/public/assets/theme/quirk/lib/jquery-toggles/toggles-full.css')?>" rel="stylesheet">
    <link href="<?php echo base_url('/public/assets/theme/quirk/css/quirk.css')?>" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="<?php echo base_url('/public/assets/theme/quirk/lib/html5shiv/html5shiv.js')?>"></script>
    <script src="<?php echo base_url('/public/assets/theme/quirk/lib/respond/respond.min.js')?>"></script>
    <![endif]-->
</head>

<body class="sticky-header">

    <section>
        <!-- Navbar -->
        <?= $this->include('quirk/layouts/navbar') ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?= $this->include('quirk/layouts/sidebar') ?>
        
        <!-- body content start-->
        <div class="body-content" style="min-height: 1200px;">
            <div class="page-content">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
        <!-- body content end-->
    </section>

    <?= $this->include('quirk/layouts/footer') ?>
</body>
</html>