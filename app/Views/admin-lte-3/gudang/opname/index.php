<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-28
 * Github : github.com/mikhaelfelian
 * description : View for displaying stock opname data.
 * This file represents the opname index view.
 */
?>

<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Data Stok Opname</h3>
                <div class="card-tools">
                    <a href="<?= base_url('gudang/opname/create') ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Tambah Opname
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th>Tgl Opname</th>
                            <th>User</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                        <?= form_open(base_url('gudang/opname'), ['autocomplete' => 'off']) ?>
                        <tr>
                            <th class="text-center"></th>
                            <th>
                                <?= form_input([
                                    'id' => 'tgl',
                                    'name' => 'tgl',
                                    'class' => 'form-control rounded-0',
                                    'placeholder' => 'Isikan Tgl ...',
                                    'type' => 'date',
                                    'value' => $tgl ?? ''
                                ]) ?>
                            </th>
                            <th></th>
                            <th>
                                <?= form_input([
                                    'id' => 'ket',
                                    'name' => 'ket',
                                    'class' => 'form-control rounded-0',
                                    'placeholder' => 'Isikan Keterangan ...',
                                    'value' => $ket ?? ''
                                ]) ?>
                            </th>
                            <th>
                                <button type="submit" class="btn btn-primary btn-flat">
                                    <i class="fa fa-search-plus"></i> Filter
                                </button>
                                <a href="<?= base_url('gudang/opname') ?>" class="btn btn-secondary btn-flat">
                                    <i class="fa fa-refresh"></i> Reset
                                </a>
                            </th>
                        </tr>
                        <?= form_close() ?>
                    </thead>
                    <tbody>
                        <?php if (empty($opname)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($opname as $key => $row): ?>
                                <tr>
                                    <td style="width: 50px;" class="text-center">
                                        <?= (($currentPage - 1) * $perPage) + $key + 1 ?>.
                                    </td>
                                    <td style="width: 100px;" class="text-left">
                                        <?= date('d/m/Y', strtotime($row->created_at)) ?>
                                    </td>
                                    <td style="width: 250px;" class="text-left">
                                        <?= $row->user_name ?>
                                    </td>
                                    <td style="width: 350px;" class="text-left">
                                        <?= $row->keterangan ?? '-' ?>
                                    </td>
                                    <td style="width: 100px;" class="text-left">
                                        <?php if (isset($user) && $row->id_user == $user->id): ?>
                                            <a href="<?= base_url("gudang/opname/edit/{$row->id}") ?>" 
                                               class="btn btn-info btn-flat btn-xs" style="width: 55px;">
                                                <i class="fa fa-edit"></i> Ubah
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= base_url("gudang/opname/detail/{$row->id}") ?>" 
                                           class="btn btn-warning btn-flat btn-xs" style="width: 55px;">
                                            <i class="fa fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
            <?php if ($pager): ?>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        <?= $pager->links('opname', 'adminlte_pagination') ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize date picker
    $('#tgl').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });
});
</script>
<?= $this->endSection() ?> 