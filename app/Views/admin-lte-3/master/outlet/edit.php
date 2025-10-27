<?= $this->extend(theme_path('main')) ?>
<?= $this->section('content') ?>
<div class="row">
    <!-- Form Edit Outlet -->
    <div class="col-md-6">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit"></i> Form Edit Outlet
                </h3>
            </div>
            <?= form_open('master/outlet/update/' . $outlet->id) ?>
            <div class="card-body">
                <div class="form-group">
                    <label>Nama <span class="text-danger">*</span></label>
                    <?= form_input([
                        'type' => 'text',
                        'name' => 'nama',
                        'class' => 'form-control rounded-0',
                        'value' => $outlet->nama,
                        'placeholder' => 'Nama Outlet