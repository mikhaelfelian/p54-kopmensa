<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-05-26
 * This file represents the footer component for the Quirk theme.
 */
?>
<footer class="main-footer">
    <div class="float-right d-none d-sm-block">
        <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; <?= date('Y') ?> <a href="<?= base_url() ?>"><?= $Pengaturan->judul_app ?? env('app.name') ?></a>.</strong> All rights reserved.
</footer> 