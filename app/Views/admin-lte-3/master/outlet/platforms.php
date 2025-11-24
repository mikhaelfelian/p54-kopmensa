<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <?= view('admin-lte-3/master/outlet/partials/platform_manager', [
            'outlet'             => $outlet,
            'assignedPlatforms'  => $assignedPlatforms,
            'availablePlatforms' => $availablePlatforms,
            'embedded'           => false,
        ]) ?>
    </div>
</div>

<?= $this->endSection() ?>

