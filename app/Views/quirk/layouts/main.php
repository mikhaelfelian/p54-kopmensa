<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-05-26
 * This file represents the main content view.
 */
?>

<?= $this->extend('quirk/layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">Dashboard</h4>
            </div>
            <div class="panel-body">
                <!-- Your main content here -->
                <p>Welcome to <?= $Pengaturan->judul_app ?? 'Kopmensa POS' ?></p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 